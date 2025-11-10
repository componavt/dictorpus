<?php namespace App\Traits\Scopes;

trait SearchByType
{    public function scopeSearchByType($builder, $type_id) {
\Log::info('scopeSearchByType called with type_id: ' . ($type_id ?? 'null'));
        if (!$type_id) {
        // Отладка
        \Log::info('searchByType: type_id is empty, returning builder');
            return $builder;
        }
    \Log::info("searchByType: searching for type_id = {$type_id}");
        
        // Для MySQL 5.7+
        return $builder->whereRaw('JSON_CONTAINS(types, ?)', [(int)$type_id]);

        // Альтернатива для MySQL < 5.7 (работает, но менее надёжно):
        // return $builder->whereRaw('types LIKE ?', ['%"' . (int)$type_id . '"%']);
    }
}    

