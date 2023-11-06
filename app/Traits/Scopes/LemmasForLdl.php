<?php namespace App\Traits\Scopes;

use App\Models\Dict\Label;

trait LemmasForLdl
{    
    public static function scopeForLdl($builder) {
        return $builder->whereIn('id', Label::ldlLemmas());
    }
}    

