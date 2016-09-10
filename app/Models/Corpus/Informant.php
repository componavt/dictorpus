<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

use App\Models\Corpus\Place;

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
    
    /** Gets name of this informant, takes into account locale.
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
            $list[$row->id] = $row->informantString();
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
    public function informantString($lang_id='')
    {
        $info = [];
        
        if ($this->name) {
            $info[0] = $this->name;
        }
        
        if ($this->birth_date) {
            $info[] = $this->birth_date;
        }
        
        if ($this->birth_place) {
            $birth_place = Place::find($this->birth_place_id);
            $info[] = $birth_place->placeString();
        }
        
        return join(', ', $info);
    }    
}
