<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

use App\Models\Corpus\Region;

class District extends Model
{
    public $timestamps = false;
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    public static function boot()
    {
        parent::boot();
    }

    /** Gets name of this corpus, takes into account locale.
     * 
     * @return String
     */
    public function getNameAttribute() : String
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $column = "name_" . $locale;
        return $this->{$column};
    }
    
    /** Gets lang, takes into account locale.
     * 
     * Corpus belongs_to Lang
     * 
     * @return Relationship, Query Builder
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }    
}
