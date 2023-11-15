<?php namespace App\Traits\Scopes;

/*
 * for Text
 */
trait WithAudio
{    
    public static function scopeWithAudio($builder) {
        return $builder->whereIn('id', function ($q1) {
                    $q1 -> select('text_id')->from('audiotexts');
                });
    }
}    

