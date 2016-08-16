<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

class Lang extends Model
{
    //
    
    public $timestamps = false;
    
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
    
    // Lang __has_many__ Lemma
    public function lemmas()
    {
        return $this->hasMany(Lemma::class);
    }
}
