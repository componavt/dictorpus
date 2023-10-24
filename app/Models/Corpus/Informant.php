<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

use App\Library\Str;

use App\Models\Corpus\Event;
use App\Models\Corpus\Place;
use App\Models\Corpus\Text;
use App\Models\Dict\Lemma;

class Informant extends Model
{
    public $timestamps = false;
    protected $fillable = ['birth_place_id','birth_date','name_en','name_ru'];
    
    use \Venturecraft\Revisionable\RevisionableTrait;
    
    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }
    
    // Has Many Relations
    use \App\Traits\Relations\HasMany\Audios;
    // Belongs To Many Relations
    use \App\Traits\Relations\BelongsToMany\Lemmas;

    // Methods
    use \App\Traits\Methods\getNameAttribute;
    
    public function getDialectsAttribute() {
        $birth_place = $this->birth_place;
        if (!$birth_place) {
            return null;
        }
        
        return $birth_place->dialects;        
    }

    public function getLangAttribute() {
        $dialects = $this->dialects;
        
        if (!isset($dialects[0])) {
            return null;
        }
        
        return $dialects[0]->lang;        
    }

    public function getDialectNameAttribute() {
        $names = [];
        
        foreach ($this->dialects as $dialect) {
            $names[] = $dialect->name;
        }
        
        return join(', ', $names);        
    }

    /** Gets place, takes into account locale.
     * 
     * Informant belongs_to Place
     * 
     * @return Relationship, Query Builder
     */
    public function birth_place()
    {
        return $this->belongsTo(Place::class);//,'birth_place_id'
    }  
    
    // Informant __has_many__ Events
    public function events()
    {
        return $this->hasMany(Event::class);
    }
    
    // Informant __has_many_through__ Texts
    public function texts()
    {
        $informant_id = $this->id;
//        return $this->hasManyThrough(Text::class, Event::class);
        $texts = Text::whereIn('event_id', function($query) use ($informant_id) {
                                $query->select('event_id')->from('event_informant')
                                      ->where('informant_id',$informant_id);
                              });
        return $texts;
    }
    
    public static function getNameById($id)
    {     
        if (!$id) {
            return;
        }
        $informant = self::find($id);
        if (!$informant) {
            return;
        }
        return $informant->name;
    }
    
    /** Gets list of informant
     * 
     * @return Array [1=>'Vepsian',..]
     */
    public static function getList()
    {     
        $locale = LaravelLocalization::getCurrentLocale();
        
        $informants = self::orderBy('name_'.$locale)->get();
        
        $list = array();
        foreach ($informants as $row) {
            $list[$row->id] = $row->informantString('',false);
        }
        
        return $list;         
    }
    
    /**
     * Gets full information about informant
     * 
     * i.e. "Калинина Александра Леонтьевна, 1909, Пондала (Pondal), Бабаевский р-н, Вологодская обл."
     * 
     * @param int $lang_id ID of text language for output translation of settlement title, f.e. Pondal
     * 
     * @return String
     */
    public function informantString($lang_id='', $all_place_names=true, $link='')
    {
        $info = [];
        
        if ($this->name) {
            $info[0] = $this->name;
        }
        if ($link) {
            $info[0] = to_link($info[0], $link.'informant='.$this->id);
        }
        
        if ($this->birth_date) {
            $info[] = $this->birth_date;
        }
        
        if ($this->birth_place) {
            $info[] = $this->birthPlaceString($lang_id, $all_place_names, $link ? $link.'birth_place=': '');
        }
        
        return join(', ', $info);
    }   

    public function unvoicedLemmasCount() {
        return Lemma::whereLangId($this->lang->id)
                ->whereNotIn('id',function ($q) {
                    $q->select('lemma_id')->from('audio_lemma');
                })->count();        
    }

    public function birthPlaceString($lang_id='', $all_place_names=true, $link='')
    {
        if (!$this->birth_place) {
            return '';
        }
        return Place::find($this->birth_place_id)
                            ->placeString($lang_id, $all_place_names, $link);
    }    
    
    public function notVoicedLemmas() {
        $informant = $this;
        return  Lemma::whereLangId($informant->lang->id)
                ->whereNotIn('id', function ($q) use ($informant) { // не озвученные информантом
                    $q->select('lemma_id')->from('audio_lemma')
                      ->whereIn('audio_id', function ($q2) use ($informant){
                          $q2->select('id')->from('audios')
                             ->whereInformantId($informant->id);
                      });
                })
                ->whereNotIn('id', function ($q) use ($informant){ // не добавленные в список для озвучки
                    $q->select('lemma_id')->from('informant_lemma')
                      ->whereInformantId($informant->id);
                });
    }
    
    public static function search(Array $url_args) {
        $locale = LaravelLocalization::getCurrentLocale();
        $informants = self::orderBy('name_'.$locale);  
        
        $informants = self::searchByName($informants, $url_args['search_name']);
        $informants = self::searchByRegion($informants, $url_args['search_birth_region']);
        $informants = self::searchByDistrict($informants, $url_args['search_birth_district']);

        if ($url_args['search_birth_place']) {
            $informants = $informants->where('birth_place_id',$url_args['search_birth_place']);
        } 

        if ($url_args['search_birth']) {
            $informants = $informants->where('birth_date',$url_args['search_birth']);
        } 

        if ($url_args['search_id']) {
            $informants = $informants->where('id',$url_args['search_id']);
        } 
        return $informants;
    }
    
    public static function searchByName($informants, $name) {
        if (!$name) {
            return $informants;
        }
        return $informants->where(function($q) use ($name){
                        $q->where('name_en','like', $name)
                          ->orWhere('name_ru','like', $name);
                });
    }

    public static function searchByRegion($informants, $region_id) {
        if (!$region_id) {
            return $informants;
        }
        return $informants->whereIn('birth_place_id',function($q) use ( $region_id){
                    $q->select('id')->from('places')
                       ->whereIn('district_id', function($q1) use ($region_id){
                            $q1->select('id')->from('districts')
                               ->whereRegionId($region_id);                                        
                        });
                });                            
    }
    
    public static function searchByDistrict($informants, $district_ids) {
        if (!sizeof($district_ids)) {
            return $informants;
        }
        return $informants->whereIn('birth_place_id',function($q) use ($district_ids){
                        $q->select('id')->from('places')
                           ->whereIn('district_id',$district_ids);
                    });                            
    } 
    
    public static function urlArgs($request) {
        $url_args = Str::urlArgs($request) + [
                    'search_birth'   => (int)$request->input('search_corpus'),
                    'search_birth_district'  => (array)$request->input('search_birth_district'),
                    'search_birth_place' => (array)$request->input('search_birth_place'),
                    'search_birth_region' => $request->input('search_birth_region'),
                    'search_id'  => (int)$request->input('search_id'),
                    'search_name'   => $request->input('search_name'),
                ];
        
        $url_args['search_birth'] = $url_args['search_birth'] ? $url_args['search_birth'] : NULL;
        
        $url_args['search_id'] = $url_args['search_id'] ? $url_args['search_id'] : NULL;
        
        return $url_args;
    }
}
