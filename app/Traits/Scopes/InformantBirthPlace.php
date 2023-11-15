<?php namespace App\Traits\Scopes;

/*
 * for Text
 */
trait InformantBirthPlace
{    
    public static function scopeInformantBirthPlace($builder, $place_id) {
        return $builder->whereIn('event_id', function ($q1) use ($place_id) {
                $q1->select('event_id')->from('event_informant')
                   ->whereIn('informant_id', function ($q2) use ($place_id) {
                    $q2->select('id')->from('informants')
                       ->whereBirthPlaceId($place_id);                       
                   });
                });
    }
}    

