<?php namespace App\Traits\Scopes;

use App\Models\Dict\Label;

trait LemmasForLdl
{    
    public static function scopeForLdl($builder) {
//        return $builder->whereIn('id', Label::ldlLemmas());
        return $builder->whereIn('id', function ($q) {
            $q->select('lemma_id')->from('meanings')
              ->whereIn('id', function ($q2) {
                  $q2->select('meaning_id')->from('meaning_place');
              });
        });
    }
}    

