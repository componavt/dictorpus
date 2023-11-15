<?php namespace App\Traits\Scopes;

//use App\Models\Dict\Label;
/*
 * for Concept
 */

trait ConceptsForLdl
{    
    public static function scopeForLdl($builder) {
        return $builder->whereIn('id', function($q) {
                            $q->select('concept_id')->from('concept_meaning')
                              ->whereIn('meaning_id', function ($q2) {
                                  $q2->select('id')->from('meanings')
                                     ->whereIn('id', function ($q) {
                                        $q->select('meaning_id')->from('meaning_place')
                                            ->where('place_id', '<>', 245); // without Koikary
                                     })->whereIn('lemma_id', function ($q) {
                                        $q->select('id')->from('lemmas')
                                          ->whereLangId(6); // Ludic                    
                                     });                                          
//                                     ->whereIn('lemma_id', Label::ldlLemmas());
                              });
                        });
    }
}    

