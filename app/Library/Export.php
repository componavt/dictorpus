<?php

namespace App\Library;

use LaravelLocalization;
use Storage;

use App\Models\Corpus\Text;
use App\Models\Dict\Dialect;
use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
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

        //        $data=[];
        $max_lemma_id = Lemma::selectRaw("max(id) as max")->first()->max;

        $portion = 100;
        $step = 0;
        while ($start + $step * $portion < $max_lemma_id) {
            $lemmas = Lemma::whereIn('lang_id', Lang::projectLangIDs())
                ->where('id', '>', $start + $step * $portion)
                ->where('id', '<=', $start + ($step + 1) * $portion)
                //                        ->take(100)
                ->get();
            foreach ($lemmas as $lemma) {
                $wordforms = Wordform::join('lemma_wordform', 'lemma_wordform.wordform_id', '=', 'wordforms.id')
                    ->whereLemmaId($lemma->id)
                    ->groupBy('wordform_id', 'gramset_id')
                    ->get(['wordform', 'gramset_id']);
                foreach ($wordforms as $wordform) {
                    Storage::disk('public')->append($filename, $count . "," . $lemma->id . ",\"" . $wordform->wordform . "\"," . $wordform->gramset_id);
                    /*                    $data[$count++] = [
                        'wordform'=>$wordform->wordform,
                        'lemma_id'=>$lemma->id,
                        'gramset_id'=>$wordform->gramset_id];*/
                    $count++;
                }
            }
            $step++;
        }
        return $data;
    }

    public static function gramsetsForMobile()
    {
        $data = [];
        foreach (Gramset::get() as $gramset) {
            $data[$gramset->id]['ru'] = $gramset->gramsetString();
        }
        LaravelLocalization::setLocale('en');
        foreach (Gramset::get() as $gramset) {
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
        $without_text_ids[] = 4343;
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
        $without_text_ids = [6083, 6158, 6918];
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

    public static function writeSentencesForYandex($filename, $sentences, $message, $with_dialect=false)
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

        echo $message . ' сохранены в ' . storage_path('app/public/' . $filename)."\n";
    }
}
