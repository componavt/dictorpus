<?php

namespace App\Library;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use App\Models\Corpus\Text;
use App\Models\Corpus\Transtext;
use App\Models\Dict\Dialect;
use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\Meaning;
use App\Models\Dict\MeaningText;
use App\Models\Dict\PartOfSpeech;
use App\Models\Corpus\Sentence;
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
    public static function exportBible($lang_id)
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

        echo $message . ' сохранены в ' . storage_path('app/public/' . $filename) . "\n";
    }

    /**
     * Экспорт русских толкований из словаря
     *
     * Выгрузка в CSV-файл с колонками:
     * - номер по порядку
     * - meanings.lemma_id
     * - meanings.id
     * - meanings.meaning_n
     * - lemmas.lemma
     * - lemmas.lang.code
     * - lemmas.pos.code
     * - meaning_texts.meaning_text
     *
     * @param string $filename Имя файла для сохранения
     * @param Lang $lang язык
     * @return bool Успех или неудача
     */
    public static function exportRussianMeanings($filename, $lang)
    {
        // ID русского языка
        $rus_lang_id = 2;

        // Получаем данные из БД
        $meaning_texts = MeaningText::where('meaning_texts.lang_id', $rus_lang_id)
            ->join('meanings', 'meaning_texts.meaning_id', '=', 'meanings.id')
            ->join('lemmas', 'meanings.lemma_id', '=', 'lemmas.id')
            ->join('langs', 'lemmas.lang_id', '=', 'langs.id')
            ->join('parts_of_speech', 'lemmas.pos_id', '=', 'parts_of_speech.id')
            ->where('lemmas.lang_id', $lang->id)
            ->select(
                'meanings.lemma_id',
                'meanings.id as meaning_id',
                'meanings.meaning_n',
                'lemmas.lemma',
                'langs.code as lang_code',
                'parts_of_speech.code as pos_code',
                'meaning_texts.meaning_text'
            )
            ->orderBy('lemmas.lang_id')
            ->orderBy('lemmas.lemma')
            ->orderBy('meanings.meaning_n')
            ->get();

        // Заголовок CSV файла
        $header = "№\tID леммы\tID значения\tномер значения\tлемма\tкод языка\tкод части речи\tтолкование\n";
        Storage::disk('public')->put($filename, $header);

        // Записываем данные
        $counter = 1;
        foreach ($meaning_texts as $row) {
            // Форматируем строку для CSV
            $line = $counter . "\t" .
                $row->lemma_id . "\t" .
                $row->meaning_id . "\t" .
                $row->meaning_n . "\t" .
                '"' . str_replace('"', '""', $row->lemma) . "\"\t" .
                $row->lang_code . "\t" .
                $row->pos_code . "\t" .
                '"' . str_replace('"', '""', $row->meaning_text) . '"';

            Storage::disk('public')->append($filename, $line);
            $counter++;
        }

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
    public static function exportRussianTranslations($filename, $lang_id)
    {
        // ID русского языка
        $lang_ru = 2;

        Storage::disk('public')->put($filename, "Номер по порядку\tID значения\tпример");

        $texts = Text::whereNotNull('transtext_id')
            ->where('lang_id', $lang_id)
            ->whereIn('id', function ($q) use ($lang_id) {
                $q->select('id')->from('meaning_text')
                    ->where('relevance', '>', 1);
            })->get();

        $count = 0;
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
                $count++;
                $s_id = $example->s_id;
                $sentence = Text::processSentenceForExport($text->getTransSentence($s_id));
                if (empty($sentence)) {
                    continue;
                }
                $line = $count . "\t" . $example->meaning_id . "\t" . $sentence;
                Storage::disk('public')->append($filename, $line);
            }
        }
    }

    public static function lemmasforMultimediaDictionary($filename)
    {
        Storage::disk('public')->put($filename, "id\tlang\tlemma\tpos");
        $lemmas = Lemma::orderBy('id')->with('lang')->with('pos')
            ->get();
        foreach ($lemmas as $lemma) {
            $line = $lemma->id . "\t" . $lemma->lang->code . "\t" . $lemma->lemma . "\t" . $lemma->pos->code;
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
}
