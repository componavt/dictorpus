<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

use App\Library\Str;

use App\Models\Corpus\District;
use App\Models\Corpus\Informant;
use App\Models\Corpus\PlaceName;
use App\Models\Corpus\Region;
use App\Models\Corpus\Text;

class Place extends Model
{
    public $timestamps = false;
    protected $fillable = ['district_id','region_id','name_en','name_ru', 'latitude', 'longitude'];
    
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
        return $this->placeString('', false);//name;
    }    

    // Belongs To Many Relations
    use \App\Traits\Relations\BelongsToMany\Dialects;
    
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

    public function texts_with_audio()
    {
        return $this->texts()->whereIn('texts.id', function ($q) {
            $q -> select('text_id')->from('audiotexts');
        });
    }

    /** Gets list of places
     * 
     * @return Array [1=>'Пондала (Pondal), Бабаевский р-н, Вологодская обл.',..]
     */
    public static function getListByLang($lang_id, $full_name=false)
    {     
        $locale = LaravelLocalization::getCurrentLocale();
        $name_field = 'name_'.$locale;
        $places = self::whereIn('id', function ($q) use ($lang_id) {
                            $q->select('place_id')->from('dialect_place')
                              ->whereIn('dialect_id', function ($q2) use ($lang_id) {
                                  $q2->select('id')->from('dialects')
                                     ->whereLangId($lang_id);
                              });
                        })->orderBy($name_field)->get();
        
        $list = array();
        foreach ($places as $row) {
            $list[$row->id] = $full_name ? $row->placeString() : $row->{$name_field};
        }
        
        return $list;         
    }
    
    /** Gets list of places
     * 
     * @return Array [1=>'Пондала (Pondal), Бабаевский р-н, Вологодская обл.',..]
     */
    public static function getList($full=true)
    {     
        $locale = LaravelLocalization::getCurrentLocale();
        
        $places = self::orderBy('name_'.$locale)->get();
        
        $list = array();
        foreach ($places as $row) {
            $list[$row->id] = $full ? $row->placeString('', false): $row->name;
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
            $name = $row->placeString('', false);
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
    
    public function placeString($lang_id='', $all_place_names=true)
    {
        $info = [];
        
        if ($this->name) {
            $info[0] = $this->name;
            if ($all_place_names && $this->other_names()->count()) {
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
    
    public static function urlArgs($request) {
        $url_args = Str::urlArgs($request) + [
                    'limit_num'       => (int)$request->input('limit_num'),
                    'page'            => (int)$request->input('page'),
                    'search_district'  => (int)$request->input('search_district'),
                    'search_id'       => (int)$request->input('search_id'),
                    'search_name'     => $request->input('search_name'),
                    'search_region'     => (int)$request->input('search_region'),
                ];
        
        if (!$url_args['search_id']) {
            $url_args['search_id'] = NULL;
        }
        
        return $url_args;
    }
    
    public static function search(Array $url_args) {
        $locale = LaravelLocalization::getCurrentLocale();
        $places = self::orderBy('name_'.$locale);

        $places = self::searchByDistrict($places, $url_args['search_district']);
        $places = self::searchByID($places, $url_args['search_id']);
        $places = self::searchByPlaceName($places, $url_args['search_name']);
        $places = self::searchByRegion($places, $url_args['search_region']);
//dd($places->toSql());                                
        return $places;
    }
    
    public static function searchByPlaceName($places, $place_name) {
        if (!$place_name) {
            return $places;
        }
        return $places->where(function($q) use ($place_name){
                        $q->whereIn('id',function($query) use ($place_name){
                            $query->select('place_id')
                            ->from(with(new PlaceName)->getTable())
                            ->where('name','like', $place_name);
                        })->orWhere('name_en','like', $place_name)
                          ->orWhere('name_ru','like', $place_name);
                });
    }
    
    public static function searchByRegion($places, $region_id) {
        if (!$region_id) {
            return $places;
        }
        return $places->where('region_id',$region_id);
    }
    
    public static function searchByDistrict($places, $district_id) {
        if (!$district_id) {
            return $places;
        }
        return $places->where('district_id',$district_id);
    }
    
    public static function searchByID($places, $search_id) {
        if (!$search_id) {
            return $places;
        }
        return $places->where('id',$search_id);
    }
    
    public function countTextBirthPlace() {
        $place = $this->id;
        $texts = Text::whereIn('event_id',function($query) use ($place){
                    $query->select('event_id')
                    ->from('event_informant')
                    ->whereIn('informant_id',function($query) use ($place){
                        $query->select('id')
                        ->from('informants')
                        ->where('birth_place_id',$place);
                    });
                });
        return $texts->count();
    }
    
    /**
     * @return Array [<dialect1> => <lang1>, ... ]
     */
    public function getDialectLangs() {
        $out = [];
        
        foreach ($this->dialects as $dialect) {
            $out[$dialect->id] = $dialect->lang_id;
        }
        
        return $out;
    }
}
