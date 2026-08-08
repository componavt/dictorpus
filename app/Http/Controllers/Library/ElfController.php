<?php

namespace App\Http\Controllers\Library;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Corpus\Place;
use App\Models\Corpus\Text;

class ElfController extends Controller
{
    public $url_args = [];
    public $args_by_get = '';

    public function __construct(Request $request)
    {
        // permission= corpus.edit, redirect failed users to /service/index, authorized actions list:
        $this->middleware(
            'auth:corpus.edit,/service/index',
            ['only' => ['textsForMap']]
        );
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
            $regions[$place->region->name][$place->district->name][$place->id] = $place->name;
        }
        //dd($regions);            
        return view(
            'service.elf.texts_for_map',
            compact('regions', 'text_places')
        );
    }

    public function bibleTexts() {}
}
