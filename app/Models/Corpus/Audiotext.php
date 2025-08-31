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
    public static function getAllFiles($without_text=null, $only_free=false) {
        $files = Storage::disk(self::DISK)->files();
        if ($only_free) {
            $audiotexts = self::pluck('filename')->toArray();
            $files = array_diff($files, $audiotexts);
        } elseif ($without_text) {
            $audiotexts = self::whereTextId($without_text)
                              ->pluck('filename')->toArray();
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
/*             $texts = Text::whereNotNull('id')
                         ->WithAudio()
                         ->where(function ($q) {
                             $q->where('created_at', '>', '2024-01-01 00:00:00')
                               ->where('created_at', '<', '2025-01-01 00:00:00');
                         })
                         ->dialectTexts()->get();    
dd($texts);             
*/       
        $place_coll = Place::whereNotNull('id')->withDialectAudio()
                           ->withCoords()->orderBy('name_ru')->get();
//dd(to_sql($place_coll));        
//dd($place_coll->pluck('name_ru')->toArray());                       
        foreach ($place_coll as $place) {
            $place_id = $place->id;
            $texts = Text::whereNotNull('id')
                         ->InformantBirthPlace($place_id)
                         ->WithAudio()
                         ->dialectTexts()->get();                                       

            $popup = '<b>'.preg_replace("/['']/", "&#39;", $place->name).'</b>';
            foreach ($texts as $text) {
                $audiotext = $text->audiotexts[0];
                $popup .= '<br><a href="'.LaravelLocalization::localizeURL('/corpus/text/'.$text->id)
                        . '">'.preg_replace("/['']/", "&#39;", $text->title).'</a> ('.$text->dialectsToString()
                        . ($text->event && $text->event->date ? ', '.$text->event->date : '') 
                        .')<br><audio controls><source src="'.$audiotext->url()
                        .'" type="audio/mpeg"></audio>';
                if (sizeof($text->getMedia())) {
                    $popup .= '<div>';
                    foreach($text->getMedia() as $photo) {
                        $popup .= '<img src="'. $photo->getUrl('thumb') .'" width="100">';
                    }
                    $popup .= '</div>';
                }
            }
            $places[]=[
                'latitude'=>$place->latitude,
                'longitude'=>$place->longitude,
                'color'=>$colors[$text->lang_id],
                'popup'=>$popup
            ];
        }
//dd($places);        
        return $places;
    }
}
