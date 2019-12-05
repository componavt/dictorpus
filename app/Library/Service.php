<?php

namespace App\Library;

use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\Wordform;

class Service
{
    /**
     * select all lemmas having wordforms without affixes;
     * select id from lemmas where lang_id=1 and id in (select lemma_id from lemma_wordform where affix is NULL and gramset_id is not NULL);
     * select id from lemmas where lang_id=1 and id in (select lemma_id from lemma_wordform where affix is NULL and gramset_id is not NULL and wordform_id in (select id from wordforms where wordform not like '% %'));
     * 
     * @param INT $lang_id
     */
    public static function addWordformAffixesForLang($lang_id) {
        $lemmas = Lemma::where('lang_id',$lang_id)
                       ->whereIn('id',function($q) use ($lang_id){
                            $q->select('lemma_id')->from('lemma_wordform')
                              ->whereNull('affix')->whereNotNull('gramset_id')
                              ->whereIn('wordform_id',function($query){
                                    $query->select('id')->from('wordforms')
                                      ->where('wordform','NOT LIKE','% %');
                               });
                       })
                       //->count();
                       ->orderBy('id')->take(10)
//                       ->whereId(17)        
                       ->get(); 
//dd($lemmas);                     
        foreach ($lemmas as $lemma) {
            list($stem, $affix) = $lemma->getStemAffix();
            if (!$stem) { continue; }
            
            $wordforms = $lemma->wordforms()->where('wordform','NOT LIKE','% %')->whereNull('affix')->whereNotNull('gramset_id')->get();
//dd($lemma);
//dd($wordforms);
            foreach ($wordforms as $wordform) {
//dd($wordform->pivot->gramset_id);
/*                $wordform_comp = preg_split("/\s/", $wordform->wordform); we don't take analytic forms
                $last_comp = array_pop($wordform_comp);
                if (preg_match("/^".$stem."(.*)$/u", $last_comp, $regs)) { */
                if (preg_match("/^".$stem."(.*)$/u", $wordform->wordform, $regs)) {
                    $w_affix = $regs[1];
                } else {
                    $w_affix = '#';
                }
//print "<p>".$lemma->lemma. " = ". $wordform->wordform. " = $w_affix</p>";  
                $wordform->updateAffix($lemma->id, $wordform->pivot->gramset_id, $w_affix);
            }
print "<p>".$lemma->lemma."</p>";                 
        }
    }    
}
