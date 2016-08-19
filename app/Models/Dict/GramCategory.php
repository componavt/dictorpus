<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

class GramCategory extends Model
{
    public $timestamps = false;
    
    
    /** Gets name of this grammatical category, takes into account locale.
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
