<?php

namespace App\Library;

use Storage;

use App\Models\Corpus\Text;
use App\Models\Dict\Dialect;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;

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
}
