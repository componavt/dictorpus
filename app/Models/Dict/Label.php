<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    public $timestamps = false;
    protected $fillable = ['name_en', 'name_ru'];
    
    public function identifiableName()
    {
        return $this->name;
    }    

    /** Gets name of this lang, takes into account locale.
     * 
     * @return String
     */
    public function getNameAttribute() : String
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $column = "name_" . $locale;
        return $this->{$column};
    }
}
