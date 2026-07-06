<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Одноразовая команда для безопасного дозаполнения meaning_texts на английском языке.
 *
 * Идея:
 * - работаем со всеми леммами, поле is_norm игнорируем;
 * - берём только те значения, у которых есть русский meaning_text (lang_id = 2);
 * - если у значения уже есть английский meaning_text (lang_id = 3), ничего не делаем;
 * - если английского текста нет, ищем "доноров" с тем же pos_id и тем же безопасно
 *   нормализованным русским толкованием;
 * - если найден ровно один distinct английский вариант, создаём новую запись
 *   в meaning_texts;
 * - если найдено 0 или 2+ вариантов, ничего автоматически не вставляем.
 */
class BackfillSafeMeaningEn extends Command
{
    protected $name = 'vepkar:backfill-safe-meaning-en';
    /**
     * Аргументы команды.
     *
     * --dry-run  : только посчитать и показать, что будет сделано
     * --limit    : ограничить число вставок для осторожного первого прогона
     * --chunk    : размер чанка для insertOrIgnore
     */
    protected $signature = 'vepkar:backfill-safe-meaning-en
                            {--dry-run : Только посчитать кандидатов, без INSERT}
                            {--limit= : Ограничить число создаваемых EN-записей}
                            {--chunk=500 : Размер чанка для пакетной вставки}';

    /**
     * Описание команды.
     */
    protected $description = 'Безопасно создаёт missing English meaning_texts по точному совпадению POS + meaning_ru (с нормализацией только пробелов).';

    /**
     * Создать экземпляр команды.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Выполнить команду.
     *
     * @return void
     */
    public function fire()
    {
        $this->info('Команда зарегистрирована и запускается.');
    }

    /**
     * Идентификаторы языков по таблице langs.
     */
    private const LANG_RU = 2;
    private const LANG_EN = 3;

    /**
     * Основная точка входа команды.
     *
     * Общая схема:
     * 1. Загружаем плоский набор данных по meanings.
     * 2. Группируем строки по safe key = pos_id + normalized meaning_ru.
     * 3. Для каждой группы ищем distinct английские значения у "доноров".
     * 4. Если distinct EN ровно один, строим INSERT-действия для строк без EN.
     * 5. В dry-run только печатаем статистику; иначе выполняем insertOrIgnore.
     */
    public function handle(): int
    {
        $rows = $this->loadMeaningRows();

        if (empty($rows)) {
            $this->warn('Не найдено строк с русским meaning_text.');
            return 0;
        }

        $grouped = $this->groupRowsBySafeKey($rows);
        $plan = $this->buildInsertPlan($grouped);

        $actions = $plan['actions'];
        $stats = $plan['stats'];

        // При необходимости ограничиваем количество вставок для осторожного запуска.
        $limit = $this->option('limit');
        if ($limit !== null) {
            $actions = array_slice($actions, 0, max(0, (int) $limit));
        }

        $this->printStats($stats, $actions);
        $this->printPreviewTable($actions);

        if ($this->option('dry-run')) {
            $this->warn('Dry-run режим: записи в БД не вносились.');
            return 0;
        }

        $inserted = $this->insertMissingEnglishTexts($actions, (int) $this->option('chunk'));

        $this->info("Готово. Попыток вставки: " . count($actions) . ". Реально вставлено: {$inserted}.");
        return 0;
    }

/**
 * Загружает рабочий набор данных:
 * - meaning_id
 * - lemma_id
 * - lemma
 * - lang_id леммы
 * - pos_id
 * - meaning_n
 * - meaning_ru
 * - meaning_en (если уже существует)
 *
 * Важно:
 * - is_norm НЕ учитываем;
 * - берём только значения, у которых есть русский meaning_text;
 * - английский meaning_text подцепляем левым JOIN;
 * - в Laravel 5.2 результат get() может прийти как обычный массив,
 *   поэтому НЕ используем ->map().
 *
 * @return array
 */
private function loadMeaningRows(): array
{
    $rows = DB::table('meanings as m')
        ->join('lemmas as l', 'l.id', '=', 'm.lemma_id')
        ->join('meaning_texts as ru', function ($join) {
            $join->on('ru.meaning_id', '=', 'm.id')
                 ->where('ru.lang_id', '=', self::LANG_RU);
        })
        ->leftJoin('meaning_texts as en', function ($join) {
            $join->on('en.meaning_id', '=', 'm.id')
                 ->where('en.lang_id', '=', self::LANG_EN);
        })
        ->select([
            'm.id as meaning_id',
            'm.lemma_id',
            'm.meaning_n',
            'l.lemma',
            'l.lang_id as lemma_lang_id',
            'l.pos_id',
            'ru.meaning_text as meaning_ru',
            'en.meaning_text as meaning_en',
        ])
        ->orderBy('m.id')
        ->get();

    /**
     * Laravel 5.2 может вернуть обычный массив объектов stdClass,
     * поэтому нормализуем результат через foreach, а не через Collection::map().
     */
    $result = [];

    foreach ($rows as $row) {
        $result[] = [
            'meaning_id' => (int) $row->meaning_id,
            'lemma_id' => (int) $row->lemma_id,
            'meaning_n' => (int) $row->meaning_n,
            'lemma' => (string) $row->lemma,
            'lemma_lang_id' => (int) $row->lemma_lang_id,
            'pos_id' => $row->pos_id !== null ? (int) $row->pos_id : null,
            'meaning_ru' => (string) $row->meaning_ru,
            'meaning_en' => $row->meaning_en !== null ? (string) $row->meaning_en : null,
        ];
    }

    return $result;
}
    /**
     * Группирует строки по безопасному ключу:
     *   pos_id + normalizeSpaces(meaning_ru)
     *
     * Здесь намеренно НЕТ:
     * - удаления текста в скобках
     * - обрезания по точке с запятой
     * - любой "умной" семантической нормализации
     */
    private function groupRowsBySafeKey(array $rows): array
    {
        $grouped = [];

        foreach ($rows as $row) {
            $normalizedRu = $this->normalizeMeaningText($row['meaning_ru']);
            if ($normalizedRu === '') {
                continue;
            }

            $safeKey = $this->buildSafeKey($row['pos_id'], $normalizedRu);

            if (!isset($grouped[$safeKey])) {
                $grouped[$safeKey] = [
                    'pos_id' => $row['pos_id'],
                    'meaning_ru_normalized' => $normalizedRu,
                    'rows' => [],
                ];
            }

            $grouped[$safeKey]['rows'][] = $row;
        }

        return $grouped;
    }

    /**
     * Строит план вставок.
     *
     * Для каждой группы:
     * - собираем distinct непустые EN тексты;
     * - находим строки без EN;
     * - если distinct EN ровно один -> готовим INSERT для каждой строки без EN;
     * - если EN нет совсем -> группа остаётся без действия;
     * - если EN несколько -> группа считается неоднозначной и тоже пропускается.
     */
    private function buildInsertPlan(array $grouped): array
    {
        $actions = [];

        $stats = [
            'groups_total' => 0,
            'groups_with_missing_en' => 0,
            'groups_unambiguous' => 0,
            'groups_ambiguous' => 0,
            'groups_without_donor_en' => 0,
            'rows_with_existing_en' => 0,
            'rows_missing_en' => 0,
            'rows_planned_for_insert' => 0,
        ];

        foreach ($grouped as $group) {
            $stats['groups_total']++;

            $distinctEnglish = [];
            $missingRows = [];

            /**
             * разбираем строки группы на:
             * - доноров с уже существующим EN
             * - целевые строки без EN
             */
            foreach ($group['rows'] as $row) {
                $canonicalEn = $this->canonicalEnglish($row['meaning_en']);

                if ($canonicalEn !== '') {
                    $distinctEnglish[$canonicalEn] = true;
                    $stats['rows_with_existing_en']++;
                } else {
                    $missingRows[] = $row;
                    $stats['rows_missing_en']++;
                }
            }

            if (empty($missingRows)) {
                continue;
            }

            $stats['groups_with_missing_en']++;

            $englishVariants = array_keys($distinctEnglish);
            sort($englishVariants, SORT_NATURAL | SORT_FLAG_CASE);

            /**
             * применяем строгое безопасное правило:
             * только один distinct EN -> можно строить INSERT-план.
             */
            if (count($englishVariants) === 1) {
                $stats['groups_unambiguous']++;
                $reusedEnglish = $englishVariants[0];
                $timestamp = Carbon::now();


                foreach ($missingRows as $row) {
                    $actions[] = [
                        'meaning_id' => $row['meaning_id'],
                        'lang_id' => self::LANG_EN,
                        'meaning_text' => $reusedEnglish,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,

                        // Ниже метаданные не для INSERT, а для preview / логирования.
                        '_lemma' => $row['lemma'],
                        '_lemma_id' => $row['lemma_id'],
                        '_meaning_n' => $row['meaning_n'],
                        '_pos_id' => $row['pos_id'],
                        '_meaning_ru' => $row['meaning_ru'],
                        '_meaning_ru_normalized' => $group['meaning_ru_normalized'],
                    ];
                }
            } elseif (count($englishVariants) >= 2) {
                $stats['groups_ambiguous']++;
            } else {
                $stats['groups_without_donor_en']++;
            }
        }

        $stats['rows_planned_for_insert'] = count($actions);

        return [
            'actions' => $actions,
            'stats' => $stats,
        ];
    }

    /**
     * Выполняет пакетные вставки новых английских meaning_texts.
     *
     * Важно:
     * - Laravel 5.2 не поддерживает insertOrIgnore в стандартном Query Builder;
     * - поэтому здесь используется обычный insert();
     * - перед вставкой дополнительно проверяем, какие meaning_id уже получили EN-запись,
     *   чтобы не словить duplicate key по unique (meaning_id, lang_id).
     *
     * @param array $actions
     * @param int   $chunkSize
     * @return int
     */
    private function insertMissingEnglishTexts(array $actions, int $chunkSize = 500): int
    {
        if (empty($actions)) {
            return 0;
        }

        $chunkSize = max(1, $chunkSize);
        $inserted = 0;

        DB::transaction(function () use ($actions, $chunkSize, &$inserted) {
            foreach (array_chunk($actions, $chunkSize) as $chunk) {
                /**
                 * Крупный блок 1:
                 * из текущего чанка собираем meaning_id,
                 * чтобы проверить, не появились ли уже EN-строки.
                 */
                $meaningIds = [];

                foreach ($chunk as $row) {
                    $meaningIds[] = $row['meaning_id'];
                }

                /**
                 * Крупный блок 2:
                 * получаем meaning_id, для которых EN уже существует.
                 */
                $existingMeaningIds = DB::table('meaning_texts')
                    ->where('lang_id', self::LANG_EN)
                    ->whereIn('meaning_id', $meaningIds)
                    ->lists('meaning_id');

                $existingMap = [];
                foreach ($existingMeaningIds as $meaningId) {
                    $existingMap[(int) $meaningId] = true;
                }

                /**
                 * Крупный блок 3:
                 * формируем payload только для тех meaning_id,
                 * у которых EN всё ещё отсутствует.
                 */
                $payload = [];

                foreach ($chunk as $row) {
                    if (isset($existingMap[(int) $row['meaning_id']])) {
                        continue;
                    }

                    $payload[] = [
                        'meaning_id' => $row['meaning_id'],
                        'lang_id' => $row['lang_id'],
                        'meaning_text' => $row['meaning_text'],
                        'created_at' => $row['created_at'],
                        'updated_at' => $row['updated_at'],
                    ];
                }

                if (!empty($payload)) {
                    DB::table('meaning_texts')->insert($payload);
                    $inserted += count($payload);
                }
            }
        });

        return $inserted;
    }

    /**
     * Печатает итоговую статистику.
     */
    private function printStats(array $stats, array $actions): void
    {
        $this->info('Статистика безопасного backfill:');
        $this->line('  Всего групп: ' . $stats['groups_total']);
        $this->line('  Групп с пропущенным EN: ' . $stats['groups_with_missing_en']);
        $this->line('  Однозначных групп (ровно 1 distinct EN): ' . $stats['groups_unambiguous']);
        $this->line('  Неоднозначных групп (2+ distinct EN): ' . $stats['groups_ambiguous']);
        $this->line('  Групп без донора EN: ' . $stats['groups_without_donor_en']);
        $this->line('  Строк с уже существующим EN: ' . $stats['rows_with_existing_en']);
        $this->line('  Строк без EN: ' . $stats['rows_missing_en']);
        $this->line('  Планируемых INSERT: ' . $stats['rows_planned_for_insert']);
        $this->line('  INSERT после учёта --limit: ' . count($actions));
    }

    /**
     * Показывает короткий preview первых строк, которые будут вставлены.
     */
    private function printPreviewTable(array $actions, int $maxRows = 20): void
    {
        if (empty($actions)) {
            $this->warn('Нет кандидатов для вставки.');
            return;
        }

        $preview = array_slice($actions, 0, $maxRows);

        $this->table(
            ['meaning_id', 'lemma', 'meaning_n', 'pos_id', 'meaning_ru', 'proposed_en'],
            array_map(function ($row) {
                return [
                    $row['meaning_id'],
                    $row['_lemma'],
                    $row['_meaning_n'],
                    $row['_pos_id'],
                    $row['_meaning_ru'],
                    $row['meaning_text'],
                ];
            }, $preview)
        );
    }

    /**
     * Безопасная нормализация русского толкования.
     *
     * Разрешено только:
     * - trim по краям
     * - схлопывание повторных пробелов / табов / переводов строки в один пробел
     *
     * Никакой "умной" нормализации здесь нет.
     */
    private function normalizeMeaningText(?string $text): string
    {
        $text = trim((string) ($text ?? ''));
        if ($text === '') {
            return '';
        }

        return preg_replace('/\s+/u', ' ', $text) ?? '';
    }

    /**
     * Канонизирует английский текст только для проверки "пусто / непусто" и distinct.
     */
    private function canonicalEnglish(?string $text): string
    {
        return trim((string) ($text ?? ''));
    }

    /**
     * Строит безопасный группировочный ключ.
     *
     * Если pos_id отсутствует, используем маркер UNKNOWN,
     * чтобы не смешивать такие строки с любым реальным POS.
     */
    private function buildSafeKey(?int $posId, string $normalizedMeaningRu): string
    {
        $posPart = $posId !== null ? (string) $posId : 'UNKNOWN';
        return $posPart . "\t" . $normalizedMeaningRu;
    }
}