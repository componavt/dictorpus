<?php

namespace App\Library;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use App\Models\Corpus\Text;
use App\Models\Dict\Lang;
use App\Models\Dict\PartOfSpeech;
use App\Models\Dict\Synset;

class SynonymExport
{
    protected $handles = [];
    protected $counts = [];
    protected $meaningIds = [];
    protected $texts = [];
    protected $translations = [];

    protected $warnings = [
        'missing_sentence' => 0,
        'missing_translation' => 0,
        'changed_xml' => 0,
    ];

    public static function filenames()
    {
        return [
            'meanings_olo.csv',
            'synsets_olo.csv',
            'synset_members_olo.csv',
            'synset_candidates_olo.csv',
            'collocations_olo.csv',
            'collocation_examples_olo.csv',
        ];
    }

    public function run($relativeDirectory = null)
    {
        $lock = fopen(storage_path('app/synonym-export.lock'), 'c');

        if ($lock === false) {
            throw new \RuntimeException('Не удалось открыть блокировку экспорта.');
        }

        if (!flock($lock, LOCK_EX | LOCK_NB)) {
            fclose($lock);

            throw new \RuntimeException('Экспорт синонимов уже выполняется.');
        }

        $relative = $relativeDirectory === null
            ? 'export/.synonyms-' . uniqid('', true)
            : $relativeDirectory;
        $temporary = storage_path('app/public/' . $relative);
        $target = storage_path('app/public/export/synonyms');
        $backup = $target . '-backup-' . uniqid('', true);

        try {
            if (
                !File::makeDirectory($temporary, 0755, true)
                && !is_dir($temporary)
            ) {
                throw new \RuntimeException('Не удалось создать каталог экспорта.');
            }

            $lang = Lang::where('code', 'olo')->first();
            $pos = PartOfSpeech::where('code', 'ADJ')->first();

            if (!$lang || !$pos) {
                throw new \RuntimeException('В справочниках не найдены olo или ADJ.');
            }

            $synsets = Synset::where('lang_id', $lang->id)
                ->where('pos_id', $pos->id)
                ->orderBy('id')
                ->get();

            if ($synsets->isEmpty()) {
                throw new \RuntimeException('Ливвиковские синсеты прилагательных не найдены.');
            }

            $this->openFiles($temporary);

            $memberIds = [];

            foreach ($synsets as $synset) {
                $members = DB::table('meaning_synset')
                    ->where('synset_id', $synset->id)
                    ->orderBy('meaning_id')
                    ->get();

                $localIds = [];

                foreach ($members as $member) {
                    $id = (int) $member->meaning_id;

                    $localIds[$id] = true;
                    $memberIds[$id] = true;
                    $this->meaningIds[$id] = true;

                    $this->write('synset_members', [
                        $synset->id,
                        $id,
                        $member->syntype_id,
                    ]);
                }

                if (!$localIds) {
                    throw new \RuntimeException(
                        'Синсет ' . $synset->id . ' не содержит значений.'
                    );
                }

                if (
                    $synset->dominant_id
                    && !isset($localIds[(int) $synset->dominant_id])
                ) {
                    throw new \RuntimeException(
                        'Доминанта не входит в состав синсета ' . $synset->id
                    );
                }

                $this->write('synsets', [
                    $synset->id,
                    'olo',
                    'ADJ',
                    $synset->dominant_id ?: null,
                    trim((string) $synset->comment),
                ]);

                $seen = [];

                foreach ($synset->searchPotentialMembers() as $candidate) {
                    $id = (int) $candidate->id;

                    if (isset($seen[$id])) {
                        continue;
                    }

                    $seen[$id] = true;
                    $this->meaningIds[$id] = true;

                    $this->write('synset_candidates', [
                        $synset->id,
                        $id,
                    ]);
                }
            }

            $this->exportCollocations(array_keys($memberIds));
            $this->validateReferencedMeanings($lang->id);
            $this->closeFiles();

            if (!Export::russianMeanings($relative . '/meanings_olo.csv', $lang)) {
                throw new \RuntimeException('Ошибка экспорта словарных значений.');
            }

            $this->checkMeaningsCsv($temporary . '/meanings_olo.csv');

            if ($relativeDirectory !== null) {
                return [
                    'counts' => $this->counts,
                    'warnings' => $this->warnings,
                ];
            }

            if (file_exists($target)) {
                if (!is_dir($target) || !rename($target, $backup)) {
                    throw new \RuntimeException(
                        'Не удалось сохранить предыдущую выгрузку.'
                    );
                }
            }

            if (!rename($temporary, $target)) {
                if (is_dir($backup) && !rename($backup, $target)) {
                    throw new \RuntimeException(
                        'Ошибка публикации. Предыдущая выгрузка сохранена в: '
                            . $backup
                    );
                }

                throw new \RuntimeException('Не удалось опубликовать выгрузку.');
            }

            if (is_dir($backup)) {
                File::deleteDirectory($backup);
            }

            return [
                'counts' => $this->counts,
                'warnings' => $this->warnings,
            ];
        } finally {
            $this->closeFiles();

            if ($relativeDirectory === null && is_dir($temporary)) {
                File::deleteDirectory($temporary);
            }

            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    protected function openFiles($directory)
    {
        $schemas = [
            'synsets' => [
                'synset_id',
                'lang',
                'pos',
                'dominant_meaning_id',
                'russian_synonyms',
            ],
            'synset_members' => [
                'synset_id',
                'meaning_id',
                'syntype_id',
            ],
            'synset_candidates' => [
                'synset_id',
                'meaning_id',
            ],
            'collocations' => [
                'collocation_id',
                'meaning_id',
                'collocate_meaning_id',
                'concept_category_id',
                'display_gloss',
            ],
            'collocation_examples' => [
                'collocation_example_id',
                'collocation_id',
                'text_id',
                'source_w_id',
                'partner_w_id',
                'sentence_id',
                'sentence_xml',
                'sentence_ru',
            ],
        ];

        foreach ($schemas as $name => $header) {
            $filename = $directory . '/' . $name . '_olo.csv';
            $handle = fopen($filename, 'wb');

            if ($handle === false) {
                throw new \RuntimeException('Не удалось открыть ' . $filename);
            }

            $this->handles[$name] = $handle;
            $this->counts[$name] = 0;

            Export::write_csv_row($handle, $header);
        }
    }

    protected function write($name, array $row)
    {
        Export::write_csv_row($this->handles[$name], $row);
        $this->counts[$name]++;
    }

    protected function exportCollocations(array $memberIds)
    {
        $collocations = DB::table('collocations')
            ->whereIn('meaning_id', $memberIds)
            ->orderBy('id')
            ->get();

        foreach ($collocations as $collocation) {
            $this->meaningIds[(int) $collocation->collocate_meaning_id] = true;

            $this->write('collocations', [
                $collocation->id,
                $collocation->meaning_id,
                $collocation->collocate_meaning_id,
                $collocation->concept_category_id,
                $collocation->display_gloss,
            ]);

            $examples = DB::table('collocation_examples')
                ->where('collocation_id', $collocation->id)
                ->orderBy('id')
                ->get();

            foreach ($examples as $example) {
                $sentence = $this->findSentence($example);
                $translation = '';

                if ($sentence) {
                    $translation = $this->translation($sentence);

                    if (
                        (string) $sentence->text_xml
                        !== (string) $example->sentence_xml
                    ) {
                        $this->warnings['changed_xml']++;
                    }
                } else {
                    $this->warnings['missing_sentence']++;
                }

                if (trim($translation) === '') {
                    $this->warnings['missing_translation']++;
                }

                $this->write('collocation_examples', [
                    $example->id,
                    $example->collocation_id,
                    $example->text_id,
                    $example->source_w_id,
                    $example->partner_w_id,
                    $sentence ? $sentence->id : null,
                    $example->sentence_xml,
                    $translation,
                ]);
            }
        }
    }

    protected function findSentence($example)
    {
        if (
            !$example->source_w_id
            || !$example->partner_w_id
            || (int) $example->source_w_id === (int) $example->partner_w_id
        ) {
            return null;
        }

        $source = DB::table('words')
            ->where('text_id', $example->text_id)
            ->where('w_id', $example->source_w_id)
            ->get();

        $partner = DB::table('words')
            ->where('text_id', $example->text_id)
            ->where('w_id', $example->partner_w_id)
            ->get();

        if (count($source) !== 1 || count($partner) !== 1) {
            return null;
        }

        $source = $source[0];
        $partner = $partner[0];

        if (
            !$source->sentence_id
            || (int) $source->sentence_id !== (int) $partner->sentence_id
            || (int) $source->s_id !== (int) $partner->s_id
        ) {
            return null;
        }

        return DB::table('sentences')
            ->where('id', $source->sentence_id)
            ->where('text_id', $example->text_id)
            ->where('s_id', $source->s_id)
            ->first();
    }

    protected function translation($sentence)
    {
        $key = (int) $sentence->id;

        if (array_key_exists($key, $this->translations)) {
            return $this->translations[$key];
        }

        $textId = (int) $sentence->text_id;

        if (!array_key_exists($textId, $this->texts)) {
            $this->texts[$textId] = Text::find($textId);
        }

        $text = $this->texts[$textId];

        $translation = $text
            ? Text::processSentenceForExport(
                $text->getTransSentence($sentence->s_id)
            )
            : '';

        $this->translations[$key] = (string) $translation;

        return $this->translations[$key];
    }

    protected function validateReferencedMeanings($langId)
    {
        foreach (array_chunk(array_keys($this->meaningIds), 500) as $ids) {
            $rows = DB::table('meanings as m')
                ->join('lemmas as l', 'l.id', '=', 'm.lemma_id')
                ->whereIn('m.id', $ids)
                ->where('l.lang_id', $langId)
                ->get(['m.id']);

            $found = [];

            foreach ($rows as $row) {
                $found[(int) $row->id] = true;
            }

            foreach ($ids as $id) {
                if (!isset($found[$id])) {
                    throw new \RuntimeException(
                        'Значение ' . $id . ' отсутствует или не относится к olo.'
                    );
                }
            }
        }
    }

    protected function checkMeaningsCsv($filename)
    {
        $handle = fopen($filename, 'rb');

        if ($handle === false) {
            throw new \RuntimeException('Не удалось прочитать экспорт значений.');
        }

        try {
            $header = fgetcsv($handle);

            if (!is_array($header)) {
                throw new \RuntimeException('Пустой файл значений.');
            }

            $idColumn = array_search('meaning_id', $header, true);

            if (
                $idColumn === false
                || !in_array('meaning_n', $header, true)
            ) {
                throw new \RuntimeException(
                    'В meanings_olo.csv нужны meaning_id и meaning_n. '
                        . 'Проверьте Export::russianMeanings().'
                );
            }

            $missing = $this->meaningIds;
            $seen = [];
            $count = 0;

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) !== count($header)) {
                    throw new \RuntimeException(
                        'Неверное число колонок в CSV значений.'
                    );
                }

                $id = (int) $row[$idColumn];

                if (isset($seen[$id])) {
                    throw new \RuntimeException(
                        'Повтор meaning_id=' . $id . ' в meanings_olo.csv. '
                            . 'Проверьте связи concept_meaning.'
                    );
                }

                $seen[$id] = true;
                unset($missing[$id]);
                $count++;
            }

            if ($missing) {
                throw new \RuntimeException(
                    'В CSV отсутствуют используемые значения: '
                        . implode(', ', array_slice(array_keys($missing), 0, 20))
                );
            }

            $this->counts['meanings'] = $count;
        } finally {
            fclose($handle);
        }
    }

    protected function closeFiles()
    {
        foreach ($this->handles as $handle) {
            if (is_resource($handle)) {
                fclose($handle);
            }
        }

        $this->handles = [];
    }
}
