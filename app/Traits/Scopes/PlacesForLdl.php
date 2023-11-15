<?php namespace App\Traits\Scopes;

trait PlacesForLdl
{    
    public static function scopeForLdl($builder) {
        return $builder->where('id', '<>', 245); // without Koikary
    }
}    

