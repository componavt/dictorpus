<?php namespace App\Traits\Methods\Search;

trait intField
{
    public static function searchIntField($objs, $search_field, $search_value) {
        if (!$search_value) {
            return $objs;
        }
        return $objs->where($search_field, $search_value);
    }
}