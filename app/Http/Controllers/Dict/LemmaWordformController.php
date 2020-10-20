<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use Response;
use DB;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

use App\Library\Grammatic;
use App\Library\Str;

use App\Models\Corpus\Text;
use App\Models\Corpus\Word;

use App\Models\Dict\Dialect;
use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\LemmaBase;
use App\Models\Dict\LemmaWordform;
use App\Models\Dict\PartOfSpeech;
use \App\Models\Dict\Wordform;

class LemmaWordformController extends Controller
{
    public $url_args=[];
    public $args_by_get='';
    
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // permission= dict.edit, redirect failed users to /dict/lemma/, authorized actions list:
        $this->middleware('auth:dict.edit,/dict/lemma/', 
                ['only' => ['store','edit','update','destroy', 'reload', 'posCommonWordforms']]);
        
        $this->url_args = Lemma::urlArgs($request);  
        
        $this->args_by_get = Str::searchValuesByURL($this->url_args);
    }

    /**
     * Saves links 1)text's word with lemma's meaning 
     *             2)wordform with gramset and dialects
     * 
     * @param Request $request
     * @return Null
     */
    public function store(Request $request)
    {
        $lemma_id = (int)$request->input('lemma_id');
        $text_id = (int)$request->input('text_id'); 
        $w_id = (int)$request->input('w_id'); 
        
        if (!$lemma_id || !$text_id || !$w_id) {
            return;
        }

        $lemma = Lemma::find($lemma_id);
        $text = Text::find($text_id);
        $word = Word::getByTextWid($text_id, $w_id);
        
        if (!$lemma || !$text || !$word || !$word->sentence_id) { return; }
        
        $meaning_id = $request->input('meaning_id'); 
        $gramset_id = $request->input('gramset_id'); 
        $dialects = (array)$request->input('dialects'); 
        
        $wordform = $request->input('wordform'); 
        if (!$wordform) {
            $wordform = $word -> word;
        }
        
        $lemma->addWordformFromText($wordform, $gramset_id, $dialects, $text_id, $w_id);
        $text->addLinkWithMeaning($lemma, $meaning_id, $w_id, $word);
        return 1;            
    }   
    
    /**
     * Shows the form for editing of lemma's wordforms.
     *
     * @param  int  $id - ID of lemma
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        $dialect_id = (int)$request->dialect_id;
        $dialect_name = Dialect::getNameByID($dialect_id);
        
        if (!$dialect_id) {
            $dialect_id = NULL;
        }

        $lemma = Lemma::find($id);
//dd($lemma->getBase(0, $dialect_id));        
        $gramset_values = ['NULL'=>'']+Gramset::getGroupedList($lemma->pos_id,$lemma->lang_id,true);
        $dialect_values = ['NULL'=>'']+Dialect::getList($lemma->lang_id)+['all'=>'ДЛЯ ВСЕХ ДИАЛЕКТОВ'];
        
        $base_list = LemmaBase::baseList($lemma->lang_id, $lemma->pos_id);
//dd($base_list);        
//dd($lemma->getBase(2, 43, null));                
        return view('dict.lemma_wordform.edit',
                    compact('base_list','dialect_id', 'dialect_name', 'dialect_values', 
                            'gramset_values', 'lemma', 'args_by_get', 'url_args'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $lemma= Lemma::findOrFail($id);
//dd($lemma->reverseLemma);        

        $bases = $request->bases;
//        $bases[0] = $lemma->reverseLemma->updateStemAffixFromBase($bases[0]);

        $dialect_id = $request->dialect_id;
        if (!(int)$dialect_id) {
            $dialect_id = NULL;
        }
        // WORDFORMS UPDATING
        //remove all records from table lemma_wordform
        $lemma->updateBases($bases, $request->dialect_id_for_bases);
        $lemma-> wordforms()->wherePivot('dialect_id',$dialect_id)->detach();
        //add wordforms from full table of gramsets
        $lemma-> storeWordformGramsets($request->lang_wordforms, $request->lang_wordforms_dialect);
        //add wordforms without gramsets
        $lemma-> storeWordformsEmpty($request->empty_wordforms, $dialect_id);

        // updates links with text examples
//        $lemma->updateTextLinks();
        $lemma->updateWordformTotal();        
        
        return Redirect::to('/dict/lemma/'.($lemma->id).($this->args_by_get).($this->args_by_get ? '&' : '?').'update_text_links=1')
                       ->withSuccess(\Lang::get('messages.updated_success'));
    }

    public function destroy(Request $request, $id) {
        $error = false;
//        $status_code = 200;
        $result =[];
        if($id > 0) {
            try{
                $dialect_id = $request->dialect_id;
                $lemma = Lemma::findOrFail($id);
                if($lemma){
                    $result['message'] = \Lang::get('dict.wordforms_removed');
                    $lemma->wordforms->wherePivot('dialect_id',$dialect_id)->detach();
                } else{
                    $error = true;
                    $result['error_message'] = \Lang::get('messages.record_not_exists');
                }
          }catch(\Exception $ex){
                    $error = true;
//                    $status_code = $ex->getCode();
                    $result['error_code'] = $ex->getCode();
                    $result['error_message'] = $ex->getMessage();
                }
        } else{
            $error =true;
//            $status_code = 400;
            $result['message']='Request data is empty';
        }
        
        if ($error) {
            return Redirect::to('/dict/lemma/'.$id.($this->args_by_get))
                           ->withErrors($result['error_message']);
        }
        return Redirect::to('/dict/lemma/'.$id.($this->args_by_get)."&update_text_links=1")
              ->withSuccess($result['message']);        
    }

    public function load($id) {
        $lemma = Lemma::findOrFail($id);        
        return view('dict.lemma_wordform._wordform_table', compact('lemma'));         
    }
    /**
     * Get bases from table OR from wordforms
     * Delete and create wordforms again
     * 
     * Example: /dict/lemma_wordform/209_1/reload/
     * 
     * @param Int $id
     * @param Int $dialect_id
     * @return \Illuminate\Http\Response
     */
    public function reload(Request $request, $id, $dialect_id) {
        $lemma = Lemma::findOrFail((int)$id);    
        $dialect_id = (int)$dialect_id;
        $lemma->reloadWordforms($dialect_id, true, $request->without_remove);
        $lemma->updateWordformTotal();        
        
        return view('dict.lemma_wordform._wordform_table', compact('lemma')); 
    }
    
    public function getBases(Request $request, $id) {
        $lemma = Lemma::findOrFail($id);  
        $dialect_id = (int)$request->dialect_id;
        
        $stems = $lemma->getBases($dialect_id);
//dd($stems);        
        return Response::json($stems);
    }
    
    public function getWordforms(Request $request, $id, $dialect_id) {
        $lemma = Lemma::findOrFail($id);        
        $stems = json_decode($request->bases);
        $stems[0] = preg_replace('/\|/', '', $stems[0]);
//dd($stems);   
        if ($lemma->lang_id != 1 && !isset($stems[10])) { // harmony for karelian
            $stems[10]=$lemma->harmony();
        }        
        
        if ($lemma->pos->isVerb()) {
            $name_num = ($lemma->features && $lemma->features->impersonal) ? 1 : null; 
            $is_reflexive = ($lemma->features && $lemma->features->reflexive) ? 1 : null;
        } else {
            $name_num = ($lemma->features && $lemma->features->number) ? Grammatic::nameNumFromNumberField($lemma->features->number) : null; 
             $is_reflexive = null;
        }

        $gramset_wordforms = Grammatic::wordformsByStems($lemma->lang_id, $lemma->pos_id, $dialect_id, $name_num, $stems, $is_reflexive);
//dd($stems);        
        
//dd($gramset_wordforms);        
        return Response::json($gramset_wordforms);
    }
    
    public function deleteWordforms($id, $dialect_id) {
        if ($dialect_id == 'NULL') {
            $dialect_id = NULL;
        }
        $lemma = Lemma::findOrFail($id);        
        
        $lemma->updateBases($lemma->getBases($dialect_id), $dialect_id);
        
        $lemma->wordforms()->wherePivot('dialect_id',$dialect_id)->detach();
        
        $lemma->wordform_total = LemmaWordform::whereLemmaId($lemma->id)->count();
        $lemma->save();        
        
        return view('dict.lemma_wordform._wordform_table', compact('lemma')); 
    }

    /**
     * select pos_id, gramset_id, affix, count(*) from lemma_wordform, lemmas where lemma_wordform.lemma_id=lemmas.id and gramset_id is not null and affix is not null and lang_id=1 
     * group by pos_id, gramset_id, affix ORDER BY pos_id, count(*) DESC, REVERSE(affix), gramset_id;
     * 
     * @param Request $request
     * @return type
     */
    public function affixFrequency(Request $request) {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
//        $lang_values = Lang::getList(Lang::nonProjectLangIDs());
        $lemmas_for_lang = Lemma::select('lang_id', DB::raw('count(*) as frequency'))
                        ->join('lemma_wordform','lemmas.id','=','lemma_wordform.lemma_id')
                        ->whereNotNull('gramset_id')
                        ->whereNotNull('affix')
                        ->groupBy('lang_id')
                        ->orderBy('frequency', 'DESC')
                        ->get();
        $lang_values = [];
        foreach ($lemmas_for_lang as $lemma) {
            $lang_values[$lemma->lang_id] = $lemma->lang->name ." (".number_format($lemma->frequency, 0, '', ' ').")";
        }

//        $pos_values = PartOfSpeech::getList();
        $lemmas_for_pos = Lemma::select('pos_id', DB::raw('count(*) as frequency'))
                        ->join('lemma_wordform','lemmas.id','=','lemma_wordform.lemma_id')
                        ->whereNotNull('gramset_id')
                        ->whereNotNull('affix')
                        ->groupBy('pos_id')
                        ->orderBy('frequency', 'DESC')
                        ->get();
        $pos_values = [NULL=>''];
        foreach ($lemmas_for_pos as $lemma) {
            $pos_values[$lemma->pos_id] = $lemma->pos->name ." (".number_format($lemma->frequency, 0, '', ' ').")";
        }

        if ($url_args['search_lang']) {
            $lemmas = Lemma::select('pos_id', 'gramset_id', 'affix', DB::raw('REVERSE(affix) as reverse_affix'), DB::raw('count(*) as frequency'))
                            ->join('lemma_wordform','lemmas.id','=','lemma_wordform.lemma_id')
                            ->whereLangId($url_args['search_lang'])
                            ->whereNotNull('gramset_id')
                            ->whereNotNull('affix')
                            ->groupBy('pos_id', 'gramset_id', 'affix')
                            ->orderBy('pos_id')
                            ->orderBy(DB::raw('REVERSE(affix)'))
                            ->orderBy(DB::raw('count(*)'), 'DESC')
                            ->orderBy('gramset_id');
                        
            if ($url_args['search_pos']) {
                $lemmas = $lemmas->wherePosId($url_args['search_pos']);
            } 

            if ($url_args['search_affix']) {
                $lemmas = $lemmas->where('affix', 'like', $url_args['search_affix']);
            } 
//var_dump($words->toSql());  
//            $totalNumber = sizeof($lemmas->get());
            $lemmas = $lemmas 
//                    ->take($this->url_args['limit_num'])
//                    ->take(1000)
                    ->get();
            $totalNumber = sizeof($lemmas);
        } else {
            $lemmas = NULL;
        }
        
        return view('dict.lemma_wordform.affix_freq',
                compact('lang_values', 'lemmas', 'pos_values', 'args_by_get', 'url_args')); //, 'totalNumber'
    }
 
    public function posCommonWordforms() {
        $search_lang = 4;
        $parts_of_speech = PartOfSpeech::changeablePOSList();
        $pos_wordforms = [];
        $pairs = [];
        $prev_pos_id = NULL;
        $wordforms = Wordform::join('lemma_wordform', 'wordforms.id', '=', 'lemma_wordform.wordform_id')
                    ->whereNotNull('gramset_id')
                    ->whereIn('lemma_id', function($q) use ($search_lang) {
                          $q->select('id')->from('lemmas')
                            ->whereLangId($search_lang);                     
                    });
        $wordforms_total = number_format($wordforms->count(), 0, ',', ' ');

        $wordforms_grouped = $wordforms->join('lemmas', 'lemmas.id', '=', 'lemma_wordform.lemma_id')
                ->groupBy('wordform','pos_id')->get();
        $wordforms_grouped_total = number_format(sizeof($wordforms_grouped), 0, ',', ' ');
        
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
        
        $unique_wordforms = [];
        foreach ($pos_wordforms as $pos_id => $wordforms) {
            foreach ($wordforms as $w=>$gramsets) {
                if (!isset($common_wordforms[$w])) {
                    $unique_wordforms[$w] = ['pos'=>$pos_id, 'gramsets'=>$gramsets];
                }
            }
        }
        
        $common_wordforms_total_counts = [];
        foreach ($common_wordforms as $w =>$poses) {
            $common_wordforms_total_counts[sizeof($poses)] = 
                    !isset($common_wordforms_total_counts[sizeof($poses)])
                    ? 1 : $common_wordforms_total_counts[sizeof($poses)]+1;
        }
//dd($pairs);    
//dd($unique_wordforms);
//dd($common_wordforms_total_counts);        
        $unique_wordforms_total = number_format(sizeof($unique_wordforms), 0, ',', ' ');
        $common_wordforms_total = number_format(sizeof($common_wordforms), 0, ',', ' ');
        $search_lang = Lang::getNameById($search_lang);
        return view('dict.lemma_wordform.pos_common_wordforms',
                compact('search_lang', 'wordforms_total', 'unique_wordforms_total', 
                        'common_wordforms_total', 'common_wordforms_total_counts',
                        'wordforms_grouped_total')); 
        
    }
}
