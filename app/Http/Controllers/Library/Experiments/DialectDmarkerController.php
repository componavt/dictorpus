<?php

namespace App\Http\Controllers\Library\Experiments;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        list($dialects, $dmarkers, $gr_dialects) = DialectDmarker::init($output);
        
        return view('experiments.dialect_dmarker.index', 
                compact('dialects', 'dmarkers', 'gr_dialects', 'output'));
    }
    
    public function fractions() {
        $output = 'fraction';
        list($dialects, $dmarkers, $gr_dialects) = DialectDmarker::init($output);
        
        return view('experiments.dialect_dmarker.index', 
                compact('dialects', 'dmarkers', 'gr_dialects', 'output'));
    }
    
    public function words() {
        $output = 'words';
        list($dialects, $dmarkers, $gr_dialects) = DialectDmarker::init($output);
        
        return view('experiments.dialect_dmarker.index', 
                compact('dialects', 'dmarkers', 'gr_dialects', 'output'));
    }
    
    public function calculate() {
        DB::statement("DELETE FROM dialect_dmarker");
        $mvariants = Mvariant::orderBy('id')
//                     ->whereIn('id', [29,35,37,49])
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
                    
        foreach ($dialects as $dialect) {
            $total_texts = $dialect->totalTexts();
            $total_words = $dialect->totalWords();
print "<h3>". $dialect->name. ", $total_texts / $total_words</h3>";          
            foreach ($mvariants as $mvariant) {
print "<p>". $mvariant->dmarker_id. ". ". $mvariant->name. "</p>";          
                $mvariant->calculateFrequencyAndFraction($dialect->id, $total_texts, $total_words);
//exit(0);                
            }
        }
print 'done.';        
    }
    
    public function calculateСoalitions() {
        ini_set('max_execution_time', 100000);
//        ini_set('memory_limit', '512M');
                
        $win_coef = 0.75;
        $players_num = 20;
        $dialects = Dialect::whereIn('lang_id', [4,5,6])
                    ->whereIn('id', function ($query) {
                        $query->select('dialect_id')
                        ->from('dialect_text');
                    })->orderBy('id')
//                    ->take(1)
                    ->get();
        foreach ($dialects as $dialect) {
            DialectDmarker::createCoalitions($dialect->id, $win_coef, $players_num);        
        }
    }
    
    public function calculateSSindex() {
        $coalitions_num = 10;
        $players_num = 20;
        $dialects = DB::table('coalition_dialect')->groupBy('dialect_id')
                      ->orderBy('dialect_id')
                      ->take(1)
                      ->get();//pluck('dialect_id')->toArray();
        foreach ($dialects as $rec) {
            DialectDmarker::calculateSSindex($rec->dialect_id, $coalitions_num, $players_num);                
        }
    }
}
