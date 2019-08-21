<?php namespace App\Traits\Scopes;

trait LangID
{    public function scopeLangID($builder, $lang_id) {
        if (!$lang_id) {
            return $builder;
        }
        return $builder->where('lang_id', $lang_id);
    }
}    

