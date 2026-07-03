<?php

namespace App\Traits\Methods;

trait getList
{
    /** Gets list of objects
     * 
     * @return Array [1=>'Object name',..]
     */
    public static function getList($short = false, $in = false, $ids = [])
    {
        $field_name = $short ? 'short' : 'name';

        if (sizeof($ids)) {
            $objects = self::whereIn('id', $ids)->get();
        } elseif ($in && self::getModelName()) {
            $objects = self::whereIn('id', function ($q) use ($in) {
                $q->select(self::getModelName() . '_id')->from($in);
            })->get();
        } else {
            $objects = self::all();
        }

        return self::getListForObjs($objects, $field_name);
    }

    public static function getListForObjs($objects, $field_name = 'name')
    {
        $list = array();
        foreach ($objects as $row) {
            $list[$row->id] = $row->{$field_name};
        }

        asort($list);

        return $list;
    }
}
