<?php namespace App\Traits\Methods;

trait getNameByID
{
    /** Gets name by code, takes into account locale.
     * 
     * @return String
     */
    public static function getNameByID($id) : String
    {
        $item = self::where('id',$id)->first();
        if ($item) {
            return $item->name;
        }
    }
}