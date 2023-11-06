<?php namespace App\Traits\Scopes;

use App\Models\Dict\Label;

trait ConceptsForLdl
{    
    public static function scopeForLdl($builder) {
        return $builder->whereIn('id', function($q) {
                            $q->select('concept_id')->from('concept_meaning')
                              ->whereIn('meaning_id', function ($q2) {
                                  $q2->select('id')->from('meanings')
                                     ->whereIn('lemma_id', Label::ldlLemmas());
                              });
                        });
    }
}    

