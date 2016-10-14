<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

class Genre extends Model
{
    public $timestamps = false;
    
    protected $fillable = ['name_en','name_ru'];
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }

    /** Gets name of this genre, takes into account locale.
     * 
     * @return String
     */
    public function getNameAttribute() : String
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $column = "name_" . $locale;
        return $this->{$column};
    }
    
    // Genre __has_many__ Texts
    public function texts(){
        return $this->belongsToMany(Text::class,'genre_text');
    }
    
    /** Gets list of genres
     * 
     * @return Array [1=>'Bridal laments',..]
     */
    public static function getList()
    {     
        $locale = LaravelLocalization::getCurrentLocale();
        
        $genres = self::orderBy('name_'.$locale)->get();
        
        $list = array();
        foreach ($genres as $row) {
            $list[$row->id] = $row->name;
        }
        
        return $list;         
    }
    
}
