<?php namespace App\Traits\Methods\Search;

trait strField
{
    public static function searchStrField($objs, $search_field, $search_value) {
        if (!$search_value) {
            return $objs;
        }
        return $objs->where($search_field, 'like', $search_value);
    }
}