<?php namespace App\Traits\Scopes;

/*
 * for Text
 */
trait DialectTexts
{    
    public static function scopeDialectTexts($builder) {
        return $builder->whereIn('id', function ($q) {
                    $q->select('text_id')->from('corpus_text')
                           ->whereIn('corpus_id', [1,4]);  // dialect texts
                });
    }
}    

