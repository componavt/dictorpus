<?php namespace App\Traits\Scopes;

trait SearchByType
{    public function scopeSearchByType($builder, $type_id) {
        if (!$type_id) {

            return $builder;
        }
        
        // Для MySQL 5.7+
        return $builder->whereRaw('JSON_CONTAINS(types, ?)', [(int)$type_id]);

        // Альтернатива для MySQL < 5.7 (работает, но менее надёжно):
        // return $builder->whereRaw('types LIKE ?', ['%"' . (int)$type_id . '"%']);
    }
}    

