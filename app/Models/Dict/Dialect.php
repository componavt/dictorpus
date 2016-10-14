<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

class Dialect extends Model
{
    public $timestamps = false;
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }
    
    // Dialect __belongs_to__ Lang
    public function lang()
    {
        return $this->belongsTo(Lang::class);
    }    

    /** Gets name of this dialect, takes into account locale.
     * 
     * @return String
     */
    public function getNameAttribute() : String
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $column = "name_" . $locale;
        return $this->{$column};
    }
    
    /** Gets list of dialects
     * 
     * @return Array [1=>'Northern Veps',..]
     */
    public static function getList()
    {     
        $locale = LaravelLocalization::getCurrentLocale();
        
        $dialects = self::orderBy('name_'.$locale)->get();
        
        $list = array();
        foreach ($dialects as $row) {
            $list[$row->id] = $row->name;
        }
        
        return $list;         
    }
}
