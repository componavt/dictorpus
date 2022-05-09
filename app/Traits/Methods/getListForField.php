<?php namespace App\Traits\Methods;

use LaravelLocalization;

trait getListForField
{
    /** Gets list of objects
     * 
     * @return Array [1=>'Bridal laments',..]
     */
    public static function getListForField($field_id=NULL, $field_name='genre_id')
    {     
        $locale = LaravelLocalization::getCurrentLocale();
        
        $objs = self::orderBy('name_'.$locale);
        
        if ($field_id) {        
            $objs = $plots->where($field_name, $field_id);
        }
        
        $list = [];
        foreach ($objs->get() as $row) {
            $list[$row->id] = $row->name;
        }
        
        return $list;         
    }
}