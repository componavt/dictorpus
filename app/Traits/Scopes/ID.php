<?php namespace App\Traits\Scopes;

trait ID
{    public static function scopeID($builder, $id) {
        if (!$id) {
            return $builder;
        }
        return $builder->where('id',$id);
    }
}    

