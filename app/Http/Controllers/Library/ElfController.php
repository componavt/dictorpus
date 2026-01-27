<?php

namespace App\Http\Controllers\Library;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Corpus\Place;
use App\Models\Corpus\Text;

class ElfController extends Controller
{
    public $url_args=[];
    public $args_by_get='';
    
    public function __construct(Request $request)
    {
        // permission= corpus.edit, redirect failed users to /service/index, authorized actions list:
        $this->middleware('auth:corpus.edit,/service/index', 
                          ['only'=>['textsForMap']]);
        
/*        $this->url_args = url_args($request) + 
            [
                'by_alpha'  => (int)$request->input('by_alpha'),
                'search_concept_category'  => $request->input('search_concept_category'),
                'search_concept'  => (int)$request->input('search_concept'),
                'search_gram'    => $request->input('search_gram'),
                'search_lemma'    => $request->input('search_lemma'),
                'search_letter'    => $request->input('search_letter'),
                'search_meaning'    => $request->input('search_meaning'),
                'search_pos'    => $request->input('search_pos'),
                'search_word'    => $request->input('search_word'),
                'with_audios'    => (int)$request->input('with_audios'),
                'with_photos'    => (int)$request->input('with_photos'),
                'with_template'    => (int)$request->input('with_template'),
                'limit_num' => 5
            ];
        $this->url_args['limit_num'] = 5;
//dd($this->url_args['by_alpha']);        
        $this->args_by_get = search_values_by_URL($this->url_args); */
    }
    
    public function textsForMap()
    {
        $texts = Text::whereIn('id', function ($q) {
                $q->select('text_id')->from('plot_text')
                  ->where('plot_id', env('PLOT_CELEBRATION_ID'));
            })->orderBy('id')->get();
//dd($texts);            
        $text_places = [];
        foreach ($texts as $text) {
            foreach ($text->getCelebrationPlaces() as $cplace) {
                $text_places[$cplace][$text->id] = $text;
            }
        }
        $places = Place::whereIn('id', array_keys($text_places))->get();
        $regions = [];
        foreach ($places as $place) {
           $regions[$place->region->name][$place->district->name][$place->id]= $place->name; 
        }
//dd($regions);            
        return view('service.elf.texts_for_map',
                compact('regions', 'text_places'));
    }
}
