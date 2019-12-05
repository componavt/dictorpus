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
                       ->orderBy('id')//->take(10)
//                       ->whereId(17)        
                       ->get(); 
        foreach ($lemmas as $lemma) {
            list($stem, $affix) = $lemma->getStemAffix();
            if (!$stem) { 
print '<p><a href="/dict/lemma/'.$lemma->id.'">'.$lemma->lemma.'</a> - WRONG STEM</p>';                
                continue; }
            
            $wordforms = $lemma->wordforms()->where('wordform','NOT LIKE','% %')->whereNull('affix')->whereNotNull('gramset_id')->get();
            foreach ($wordforms as $wordform) {
                $w_affix = $lemma->affixForWordform($wordform->wordform);
//print "<p>".$lemma->lemma. " = ". $wordform->wordform. " = $w_affix</p>";  
                $wordform->updateAffix($lemma->id, $wordform->pivot->gramset_id, $w_affix);
            }
print "<p>".$lemma->lemma."</p>";                 
        }
    }    
}
