<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

use App\Models\Corpus\District;
use App\Models\Corpus\Informant;
use App\Models\Corpus\PlaceName;
use App\Models\Corpus\Region;

class Place extends Model
{
    public $timestamps = false;
    protected $fillable = ['district_id','region_id','name_en','name_ru'];
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }

    /** Gets name of this place, takes into account locale.
     * 
     * @return String
     */
    public function getNameAttribute()
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $column = "name_" . $locale;
        $name = $this->{$column};
        
        if (!$name && $locale!='ru') {
            $name = $this->name_ru;
        }
        
        return $name;
    }

    public function identifiableName()
    {
        return $this->placeString();//name;
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

    // Place __has_many__ Events
    public function events()
    {
        return $this->hasMany(Event::class,'place_id');
    }

    // Place __has_many__ Informants
    public function informants()
    {
        return $this->hasMany(Informant::class,'birth_place_id');
    }

    // Place __has_many_through__ Texts
    public function texts()
    {
        return $this->hasManyThrough(Text::class, Event::class);
    }

    /** Gets list of places
     * 
     * @return Array [1=>'Пондала (Pondal), Бабаевский р-н, Вологодская обл.',..]
     */
    public static function getList()
    {     
        $locale = LaravelLocalization::getCurrentLocale();
        
        $places = self::orderBy('name_'.$locale)->get();
        
        $list = array();
        foreach ($places as $row) {
            $list[$row->id] = $row->placeString();
        }
        
        return $list;         
    }
    
    /** Gets list of places
     * 
     * @return Array [1=>'Dialectal texts (199)',..]
     */
    public static function getListWithQuantity($method_name)
    {     
        $locale = LaravelLocalization::getCurrentLocale();
        
        $places = self::orderBy('name_'.$locale)->get();
        
        $list = array();
        foreach ($places as $row) {
            $count=$row->$method_name()->count();
            $name = $row->placeString();
            if ($count) {
                $name .= " ($count)";
            }
            $list[$row->id] = $name;
        }
        
        return $list;         
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
    
    public function placeString($lang_id='')
    {
        $info = [];
        
        if ($this->name) {
            $info[0] = $this->name;
            if ($this->other_names()->count()) {
                $other_names = $this->other_names();
                if ($lang_id) {
                    $other_names = $other_names -> where('lang_id',$lang_id);
                }
                $other_names = $other_names -> get();
                
                $tmp = [];
                foreach ($other_names as $other_name) {
                    $tmp[] = $other_name->name; 
                }
                if (sizeof($tmp)) {
                    $info[0] .= ' ('.join(', ',$tmp).')';
                }
            }
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
