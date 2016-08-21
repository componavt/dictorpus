<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

use LaravelLocalization;

class Gram extends Model
{
    public $timestamps = false;
        
    /** Gets name of this grammatical attribute, takes into account locale.
     * 
     * @return String
     */
    public function getNameAttribute() : String
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $column = "name_" . $locale;
        return $this->{$column};
    }

    /** Gets short name of this grammatical attribute, takes into account locale.
     * 
     * @return String
     */
    public function getNameShortAttribute() : String
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $column = "name_short_" . $locale;
        if (!$this->{$column}) {
            $column = "name_" . $locale;
        }
        return $this->{$column};
    }
    
}
