<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;
use Response;
use LaravelLocalization;

use App\Library\Map;

use App\Models\Corpus\Audiotext;
use App\Models\Corpus\Place;

use App\Models\Dict\Lang;

class AudiotextController extends Controller
{
    public function __construct(Request $request)
    {
        // permission= corpus.edit, redirect failed users to /corpus/text/, authorized actions list:
        $this->middleware('auth:corpus.edit,/corpus/text/', 
                         ['except' => ['onMap']]);
    }
    
    public function chooseFiles(int $text_id)
    {
        $audio_values = Audiotext::getAllFiles($text_id);
        return view('corpus.audiotext._choose_files',
                compact('audio_values'));
    }
    
    public function addFiles(int $text_id, Request $request) {
        $filenames = $request->input('filenames');
        foreach ($filenames as $filename) {
            Audiotext::create(['filename'=>$filename, 'text_id'=>$text_id]);            
        }
        
        $audiotexts = Audiotext::whereTextId($text_id)->get();        
        return view('corpus.audiotext._show_files',
                compact('audiotexts'));
    }
    
    public function show(int $id)
    {
        $audiotext = Audiotext::find($id);
        if (!$audiotext || !Storage::disk('audiotexts')->exists($audiotext->filename)) {
            abort(404);
        }
        $type = Storage::disk('audiotexts')->mimeType($audiotext->filename);

        $file = Storage::disk('audiotexts')->get($audiotext->filename);
        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }  
    
    public function onMap(Request $request) {
        $legend = Lang::legendForMap();
        $colors = Lang::MAP_COLORS;
        
        $place_coll = Place::whereNotNull('latitude')
                       ->whereNotNull('longitude')
                       ->whereIn('id', function ($query1) {
                            $query1->select('place_id')->from('events')
                                   ->whereIn('id', function ($query2) {
                                    $query2->select('event_id')->from('texts')
                                       ->whereIn('id', function ($query3) {
                                           $query3->select('text_id')->from('audiotexts');
                                       });
                                   });
                       })->get();
        $places = [];
        foreach ($place_coll as $place) {
            $texts = $place->texts_with_audio()->get();//$place->texts;
            foreach ($texts as $text) {
                $audiotext = $text->audiotexts[0];
                $popup = '<b>'.$place->name.'</b>';
                $popup .= '<br><a href="'.LaravelLocalization::localizeURL('/corpus/text/'.$text->id)
                        . '">'.$text->title.'</a> ('.$text->dialectsToString().')<br>'
                        . '<audio controls><source src="'.$audiotext->url().'" type="audio/mpeg"></audio>';
            }
            $places[]=[
                'latitude'=>$place->latitude,
                'longitude'=>$place->longitude,
                'color'=>$colors[$text->lang_id],
                'popup'=>$popup
            ];
        }
        return view('corpus.audiotext.map', 
                compact('legend', 'places')); 
    }
}
