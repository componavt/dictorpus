<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

use App\Library\Str;

use App\Models\Corpus\Region;

class District extends Model
{
    public $timestamps = false;
    protected $fillable = ['region_id','name_en','name_ru'];
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }

    // Methods
    use \App\Traits\Methods\getNameAttribute;
    
    /** Gets Region
     * 
     * District belongs_to Region
     * 
     * @return Relationship, Query Builder
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    } 
    
    // District __has_many__ Places
    public function places()
    {
        return $this->hasMany(Place::class);
    }

    /** Gets list of districts
     * 
     * @return Array [1=>'Бабаевский р-н',..]
     */
    public static function getList()
    {     
        $locale = LaravelLocalization::getCurrentLocale();
        
        $districts = self::orderBy('name_'.$locale)->get();
        
        $list = array();
        foreach ($districts as $row) {
            $list[$row->id] = $row->name;
        }
        
        return $list;         
    }
    
    /** Gets list of districts with quantity of relations $method_name
     * 
     * @return Array [1=>'Бабаевский р-н (199)',..]
     */
    public static function getListWithQuantity($method_name)
    {     
        $locale = LaravelLocalization::getCurrentLocale();
        
        $districts = self::orderBy('name_'.$locale)->get();
        
        $list = array();
        foreach ($districts as $row) {
            $count=$row->$method_name()->count();
            $name = $row->name;
            if ($count) {
                $name .= " ($count)";
            }
            $list[$row->id] = $name;
        }
        
        return $list;         
    }
    
    public static function urlArgs($request) {
        $url_args = Str::urlArgs($request) + [
                    'search_id' => (int)$request->input('search_id'),
                    'search_name' => $request->input('search_name'),
                    'search_region' => (int)$request->input('search_region'),
                ];
        if (!$url_args['search_id']) {
            $url_args['search_id'] = NULL;
        }                
        return $url_args;
    }
    
    public static function search(Array $url_args) {
        $locale = LaravelLocalization::getCurrentLocale();
        $builder = self::orderBy('name_'.$locale);
        $builder = self::searchById($builder, $url_args['search_id']);
        $builder = self::searchByName($builder, $url_args['search_name']);
        $builder = self::searchByRegion($builder, $url_args['search_region']);
        return $builder;
    }
    
    public static function searchByName($builder, $name) {
        if (!$name) {
            return $builder;
        }
        return $builder->where(function($q) use ($name){
                            $q->where('name_en','like', $name)
                              ->orWhere('name_ru','like', $name);
                    });
    }
    
    public static function searchByRegion($builder, $region) {
        if (!$region) {
            return $builder;
        }
        return $builder->where('region_id',$region);
    }
    
    public static function searchById($builder, $id) {
        if (!$id) {
            return $builder;
        }
        return $builder->where('id',$id);
    }
}
