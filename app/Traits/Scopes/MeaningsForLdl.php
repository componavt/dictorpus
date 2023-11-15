<?php namespace App\Traits\Scopes;

trait MeaningsForLdl
{    
    public static function scopeForLdl($builder) {
        return $builder->whereIn('id', function ($q) {
                    $q->select('meaning_id')->from('meaning_place')
                        ->where('place_id', '<>', 245); // without Koikary
                })->whereIn('lemma_id', function ($q) {
                    $q->select('id')->from('lemmas')
                      ->whereLangId(6); // Ludic                    
                });
    }
}    

