<?php

namespace App\Http\Controllers\Library;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Library\Experiment;

use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;
use App\Models\Dict\Wordform;

class ExperimentsController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('auth:admin,/');
    }
    
    /**
     * Fill data [wordform, pos ID] in table search_pos
     * 
     * @return \Illuminate\Http\Response
     */
    public function fillSearchPos() {
        $search_lang = 4;
        $pairs = [];
        
//        DB::table('search_pos')->all()->delete();
  //      DB::statement('ALTER TABLE search_pos AUTO_INCREMENT = 1');
        $lemmas = Lemma::whereLangId($search_lang)->get();
        
        foreach ($lemmas as $lemma) {
            if (strlen($lemma->lemma)>1 && !preg_match("/\s/", $lemma->lemma)) {
                if ($lemma->pos_id == 14) {
                    $pos_id = 5;
                } else {
                    $pos_id = $lemma->pos_id;
                }
                $pairs[$lemma->lemma.'_'.$pos_id] = [$lemma->lemma,$pos_id];
                foreach ($lemma->wordforms as $wordform) {
                    if (strlen($wordform->wordform)>1 && !preg_match("/\s/", $wordform->wordform)) { // without analytic forms
                        $pairs[$wordform->wordform.'_'.$pos_id] = [$wordform->wordform,$pos_id];                
                    }
                }
            }
        }
        ksort($pairs);
//dd($pairs);   
        foreach ($pairs as $k=>$info) {
            DB::table('search_pos')->insert([
                'wordform' => $info[0],
                'pos_id'=> $info[1]
            ]);
        }
print sizeof($pairs).' records are created.';        
    }
    
    /**
     * Fill data [wordform, gramset ID] in table search_gramset
     * 
     * @return \Illuminate\Http\Response
     */
    public function fillSearchGramset() {
        $search_lang = 4;
        $pairs = [];
        
//        DB::table('search_gramset')->all()->delete();
  //      DB::statement('ALTER TABLE search_gramset AUTO_INCREMENT = 1');
        
        $lemmas = Lemma::whereLangId($search_lang)->get();
        $wordforms = Wordform::join('lemma_wordform', 'wordforms.id', '=', 'lemma_wordform.wordform_id')
                    ->join('lemmas', 'lemmas.id', '=', 'lemma_wordform.lemma_id')
                    ->where('wordform','not like', '% %')
                    ->whereNotNull('gramset_id')
                    ->whereLangId($search_lang)
                    ->groupBy('wordform','gramset_id')
                    ->get();
        
        foreach ($wordforms as $wordform) {
            if (!preg_match("/\s/", $wordform->wordform)) { // without analytic forms
                $pairs[$wordform->wordform.'_'.$wordform->gramset_id] = [$wordform->wordform,$wordform->gramset_id];                
            }
        }
        ksort($pairs);
//dd($pairs);   
        foreach ($pairs as $k=>$info) {
            DB::table('search_gramset')->insert([
                'wordform' => $info[0],
                'gramset_id'=> $info[1]
            ]);
        }
print sizeof($pairs).' records are created.';        
    }
    
    /**
     * select eval_end, count(*) from search_pos where eval_end is not null group by eval_end order by eval_end;
     * select ROUND(eval_end,1) as eval1, count(*) from search_pos where eval_end is not null group by eval1 order by eval1;     * 
     */
/*    
    public function evaluateSearchPos() {
        $is_all_checked = false;
        while (!$is_all_checked) {
            $wordform = DB::table('search_pos')
                      ->whereNull('eval_end')
//->where('wordform', 'like','toizih')                    
                      ->first();
            if ($wordform) {
print "<p><b>".$wordform->wordform."</b>";   
                list($ending,$pos_list) = Experiment::searchPosByWord($wordform->wordform);     
                if (!$pos_list) {
                    DB::statement("UPDATE search_pos SET ending=NULL"
                                 .", eval_end=0, eval_end_gen=0"
                                 ." where wordform like '".$wordform->wordform."'");
                    
                } else {
print "<br>COUNTS: ";                
foreach ($pos_list as $p=>$c) {
    print "<b>$p</b>: $c, ";
}   
                    $wordforms = DB::table('search_pos')
                               ->where('wordform', 'like', $wordform->wordform)
                               ->get();
                    $eval_ends = [];
                    foreach ($wordforms as $w) { 
                        $eval_ends[$w->id] = Experiment::getEvalForOneValue($pos_list, $w->pos_id);
    print "<br>".$w->pos_id.": ". $eval_ends[$w->id];
                    }
                    $max = max($eval_ends);
    print "<br><b>max:</b> ".$max;  
    //dd($eval_ends);
    //exit(0);
                    foreach ($eval_ends as $w_id=>$eval_end) {
                        DB::statement("UPDATE search_pos SET ending='".$ending
                                     ."', eval_end=$eval_end, eval_end_gen=$max"
                                     ." where id=".$w_id);
                    }
                }
            } else {
                $is_all_checked = true;
            }
        }
    }
*/    
    /**
     * select eval_end, count(*) from search_gramset where eval_end is not null group by eval_end order by eval_end;
     * select ROUND(eval_end,1) as eval1, count(*) from search_gramset where eval_end is not null group by eval1 order by eval1;     * 
     */
    public function evaluateSearchPosGramset(Request $request) {
        $property = $request->input('property');
        $is_all_checked = false;
        $property_id = $property.'_id';
        $table_name = 'search_'.$property;
        while (!$is_all_checked) {
            $wordform = DB::table($table_name)
                      ->whereNull('eval_end')
//->where('wordform', 'like','toizih')                    
                      ->first();
            if ($wordform) {
print "<p><b>".$wordform->wordform."</b>";   
                list($ending,$list) = Experiment::searchPosGramsetByWord($wordform->wordform, $property);     
                if (!$list) {
                    DB::statement("UPDATE $table_name SET ending=NULL"
                                 .", eval_end=0, eval_end_gen=0"
                                 ." where wordform like '".$wordform->wordform."'");
                    
                } else {
print "<br>COUNTS: ";                
foreach ($list as $p=>$c) {
    print "<b>$p</b>: $c, ";
}   
                    $wordforms = DB::table($table_name)
                               ->where('wordform', 'like', $wordform->wordform)
                               ->get();
                    $eval_ends = [];
                    foreach ($wordforms as $w) { 
                        $eval_ends[$w->id] = Experiment::getEvalForOneValue($list, $w->{$property_id});
    print "<br>".$w->{$property_id}.": ". $eval_ends[$w->id];
                    }
                    $max = max($eval_ends);
    print "<br><b>max:</b> ".$max;  
    //dd($eval_ends);
//    exit(0);
                    foreach ($eval_ends as $w_id=>$eval_end) {
                        DB::statement("UPDATE $table_name SET ending='".$ending
                                     ."', eval_end=$eval_end, eval_end_gen=$max"
                                     ." where id=".$w_id);
                    }
                }
            } else {
                $is_all_checked = true;
            }
        }
    }
    
    /**
     * select ROUND(eval_end,1) as eval1, count(*) from search_pos where eval_end is not null group by eval1 order by eval1;
     */
    public function resultsSearch(Request $request) {
        $search_lang = 4;
        $search_lang_name = Lang::getNameById($search_lang);
        $property = $request->input('property');
        $table_name = 'search_'.$property;
        
        $results[0] = Experiment::resultsSearch($table_name);
//        $results[1] = Experiment::resultsSearchPos($table_name);
        
        return view('experiments.results_search',
                    compact('search_lang_name', 'property', 'results'));
    }
    
    public function evaluateSearchGramsetByAffix() {
        $search_lang = 4;
        $is_all_checked = false;
        
        while (!$is_all_checked) {
            $wordform = DB::table('search_gramset')
                      ->whereNull('eval_aff')
->where('wordform', 'like','toizih')                    
                      ->first();
            if ($wordform) {
print "<p><b>".$wordform->wordform."</b>";   
                list($affix,$list) = Experiment::searchGramsetByAffix($wordform->wordform, $search_lang);     
                if (!$list) {
                    DB::statement("UPDATE search_gramset SET affix=NULL,"
                                 ." eval_aff=0, eval_aff_gen=0"
                                 ." where wordform like '".$wordform->wordform."'");
                    
                } else {
print "<br>COUNTS: ";                
foreach ($list as $p=>$c) {
    print "<b>$p</b>: $c, ";
}   
                    $wordforms = DB::table('search_gramset')
                               ->where('wordform', 'like', $wordform->wordform)
                               ->get();
                    $eval_affs = [];
                    foreach ($wordforms as $w) { 
                        $eval_affs[$w->id] = Experiment::getEvalForOneValue($list, $w->gramset_id);
    print "<br>".$w->gramset_id.": ". $eval_affs[$w->id];
                    }
                    $max = max($eval_affs);
    print "<br><b>max:</b> ".$max;  
    //dd($eval_affs);
//    exit(0);
                    foreach ($eval_affs as $w_id=>$eval_aff) {
                        DB::statement("UPDATE search_gramset SET affix='$affix',"
                                     ." eval_aff=$eval_aff, eval_aff_gen=$max"
                                     ." where id=".$w_id);
                    }
                }
            } else {
                $is_all_checked = true;
            }
        }
    }
        
}
