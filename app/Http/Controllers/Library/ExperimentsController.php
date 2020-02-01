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
//        $this->middleware('auth:admin,/');
        $this->middleware('auth:dict.edit,/'); 
    }

    public function index() {
        $langs = []; 
        $totals = [];
        foreach ([1,4] as $l) {
            $langs[$l] = Lang::getNameById($l);
            $total_pos = Experiment::totalFill('search_pos',$l);
            $totals[$l]['total_in_pos'] = number_format($total_pos, 0, ',', ' ');
            $totals[$l]['eval_pos_compl_proc'] = round(Experiment::evaluationCompletedInProcents('search_pos',$l, $total_pos), 2);
            $totals[$l]['evals_pos_compl_proc'] = round(Experiment::evaluationCompletedInProcents('search_pos',$l, $total_pos, 'eval_ends'), 2);
            $totals[$l]['pos_win_proc'] = round(Experiment::evaluationCompletedInProcents('search_pos',$l, $total_pos, 'win_end'), 2);
            
            $total_gramset = Experiment::totalFill('search_gramset',$l);
            $totals[$l]['total_in_gramset'] = number_format($total_gramset, 0, ',', ' ');
            $totals[$l]['eval_gramset_compl_proc'] = round(Experiment::evaluationCompletedInProcents('search_gramset',$l, $total_gramset, 'eval_end'), 2);
            $totals[$l]['evals_gramset_compl_proc'] = round(Experiment::evaluationCompletedInProcents('search_gramset',$l, $total_gramset, 'eval_ends'), 2);
            $totals[$l]['eval_gramset_aff_compl_proc'] = round(Experiment::evaluationCompletedInProcents('search_gramset',$l, $total_gramset, 'eval_aff'), 2);
            $totals[$l]['eval_gramset_affs_compl_proc'] = round(Experiment::evaluationCompletedInProcents('search_gramset',$l, $total_gramset, 'eval_affs'), 2);
            $totals[$l]['gramset_win_end_proc'] = round(Experiment::evaluationCompletedInProcents('search_gramset',$l, $total_gramset, 'win_end'), 2);
            $totals[$l]['win_aff_proc'] = round(Experiment::evaluationCompletedInProcents('search_gramset',$l, $total_gramset, 'win_aff'), 2);
        }
        
        return view('experiments/index', compact('langs', 'totals'));
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
                       ->where('pos_id', '<>', 12) // вспомогательный глагол
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
                        ->where('pos_id', '<>', 12) // вспомогательный глагол
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
     * update search_gramset set eval_end=NULL, eval_end_gen=NULL, ending=NULL, win_end=NULL where lang_id=4;
     * select eval_end, count(*) from search_gramset where eval_end is not null group by eval_end order by eval_end;
     * select ROUND(eval_end,1) as eval1, count(*) from search_gramset where eval_end is not null group by eval1 order by eval1;     * 
     */
    public function evaluateSearchPosGramset(Request $request) {
        $search_lang =  $request->input('search_lang');
        $property = $request->input('property');
        $is_all = $request->input('all');
        $is_all_checked = false;
        $table_name = 'search_'.$property;
        while (!$is_all_checked) {
            $wordforms = DB::table($table_name)
//                           ->select('wordform', 'ending')
                           ->whereLangId($search_lang);
            if ($is_all) {
                $wordforms = $wordforms->whereNull('eval_ends')
                          ->whereNotNull('ending');
            } else {
                $wordforms = $wordforms->whereNull('eval_end');
            }
            $wordforms = $wordforms->groupBy('wordform')
                      ->take(100)
                      ->get();
            if ($wordforms) {
                foreach ($wordforms as $wordform) {
                    if ($is_all) {
                        Experiment::evaluateSearchPosGramsetByAllEndings($search_lang, $table_name, $property, $wordform);
                    } else {
                        Experiment::evaluateSearchPosGramset($search_lang, $table_name, $property, $wordform);
                    }
                }
            } else {
                $is_all_checked = true;
            }
        }
    }
    
    public function evaluateSearchGramsetByAffix(Request $request) {
        $search_lang =  $request->input('search_lang');
        $is_all =  $request->input('all');
        $is_all_checked = false;
        
        while (!$is_all_checked) {
            $wordforms = DB::table('search_gramset')
                      ->whereLangId($search_lang);
            if ($is_all) {
                $wordforms = $wordforms->whereNull('eval_affs')
                          ->whereNotNull('affix');
            } else {
                $wordforms = $wordforms->whereNull('eval_aff');
            }
            $wordforms = $wordforms->groupBy('wordform')
                      ->take(100)
                      ->get();
//dd($wordforms);            
            if ($wordforms) {
                foreach ($wordforms as $wordform) {
                    if ($is_all) {
                        Experiment::evaluateSearchGramsetByAllAffixes($wordform, $search_lang);                        
                    } else {
                        Experiment::evaluateSearchGramsetByAffix($wordform, $search_lang);
                    }
                }
            } else {
                $is_all_checked = true;
            }
        }
    }
    
//select count(*) from search_gramset where lang_id=1 and eval_end is not null and win_end is null and ending is NULL;
    public function writeWinners(Request $request) {
        $search_lang =  $request->input('search_lang');
        $property = $request->input('property');
        $type = $request->input('type');
        $is_all_checked = false;
        $table_name = 'search_'.$property;
        while (!$is_all_checked) {
            $wordforms = DB::table($table_name)
                           //->select('wordform')
                           ->whereLangId($search_lang)
                           ->whereNotNull('eval_'.$type)
                           ->whereNull('win_'.$type)
                           ->groupBy('wordform')
                           ->take(100)
                           ->get();
                      //->first();
//dd($wordforms);            
            if ($wordforms) {
                foreach ($wordforms as $wordform) {
                    Experiment::writeWinners($search_lang, $table_name, $property, $wordform, $type);
                }
            } else {
                $is_all_checked = true;
            }
        }
    }
    
    public function exportErrorShift(Request $request) {
        $search_lang =  $request->input('search_lang');
        $all_errors =  $request->input('all');
        $property = $request->input('property');
        $property_id = $property.'_id';
        $table_name = 'search_'.$property;
        
        $dir_name = "export/error_shift/";
        $filename = $dir_name.$property.'-'.$search_lang;
        if ($all_errors) {
            $filename .= '_all';
        }
        $filename .= '.txt';
            
        Storage::disk('public')->put($filename, ''); 
        $shift_list = Experiment::createShiftErrors($search_lang, $table_name, $property_id, $all_errors);
//dd($shift_list);        
        foreach ($shift_list as $p1 =>$p_info) {
            foreach ($p_info as $p2 => $count) {
                Storage::disk('public')->append($filename, "$p1\t$p2\t$count");
            }
        }
print 'done.';        
}
    
    public function exportErrorShiftToDot(Request $request) {
        $search_lang =  $request->input('search_lang');
        $all_errors =  $request->input('all');
        $with_claster =  $request->input('with_claster');
        $property = $request->input('property');
        $total_limit = $request->input('total_limit'); // total of pos (gramsets) > $total_limit
        
        $property_id = $property.'_id';
        $table_name = 'search_'.$property;
        if ($property == 'pos') {
            $p_names = PartOfSpeech::getList();
            $range = range(0,20,10); // отсекаем ребра весом меньше чем $min_limit
        } else {
            $p_names = Gramset::getList(0);
            $range = range(0,10,1);
        }
        
        $dir_name = "export/error_shift/";
        foreach($range as $min_limit) { // отсекаем ребра весом меньше чем $min_limit
            $file_with_data = $dir_name.$property.'-'.$search_lang;
            $filename = $dir_name.$property.'-'.$search_lang;
            if ($all_errors) {
                $file_with_data .= '_all';
                $filename .= '_all';
            }
            if ($total_limit) {
                $filename .= '_part'.$total_limit;
            }
            if ($with_claster) {
                $filename .= '_sub';
            }
            
            $file_with_data .= '.txt';
            $filename .= '_'.$min_limit.'.dot';
            list($node_list, $edge_list) = Experiment::readShiftErrorsForDot($search_lang, $file_with_data, $table_name, $property_id, $min_limit, $p_names, $total_limit);
//dd($node_list, $edge_list);            
            Experiment::writeShiftErrorsToDot($filename, $node_list, $edge_list, $with_claster, $property);
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
        if (Storage::disk('public')->exists($filename)) {
            $results[3]['list'] = Experiment::readShiftErrors($filename, $p_names);
            $results[3]['limit'] = 6;
        }
        
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
        $results[4] = Experiment::resultsSearch($search_lang, $table_name, 'eval_affs');
            
        $results[2] = Experiment::lenEndDistribution($search_lang, $table_name, 'gramset_id', $p_names);
        
        $dir_name = "export/error_shift/";
        $filename = $dir_name.$property.'-'.$search_lang.'.txt';
        if (Storage::disk('public')->exists($filename)) {
            $results[3]['list'] = Experiment::readShiftErrors($filename, $p_names);
            $results[3]['limit'] = 9;
        }
        return view('experiments.results_search',
                    compact('search_lang_name', 'property', 'results'));
    }
    
    public function errorList(Request $request) {
        $search_lang =  $request->input('search_lang');
        $search_lang_name = Lang::getNameById($search_lang);
        $property = $request->input('property');
        $type = $request->input('type');
        if ($type=='aff') {
            $tale='affix';
        } else {
            $tale='ending';
        }
        $property_id = $property.'_id';
        $table_name = 'search_'.$property;
        
        $wordforms = DB::table($table_name)
                   ->whereLangId($search_lang)
                   ->where('eval_'.$type.'_gen',0)
                   ->orderBy($property_id)
                   ->orderBy('win_'.$type)
                   ->orderBy('wordform')
                   ->select('wordform', DB::raw($property_id.' as prop'), DB::raw($tale.' as tale'), DB::raw('win_'.$type.' as winner'))
                   ->get();

        return view('experiments.error_list',
                    compact('search_lang', 'search_lang_name', 'property', 'type', 'wordforms'));
    }
    
    public function checkWord(Request $request) {
        $search_lang =  $request->input('search_lang');
        $property =  $request->input('property');
        $word =  $request->input('word');
        $property_id = $property.'_id';
        $table_name = 'search_'.$property;
        
        $wordforms = DB::table($table_name)
                   ->whereLangId($search_lang)
                   ->where('wordform', 'like', $word)
                   ->get();
        
        return view('experiments.check_word',
                    compact('wordforms', 'property', 'property_id', 'search_lang', 'table_name'));
    }
}
