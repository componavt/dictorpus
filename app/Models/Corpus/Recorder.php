<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

class Recorder extends Model
{
    public $timestamps = false;
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    public static function boot()
    {
        parent::boot();
    }
    
    /** Gets name of this informant, takes into account locale.
     * 
     * @return String
     */
    public function getNameAttribute() : String
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $column = "name_" . $locale;
        $name = $this->{$column};
        
        if (!$name && $locale!='ru') {
            $name = $this->name_ru;
        }
        
        return $name;
    }
    
    /** Gets list of recorders
     * 
     * @return Array [1=>'Онегина Нина Федоровна',..]
     */
    public static function getList()
    {     
        $locale = LaravelLocalization::getCurrentLocale();
        
        $recorders = self::orderBy('name_'.$locale)->get();
        
        $list = array();
        foreach ($recorders as $row) {
            $list[$row->id] = $row->name;
        }
        
        return $list;         
    }
    
}
