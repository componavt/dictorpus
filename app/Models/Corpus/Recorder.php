<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

class Recorder extends Model
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
    
    // Recorder __has_many_through__ Texts
    public function texts()
    {
        $recorder_id = $this->id;
//        return $this->hasManyThrough(Text::class, Event::class);
        $texts = Text::whereIn('event_id', function($query) use ($recorder_id) {
                                $query->select('event_id')->from('event_recorder')
                                      ->where('recorder_id',$recorder_id);
                              });
        return $texts;
    }
    
    /** Gets list of recorders
     * 
     * @return Array [1=>'Онегина Нина Федоровна',..]
     */
    public static function getList()
    {     
        $locale = LaravelLocalization::getCurrentLocale();
        
        $recorders = self::orderBy('name_'.$locale)->get();
        
        $list = array();
        foreach ($recorders as $row) {
            $list[$row->id] = $row->name;
        }
        
        return $list;         
    }
    
}
