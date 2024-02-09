<?php

namespace App\Http\Controllers\Library\Experiments;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
//use App\Http\Requests;
use App\Http\Controllers\Controller;
use Storage;

//use App\Charts\DistributionChart;

use App\Library\Experiments\Ludgen;

use App\Models\Dict\Lemma;

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
            $lemmas = Ludgen::getVerbs();
        } else {
            $lemmas = Ludgen::getNames();
        }
        
        foreach ($lemmas as $id => $w) {
            $lemmas[$id] = Lemma::find($id);
            $lemmas[$id]->reloadStemAffixByWordforms();        
            $lemmas[$id]->updateWordformAffixes(true);
        }
        
        $dialect_id = Ludgen::dialect_id;
        $gramsets = array_first($lemmas)->existGramsetsGrouped();
/*        
        $lemmas = [];
        $names = preg_split("/\s+/", Ludgen::getNames());
        foreach ($names as $w) {
            $ls = Lemma::whereLangId(Ludgen::lang_id)
                           ->where('lemma', 'like', $w);
//dd(to_sql($ls));            
            if ($ls->count()==0) {
                dd("Не найдена лемма ($w)");
            } elseif ($ls->count()>1) {
                dd('Найдены омонимы '.$w);
            } else {
                $l = $ls->first();
print $l->id." => '".$w."',<br>";                
//                $lemmas['names'][$l->id] = $w;
            }
        }
//dd($names);        
        $verbs = preg_split("/\s+/", Ludgen::getVerbs());
print "<p>verbs</p>";        
        foreach ($verbs as $w) {
            $ls = Lemma::whereLangId(Ludgen::lang_id)
                           ->where('lemma', 'like', $w);
//dd(to_sql($ls));            
            if ($ls->count()==0) {
                dd("Не найдена лемма ($w)");
            } elseif ($ls->count()>1) {
                dd('Найдены омонимы '.$w);
            } else {
                $l = $ls->first();
print $l->id." => '".$w."',<br>";                
//                $lemmas['verbs'][$l->id] = $w;
            }
        }
exit();  */      
        return view('experiments.ludgen.words',
                compact('dialect_id', 'gramsets', 'lemmas', 'what'));
    }
    
}
