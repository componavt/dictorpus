<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

class Dialect extends Model
{
    public $timestamps = false;
    
    
    /** Gets name of this dialect, takes into account locale.
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
