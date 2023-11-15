<?php namespace App\Traits\Scopes;

/*
 * for Text
 */
trait DialectTexts
{    
    public static function scopeDialectTexts($builder) {
        return $builder->whereCorpusId(1);
    }
}    

