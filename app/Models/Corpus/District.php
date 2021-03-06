<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

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

    /** Gets name of this corpus, takes into account locale.
     * 
     * @return String
     */
    public function getNameAttribute() : String
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $column = "name_" . $locale;
        $name = $this->{$column};
        
        if (!$name && $locale!='ru') {
            $name = $this->name_ru;
        }
        
        return $name;
    }
    
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
}
