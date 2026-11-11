<?php

namespace App\Console\Commands;

use App\Library\Grammatic;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Проверяет и восстанавливает ссылки text_wordform.word_id на слова корпуса.
 *
 * После пересоздания words грамматические аннотации могут ссылаться на старые,
 * уже отсутствующие записи. Команда восстанавливает только word_id,
 * не пересоздавая разборы и не изменяя relevance.
 *
 * Для восстановления требуется одно слово по text_id + w_id, единственное
 * соответствующее вхождение в XML и совпадение слова и предложения.
 * Неоднозначные случаи пропускаются.
 *
 * По умолчанию выполняется аудит. Для записи нужны --apply и --text-id.
 * Перед записью кандидаты повторно проверяются внутри одной транзакции.
 * После обновления проверяется неизменность остальных полей каждой строки.
 *
 * Команда не изменяет XML и meaning_text, не проверяет лингвистическую
 * правильность разбора. Применять без параллельного редактирования текста.
 * Лимит ограничивает отобранные связи, а не объём чтения БД и XML.
 *
 * Все журналы применения нужно сохранять: каждый содержит только свою партию.
 * repair_prepared не означает фиксацию; успешная фиксация отмечается committed.
 * Файл журнала и транзакция БД не атомарны между собой. При аварийном
 * завершении без финального статуса результат нужно сверять с БД.
 *
 * Примеры:
 * php artisan corpus:repair-wordform-word-ids --text-id=16 --limit=100
 * php artisan corpus:repair-wordform-word-ids --text-id=16 --limit=100 --apply
 */
class RepairTextWordformWordIds extends Command
{
    protected $signature = 'corpus:repair-wordform-word-ids
        {--text-id= : Restrict to one text; required with --apply}
        {--limit=100 : Maximum number of broken links examined, 1..1000}
        {--apply : Apply verified repairs in one transaction}
        {--report= : New JSONL report path; existing files are never overwritten}';

    protected $description = 'Audit or repair missing text_wordform.word_id without changing relevances';

    /** @var resource|null Открытый поток JSONL-журнала текущего запуска. */
    protected $reportHandle;

    /** @var array Индексы XML по текстам; перед применением перечитываются. */
    protected $xmlCache = [];

    /** @var bool Успешно ли вернулась транзакция применения. */
    protected $committed = false;

    /**
     * Управляет запуском команды и освобождает файл журнала при завершении.
     *
     * @return int 0 при успешном завершении или отмене, 1 при ошибке.
     */
    public function handle()
    {
        $this->reportHandle = null;
        $this->xmlCache = [];
        $this->committed = false;

        try {
            $options = $this->readOptions();
            $reportPath = $this->openReport();
            $this->writeStart($options);

            return $this->runAudit($options, $reportPath);
        } catch (\Throwable $e) {
            $this->reportFailure($e);
            return 1;
        } finally {
            if (is_resource($this->reportHandle)) {
                fclose($this->reportHandle);
            }

            $this->reportHandle = null;
        }
    }

    /**
     * Проверяет параметры и запрещает применение без конкретного текста.
     *
     * @return array Параметры apply, text_id и limit.
     */
    protected function readOptions()
    {
        $textOption = $this->option('text-id');
        $textId = $textOption === null ? null : $this->positiveInteger($textOption);
        $limit = $this->positiveInteger($this->option('limit'), 1000);
        $apply = (bool) $this->option('apply');

        if ($textOption !== null && $textId === false) {
            throw new \InvalidArgumentException('--text-id must be a positive integer.');
        }

        if ($limit === false) {
            throw new \InvalidArgumentException('--limit must be between 1 and 1000.');
        }

        if ($apply && $textId === null) {
            throw new \InvalidArgumentException('--apply requires --text-id.');
        }

        return ['apply' => $apply, 'text_id' => $textId, 'limit' => $limit];
    }

    /**
     * Проверяет положительное целое число с необязательной верхней границей.
     *
     * @return int|false
     */
    protected function positiveInteger($value, $maximum = null)
    {
        $options = ['min_range' => 1];

        if ($maximum !== null) {
            $options['max_range'] = $maximum;
        }

        return filter_var($value, FILTER_VALIDATE_INT, ['options' => $options]);
    }

    /**
     * Создаёт новый журнал, не перезаписывая существующий файл.
     *
     * @return string Путь к открытому журналу.
     */
    protected function openReport()
    {
        $path = $this->option('report');

        if (!$path) {
            $directory = storage_path('logs/word-id-repairs');
            $this->ensureReportDirectory($directory);
            $path = $directory . '/' . date('Ymd_His') . '_' . uniqid() . '.jsonl';
        }

        $this->reportHandle = @fopen($path, 'x');

        if (!$this->reportHandle) {
            throw new \RuntimeException('Cannot create report. Parent directory must exist; report file must not already exist.');
        }

        return $path;
    }

    /**
     * Создаёт стандартную папку журналов, если её ещё нет.
     *
     * @return void
     */
    protected function ensureReportDirectory($directory)
    {
        if (!is_dir($directory) && !@mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new \RuntimeException('Cannot create report directory.');
        }
    }

    /**
     * Записывает режим и параметры запуска в начало журнала.
     *
     * @return void
     */
    protected function writeStart(array $options)
    {
        $this->writeReport([
            'event' => 'start',
            'time' => date('c'),
            'mode' => $options['apply'] ? 'apply' : 'audit',
            'text_id' => $options['text_id'],
            'limit' => $options['limit'],
        ]);
    }

    /**
     * Выполняет аудит и при явном разрешении передаёт план на применение.
     *
     * @return int Код завершения команды.
     */
    protected function runAudit(array $options, $reportPath)
    {
        $rows = $this->brokenLinks($options['text_id'], $options['limit']);
        $audit = $this->buildPlan($rows);
        $this->showSummary(count($rows), $audit['counts'], $reportPath);

        if (!$options['apply']) {
            $this->writeReport([
                'event' => 'audit_finished',
                'examined' => count($rows),
                'counts' => $audit['counts'],
            ]);

            $this->info('AUDIT ONLY: database unchanged.');
            return 0;
        }

        return $this->applyIfConfirmed($audit['plan'], $options['text_id']);
    }

    /**
     * Читает ограниченную выборку связей, у которых отсутствует слово.
     *
     * @return mixed Результат get(); допускается массив строк.
     */
    protected function brokenLinks($textId, $limit)
    {
        $query = DB::table('text_wordform as links')
            ->leftJoin('words as w', 'w.id', '=', 'links.word_id')
            ->whereNull('w.id')
            ->select('links.*')
            ->orderBy('links.text_id')
            ->orderBy('links.w_id')
            ->orderBy('links.wordform_id')
            ->orderBy('links.gramset_id')
            ->orderBy('links.word_id');

        if ($textId !== null) {
            $query->where('links.text_id', $textId);
        }

        return $query->limit($limit)->get();
    }

    /**
     * Проверяет строки, журналирует результаты и собирает план исправлений.
     *
     * @return array Счётчики статусов и список однозначных кандидатов.
     */
    protected function buildPlan($rows)
    {
        $counts = [];
        $plan = [];

        foreach ($rows as $row) {
            $row = (array) $row;
            $assessment = $this->assess($row, false);
            $status = $assessment['status'];
            $counts[$status] = ($counts[$status] ?? 0) + 1;

            $this->writeReport([
                'event' => 'assessment',
                'status' => $status,
                'before' => $row,
                'candidate_word_id' => $assessment['word_id'] ?? null,
            ]);

            if ($status === 'candidate') {
                $plan[] = ['before' => $row, 'word_id' => $assessment['word_id']];
            }
        }

        return ['counts' => $counts, 'plan' => $plan];
    }

    /**
     * Выводит краткую сводку аудита и путь к полному журналу.
     *
     * @return void
     */
    protected function showSummary($examined, array $counts, $reportPath)
    {
        $this->line('Examined links: ' . $examined);

        foreach ($counts as $status => $count) {
            $this->line($status . ': ' . $count);
        }

        $this->line('Report: ' . $reportPath);
    }

    /**
     * Запрашивает разрешение и отмечает успешную фиксацию партии.
     *
     * Признак committed устанавливается до записи финального события:
     * ошибка журналирования после фиксации не должна сообщаться как откат.
     *
     * @return int Код завершения команды.
     */
    protected function applyIfConfirmed(array $plan, $textId)
    {
        if (!$plan) {
            $this->writeReport(['event' => 'nothing_to_apply']);
            $this->info('No verified candidates to apply.');
            return 0;
        }

        if (!$this->confirmRepair(count($plan), $textId)) {
            $this->writeReport(['event' => 'cancelled']);
            $this->info('Cancelled: database unchanged.');
            return 0;
        }

        $updated = $this->applyPlan($plan, $textId);
        $this->committed = true;

        $this->writeReport(['event' => 'committed', 'time' => date('c'), 'updated' => $updated]);
        $this->info('COMMITTED: ' . $updated . ' word_id values repaired; relevances unchanged.');

        return 0;
    }

    /**
     * Принимает только явные yes или y; журналирует полученный ответ.
     *
     * Пустой ответ и неинтерактивный запуск не разрешают запись.
     *
     * @return bool
     */
    protected function confirmRepair($count, $textId)
    {
        if (!$this->input->isInteractive()) {
            $this->writeReport(['event' => 'confirmation', 'accepted' => false, 'reason' => 'non_interactive']);
            return false;
        }

        $question = 'Repair ' . $count . ' links in text ' . $textId . '? Only word_id will change. Type yes to continue';
        $answer = (string) $this->ask($question, 'no');
        $accepted = in_array(strtolower(trim($answer)), ['yes', 'y'], true);

        $this->writeReport([
            'event' => 'confirmation',
            'answer' => $answer,
            'answer_hex' => bin2hex($answer),
            'accepted' => $accepted,
        ]);

        return $accepted;
    }

    /**
     * Применяет всю партию в одной транзакции с повторным чтением XML.
     *
     * Ошибка любой строки прерывает применение и откатывает всю партию.
     *
     * @return int Число проверенных и обновлённых строк.
     */
    protected function applyPlan(array $plan, $textId)
    {
        return DB::transaction(function () use ($plan, $textId) {
            $this->xmlCache = [];
            $this->loadXmlIndex($textId, true);

            foreach ($plan as $item) {
                $this->repairRow($item);
            }

            $updated = count($plan);
            $this->writeReport(['event' => 'batch_verified_before_commit', 'updated' => $updated]);

            return $updated;
        });
    }

    /**
     * Повторно проверяет кандидата, журналирует снимки и меняет только word_id.
     *
     * Вызывается только внутри транзакции применения.
     *
     * @return void
     */
    protected function repairRow(array $item)
    {
        $before = $item['before'];
        $assessment = $this->assess($before, true);

        if ($assessment['status'] !== 'candidate' || (int) $assessment['word_id'] !== (int) $item['word_id']) {
            throw new \RuntimeException(
                'Candidate changed: text ' . $before['text_id']
                    . ', w_id ' . $before['w_id'] . ', status ' . $assessment['status']
            );
        }

        $after = $before;
        $after['word_id'] = (int) $item['word_id'];

        $this->writeReport(['event' => 'repair_prepared', 'before' => $before, 'after' => $after]);
        $query = $this->snapshotQuery($before);

        if ($query->update(['word_id' => $after['word_id']]) !== 1) {
            throw new \RuntimeException('Expected exactly one updated row.');
        }

        $this->verifyUpdatedRow($after);
    }

    /**
     * Ограничивает обновление полным исходным снимком строки, включая NULL.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function snapshotQuery(array $before)
    {
        $query = $this->pairQuery($before);

        foreach ($before as $column => $value) {
            if ($value === null) {
                $query->whereNull($column);
            } else {
                $query->where($column, $value);
            }
        }

        return $query;
    }

    /**
     * Проверяет полное совпадение сохранённой строки с ожидаемым снимком.
     *
     * Это также проверка неизменности relevance и остальных полей.
     *
     * @return void
     */
    protected function verifyUpdatedRow(array $after)
    {
        $rows = $this->pairQuery($after)->lockForUpdate()->get();

        if (count($rows) !== 1 || $this->canonical((array) $rows[0]) !== $this->canonical($after)) {
            throw new \RuntimeException('Post-update verification failed.');
        }
    }

    /**
     * Проверяет однозначность нового слова, состояние связи и соответствие XML.
     *
     * Режим с блокировками предназначен для вызова внутри транзакции.
     *
     * @return array Статус проверки; для candidate также актуальный word_id.
     */
    protected function assess(array $row, $lock)
    {
        $textId = (int) $row['text_id'];
        $wId = (int) $row['w_id'];

        if ($textId < 1 || $wId < 1) {
            return ['status' => 'invalid_identity'];
        }

        $query = DB::table('words')->where('text_id', $textId)->where('w_id', $wId);
        $words = $this->readRows($query, $lock);

        if (count($words) === 0) {
            return ['status' => 'no_word'];
        }

        if (count($words) !== 1) {
            return ['status' => 'multiple_words'];
        }

        $status = $this->assessAnnotation($row, $lock);

        if ($status !== null) {
            return ['status' => $status];
        }

        return $this->assessWordXml((array) $words[0], $textId, $wId, $lock);
    }

    /**
     * Проверяет исходную строку разбора и отсутствие прежнего слова.
     *
     * @return string|null Причина отказа либо null при успешной проверке.
     */
    protected function assessAnnotation(array $row, $lock)
    {
        $pairs = $this->readRows($this->pairQuery($row), $lock);

        if (count($pairs) !== 1) {
            return 'annotation_pair_missing_or_duplicate';
        }

        $current = (array) $pairs[0];

        if ($this->canonical($current) !== $this->canonical($row)) {
            return 'annotation_changed';
        }

        if ($current['word_id'] !== null) {
            $query = DB::table('words')->where('id', $current['word_id']);

            if ($lock) {
                $query->lockForUpdate();
            }

            if ($query->first()) {
                return 'old_word_still_exists';
            }
        }

        if ((int) $row['wordform_id'] < 1 || (int) $row['gramset_id'] < 1) {
            return 'invalid_annotation_pair';
        }

        return null;
    }

    /**
     * Сопоставляет поисковое слово с единственным XML-вхождением.
     *
     * В words.word хранится поисковая форма, а XML сохраняет исходное
     * написание. Поэтому XML-слово преобразуется через changeLetters()
     * с языком текста, как при подготовке word_for_search.
     *
     * При применении язык текста читается с блокировкой внутри транзакции.
     * XML и сохранённое слово не изменяются.
     *
     * @return array Статус проверки и, при успехе, идентификатор слова.
     */
    protected function assessWordXml(array $word, $textId, $wId, $lock)
    {
        $texts = $this->readRows(
            DB::table('texts')->where('id', $textId),
            $lock
        );

        if (count($texts) !== 1) {
            return ['status' => 'text_missing_or_invalid_language'];
        }

        $text = (array) $texts[0];
        $langId = $this->positiveInteger($text['lang_id']);

        if ($langId === false) {
            return ['status' => 'text_missing_or_invalid_language'];
        }

        $index = $this->loadXmlIndex($textId, $lock);

        if ($index['invalid']) {
            return ['status' => 'invalid_text_sentence_xml'];
        }

        $matches = $index['words'][$wId] ?? [];

        if (count($matches) !== 1) {
            return ['status' => 'xml_missing_or_duplicate'];
        }

        $match = $matches[0];

        $xmlWordForSearch = Grammatic::changeLetters(
            trim((string) $match['word']),
            $langId
        );

        if (
            (int) $word['sentence_id'] !== $match['sentence_id']
            || (int) $word['s_id'] !== $match['s_id']
            || $this->normalize($word['word']) !== $xmlWordForSearch
        ) {
            return ['status' => 'word_xml_mismatch'];
        }

        return [
            'status' => 'candidate',
            'word_id' => (int) $word['id'],
        ];
    }

    /**
     * Выполняет чтение строк с необязательной блокировкой.
     *
     * @return mixed Результат get(); допускается массив строк.
     */
    protected function readRows($query, $lock)
    {
        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->get();
    }

    /**
     * Строит и кеширует индекс всех XML-вхождений текста.
     *
     * Ошибка любого предложения делает индекс непригодным для восстановления.
     * Перед применением кеш очищается, затем XML читается под блокировками.
     *
     * @return array Признак invalid и списки вхождений words.
     */
    protected function loadXmlIndex($textId, $lock)
    {
        if (isset($this->xmlCache[$textId])) {
            return $this->xmlCache[$textId];
        }

        $query = DB::table('sentences')->where('text_id', $textId)->orderBy('id');
        $sentences = $this->readRows($query, $lock);
        $index = ['invalid' => count($sentences) === 0, 'words' => []];
        $previous = libxml_use_internal_errors(true);

        try {
            foreach ($sentences as $sentence) {
                $this->appendSentenceXml($index, (array) $sentence);
            }
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        $this->xmlCache[$textId] = $index;
        return $index;
    }

    /**
     * Добавляет в индекс слова предложения, сохраняя повторные вхождения.
     *
     * DTD/ENTITY, ошибки XML и недопустимые идентификаторы запрещают ремонт.
     *
     * @return void
     */
    protected function appendSentenceXml(array &$index, array $sentence)
    {
        $source = (string) $sentence['text_xml'];
        libxml_clear_errors();

        if (preg_match('/<!DOCTYPE|<!ENTITY/i', $source)) {
            $index['invalid'] = true;
            return;
        }

        $xml = simplexml_load_string($source, 'SimpleXMLElement', LIBXML_NONET);

        if ($xml === false) {
            $index['invalid'] = true;
            return;
        }

        foreach ($xml->xpath('//w') as $word) {
            $wId = $this->positiveInteger((string) $word['id']);

            if ($wId === false) {
                $index['invalid'] = true;
                continue;
            }

            $index['words'][$wId][] = [
                'sentence_id' => (int) $sentence['id'],
                's_id' => (int) $sentence['s_id'],
                'word' => (string) $word,
            ];
        }
    }

    /**
     * Создаёт запрос пары по её составному ключу, без ограничения по word_id.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function pairQuery(array $row)
    {
        return DB::table('text_wordform')
            ->where('text_id', $row['text_id'])
            ->where('w_id', $row['w_id'])
            ->where('wordform_id', $row['wordform_id'])
            ->where('gramset_id', $row['gramset_id']);
    }

    /**
     * Нормализует слово для сравнения с XML, не изменяя сохранённые данные.
     *
     * @return string
     */
    protected function normalize($word)
    {
        return Grammatic::changeLetters(mb_strtolower(trim((string) $word)));
    }

    /**
     * Выравнивает порядок полей и типы значений для сравнения снимков строк.
     *
     * NULL сохраняется отдельно от пустой строки.
     *
     * @return array
     */
    protected function canonical(array $row)
    {
        ksort($row);

        foreach ($row as $key => $value) {
            $row[$key] = $value === null ? null : (string) $value;
        }

        return $row;
    }

    /**
     * Кодирует событие JSONL, полностью записывает его и сбрасывает буфер.
     *
     * Сброс буфера не делает журнал атомарным с БД и не заменяет резервную копию.
     *
     * @return void
     */
    protected function writeReport(array $record)
    {
        $json = json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($json === false) {
            throw new \RuntimeException('Cannot encode report record.');
        }

        $this->writeReportBytes($json . PHP_EOL);

        if (!fflush($this->reportHandle)) {
            throw new \RuntimeException('Cannot flush report.');
        }
    }

    /**
     * Записывает всю строку журнала, учитывая возможную частичную запись.
     *
     * @return void
     */
    protected function writeReportBytes($data)
    {
        $length = strlen($data);
        $written = 0;

        while ($written < $length) {
            $bytes = fwrite($this->reportHandle, substr($data, $written));

            if ($bytes === false || $bytes === 0) {
                throw new \RuntimeException('Cannot write report.');
            }

            $written += $bytes;
        }
    }

    /**
     * Сообщает об ошибке, различая сбой до и после успешной фиксации.
     *
     * Ошибка финальной записи журнала не должна скрывать исходную ошибку.
     *
     * @return void
     */
    protected function reportFailure(\Throwable $error)
    {
        if (is_resource($this->reportHandle)) {
            try {
                $this->writeReport([
                    'event' => $this->committed ? 'error_after_commit' : 'failed_without_successful_commit',
                    'message' => $error->getMessage(),
                ]);
            } catch (\Throwable $reportError) {
                $this->error('Could not append final report status: ' . $reportError->getMessage());
            }
        }

        if ($this->committed) {
            $this->error('DATABASE WAS COMMITTED, but final reporting failed.');
        } else {
            $this->error('Command failed before successful completion. Check the report; no successful commit is confirmed.');
        }

        $this->error($error->getMessage());
    }
}
