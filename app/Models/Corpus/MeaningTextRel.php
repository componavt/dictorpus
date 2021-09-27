<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Models\Corpus\TextWordform;
use App\Models\Corpus\Text;

use App\Models\Dict\Lang;
use App\Models\Dict\Meaning;
use App\Models\Dict\MeaningText;

class MeaningTextRel extends Model
{
    protected $table = 'meaning_text';
    
    public $timestamps = false;
    
    
    public static function updateExamples($relevances) {
        foreach ($relevances as $key => $value) {
            if (preg_match("/^(\d+)\_(\d+)_(\d+)_(\d+)$/",$key,$regs)) {
                self::updateExample((int)$value, (int)$regs[1], (int)$regs[2], /*(int)$regs[3],*/ (int)$regs[4]);
            }
        }
    }
    
    public static function updateExample($relevance, $meaning_id, $text_id, /*$s_id,*/ $w_id) {
        if ($relevance == 1) { // не выставлена оценка
            if (self::existsPositiveRelevance($text_id, /*$s_id,*/ $w_id, $meaning_id)) { // этот пример привязан к другому значению
                $relevance = 0;
            }
        } elseif ($relevance != 0) { // положительная оценка
            self::setNegativeToUndefOthers($text_id, /*$s_id,*/ $w_id, $meaning_id); // всем значениям с неопределенными оценками проставим отрицательные
        }
        DB::statement('UPDATE meaning_text SET relevance='.$relevance // запишем оценку этому значению
                     .' WHERE meaning_id='.$meaning_id
                     .' AND text_id='.$text_id
//                     .' AND sentence_id='.$s_id
                     .' AND w_id='.$w_id);
        if ($relevance>1) {
            TextWordform::updateWordformLinksAfterCheckExample($text_id, $w_id, $meaning_id);
        }        
    }

    // ищем другие значения лемм с положительной оценкой
    public static function existsPositiveRelevance($text_id, /*$s_id,*/ $w_id, $meaning_id) {
        return DB::table('meaning_text') 
                -> where('text_id',$text_id)
//                -> where('sentence_id',$s_id)
                -> where('w_id',$w_id)
                -> where('meaning_id', '<>', $meaning_id)
                -> where ('relevance','>',1)->count();
    }

    // всем значениям с неопределенными оценками проставим отрицательные
    public static function setNegativeToUndefOthers($text_id, /*$s_id,*/ $w_id, $meaning_id) {
        DB::statement('UPDATE meaning_text SET relevance=0'. 
                      ' WHERE meaning_id <> '.$meaning_id.
                      ' AND relevance=1'.
                      ' AND text_id='.$text_id.
//                      ' AND sentence_id='.$s_id.
                      ' AND w_id='.$w_id);
    }
    
    public static function preparationForExampleEdit($example_id){
        if (preg_match("/^(\d+)_(\d+)_(\d+)$/",$example_id,$regs)) {
            $text_id = (int)$regs[1];
            $s_id = (int)$regs[2];
            $w_id = (int)$regs[3];
        
            $sentence = Text::extractSentence($text_id, $s_id, $w_id);            

            $meanings = Meaning::join('meaning_text','meanings.id','=','meaning_text.meaning_id')
                               -> where('text_id',$text_id)
                               -> where('sentence_id',$s_id)
                               -> where('w_id',$w_id)
                               -> get();
            $meaning_texts = [];

            foreach ($meanings as $meaning) {
                $langs_for_meaning = Lang::getListWithPriority($meaning->lemma->lang_id);
                foreach ($langs_for_meaning as $lang_id => $lang_text) {
                    $meaning_text_obj = MeaningText::where('lang_id',$lang_id)->where('meaning_id',$meaning->id)->first();
                    if ($meaning_text_obj) {
                        $meaning_texts[$meaning->id][$lang_text] = $meaning_text_obj->meaning_text;
                    }
                }
            }   
            
            return [$sentence, $meanings, $meaning_texts];
        } else {
            return [NULL, NULL, NULL];
        }
    }
    
    /**
     * Update meaning-text links after choosing gramset.
     * 
     * @param type $text_id
     * @param type $w_id
     * @param type $gramset_id
     */
    public static function updateMeaningLinksAfterCheckExample($text_id, $w_id, $gramset_id) {
        $meaning_text_rels = self::whereTextId($text_id)
                                 ->whereWId($w_id)->get();
        foreach ($meaning_text_rels as $meaning_text_rel) {
            $meaning=Meaning::find($meaning_text_rel->meaning_id);
            $pos=$meaning->lemma->pos;
            if ($pos->gramsets()->wherePivot('gramset_id', $gramset_id)->count()==0) {
                DB::statement('UPDATE meaning_text SET relevance=0'
                             .' WHERE meaning_id='.$meaning_text_rel->meaning_id
                             .' AND text_id='.$text_id
                             .' AND w_id='.$w_id);
            }
        }
    }
}
