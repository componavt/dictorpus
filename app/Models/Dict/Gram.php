<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

use LaravelLocalization;

class Gram extends Model
{
    public $timestamps = false;
        
    use \Venturecraft\Revisionable\RevisionableTrait;

    public static function boot()
    {
        parent::boot();
    }

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
    
    
    /** Gets all grams for given category sorted, 
     * for example objects "sg", "pl" ($category_id is 2), 
     * or case objects: "nominative", "genititive", ... (when ($category_id is 1).
     * 
     * @param int $category_id ID of category of grams
     * 
     * @return \Illuminate\Http\Response
     */
    public static function getByCategory($category_id)
    {
        return self::where('gram_category_id',$category_id)->orderBy('sequence_number')->get();
         
    }
}
