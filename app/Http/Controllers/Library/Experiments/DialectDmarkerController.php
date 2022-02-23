<?php

namespace App\Http\Controllers\Library\Experiments;

use Illuminate\Http\Request;

//use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Library\Experiments\DialectDmarker;
use App\Library\Experiments\Dmarker;
use App\Library\Experiments\Mvariant;

use App\Models\Dict\Dialect;

class DialectDmarkerController extends Controller
{
    public function __construct(Request $request)
    {
        // permission= corpus.edit, redirect failed users to /corpus/text/, authorized actions list:
        $this->middleware('auth:corpus.edit,/', 
                         ['only' => ['calculate']]);
    }
    /**
     * /experiments/dialect_dmarker/
     * 
     */
    public function index() {
        $gr_dialects = $dialects = [];
        foreach (DialectDmarker::dialectListByGroups() as $gr_name => $dialect_grs) {
            $gr_dialects[$gr_name] = sizeof($dialect_grs);
            foreach ($dialect_grs as $dialect_id) {
                $dialect = Dialect::find($dialect_id);
                $dialects[$dialect_id] = [
                    'name' => $dialect->name,
                    'text_total' => sizeof($dialect->texts),
                    'word_total' => $dialect->totalWords()
                ];
            }
        }                    
        $dmarkers = Dmarker::orderBy('id')->get();
        
        return view('experiments.dialect_dmarker.index', 
                compact('dialects', 'dmarkers', 'gr_dialects'));
    }
    
    public function words() {
        $gr_dialects = $dialects = [];
        foreach (DialectDmarker::dialectListByGroups() as $gr_name => $dialect_grs) {
            $gr_dialects[$gr_name] = sizeof($dialect_grs);
            foreach ($dialect_grs as $dialect_id) {
                $dialects[$dialect_id] = Dialect::getNameByID($dialect_id);
            }
        }                    
        $dmarkers = Dmarker::where('id', '<', 3)
                           ->orderBy('id')->get();
        
        return view('experiments.dialect_dmarker.words', 
                compact('dialects', 'dmarkers', 'gr_dialects'));
    }
    
    public function calculate() {
        $mvariants = Mvariant::orderBy('id')
//                     ->where('dmarker_id', '>', 5)
//                     ->where('dmarker_id', '<', 6)
/*                     ->whereIn('dmarker_id', function ($q) {
                         $q->select('id')->from('dmarkers')
                           ->where('absence', 0);
                     })*/
                     ->get();
        
        $dialects = Dialect::whereIn('lang_id', [4,5,6])
                    ->whereIn('id', function ($query) {
                        $query->select('dialect_id')
                        ->from('dialect_text');
                    })->get();
                    
        foreach ($mvariants as $mvariant) {
print "<p>". $mvariant->dmarker_id. ". ". $mvariant->name. "</p>";          
            foreach ($dialects as $dialect) {
                $mvariant->calculateFrequency($dialect);
//exit(0);                
            }
        }
print 'done.';        
    }
}
