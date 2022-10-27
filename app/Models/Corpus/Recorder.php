<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

use App\Library\Str;

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
    
    // Methods
    use \App\Traits\Methods\getNameAttribute;
    
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
    
    public static function search(Array $url_args) {
        $locale = LaravelLocalization::getCurrentLocale();
        $recorders = self::orderBy('name_'.$locale);  
        
        $recorders = self::searchByName($recorders, $url_args['search_name']);

        if ($url_args['search_id']) {
            $recorders = $recorders->where('id',$url_args['search_id']);
        } 
        return $recorders;
    }
    
    public static function searchByName($recorders, $name) {
        if (!$name) {
            return $recorders;
        }
        return $recorders->where(function($q) use ($name){
                        $q->where('name_en','like', $name)
                          ->orWhere('name_ru','like', $name);
                });
    }

    public static function urlArgs($request) {
        $url_args = Str::urlArgs($request) + [
                    'search_id'  => (int)$request->input('search_id'),
                    'search_name'   => $request->input('search_name'),
                ];
        
        $url_args['search_id'] = $url_args['search_id'] ? $url_args['search_id'] : NULL;
        
        return $url_args;
    }    
}
