<?php namespace App\Traits\Methods;

trait sortList
{
    public static function sortList() {
        $list = [];
        foreach (self::SortList as $field) {
            $list[$field] = trans('search.sort'). ' '. trans('search.by_'.$field);
        }
        return $list;
    }
}