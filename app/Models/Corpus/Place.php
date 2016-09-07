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

    /**
     * Gets full information about place
     * 
     * f.e. "Пондала (Pondal), Бабаевский р-н, Вологодская обл."
     * 
     * @param int $lang_id ID of text language for output translation of settlement title, f.e. Pondal
     * 
     * @return String
     */
    public function placeString()//$lang_id=''
    {
        $info = [];
        
        if ($this->name) {
            if ($this->other_names()->count()) {
                $other_names = $this->other_names()->get();//where('lang_id',$lang_id)->first();
                $tmp = [];
                foreach ($other_names as $other_name) {
                    $tmp[] = $other_name->name; 
                }
            }
            $info[0] = $this->name. sizeof($tmp) ? '('.join(', ',$tmp).')' : '';
        }
        
        if ($this->district) {
            $info[] = $this->district->name;
        }
        
        if ($this->region) {
            $info[] = $this->region->name;
        }
        
        return join(', ', $info);
    }    
    
}
