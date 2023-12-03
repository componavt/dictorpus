<?php namespace App\Traits\Scopes;

use DB;
/*
 * for Text
 */
trait HasOneDialect
{    
    public static function scopeHasOneDialect($builder) {
        return $builder->whereNotIn('id', function ($q) {
            $q->select('text_id')->from('dialect_text')->groupBy('text_id')
                ->having(DB::raw('count(*)'), '>', 1);
        });
    }
}    

