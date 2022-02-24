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
        $output = 'frequency';
        list($dialects, $dmarkers, $gr_dialects) = DialectDmarker::init();
        
        return view('experiments.dialect_dmarker.index', 
                compact('dialects', 'dmarkers', 'gr_dialects', 'output'));
    }
    
    public function fractions() {
        $output = 'fraction';
        list($dialects, $dmarkers, $gr_dialects) = DialectDmarker::init();
        
        return view('experiments.dialect_dmarker.index', 
                compact('dialects', 'dmarkers', 'gr_dialects', 'output'));
    }
    
    public function words() {
        $output = 'words';
        list($dialects, $dmarkers, $gr_dialects) = DialectDmarker::init();
        
        return view('experiments.dialect_dmarker.index', 
                compact('dialects', 'dmarkers', 'gr_dialects', 'output'));
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
                $mvariant->calculateFrequencyAndFraction($dialect);
//exit(0);                
            }
        }
print 'done.';        
    }
}
