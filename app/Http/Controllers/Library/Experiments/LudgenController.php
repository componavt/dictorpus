<?php

namespace App\Http\Controllers\Library\Experiments;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
//use App\Http\Requests;
use App\Http\Controllers\Controller;
use Storage;

//use App\Charts\DistributionChart;

use App\Library\Experiments\Ludgen;

use App\Models\Dict\Gramset;

class LudgenController extends Controller
{
    public function __construct(Request $request)
    {
        // permission= corpus.edit, redirect failed users to /corpus/text/, authorized actions list:
        $this->middleware('auth:corpus.edit,/', 
                         ['only' => ['calculate']]);
    }

    public function index() {
        return view('experiments.ludgen.index');
    }
    
    public function words(Request $request) {
        $what = $request->what;
        if ($what == 'verbs') {
            $words = Ludgen::getVerbs();
            $pos_id = 11;
        } else {
            $words = Ludgen::getNames();
            $pos_id = 5;
        }
        
        $gramsets = Gramset::getGroupedList($pos_id, Ludgen::lang_id);
        
        $lemmas = Ludgen::groupedLemmas($words, $gramsets);

        return view('experiments.ludgen.words',
                compact('gramsets', 'lemmas', 'what'));
    }
    
    public function affixes(Request $request) {
        $what = $request->what;
        if ($what == 'verbs') {
            $lemmas = array_keys(Ludgen::getVerbs());
            $pos_id = 11;
        } else {
            $lemmas = array_keys(Ludgen::getNames());
            $pos_id = 5;
        }
        
        $gramsets = Gramset::getGroupedList($pos_id, Ludgen::lang_id);
        
        $affixes = Ludgen::getAffixes($lemmas, $gramsets, $what);
//dd($affixes[array_key_first($affixes)]);
        $cols = array_keys($affixes[array_key_first($affixes)]);
        
        return view('experiments.ludgen.affixes',
                compact('affixes', 'cols', 'gramsets', 'what'));
    }
    
}