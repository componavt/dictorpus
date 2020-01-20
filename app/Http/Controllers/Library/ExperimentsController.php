<?php

namespace App\Http\Controllers\Library;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Storage;
use Carbon\Carbon;

use App\Library\Experiment;

use App\Models\Dict\Gramset;
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
    public function __construct()
    {
        $this->middleware('auth:admin,/');
    }

    public function index() {
        $langs = []; 
        foreach ([1,4] as $l) {
            $langs[$l] = Lang::getNameById($l);
        }
        
        return view('experiments/index', compact('langs'));
    }    
    /**
     * Fill data [wordform, pos ID] in table search_pos
     * 
     * @return \Illuminate\Http\Response
     */
    public function fillSearchPos(Request $request) {
        $search_lang =  $request->input('search_lang');
        $table_name = 'search_pos';
        $count = 0;
        
//        DB::table('search_pos')->all()->delete();
  //      DB::statement('ALTER TABLE search_pos AUTO_INCREMENT = 1');
        
        $lemmas = Lemma::whereLangId($search_lang)
                       ->where(DB::raw("length(lemma)"), ">", 2)
                       ->where('lemma', 'not like', '% %')
                       ->where('lemma','not like', '-%')
                       ->whereNotNull('pos_id')
                       ->groupBy('lemma','pos_id')
                       ->orderBy('lemma')->get();
        
        foreach ($lemmas as $lemma) {
print "<P>lemma: ".$lemma->lemma.', '.$lemma->pos_id;
            $lemma_writed = Experiment::writePosGramset($table_name, 'pos_id', $search_lang, $lemma->lemma, $lemma->pos_id);
            $count += $lemma_writed;
print ", $lemma_writed, $count</p>";
        }
        
// select wordform, pos_id from wordforms, lemma_wordform, lemmas where wordforms.id=lemma_wordform.wordform_id and lemmas.id=lemma_wordform.lemma_id and wordform not like '% %' and pos_id is not null and gramset_id is not null and lang_id=1 group by wordform, pos_id;
        $start = 0;
        $limit = 1000;
        $wordfoms_exists=true;
        while ($wordfoms_exists) {
            $wordforms = Wordform::join('lemma_wordform', 'wordforms.id', '=', 'lemma_wordform.wordform_id')
                        ->join('lemmas', 'lemmas.id', '=', 'lemma_wordform.lemma_id')
                        ->where('wordform','not like', '% %')
                        ->where('wordform','not like', '-%')
                        ->where(DB::raw("length(wordform)"), ">", 2)
                        ->whereNotNull('pos_id')
                        ->whereNotNull('gramset_id')
                        ->whereLangId($search_lang)
                        ->groupBy('wordform','pos_id')
                        ->skip($start)
                        ->take($limit)
                        ->get();
                    //->count();
    //dd($wordforms);   
            if (!sizeof($wordforms)) {
                $wordfoms_exists = false;
            } else {
                foreach ($wordforms as $wordform) {
        print "<P>wordform: ".$wordform->wordform.', '.$wordform->pos_id;
                    $wordform_writed = Experiment::writePosGramset($table_name, 'pos_id', $search_lang, $wordform->wordform, $wordform->pos_id);
                    $count += $wordform_writed;
    print ", $wordform_writed, $count</p>";
                }
                $start +=$limit;
            }
        }
print '$count records are writed.';        
    }
    
    /**
     * Fill data [wordform, gramset ID] in table search_gramset
     * 
     * @return \Illuminate\Http\Response
     */
    public function fillSearchGramset(Request $request) {
        $search_lang =  $request->input('search_lang');
        $table_name = 'search_gramset';
        $count = 0;
        
//        DB::table('search_gramset')->all()->delete();
  //      DB::statement('ALTER TABLE search_gramset AUTO_INCREMENT = 1');
        
        $start = 0;
        $limit = 1000;
        $wordfoms_exists=true;
        while ($wordfoms_exists) {
// select wordform, gramset_id from wordforms, lemma_wordform, lemmas where wordforms.id=lemma_wordform.wordform_id and lemmas.id=lemma_wordform.lemma_id and wordform not like '% %' and pos_id is not null and gramset_id is not null and lang_id=1 group by wordform, gramset_id;
            $wordforms = Wordform::join('lemma_wordform', 'wordforms.id', '=', 'lemma_wordform.wordform_id')
                        ->join('lemmas', 'lemmas.id', '=', 'lemma_wordform.lemma_id')
                        ->where(DB::raw("length(wordform)"), ">", 2)
                        ->where('wordform','not like', '% %')
                        ->whereNotNull('gramset_id')
                        ->whereLangId($search_lang)
                        ->groupBy('wordform','gramset_id')
                        ->skip($start)
                        ->take($limit)
                        ->get();
        
            if (!sizeof($wordforms)) {
                $wordfoms_exists = false;
            } else {
                foreach ($wordforms as $wordform) {
print "<P>wordform: ".$wordform->wordform.', '.$wordform->gramset_id;
                    $wordform_writed = Experiment::writePosGramset($table_name, 'gramset_id', $search_lang, $wordform->wordform, $wordform->gramset_id);
                    $count += $wordform_writed;
print ", $wordform_writed, $count</p>";
                }
                $start +=$limit;
            }
        }
print '$count records are created.';        
    }
    
    /**
     * select eval_end, count(*) from search_gramset where eval_end is not null group by eval_end order by eval_end;
     * select ROUND(eval_end,1) as eval1, count(*) from search_gramset where eval_end is not null group by eval1 order by eval1;     * 
     */
    public function evaluateSearchPosGramset(Request $request) {
        $search_lang =  $request->input('search_lang');
        $property = $request->input('property');
        $is_all_checked = false;
        $table_name = 'search_'.$property;
        while (!$is_all_checked) {
            $wordforms = DB::table($table_name)
                           ->select('wordform')
                           ->whereLangId($search_lang)
                           ->whereNull('eval_end')
                           ->groupBy('wordform')
                           ->take(100)
                           ->get();
                      //->first();
            if ($wordforms) {
                foreach ($wordforms as $wordform) {
                    Experiment::evaluateSearchPosGramset($search_lang, $table_name, $property, $wordform);
                }
            } else {
                $is_all_checked = true;
            }
        }
    }
    
    public function evaluateSearchGramsetByAffix(Request $request) {
        $search_lang =  $request->input('search_lang');
        $is_all_checked = false;
        
        while (!$is_all_checked) {
            $wordform = DB::table('search_gramset')
                      ->whereLangId($search_lang)
                      ->whereNull('eval_aff')
//->where('wordform', 'like','toizih')                    
                      ->first();
            if ($wordform) {
print "<p><b>".$wordform->wordform."</b>";   
                list($affix,$list) = Experiment::searchGramsetByAffix($wordform->wordform, $search_lang);     
                if (!$list) {
                    DB::statement("UPDATE search_gramset SET affix=NULL,"
                                 ." eval_aff=0, eval_aff_gen=0"
                                 ." where wordform like '".$wordform->wordform."' and lang_id=".$search_lang);
                    
                } else {
print "<br>COUNTS: ";                
foreach ($list as $p=>$c) {
    print "<b>$p</b>: $c, ";
}   
                    $wordforms = DB::table('search_gramset')
                               ->whereLangId($search_lang)
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

    public function exportErrorShift(Request $request) {
        $search_lang =  $request->input('search_lang');
        $property = $request->input('property');
        $property_id = $property.'_id';
        $table_name = 'search_'.$property;
        
        $dir_name = "export/error_shift/";
        $filename = $dir_name.$property.'-'.$search_lang.'.txt';
        Storage::disk('public')->put($filename, ''); 
        $shift_list = Experiment::createShiftErrors($search_lang, $table_name, $property_id);
        foreach ($shift_list as $p1 =>$p_info) {
            foreach ($p_info as $p2 => $count) {
                Storage::disk('public')->append($filename, "$p1\t$p2\t$count");
            }
        }
print 'done.';        
}
    
    public function exportErrorShiftToDot(Request $request) {
        $search_lang =  $request->input('search_lang');
        $property = $request->input('property');
        $property_id = $property.'_id';
        $table_name = 'search_'.$property;
        $color_names = ['darkgreen', 'darkgoldenrod3', 'brown', 'aquamarine3', 'darkorange2', 'crimson', 'indigo', 'navyblue', 'mistyrose3', 'peru'];
        $limit_color = 10;
        $limit_dotted = 10;
        if ($property == 'pos') {
            $p_names = PartOfSpeech::getList();
            $range = range(0,20,10);
        } else {
            $p_names = Gramset::getList(0);
            $range = range(0,10,1);
        }
        
        $dir_name = "export/error_shift/";
        foreach($range as $min_limit) {
            $file_with_data = $dir_name.$property.'-'.$search_lang.'.txt';
            list($node_list, $edge_list) = Experiment::readShiftErrorsForDot($search_lang, $file_with_data, $table_name, $property_id, $min_limit, $p_names);
//dd($node_list, $edge_list);            
            $filename = $dir_name.$property.'-'.$search_lang.'_'.$min_limit.'.dot';
            Storage::disk('public')->put($filename, "digraph G {\n");
//                    "edge[colorscheme=accent8]\n"); 
            $colors = $p_total = [];
            $count_color = 0;
            foreach ($node_list as $node=>$label) {
                $line = "$node\t[label=\"".$label.'"';
                if ($count_color < 3) {
                    $line .=", peripheries=2";                    
                }
                if ($count_color < sizeof($color_names)) {
                    $colors[$node] = $color_names[$count_color++];
                    $line .=", color=".$colors[$node];                    
                }
                $line .= '];';
                Storage::disk('public')->append($filename, $line);
            }
            Storage::disk('public')->append($filename, '');
            foreach ($edge_list as $p1 =>$p_info) {
                foreach ($p_info as $p2 => $weight) {
                    $line = "$p1 -> $p2\t[label=\"$weight %\", weight=$weight";
                    if ($weight>$limit_color && isset($colors[$p1]))  {
                        $line .=", color=".$colors[$p1];
                    } elseif($weight<$limit_dotted) {
                        $line .=", style=dotted";
                    }
                    $line .="];";
                    Storage::disk('public')->append($filename, $line);
                }
                Storage::disk('public')->append($filename, '');
            }
            Storage::disk('public')->append($filename, "}"); 
        }
print 'done.';        
}
    
    /**
     * select ROUND(eval_end,1) as eval1, count(*) from search_pos where eval_end is not null group by eval1 order by eval1;
     * 
     * Связь частей речи и длин конечных буквосочетаний
     * select pos_id, length(ending) as len, count(*) as count from search_pos where ending is not null group by pos_id, len order by count DESC;
     * 
     * Части речи и ошибки
     * select pos_id, count(*) as count from search_pos where ending is not null group by pos_id order by count DESC;
     * select pos_id, count(*) as count from search_pos where ending is not null AND eval_end_gen=0 group by pos_id order by count DESC;
     * 
     */
    public function resultsSearchPos(Request $request) {
        $search_lang =  $request->input('search_lang');
        $search_lang_name = Lang::getNameById($search_lang);
        $property = 'pos';
        $table_name = 'search_'.$property;
        $p_names = PartOfSpeech::getList();
        
        $results[0] = Experiment::resultsSearch($search_lang, $table_name);
        
        $results[2] = Experiment::lenEndDistribution($search_lang, $table_name, 'pos_id', $p_names);
        
        $dir_name = "export/error_shift/";
        $filename = $dir_name.$property.'-'.$search_lang.'.txt';
        $results[3]['list'] = Experiment::readShiftErrors($filename, $p_names);
        $results[3]['limit'] = 6;
        
        return view('experiments.results_search',
                    compact('search_lang_name', 'property', 'results'));
    }
    
    /**
     * select ROUND(eval_end,1) as eval1, count(*) from search_pos where eval_end is not null group by eval1 order by eval1;
     */
    public function resultsSearchGramset(Request $request) {
        $search_lang =  $request->input('search_lang');
        $search_lang_name = Lang::getNameById($search_lang);
        $property = 'gramset';
        $table_name = 'search_'.$property;
        $p_names = Gramset::getList(0);
        
        $results[0] = Experiment::resultsSearch($search_lang, $table_name);
        $results[1] = Experiment::resultsSearch($search_lang, $table_name, 'eval_aff');
            
        $results[2] = Experiment::lenEndDistribution($table_name, 'gramset_id', $p_names);
        
        $dir_name = "export/error_shift/";
        $filename = $dir_name.$property.'-'.$search_lang.'.txt';
        $results[3]['list'] = Experiment::readShiftErrors($filename, $p_names);
        $results[3]['limit'] = 9;
        
        return view('experiments.results_search',
                    compact('search_lang_name', 'property', 'results'));
    }
    
}
