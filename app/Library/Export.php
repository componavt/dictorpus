<?php

namespace App\Library;

use LaravelLocalization;
use Storage;

use App\Models\Corpus\Text;
use App\Models\Dict\Dialect;
use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\LemmaWordform;
use App\Models\Dict\Wordform;

class Export
{
    public static function lemmasToUnimorph($lang_id, $dir_name, $date_now) {
        $lang = Lang::find($lang_id);
        $dialects = Dialect::where('lang_id',$lang_id)->get();
        foreach ($dialects as $dialect) {
            $filename = $dir_name.'vepkar-'.$date_now.'-'.$dialect->code.'.txt';
            $lemmas = Lemma::where('lang_id',$lang_id)
//                    ->where('id',1416)
//                    ->take(100)
                    ->orderBy('lemma')
                    ->get();
            $count = 0;
            foreach ($lemmas as $lemma) {
                $line = $lemma->toUniMorph($dialect->id);
                if ($line) {
                    $count++;
                    if ($count==1) {
                        Storage::disk('public')->put($filename, "# ".$lang->name_en.': '.$dialect->name_en);                            
                    }
                    Storage::disk('public')->append($filename, $line);
                }
            }
            if ($count) {
                print  '<p><a href="'.Storage::url($filename).'">'.$dialect->name_en.'</a>';            
            }
        }
    }
    
    /**
     * 
     * @param Collection $text
     */
    public static function exportBible($lang_id) {
        $texts = Text::where('lang_id',$lang_id)
                     ->whereCorpusId(2)
                     ->whereIn('source_id', function ($query) {
                         $query->select('id')->from('sources')
                               ->where('comment','like','%en=%');
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
            $book=$regs[1];
            $chapter=$regs[2];
            foreach ($text->breakIntoVerses() as $verse => $v_text) {
                $lines[$book][$chapter][$verse] = $v_text;
            }
        }
//        dd($lines);
        return $lines;
    }
    
    public static function lemmasForMobile() {
        $data=[];
        $lemmas = Lemma::whereIn('lang_id', Lang::projectLangIDs())
                        //->take(100)
                        ->get();
        foreach ($lemmas as $lemma) {
            $meanings=$lemma->getLangMeaningTexts('ru');
            if (!sizeof($meanings)) { continue; }
            $meaning = preg_replace("/\"/", "'", join("\n", $meanings));
            $data[$lemma->id] = [
                'lemma'=>$lemma->lemma,
                'lang_id'=>$lemma->lang_id,
                'pos_id'=>$lemma->pos_id,
                'meaning_ru'=>$meaning];
        }
        return $data;
    }
    
    public static function wordformsForMobile(string $filename) {
        $start=12086;
        $filename .= '_from_'.$start; 
        Storage::disk('public')->put($filename, '');
        
//        $data=[];
        $max_lemma_id = Lemma::selectRaw("max(id) as max")->first()->max;
        
        $count=1;
        $portion=100;
        $step=0;
        while ($start+$step*$portion < $max_lemma_id) {
            $lemmas = Lemma::whereIn('lang_id', Lang::projectLangIDs())
                           ->where('id', '>', $start+$step*$portion)
                           ->where('id', '<=', $start+($step+1)*$portion)
    //                        ->take(100)
                            ->get();
            foreach ($lemmas as $lemma) {
                $wordforms = Wordform::join('lemma_wordform', 'lemma_wordform.wordform_id', '=', 'wordforms.id')
                                ->whereLemmaId($lemma->id)
                                ->groupBy('wordform_id', 'gramset_id')
                                ->select('wordform', 'gramset_id')
                                ->get();
                foreach ($wordforms as $wordform) {
                    Storage::disk('public')->append($filename, $count.",".$lemma->id.",\"".$wordform->wordform."\",".$wordform->gramset_id);
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
    
    public static function gramsetsForMobile() {
        $data=[];
        foreach (Gramset::get() as $gramset) {
            $data[$gramset->id]['ru'] = $gramset->gramsetString();
        }
        LaravelLocalization::setLocale('en');
        foreach (Gramset::get() as $gramset) {
            $data[$gramset->id]['en'] = $gramset->gramsetString();
        }        
        return $data;
    }
}
