<?php

namespace App\Models\Corpus;

//use \Venturecraft\Revisionable\Revision;
use Illuminate\Database\Eloquent\Model;
use Storage;
use LaravelLocalization;

use App\Models\Dict\Lang;

class Audiotext extends Model
{
    const DISK = 'audiotexts';
    const DIR = 'audio/texts/';
    protected $fillable = ['text_id', 'filename'];
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.
    
    public static function boot()
    {
        parent::boot();
    }
    
    // Belongs To Relations
    use \App\Traits\Relations\BelongsTo\Text;
    
    /**
     * 
     * @return array with all file names in the disk directory 
     */
    public static function getAllFiles($without_text=null) {
        $files = Storage::disk(self::DISK)->files();
        if ($without_text) {
            $audiotexts = self::whereTextId($without_text)->pluck('filename')->toArray();
            $files = array_diff($files, $audiotexts);
        }
        return $files;
    }
    
    public function url() {
//        return route('audiotext.show', ['id'=>$this->id]);
//        return Storage::disk('audiotexts')->url($this->filename);
        return Storage::url(self::DIR . $this->filename);
    }
    
    public static function onMap() {
        $places = [];
        $colors = Lang::MAP_COLORS;
        
        $place_coll = Place::whereNotNull('latitude')
                       ->whereNotNull('longitude')
                       ->whereIn('id', function ($q1) {
                            $q1->select('birth_place_id')->from('informants')
                               ->whereIn('id', function ($q2) {
                                    $q2->select('informant_id')->from('event_informant')
                                    ->whereIn('event_id', function ($q3) {
                                        $q3->select('event_id')->from('texts')
                                           ->whereIn('id', function ($query3) {
                                               $query3->select('text_id')->from('audiotexts');
                                           });
                                    });
                               });
                       })->get();
        foreach ($place_coll as $place) {
            $texts = $place->texts_with_audio()->get();//$place->texts;
            $popup = '<b>'.$place->name.'</b>';
            foreach ($texts as $text) {
                $audiotext = $text->audiotexts[0];
                $popup .= '<br><a href="'.LaravelLocalization::localizeURL('/corpus/text/'.$text->id)
                        . '">'.$text->title.'</a> ('.$text->dialectsToString()
                        . ($text->event && $text->event->date ? ', '.$text->event->date : '') 
                        .')<br><audio controls><source src="'.$audiotext->url()
                        .'" type="audio/mpeg"></audio>';
            }
            $places[]=[
                'latitude'=>$place->latitude,
                'longitude'=>$place->longitude,
                'color'=>$colors[$text->lang_id],
                'popup'=>$popup
            ];
        }
        return $places;
    }
}
