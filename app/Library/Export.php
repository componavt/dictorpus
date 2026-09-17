<?php

namespace App\Library;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;

use App\Models\Corpus\Text;
//use App\Models\Corpus\Transtext;
//use App\Models\Corpus\Sentence;
use App\Models\Corpus\Word;

use App\Models\Dict\Dialect;
use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\Meaning;
//use App\Models\Dict\MeaningText;
use App\Models\Dict\PartOfSpeech;
use App\Models\Dict\Wordform;

class Export
{
    public static function lemmasToUnimorph($lang_id, $dir_name)
    {
        $lang = Lang::find($lang_id);
        $dialects = Dialect::where('lang_id', $lang_id)->get();
        foreach ($dialects as $dialect) {
            $filename = $dir_name . $lang->code . '-' . strtolower(preg_replace("/\s+/", "-", $dialect->name_en)); //.'.txt';
            $lemmas = Lemma::where('lang_id', $lang_id)
                //                    ->where('id',1416)
                //                    ->take(100)
                ->orderBy('lemma')
                ->get();
            $count = 0;
            foreach ($lemmas as $lemma) {
                $line = $lemma->toUniMorph($dialect->id);
                if ($line) {
                    $count++;
                    if ($count == 1) {
                        Storage::disk('public')->put($filename, "# " . $lang->name_en . ': ' . $dialect->name_en);
                    }
                    Storage::disk('public')->append($filename, $line);
                }
            }
            if ($count) {
                print  '<p><a href="' . Storage::url($filename) . '">' . $dialect->name_en . '</a>';
            }
        }
    }

    /**
     * 
     * @param Collection $text
     */
    public static function Bible($lang_id)
    {
        $texts = Text::where('lang_id', $lang_id)
            ->whereCorpusId(2)
            ->whereIn('source_id', function ($query) {
                $query->select('id')->from('sources')
                    ->where('comment', 'like', '%en=%');
            })
            //                    ->where('id',1416)
            //                    
            ->orderBy('id')
            ->get();
        //dd($texts->count());                         
        $lines = [];
        foreach ($texts as $text) {
            if (!preg_match("/en=(.+?)\|(.+?)\s*$/", $text->source->comment, $regs)) {
                dd('ERROR');
            }
            $book = $regs[1];
            $chapter = $regs[2];
            foreach ($text->breakIntoVerses() as $verse => $v_text) {
                $lines[$book][$chapter][$verse] = $v_text;
            }
        }
        //        dd($lines);
        return $lines;
    }

    public static function lemmasForMobile()
    {
        $data = [];
        $lemmas = Lemma::whereIn('lang_id', Lang::projectLangIDs())
            //->take(100)
            ->get();

        if ($lemmas->isEmpty()) {
            return $data; // Возвращаем пустой массив, если нет лемм
        }

        foreach ($lemmas as $lemma) {
            $meanings = $lemma->getLangMeaningTexts('ru');
            if (!sizeof($meanings)) {
                continue;
            }
            $meaning = preg_replace("/\"/", "'", join("\n", $meanings));
            $data[$lemma->id] = [
                'lemma' => $lemma->lemma,
                'lang_id' => $lemma->lang_id,
                'pos_id' => $lemma->pos_id,
                'meaning_ru' => $meaning,
                'stem',
                'affix'
            ];
        }
        return $data;
    }

    public static function wordformsForMobile(string $filename)
    {
        $start = 1;
        $count = 1;
        $filename .= '_from_' . $start;
        Storage::disk('public')->put($filename, '');

        $data = []; // Подготовим данные для возврата
        $max_lemma_id = Lemma::selectRaw("max(id) as max")->first()->max;
        if (!$max_lemma_id) {
            return $data; // Возвращаем пустой массив, если нет лемм
        }

        $portion = 100;
        $step = 0;
        while ($start + $step * $portion < $max_lemma_id) {
            $lemmas = Lemma::whereIn('lang_id', Lang::projectLangIDs())
                ->where('id', '>', $start + $step * $portion)
                ->where('id', '<=', $start + ($step + 1) * $portion)
                ->get();

            foreach ($lemmas as $lemma) {
                $wordforms = Wordform::join('lemma_wordform', 'lemma_wordform.wordform_id', '=', 'wordforms.id')
                    ->whereLemmaId($lemma->id)
                    ->groupBy('wordform_id', 'gramset_id')
                    ->get(['wordform', 'gramset_id']);

                foreach ($wordforms as $wordform) {
                    Storage::disk('public')->append($filename, $count . "," . $lemma->id . ",\"" . $wordform->wordform . "\"," . $wordform->gramset_id);
                    $data[$count] = [
                        'wordform' => $wordform->wordform,
                        'lemma_id' => $lemma->id,
                        'gramset_id' => $wordform->gramset_id
                    ];
                    $count++;
                }
            }
            $step++;
        }

        return $data; // Возвращаем данные для возможного использования
    }

    public static function gramsetsForMobile()
    {
        $data = [];
        $gramsets = Gramset::get();

        if ($gramsets->isEmpty()) {
            return []; // Возвращаем пустой массив вместо null
        }

        foreach ($gramsets as $gramset) {
            $data[$gramset->id]['ru'] = $gramset->gramsetString();
        }
        LaravelLocalization::setLocale('en');
        foreach ($gramsets as $gramset) {
            $data[$gramset->id]['en'] = $gramset->gramsetString();
        }
        return $data;
    }

    public static function oloDict($dir_name)
    {
        $lang_id = 5;
        $dialect_id = 44;
        $lemmas = Lemma::where('lang_id', $lang_id)
            ->orderBy('lemma')
            //                ->limit(100)
            ->get();

        $file_lemmas = $dir_name . 'lemmas.csv';
        Storage::disk('public')->put($file_lemmas, " ");
        foreach ($lemmas as $lemma) {
            if (!$lemma->pos) {
                continue;
            }
            $lemma_line = $lemma->id . "\t" . $lemma->lemma . "\t" . $lemma->pos->lgr . "\t" . $lemma->featsToString() . "\t" . join('; ', $lemma->getMultilangMeaningTexts());
            Storage::disk('public')->append($file_lemmas, $lemma_line);
        }

        $file_wordforms = $dir_name . 'wordforms.csv';
        Storage::disk('public')->put($file_wordforms, " ");
        //        $count = 0;
        foreach ($lemmas as $lemma) {
            if (!$lemma->pos || !in_array($lemma->pos_id, PartOfSpeech::getNameIDs()) && $lemma->pos_id != PartOfSpeech::getVerbID()) {
                continue;
            }

            $pos_code = $lemma->pos->lgr;
            if (!$pos_code) {
                continue;
            }

            if ($pos_code == 'V' && $lemma->features && $lemma->features->reflexive) {
                $pos_code = '.REFL';
            }

            $wordforms = $lemma->wordforms()->wherePivot('dialect_id', $dialect_id)->get();
            if (!$wordforms) {
                continue;
            }
            $lines = [];
            foreach ($wordforms as $wordform) {
                $gramset = $wordform->gramsetPivot();
                if (!$gramset) {
                    continue;
                }
                $features = $gramset->tolgr('.');
                if (!$features) {
                    continue;
                }
                $lines[] = $lemma->id . "\t" . $wordform->wordform . "\t" . $features;
            }

            if (sizeof($lines)) {
                Storage::disk('public')->append($file_wordforms, join("\n", $lines));
            }

            //            $count++;
        }
        print 'done.';
    }

    /**
     * Collection $texts
     */
    public static function forYandex($dir_name)
    {
        $lang_id = 5; // olo
        $dialect_id = 44; // olo norm
        $olo_norm_texts_with_translations = Text::whereLangId($lang_id)
            ->whereNotNull('transtext_id')
            ->whereIn('id', function ($q) use ($dialect_id) {
                $q->select('text_id')->from('dialect_text')
                    ->whereDialectId($dialect_id);
            })->orderBy('id')->get();
        //dd($texts->count());  
        $lipas_sentence_ids = self::oloNormSentencesWithTranslation($olo_norm_texts_with_translations, $dir_name);
        self::otherOloSentences($olo_norm_texts_with_translations->pluck('id')->toArray(), $lipas_sentence_ids, $dir_name);
        self::krlSentences($dir_name);
    }

    /**
     * Collection $texts
     */
    public static function oloNormSentencesWithTranslation($texts, $dir_name)
    {
        $filename = $dir_name . "olo_norm_with_translations" . ".csv";
        $sentences = [];

        foreach ($texts as $text) {
            $sentences = $text->sentencesWithTranslation($sentences);
        }
        list($sentences, $lipas_sentence_ids)
            = Text::sentencesFromOlodict($sentences, $texts->pluck('id')->toArray());

        self::writeSentencesForYandex($filename, $sentences, "Ливвиковские нормированные предложения с переводами");

        return $lipas_sentence_ids;
    }

    /**
     * Collection $texts
     */
    public static function otherOloSentences($without_text_ids, $without_sentence_ids, $dir_name)
    {
        $lang_id = 5; // olo
        $filename = $dir_name . "other_olo" . ".csv";
        $sentences = [];

        $texts_with_translations = Text::whereLangId($lang_id)
            ->whereNotNull('transtext_id')
            ->whereNotIn('id', $without_text_ids)
            ->orderBy('id')->get();
        foreach ($texts_with_translations as $text) {
            $sentences = $text->sentencesWithTranslation($sentences, $without_sentence_ids, 1);
        }

        $texts_without_translations = Text::whereLangId($lang_id)
            ->whereNull('transtext_id')
            ->whereNotIn('id', $without_text_ids)
            ->orderBy('id')->get();
        foreach ($texts_without_translations as $text) {
            $sentences = $text->sentencesWithoutTranslation($sentences, $without_sentence_ids);
        }

        self::writeSentencesForYandex($filename, $sentences, "Остальные ливвиковские предложения", 1);
    }

    /**
     * Collection $texts
     */
    public static function krlSentences($dir_name)
    {
        $lang_id = 4; // krl
        $without_text_ids = [6083, 6158];
        $sentences = [];
        $filename = $dir_name . "krl" . ".csv";

        $texts_with_translations = Text::whereLangId($lang_id)
            ->whereNotNull('transtext_id')
            ->whereNotIn('id', $without_text_ids)
            ->orderBy('id')->get();
        foreach ($texts_with_translations as $text) {
            $sentences = $text->sentencesWithTranslation($sentences, [], 1);
        }

        $texts_without_translations = Text::whereLangId($lang_id)
            ->whereNull('transtext_id')
            ->orderBy('id')->get();
        foreach ($texts_without_translations as $text) {
            $sentences = $text->sentencesWithoutTranslation($sentences);
        }

        self::writeSentencesForYandex($filename, $sentences, "Собственно карельские предложения", 1);
    }

    public static function writeSentencesForYandex($filename, $sentences, $message, $with_dialect = false)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $handle = fopen('php://temp', 'r+');

        foreach ($sentences as $s => $info) {
            $line = $with_dialect ?
                [$info['corpus'], $info['dialect'], $s, !empty($info['trans']) ? $info['trans'] : '']
                : [$info['corpus'], $s, $info['trans']];
            fputcsv($handle, $line, "\t"); // с кавычками
        }

        // Переместить указатель в начало файла
        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        // Сохранение файла в хранилище
        Storage::disk('public')->put($filename, $csvContent);

        echo "<h3>" . $message . ' сохранены в ' . storage_path('app/public/' . $filename) . "</h3>\n";
    }

    /**
     * Экспорт русских толкований из словаря
     *
     * Выгрузка в CSV-файл с колонками:
     * - номер по порядку
     * - meanings.id
     * - meanings.lemma_id
     * - lemmas.lemma
     * - lemmas.lang.code
     * - lemmas.pos.code
     * - meaning_texts.meaning_text (ru)
     * - meaning_texts.meaning_text (en)
     * - concept_meaning.concept_id
     * - concepts.concept_category_id
     *
     * @param string $filename Имя файла для сохранения
     * @param Lang $lang язык
     * @return bool Успех или неудача
     */
    public static function russianMeanings($filename, $lang)
    {
        // ID русского языка
        $ru_lang_id = 2;
        $en_lang_id = 3;

        // Получаем данные из БД
        $meanings = Meaning::join('lemmas', 'meanings.lemma_id', '=', 'lemmas.id')
            ->join('langs', 'lemmas.lang_id', '=', 'langs.id')
            ->join('parts_of_speech', 'lemmas.pos_id', '=', 'parts_of_speech.id')
            ->leftJoin('concept_meaning', 'meanings.id', '=', 'concept_meaning.meaning_id')
            ->leftJoin('concepts', 'concept_meaning.concept_id', '=', 'concepts.id')
            ->where('lemmas.lang_id', $lang->id)
            ->select(
                'meanings.id',
                'meanings.lemma_id',
                'meanings.id as meaning_id',
                //                'meanings.meaning_n',
                'lemmas.lemma',
                'langs.code as lang_code',
                'parts_of_speech.code as pos_code',
                'concept_meaning.concept_id as concept_id',
                'concepts.concept_category_id as category_id'
            )
            ->orderBy('lemmas.lang_id')
            ->orderBy('lemmas.lemma')
            ->orderBy('meanings.meaning_n')
            ->with('meaningTexts')
            ->get();

        // Заголовок CSV файла
        $file = fopen(storage_path('app/public/' . $filename), 'w');
        fwrite($file, csv_row([
            'id',
            'meaning_id',
            'lemma_id',
            //            'meaning_num',
            'lemma',
            'lang',
            'pos',
            'meaning_ru',
            'meaning_en',
            'concept_id',
            'category_id'
        ]));

        // Записываем данные
        $counter = 1;
        foreach ($meanings as $row) {
            //            dd($row);
            $meaning_text = $row->meaningTexts->where('lang_id', $ru_lang_id)->first();
            $meaning_text_ru = $meaning_text ? $meaning_text->meaning_text : '';
            $meaning_text = $row->meaningTexts->where('lang_id', $en_lang_id)->first();
            $meaning_text_en = $meaning_text ? $meaning_text->meaning_text : '';
            fwrite($file, csv_row([
                $counter++,
                $row->meaning_id,
                $row->lemma_id,
                //                $row->meaning_n,
                $row->lemma,
                $row->lang_code,
                $row->pos_code,
                $meaning_text_ru,
                $meaning_text_en,
                $row->concept_id,
                $row->category_id
            ]));
        }
        fclose($file);
        return true;
    }

    /**
     * Экспорт русских переводов предложений 
     *
     * Выгрузка в CSV-файл с колонками:
     * - номер по порядку
     * - ID значения
     * - meaning_texts.meaning_text - предложение на русском языке, перевод проверенного примера
     *
     * @param string $filename Имя файла для сохранения
     * @param int $lang_id язык текстов
     * @return bool Успех или неудача
     */
    public static function russianTranslations($filename, $lang_id)
    {
        // ID русского языка
        $lang_ru = 2;

        $file = fopen(storage_path('app/public/' . $filename), 'w');
        fwrite($file, csv_row([
            'id',
            'meaning_id',
            'example'
        ]));

        $texts = Text::whereNotNull('transtext_id')
            ->where('lang_id', $lang_id)
            ->whereIn('id', function ($q) use ($lang_id) {
                $q->select('id')->from('meaning_text')
                    ->where('relevance', '>', 1);
            })->get();

        $count = 1;
        foreach ($texts as $text) {
            $text_id = $text->id;
            $transtext = $text->transtext;
            $examples = DB::table('meaning_text')
                ->select(['s_id', 'meaning_id'])
                ->where('text_id', $text_id)
                ->where('relevance', '>', 1)
                ->get();

            if (empty($examples)) {
                continue;
            }

            foreach ($examples as $example) {
                $s_id = $example->s_id;
                $sentence = Text::processSentenceForExport($text->getTransSentence($s_id));
                if (empty($sentence)) {
                    continue;
                }
                fwrite($file, csv_row([
                    $count++,
                    $example->meaning_id,
                    $sentence,
                ]));
            }
        }
        fclose($file);
    }

    public static function lemmasforMultimediaDictionary($filename)
    {
        Storage::disk('public')->put($filename, "id\tlang\tlemma\tpos");
        $lemmas = Lemma::orderBy('id')->with('lang')->with('pos')
            ->get();
        foreach ($lemmas as $lemma) {
            $lang_code = empty($lemma->lang) ? null : $lemma->lang->code;
            $pos_code = empty($lemma->pos) ? null : $lemma->pos->code;
            $line = $lemma->id . "\t" . $lang_code . "\t" . $lemma->lemma . "\t" . $pos_code;
            Storage::disk('public')->append($filename, $line);
        }
    }

    public static function meaningsforMultimediaDictionary($filename)
    {
        Storage::disk('public')->put($filename, "id\tlemma_id\tmeaning_n\tmeaning_ru\tmeaning_en\tmeaning_fi\timage_id");
        $meanings = Meaning::orderBy('id')->get();
        $images = [];

        foreach ($meanings as $meaning) {
            $image = $meaning->photoInfo();
            $meaning_ru = $meaning->meaningTexts()->where('lang_id', 2)->first()->meaning_text ?? '';
            $meaning_en = $meaning->meaningTexts()->where('lang_id', 3)->first()->meaning_text ?? '';
            $meaning_fi = $meaning->meaningTexts()->where('lang_id', 7)->first()->meaning_text ?? '';
            $image_id = $image['id'] ?? '';
            $line = $meaning->id . "\t" . $meaning->lemma_id . "\t" . $meaning->meaning_n . "\t" .
                $meaning_ru . "\t" . $meaning_en . "\t" . $meaning_fi . "\t" . $image_id;
            Storage::disk('public')->append($filename, $line);
            if (!empty($image['id'])) {
                $images[$image_id] = ['thumb_path' => $image['thumb_path'], 'wiki_photo' => $image['wiki_photo']];
            }
        }

        return $images;
    }

    public static function imagesforMultimediaDictionary($images, $imagedir, $imagefile)
    {
        Storage::disk('public')->put($imagefile, "id\timage");
        foreach ($images as $image_id => $info) {
            $relativePath = $imagedir . '/' . $image_id . '.jpg';
            $target = Storage::disk('public')->getAdapter()->applyPathPrefix($relativePath);
            File::copy($info['thumb_path'], $target);
            $line = $image_id . "\t" . $info['wiki_photo'];
            Storage::disk('public')->append($imagefile, $line);
        }
    }

    /**
     * Выгружает словарь в Word
     *
     * @param Collection<int, Lemma> $lemmas
     * @param string $filename
     * @param int $dialect_id
     * @return void
     */
    public static function dictionaryToWord($lemmas, $filename, $dialect_id, $label_id)
    {
        ini_set('max_execution_time', 7200);
        ini_set('memory_limit', '512M');

        $tempDir = storage_path('tmp');
        $filePath = storage_path('tmp/' . $filename);

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        Settings::setTempDir($tempDir);

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        foreach ($lemmas as $lemma) {
            $textRun = $section->addTextRun();

            $textRun->addText($lemma->stemAffixForm(), ['bold' => true]);
            $textRun->addText(' ' . $lemma->inflectionForms($dialect_id) . ' ');
            $textRun->addText(($lemma->pos ? $lemma->pos->dict_code : ''), ['italic' => true]);

            $meanings = $lemma->meaningsWithLabel($label_id);
            $count_meanings = 1;
            foreach ($meanings as $meaning) {
                $textRun->addText(' ');
                if ($meanings->count() > 1) {    // номер значения
                    $textRun->addText($meaning->meaning_n . '. ');
                }
                $textRun->addText($meaning->getMeaningTextByLangCode('ru'), ['italic' => true]); // значение

                $labels = join(', ', $meaning->labels()->where('visible', 1)->pluck('short_ru')->toArray()); // метки
                if ($labels) {
                    $textRun->addText(' (' . $labels . ')');
                }

                foreach ($meaning->examples as $example) { // примеры
                    $textRun->addText('; ' . $example->example . ' ' . $example->example_ru);
                }

                if ($meaning->phrases()->count()) { // фразы
                    $textRun->addText(' ◊');
                    $phrases = [];
                    foreach ($meaning->phrases as $phrase) {
                        $phrase_line = $phrase->lemma;
                        if ($phrase->meanings && isset($phrase->meanings[0])) {
                            $phrase_line .= ' ' . $phrase->meanings[0]->getMeaningTextByLangCode('ru');
                        }
                        $phrases[] = $phrase_line;
                    }
                    $textRun->addText(' ' . join('; ', $phrases));
                }
                if ($count_meanings < $meanings->count()) {
                    $textRun->addText(';');
                }
                $count_meanings++;
            }

            $section->addTextBreak(1);
        }

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($filePath);

        return response()->download($filePath, $filename)->deleteFileAfterSend(true);
    }

    /*
    * Экспорт текстов для задач разрешения морфологической неоднозначности.
    *
    * text_id,corpus_id,dialect_code,genre_id,year_recorded
    *
    * Несколько корпусов, диалектов и жанров разделяются символом "|".
    */
    public static function textsforMorphDisambig(int $lang_id, string $filename)
    {
        $csv_handle = fopen('php://temp/maxmemory:5242880', 'w+');

        if ($csv_handle === false) {
            throw new \RuntimeException('Cannot open temporary CSV buffer.');
        }

        try {
            self::write_csv_row($csv_handle, [
                'text_id',
                'corpus_id',
                'dialect_code',
                'genre_id',
                'year_recorded',
            ]);

            Text::query()
                ->from('texts as t')
                ->selectRaw("t.id AS text_id,
                GROUP_CONCAT(DISTINCT c.id ORDER BY c.id SEPARATOR '|') AS corpus_id,
                GROUP_CONCAT(DISTINCT d.code ORDER BY d.id SEPARATOR '|') AS dialect_code,
                GROUP_CONCAT(DISTINCT g.id ORDER BY g.id SEPARATOR '|') AS genre_id,
                e.date AS year_recorded")
                ->leftJoin('corpus_text as ct', 'ct.text_id', '=', 't.id')
                ->leftJoin('corpuses as c', 'c.id', '=', 'ct.corpus_id')
                ->leftJoin('dialect_text as dt', 'dt.text_id', '=', 't.id')
                ->leftJoin('dialects as d', 'd.id', '=', 'dt.dialect_id')
                ->leftJoin('genre_text as gt', 'gt.text_id', '=', 't.id')
                ->leftJoin('genres as g', 'g.id', '=', 'gt.genre_id')
                ->leftJoin('events as e', 'e.id', '=', 't.event_id')
                ->where('t.lang_id', $lang_id)
                ->groupBy('t.id', 'e.date')
                ->orderBy('t.id')
                ->chunk(500, function ($texts) use ($csv_handle) {
                    foreach ($texts as $text) {
                        self::write_csv_row($csv_handle, [
                            $text->text_id,
                            $text->corpus_id,
                            $text->dialect_code,
                            $text->genre_id,
                            $text->year_recorded,
                        ]);
                    }
                });

            rewind($csv_handle);

            if (!Storage::disk('public')->put($filename, $csv_handle)) {
                throw new \RuntimeException('Cannot save CSV file: ' . $filename);
            }
        } finally {
            fclose($csv_handle);
        }
    }

    /*
     * Экспорт предложений для задач разрешения морфологической неоднозначности
     * 
     * sentence_id,text_id,sentence_xml,sentence_ru 
     */
    public static function sentencesforMorphDisambig(int $lang_id, string $filename)
    {
        $export_db = DB::connection('mysql_export');
        $csv_handle = fopen('php://temp/maxmemory:5242880', 'w+');

        if ($csv_handle === false) {
            throw new \RuntimeException('Cannot open temporary CSV buffer.');
        }

        try {
            self::write_csv_row($csv_handle, [
                'sentence_id',
                'text_id',
                'sentence_xml',
                'sentence_ru',
            ]);

            $text_batch_size = 10;

            Text::query()
                ->where('lang_id', $lang_id)
                ->select('id', 'transtext_id')
                ->orderBy('id')
                ->chunk($text_batch_size, function ($texts) use ($csv_handle, $export_db) {
                    $text_ids = [];
                    $transtext_ids = [];
                    $transtext_id_by_text_id = [];

                    foreach ($texts as $text) {
                        $text_ids[] = $text->id;
                        $transtext_id_by_text_id[$text->id] = $text->transtext_id;

                        if ($text->transtext_id) {
                            $transtext_ids[] = $text->transtext_id;
                        }
                    }

                    $translation_xml_by_id = $transtext_ids
                        ? $export_db->table('transtexts')
                        ->whereIn('id', array_unique($transtext_ids))
                        ->pluck('text_xml', 'id')
                        : [];

                    $translation_cache = [];

                    foreach (
                        $export_db->table('sentences')
                            ->whereIn('text_id', $text_ids)
                            ->select(
                                'id as sentence_id',
                                'text_id',
                                's_id',
                                'text_xml as sentence_xml'
                            )
                            ->cursor() as $sentence_row
                    ) {
                        $sentence_ru = '';
                        $transtext_id = $transtext_id_by_text_id[$sentence_row->text_id];

                        if (
                            $transtext_id && isset($translation_xml_by_id[$transtext_id])
                        ) {
                            if (!array_key_exists($transtext_id, $translation_cache)) {
                                $translation_cache[$transtext_id] =
                                    self::get_translation_sentences($translation_xml_by_id[$transtext_id], $transtext_id);
                            }

                            $s_id = (string)$sentence_row->s_id;

                            if (isset($translation_cache[$transtext_id][$s_id])) {
                                $sentence_ru = $translation_cache[$transtext_id][$s_id];
                            }
                        }

                        self::write_csv_row($csv_handle, [
                            $sentence_row->sentence_id,
                            $sentence_row->text_id,
                            $sentence_row->sentence_xml,
                            $sentence_ru,
                        ]);
                    }
                });

            rewind($csv_handle);

            if (!Storage::disk('public')->put($filename, $csv_handle)) {
                throw new \RuntimeException('Cannot save CSV file: ' . $filename);
            }
        } finally {
            fclose($csv_handle);
        }
    }

    /*
    * Экспорт слов для задач разрешения морфологической неоднозначности.
    *
    * word_id,sentence_id,word_number,word (word-нормализованное слово)
    *
    * SELECT w.id AS word_id, w.sentence_id, w.word_number, w.word FROM words AS w WHERE 
    w.text_id IN (SELECT id FROM texts WHERE lang_id = $lang_id);
    */
    public static function wordsforMorphDisambig(int $lang_id, string $filename)
    {
        $export_db = DB::connection('mysql_export');
        $csv_handle = fopen('php://temp/maxmemory:5242880', 'w+');

        if ($csv_handle === false) {
            throw new \RuntimeException('Cannot open temporary CSV buffer.');
        }

        try {
            self::write_csv_row($csv_handle, [
                'word_id',
                'sentence_id',
                'word_number',
                'word',
            ]);

            foreach (
                $export_db->table('words as w')
                    ->join('texts as t', 't.id', '=', 'w.text_id')
                    ->where('t.lang_id', $lang_id)
                    ->select('w.id as word_id', 'w.sentence_id', 'w.word_number', 'w.word')
                    ->cursor() as $word
            ) {
                self::write_csv_row($csv_handle, [
                    $word->word_id,
                    $word->sentence_id,
                    $word->word_number,
                    $word->word,
                ]);
            }

            rewind($csv_handle);

            if (!Storage::disk('public')->put($filename, $csv_handle)) {
                throw new \RuntimeException('Cannot save CSV file: ' . $filename);
            }
        } finally {
            fclose($csv_handle);
        }
    }

    /*
    * Экспорт кандидатов для анализа задач разрешения морфологической неоднозначности.
    *
    * word_id,wordform_id,gramset,relevance
    *
     * gramset - сериализация gramsets + grams:
     * Порядок категорий:
     *   number → case → tense → person → mood →  negation → infinitive → voice → participle → reflexive
     * берутся коды lgr
     * SELECT gs.id AS gramset_id, CONCAT_WS('+', g_number.lgr, g_case.lgr, g_tense.lgr, g_person.lgr, g_mood.lgr, g_negation.lgr, g_infinitive.lgr, g_voice.lgr, g_participle.lgr, g_reflexive.lgr) AS gramset FROM gramsets AS gs LEFT JOIN grams AS g_number ON g_number.id = gs.gram_id_number LEFT JOIN grams AS g_case   ON g_case.id   = gs.gram_id_case LEFT JOIN grams AS g_tense  ON g_tense.id  = gs.gram_id_tense LEFT JOIN grams AS g_person ON g_person.id = gs.gram_id_person LEFT JOIN grams AS g_mood ON g_mood.id = gs.gram_id_mood LEFT JOIN grams AS g_negation ON g_negation.id = gs.gram_id_negation LEFT JOIN grams AS g_infinitive ON g_infinitive.id = gs.gram_id_infinitive LEFT JOIN grams AS g_voice ON g_voice.id = gs.gram_id_voice LEFT JOIN grams AS g_participle ON g_participle.id = gs.gram_id_participle LEFT JOIN grams AS g_reflexive ON g_reflexive.id = gs.gram_id_reflexive;
     * 
    * SELECT tw.word_id, tw.wordform_id, tw.gramset_id, tw.relevance FROM text_wordform AS tw JOIN words AS w ON w.id = tw.word_id WHERE tw.word_id > 0 AND w.text_id IN (SELECT id FROM texts WHERE lang_id = 1);
    */
    public static function candidateAnalysesforMorphDisambig(int $lang_id, string $filename)
    {
        $csv_handle = fopen('php://temp/maxmemory:5242880', 'w+');

        if ($csv_handle === false) {
            throw new \RuntimeException('Cannot open temporary CSV buffer.');
        }

        try {
            self::write_csv_row($csv_handle, ['word_id', 'wordform_id', 'gramset', 'relevance']);

            $gramset_by_id = self::get_gramset_by_id();

            foreach (
                DB::connection('mysql_export')
                    ->table('text_wordform as tw')
                    ->join('words as w', 'w.id', '=', 'tw.word_id')
                    ->join('texts as t', 't.id', '=', 'w.text_id')
                    ->where('t.lang_id', $lang_id)
                    ->select('tw.word_id', 'tw.wordform_id', 'tw.gramset_id', 'tw.relevance')
                    ->cursor() as $analysis
            ) {
                self::write_csv_row($csv_handle, [
                    $analysis->word_id,
                    $analysis->wordform_id,
                    isset($gramset_by_id[$analysis->gramset_id])
                        ? $gramset_by_id[$analysis->gramset_id]
                        : '',
                    $analysis->relevance,
                ]);
            }

            rewind($csv_handle);

            if (!Storage::disk('public')->put($filename, $csv_handle)) {
                throw new \RuntimeException('Cannot save CSV file: ' . $filename);
            }
        } finally {
            fclose($csv_handle);
        }
    }

    /**
     * Разбирает text_xml перевода и возвращает:
     *
     * [
     *     '1' => 'Вот уж мне бы погулять и покрасовать...',
     *     '2' => 'Посмотри-ка, кормилец-батюшка...',
     * ]
     *
     * @param string $translation_xml
     * @param int    $transtext_id
     *
     * @return array
     */
    public static function get_translation_sentences($translation_xml, $transtext_id)
    {
        $translation_sentences = [];

        if (trim($translation_xml) === '') {
            return $translation_sentences;
        }

        // &nbsp; не является XML-сущностью, поэтому заменяем его на числовой эквивалент до разбора.
        $translation_xml = str_replace('&nbsp;', '&#160;', $translation_xml);

        $xml_document = new \DOMDocument('1.0', 'UTF-8');

        $previous_use_internal_errors = libxml_use_internal_errors(true);

        // transtexts.text_xml содержит несколько корневых <s>. Поэтому для DOMDocument оборачиваем их в один общий корень.
        $is_loaded = $xml_document->loadXML('<?xml version="1.0" encoding="UTF-8"?><translation_root>' . $translation_xml . '</translation_root>', LIBXML_NONET | LIBXML_COMPACT);

        if ($is_loaded) {
            $xpath = new \DOMXPath($xml_document);

            // Берём только предложения верхнего уровня, то есть <s id="1"> ... </s>, <s id="2"> ... </s> и т. п.
            $sentence_nodes = $xpath->query('/translation_root/s[@id]');

            foreach ($sentence_nodes as $sentence_node) {
                $s_id = $sentence_node->getAttribute('id');

                if ($s_id === '') {
                    continue;
                }

                // textContent удаляет XML-теги <w>, <br/> и т. п., сохраняя сам русский текст.
                $sentence_ru = self::normalize_sentence_text($sentence_node->textContent);

                $translation_sentences[$s_id] = $sentence_ru;
            }
        } else {
            /*
             * Запасной вариант на случай невалидного XML в старом тексте:
             * например, одиночного <br>, неэкранированного амперсанда и т. п.
             */
            Log::warning('Cannot parse transtext XML with DOMDocument', [
                'transtext_id' => $transtext_id,
                'xml_errors' => $this->get_libxml_errors(),
            ]);

            $translation_sentences = $this->get_translation_sentences_by_regex($translation_xml);
        }

        libxml_clear_errors();
        libxml_use_internal_errors($previous_use_internal_errors);

        return $translation_sentences;
    }

    /**
     * Резервное извлечение предложений, если text_xml не прошёл XML-разбор.
     *
     * @param string $translation_xml
     *
     * @return array
     */
    protected function get_translation_sentences_by_regex($translation_xml)
    {
        $translation_sentences = [];

        $pattern = '~<s\b[^>]*\bid\s*=\s*(["\'])([^"\']+)\1[^>]*>'
            . '(.*?)'
            . '</s\s*>~isu';

        if (!preg_match_all($pattern, $translation_xml, $matches, PREG_SET_ORDER)) {
            return $translation_sentences;
        }

        foreach ($matches as $match) {
            $s_id = trim($match[2]);
            $sentence_xml = $match[3];

            if ($s_id === '') {
                continue;
            }

            $sentence_ru = html_entity_decode(strip_tags($sentence_xml), ENT_QUOTES, 'UTF-8');

            $translation_sentences[$s_id] = $this->normalize_sentence_text($sentence_ru);
        }

        return $translation_sentences;
    }

    /**
     * Приводит текст предложения к одной строке:
     * удаляет лишние переводы строк, табуляции и повторяющиеся пробелы.
     *
     * @param string $sentence_text
     *
     * @return string
     */
    public static function normalize_sentence_text($sentence_text)
    {
        $sentence_text = html_entity_decode($sentence_text, ENT_QUOTES, 'UTF-8');

        $sentence_text = preg_replace('/\s+/u', ' ', $sentence_text);

        return trim($sentence_text);
    }

    /**
     * Текст XML-ошибок для Laravel log.
     *
     * @return array
     */
    public static function get_libxml_errors()
    {
        $xml_errors = [];

        foreach (libxml_get_errors() as $xml_error) {
            $xml_errors[] = trim($xml_error->message) . ' (line ' . $xml_error->line . ')';
        }

        return $xml_errors;
    }

    /**
     * Записывает одну строку CSV в строго заданном формате:
     *
     * - UTF-8 без BOM;
     * - delimiter = ",";
     * - quotechar = '"';
     * - doublequote = true.
     *
     * @param resource $csv_handle
     * @param array    $fields
     *
     * @return void
     */
    public static function write_csv_row($csv_handle, array $fields)
    {
        $fields = array_map(function ($field) {
            return '"' . str_replace('"', '""', (string)$field) . '"';
        }, $fields);

        fwrite($csv_handle, implode(',', $fields) . "\r\n");
    }

    public static function get_gramset_by_id()
    {
        $gramset_rows = DB::table('gramsets as gs')
            ->leftJoin('grams as g_number', 'g_number.id', '=', 'gs.gram_id_number')
            ->leftJoin('grams as g_case', 'g_case.id', '=', 'gs.gram_id_case')
            ->leftJoin('grams as g_tense', 'g_tense.id', '=', 'gs.gram_id_tense')
            ->leftJoin('grams as g_person', 'g_person.id', '=', 'gs.gram_id_person')
            ->leftJoin('grams as g_mood', 'g_mood.id', '=', 'gs.gram_id_mood')
            ->leftJoin('grams as g_negation', 'g_negation.id', '=', 'gs.gram_id_negation')
            ->leftJoin('grams as g_infinitive', 'g_infinitive.id', '=', 'gs.gram_id_infinitive')
            ->leftJoin('grams as g_voice', 'g_voice.id', '=', 'gs.gram_id_voice')
            ->leftJoin('grams as g_participle', 'g_participle.id', '=', 'gs.gram_id_participle')
            ->leftJoin('grams as g_reflexive', 'g_reflexive.id', '=', 'gs.gram_id_reflexive')
            ->selectRaw("gs.id AS gramset_id, CONCAT_WS('+', g_number.lgr, g_case.lgr, g_tense.lgr, g_person.lgr, g_mood.lgr, g_negation.lgr, g_infinitive.lgr, g_voice.lgr, g_participle.lgr, g_reflexive.lgr) AS gramset")
            ->get();

        $gramset_by_id = [];

        foreach ($gramset_rows as $gramset_row) {
            $gramset_by_id[$gramset_row->gramset_id] = $gramset_row->gramset;
        }

        return $gramset_by_id;
    }
}
