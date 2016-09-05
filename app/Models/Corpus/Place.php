<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

use App\Models\Corpus\District;
use App\Models\Corpus\PlaceName;
use App\Models\Corpus\Region;

class Place extends Model
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
    
    public function district()
    {
        return $this->belongsTo(District::class);
    }  
    
    public function region()
    {
        return $this->belongsTo(Region::class);
    }    
    
    public function other_names()
    {
        return $this->hasMany(PlaceName::class);
    }
}
