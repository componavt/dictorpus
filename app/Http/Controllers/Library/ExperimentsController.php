<?php

namespace App\Http\Controllers\Library;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Library\Experiment;

use App\Models\Dict\Lang;
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
     * Fill data [wordform, pos ID, gramsets] in table unique_wordforms 
     * 
     * @return \Illuminate\Http\Response
     */
    public function fillUniqueWordforms() {
        $search_lang = 4;
        $parts_of_speech = PartOfSpeech::changeablePOSList();
        $pos_wordforms = [];
        $pairs = [];
        $prev_pos_id = NULL;
        
        DB::table('unique_wordforms')->all()->delete();
        DB::statement('ALTER TABLE unique_wordforms AUTO_INCREMENT = 1');
        
        foreach ($parts_of_speech as $pos) {
            $pos_id = $pos->id;
            $coll_wordforms = Wordform::join('lemma_wordform', 'wordforms.id', '=', 'lemma_wordform.wordform_id')
                    ->whereNotNull('gramset_id')
                    ->whereIn('lemma_id', function($q) use ($search_lang, $pos_id) {
                          $q->select('id')->from('lemmas')
                            ->whereLangId($search_lang)->wherePosId($pos_id);
                     
                    })->orderBy('wordform')->get();
//dd($w);       
            foreach($coll_wordforms as $wordform) {
                $w = $wordform->wordform;
                if (!isset($pos_wordforms[$pos_id][$w]) 
                        || !in_array($wordform->gramset_id, $pos_wordforms[$pos_id][$w])) {
                    $pos_wordforms[$pos_id][$w][] = $wordform->gramset_id;
                }
/*                
                if ($prev_pos_id && isset($pos_wordforms[$prev_pos_id][$w])
                        && (!isset($pairs[$prev_pos_id.'_'.$pos_id]) 
                                || !in_array($w, $pairs[$prev_pos_id.'_'.$pos_id]))) {
                    $pairs[$prev_pos_id.'_'.$pos_id][] = $w;
                }*/
            }
            $prev_pos_id = $pos_id;
        }
        
//dd($pos_wordforms);  
        $common_wordforms = [];
        $parts_of_speech = array_keys($pos_wordforms);
        for ($i=0; $i<sizeof($parts_of_speech)-1; $i++) {
            for ($j=$i+1; $j<sizeof($parts_of_speech); $j++) {
                $common = array_intersect(array_keys($pos_wordforms[$parts_of_speech[$i]]), 
                                          array_keys($pos_wordforms[$parts_of_speech[$j]]));
                if (sizeof($common)) {
                    $pairs[$parts_of_speech[$i].'_'.$parts_of_speech[$j]] = $common;
                    foreach ($common as $w) {
                        $common_wordforms[$w][$parts_of_speech[$i]] = $pos_wordforms[$parts_of_speech[$i]][$w];
                        $common_wordforms[$w][$parts_of_speech[$j]] = $pos_wordforms[$parts_of_speech[$j]][$w];
                    }
                }
            }
        }
        print "<p>".Lang::getNameById($search_lang)."</p>";
        
        foreach ($pos_wordforms as $pos_id => $wordforms) {
            foreach ($wordforms as $w=>$gramsets) {
                if (!isset($common_wordforms[$w])) {
                    sort($gramsets);
                    DB::table('unique_wordforms')->insert([
                        'wordform' => $w,
                        'pos_id'=> $pos_id, 
                        'gramsets'=> join('_',$gramsets)
                    ]);
                }
            }
        }
        print "<P>Записано ". DB::table('unique_wordforms')->count();
  
        
//dd($pairs);    
//dd($unique_wordforms);
//dd($common_wordforms_total_counts);        
//        $unique_wordforms_total = number_format(sizeof($unique_wordforms), 0, ',', ' ');
/*        return view('experiments.fill_unique_wordforms',
                compact('search_lang', //'unique_wordforms_total', 
                        'unique_wordforms')); */
        
    }
    
    /**
     * select pos_val, count(*) from unique_wordforms where pos_val is not null group by pos_val;
     * select ROUND(pos_val,1) as val, count(*) from unique_wordforms where pos_val is not null group by val;
     * 
     * удалили аналитические формы 
     * delete from unique_wordforms where wordform like '% %'; 
     * и оценки
     * update unique_wordforms set pos_val=null, gram_val=null;
     * и прогнали заново
     */
    public function searchPosGramsetsByUniqueWordforms() {
        $wordforms = DB::table('unique_wordforms')->whereNull('pos_val')
                   ->orderBy('id')
                //->take(100)
                   ->get();
//        $pos_val_count = $gram_val_count = [];
        foreach ($wordforms as $wordform) {
            list($pos_val, $gram_val) 
                    = Experiment::searchPosGramsetsByUniqueWordforms($wordform);
            DB::statement("UPDATE unique_wordforms SET pos_val=".$pos_val
                         .", gram_val=$gram_val where id=".$wordform->id);
/*            $pos_val_count[$pos_val] = !isset($pos_val_count[$pos_val])
                                     ? 1 : 1+$pos_val_count[$pos_val];
            $gram_val_count[$gram_val] = !isset($gram_val_count[$gram_val])
                                     ? 1 : 1+$gram_val_count[$gram_val];*/
        }
//dd($pos_val_count, $gram_val_count);        
    }
    
    /**
     * select ROUND(gram_val,1) as val, count(*) from unique_wordforms where gram_val is not null group by val;
     */
    public function searchPosGramsetsByUniqueWordformsResults() {
        $search_lang = 4;
        $search_lang_name = Lang::getNameById($search_lang);
        
        $results[0] = Experiment::searchPosGramsetsByUniqueWordformsResults('unique_wordforms_with_af');
        $results[1] = Experiment::searchPosGramsetsByUniqueWordformsResults('unique_wordforms');
        
        return view('experiments.search_pos_gramsets_by_unique_wordforms_results',
                    compact('search_lang_name', 'results'));
    }
    
    public function searchPosGramsetsByAffix() {
        $search_lang = 4;
        $search_lang_name = Lang::getNameById($search_lang);
        
        $wordforms = Wordform::join('lemma_wordform', 'wordforms.id', '=', 'lemma_wordform.wordform_id')
                    ->join('lemmas', 'lemmas.id', '=', 'lemma_wordform.lemma_id')
                    ->whereNotNull('gramset_id')
                    ->whereLangId($search_lang)
                    ->groupBy('wordform','pos_id', 'gramset_id')
                    ->take(10)->get();
 //dd($wordforms);                    
        foreach ($wordforms as $wordform) {
            list($pos_val, $gram_val) = Experiment::searchPosGramsetsByAffix($wordform, $search_lang);
        }
    }
}
