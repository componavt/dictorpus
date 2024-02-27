<?php namespace App\Traits\Scopes;

/*
 * for Place
 */
trait WithDialectAudio
{    
    public static function scopeWithDialectAudio($builder) {
        return $builder->whereIn('id', function ($q1) {
                            $q1->select('birth_place_id')->from('informants')
                               ->whereIn('id', function ($q2) {
                                    $q2->select('informant_id')->from('event_informant')
                                    ->whereIn('event_id', function ($q3) {
                                        $q3->select('event_id')->from('texts')
                                           ->whereIn('id', function ($query3) {
                                               $query3->select('text_id')->from('audiotexts');
                                           })
                                           ->whereIn('id', function ($query4) {
                                               $query4->select('text_id')->from('corpus_text')
                                                      ->whereIn('corpus_id', [1,4]);  // dialect texts
                                           });
                                    });
                               });
                       });
    }
}    

