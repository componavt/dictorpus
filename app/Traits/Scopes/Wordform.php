<?php namespace App\Traits\Scopes;

use App\Library\Grammatic;

trait Wordform
{    public function scopeWordform($builder, $wordform) {
        if (!$wordform) {
            return $builder;
        }
        return $builder->whereIn('wordform_id',function($query) use ($wordform){
                            $query->select('id')
                            ->from('wordforms')
                            ->where('wordform_for_search','like', Grammatic::toSearchForm($wordform));
                        });
    }
}    

