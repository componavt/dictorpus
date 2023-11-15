<?php namespace App\Traits\Scopes;

/*
 * for Lemma
 */
trait LemmasForConcept
{    
    public static function scopeForConcept($builder, $concept_id) {
        return $builder->whereIn('id', function($q) use ($concept_id) {
                            $q->select('lemma_id')->from('meanings')
                              ->whereIn('id', function($q2) use ($concept_id) {
                                $q2->select('meaning_id')->from('concept_meaning')
                                   ->where('concept_id', $concept_id);
                              });
                        });
    }
}    

