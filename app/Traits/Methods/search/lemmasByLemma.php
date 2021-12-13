<?php namespace App\Traits\Methods\search;

use App\Library\Grammatic;

trait lemmasByLemma
{
    /** Gets name of this object, takes into account locale.
     * 
     * @return String
     */
    public static function searchByLemma($lemmas, $lemma) {
        if (!$lemma) {
            return $lemmas;
        }
        
        $lemma = preg_replace("/\|/", '', $lemma);
        return $lemmas->where(function ($query) use ($lemma) {
                            $query -> where('lemma_for_search', 'like', Grammatic::toSearchForm($lemma))
                                   -> orWhere('lemma_for_search', 'like', $lemma)
                                   -> orWhereIn('id', function ($q) use ($lemma) {
                                       $q->select('lemma_id')->from('phonetics')
                                         ->where('phonetic', 'like', $lemma);
                                   });
//                                   -> where('lemma_for_search', '');
                });
    }    
}