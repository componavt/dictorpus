<?php namespace App\Traits\Methods\search;

trait byId
{
    public static function searchById($objs, $search_id) {
        if (!$search_id) {
            return $objs;
        }
        return $objs->where('id',$search_id);
    }
}