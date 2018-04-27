<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use DB;
use Gate;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Response;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Models\User;

use App\Models\Dict\Dialect;
use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\Meaning;
use App\Models\Dict\MeaningText;
use App\Models\Dict\PartOfSpeech;
use App\Models\Dict\Relation;
use App\Models\Dict\Wordform;
use App\Models\Corpus\Text;

class LemmaController extends Controller
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
                          ['only' => ['create','store','edit','update','destroy',
                                      'createMeaning', 'editExamples', 'editExample',
                                      'editWordforms','updateWordforms','updateExamples']]);
        
        $this->url_args = [
                    'limit_num'       => (int)$request->input('limit_num'),
                    'page'            => (int)$request->input('page'),
                    'search_gramset'  => (int)$request->input('search_gramset'),
                    'search_id'       => (int)$request->input('search_id'),
                    'search_lang'     => (int)$request->input('search_lang'),
                    'search_lemma'    => $request->input('search_lemma'),
                    'search_meaning'  => $request->input('search_meaning'),
                    'search_pos'      => (int)$request->input('search_pos'),
                    'search_relation' => (int)$request->input('search_relation'),
                    'search_wordform' => $request->input('search_wordform'),
                ];
        
        if (!$this->url_args['page']) {
            $this->url_args['page'] = 1;
        }
        
        if (!$this->url_args['search_id']) {
            $this->url_args['search_id'] = NULL;
        }
        
        if ($this->url_args['limit_num']<=0) {
            $this->url_args['limit_num'] = 10;
        } elseif ($this->url_args['limit_num']>1000) {
            $this->url_args['limit_num'] = 1000;
        }   
        
        $this->args_by_get = Lang::searchValuesByURL($this->url_args);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * 
     */
    public function index()
    {
        $lemmas = Lemma::orderBy('lemma');
        
        if ($this->url_args['search_wordform'] || $this->url_args['search_gramset']) {
            $search_wordform = $this->url_args['search_wordform'];
            $lemmas = $lemmas->join('lemma_wordform', 'lemmas.id', '=', 'lemma_wordform.lemma_id');
            if ($search_wordform) {
                $lemmas = $lemmas->whereIn('wordform_id',function($query) use ($search_wordform){
                                    $query->select('id')
                                    ->from('wordforms')
                                    ->where('wordform','like', $search_wordform);
                                });
            } 

            if ($this->url_args['search_gramset']) {
                $lemmas = $lemmas->where('gramset_id',$this->url_args['search_gramset']);
                /*if (!$this->url_args['search_pos']) {
                    
                }*/
            }
        }    

        if ($this->url_args['search_lemma']) {
            $lemmas = $lemmas->where('lemma','like', $this->url_args['search_lemma']);
        } 

        if ($this->url_args['search_lang']) {
            $lemmas = $lemmas->where('lang_id',$this->url_args['search_lang']);
        } 
         
        if ($this->url_args['search_pos']) {
            $lemmas = $lemmas->where('pos_id',$this->url_args['search_pos']);
        } 
         
        if ($this->url_args['search_id']) {
            $lemmas = $lemmas->where('id',$this->url_args['search_id']);
        } 
        
        if ($this->url_args['search_meaning']) {
            $search_meaning = $this->url_args['search_meaning'];
            $lemmas = $lemmas->whereIn('id',function($query) use ($search_meaning){
                                    $query->select('lemma_id')
                                    ->from('meanings')
                                    ->whereIn('id',function($query) use ($search_meaning){
                                        $query->select('meaning_id')
                                        ->from('meaning_texts')
                                        ->where('meaning_text','like', $search_meaning);
                                    });
                                });
        } 


        $lemmas = $lemmas->groupBy('lemmas.id')
                         ->with(['meanings'=> function ($query) {
                                    $query->orderBy('meaning_n');
                                }]);
//dd($lemmas->get());                                
        $numAll = $lemmas->get()->count();
//dd($numAll); 
        $lemmas = $lemmas->paginate($this->url_args['limit_num']);         
//dd($lemmas);        
        $pos_values = PartOfSpeech::getGroupedListWithQuantity('lemmas');
        
        //$lang_values = Lang::getList();
        $lang_values = Lang::getListWithQuantity('lemmas');
        $gramset_values = Gramset::getList($this->url_args['search_pos'],$this->url_args['search_lang'],true);

        return view('dict.lemma.index')
                  ->with(array(
                               'gramset_values' => $gramset_values,
                               'lang_values'    => $lang_values,
                               'lemmas'         => $lemmas,
                               'numAll'         => $numAll,
                               'pos_values'     => $pos_values,
                               'args_by_get'    => $this->args_by_get,
                               'url_args'       => $this->url_args,
                              )
                        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pos_values = PartOfSpeech::getGroupedList();   
        $lang_values = Lang::getList();
        $langs_for_meaning = Lang::getListWithPriority();
        
        $lang_id = User::userLangID();
        $new_meaning_n = 1;
                
        return view('dict.lemma.create')
                  ->with(array(
                               'langs_for_meaning' => $langs_for_meaning,
                               'lang_id' => $lang_id,
                               'lang_values' => $lang_values,
                               'new_meaning_n' => $new_meaning_n,
                               'pos_values' => $pos_values,
                               'args_by_get'    => $this->args_by_get,
                               'url_args'       => $this->url_args,
                              )
                        );
    }

    /**
     * Shows the form for creating a new resource.
     * 
     * Called by ajax request
     *
     * @return \Illuminate\Http\Response
     */
    public function createMeaning(Request $request)
    {
        $count = (int)$request->input('count');
        $meaning_n = (int)$request->input('meaning_n');
//        $langs_for_meaning = Lang::getList();
        $langs_for_meaning = Lang::getListWithPriority();
                                
        return view('dict.lemma._form_create_meaning')
                  ->with(array('count' => $count,
                               'new_meaning_n' => $meaning_n,
                               'langs_for_meaning' => $langs_for_meaning
                              )
                        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'lemma'  => 'required|max:255',
            'lang_id'=> 'required|numeric',
//            'pos_id' => 'numeric',
        ]);
        
        $data = $request->all();
        if ($data['pos_id'] != 11) { // is not verb
            $data['reflexive'] = 0;
        }
        $data['lemma'] = trim($data['lemma']);
        $request->replace($data);
        
        $lemma = Lemma::create($request->only('lemma','lang_id','pos_id','reflexive'));
        
        $lemma->createDictionaryWordforms($request->wordforms);
            
        Meaning::storeLemmaMeanings($request->new_meanings, $lemma->id);
        
        return Redirect::to('/dict/lemma/'.($lemma->id).($this->args_by_get))
            ->withSuccess(\Lang::get('messages.created_success'));        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $lemma = Lemma::find($id);
        if (!$lemma)
            return Redirect::to('/dict/lemma/'.($this->args_by_get))
                           ->withErrors('error.no_lemma');

        $langs_for_meaning = Lang::getListWithPriority($lemma->lang_id);
        $relations = Relation::getList();
//dd($relations);
        $meanings = $lemma->meanings;
        $meaning_texts = 
        $meaning_relations = 
        $translation_values = [];
          
        foreach ($meanings as $meaning) {
            foreach ($langs_for_meaning as $lang_id => $lang_text) {
                $meaning_text_obj = MeaningText::where('lang_id',$lang_id)->where('meaning_id',$meaning->id)->first();
                if ($meaning_text_obj) {
                    $meaning_texts[$meaning->id][$lang_text] = $meaning_text_obj->meaning_text;
                }
            }

            $relation_meanings = $meaning->meaningRelations;
            if ($relation_meanings) {
                foreach ($relation_meanings as $relation_meaning) {
                    $meaning2_id = $relation_meaning->pivot->meaning2_id;
                    $relation_id = $relation_meaning->pivot->relation_id;
                    $relation_text = $relations[$relation_id];
                    $relation_meaning_obj = Meaning::find($meaning2_id);
                    $relation_lemma_obj = $relation_meaning_obj->lemma;
                    $relation_lemma = $relation_lemma_obj->lemma;
                    $meaning_relations[$meaning->id][$relation_text][$relation_lemma_obj->id]  
                            = ['lemma' => $relation_lemma,
                               'meaning' => $relation_meaning_obj->getMultilangMeaningTextsString()];
                }
            }
            
            foreach ($langs_for_meaning as $l_id => $lang_text) {
                $meaning_translations = $meaning->translations()->wherePivot('lang_id',$l_id)->get();
                if ($meaning_translations) {
                    foreach ($meaning_translations as $meaning_translation) {
                        $meaning2_id = $meaning_translation->pivot->meaning2_id; 
                        $meaning2_obj = Meaning::find($meaning2_id);
                        $translation_lemma_obj = $meaning2_obj->lemma;
                        $translation_lemma = $translation_lemma_obj->lemma;
                        $translation_values[$meaning->id][$lang_text][$translation_lemma_obj->id] 
                            = ['lemma' => $translation_lemma,
                               'meaning' => $meaning2_obj->getMultilangMeaningTextsString()];
                    }
                }
            }
        }   
        
        $dialect_values = Dialect::getList($lemma->lang_id);
        return view('dict.lemma.show')
                  ->with([
                          'dialect_values'    => $dialect_values,
                          'lemma'             => $lemma,
                          'meaning_texts'     => $meaning_texts,
                          'meaning_relations' => $meaning_relations,
                          'translation_values'=> $translation_values,
                          'args_by_get'       => $this->args_by_get,
                          'url_args'          => $this->url_args,
            ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $lemma = Lemma::find($id);
        
        $pos_values = ['NULL'=>''] + PartOfSpeech::getGroupedList(); 
        $lang_values = Lang::getList();
//        $gramset_values = ['NULL'=>'']+Gramset::getList($lemma->pos_id,$lemma->lang_id,true);
        $langs_for_meaning = Lang::getListWithPriority($lemma->lang_id);
        $new_meaning_n = $lemma->getNewMeaningN();
        $relation_values = Relation::getList();

        $all_meanings = [];
        $lemmas = Lemma::where('lang_id',$lemma->lang_id)
                       ->where('pos_id',$lemma->pos_id)
                       ->where('id','<>',$lemma->id) 
                       ->orderBy('lemma')->get();
        foreach ($lemmas as $lem) {
            foreach ($lem->meanings as $meaning) {
                $all_meanings[$meaning->id] = $lem->lemma .' ('.$meaning->getMultilangMeaningTextsString().')';
            }
        }  

        $relation_meanings = 
        $meaning_relation_values = 
        $translation_values = [];
        
        foreach ($lemma->meanings as $meaning) {
            foreach ($meaning->meaningRelations as $meaning_relation) {
                $relation_id = $meaning_relation->pivot->relation_id;
                $meaning2_id = $meaning_relation->pivot->meaning2_id;
                $relation_meanings[$meaning->id][$relation_id][] = $meaning2_id;
                if (isset($all_meanings[$meaning2_id])) {
                    $meaning_relation_values[$meaning2_id] = $all_meanings[$meaning2_id];
                }
            }
            
            foreach (array_keys($langs_for_meaning) as $l_id) {
                $translation_values[$meaning->id][$l_id] = [];
                $meaning_translations = $meaning->translations()->wherePivot('lang_id',$l_id)->get();
                if ($meaning_translations) {
                    foreach ($meaning_translations as $meaning_translation) {
                        $meaning2_id = $meaning_translation->pivot->meaning2_id; 
                        $meaning2_obj = Meaning::find($meaning2_id);
                        if ($meaning2_obj) {
                            $translation_values[$meaning->id][$l_id][$meaning2_id] 
                                = $meaning2_obj->getLemmaMultilangMeaningTextsString();
                        }
                    }
                }
            }
        }                      

        return view('dict.lemma.edit')
                  ->with(array('lemma' => $lemma,
                               'lang_values' => $lang_values,
                               'pos_values' => $pos_values,
//                               'gramset_values' => $gramset_values,
                               'langs_for_meaning' => $langs_for_meaning,
                               'new_meaning_n' => $new_meaning_n,
                               'all_meanings' => $meaning_relation_values,//$all_meanings,
                               'relation_values' => $relation_values,
                               'relation_meanings' => $relation_meanings,
                               'translation_values' => $translation_values,
                               'args_by_get'    => $this->args_by_get,
                               'url_args'       => $this->url_args,
                              )
                        );
    }

    /**
     * Shows the form for editing of lemma's wordforms.
     *
     * @param  int  $id - ID of lemma
     * @return \Illuminate\Http\Response
     */
    public function editWordforms(Request $request, $id)
    {
        $dialect_id = (int)$request->dialect_id;
        $dialect_name = Dialect::getNameByID($dialect_id);
        
        if (!$dialect_id) {
            $dialect_id = NULL;
        }

        $lemma = Lemma::find($id);
//dd($lemma->wordformsWithAllGramsets($dialect_id));        
        $gramset_values = ['NULL'=>'']+Gramset::getList($lemma->pos_id,$lemma->lang_id,true);
        $dialect_values = ['NULL'=>'']+Dialect::getList($lemma->lang_id);
        return view('dict.lemma.edit_wordforms')
                  ->with(array(
                               'dialect_id' => $dialect_id,
                               'dialect_name' => $dialect_name,
                               'dialect_values' => $dialect_values,
                               'gramset_values' => $gramset_values,
                               'lemma' => $lemma,                      
                               'args_by_get'    => $this->args_by_get,
                               'url_args'       => $this->url_args,
                              )
                        );
    }

    /**
     * Shows the form for editing of lemma's text examples.
     *
     * @param  int  $id - ID of lemma
     * @return \Illuminate\Http\Response
     */
    public function editExamples(Request $request, $id)
    {
        $lemma = Lemma::find($id);
        
        $langs_for_meaning = Lang::getListWithPriority($lemma->lang_id);
        $meanings = $lemma->meanings;
        $meaning_texts = [];
          
        foreach ($meanings as $meaning) {
            foreach ($langs_for_meaning as $lang_id => $lang_text) {
                $meaning_text_obj = MeaningText::where('lang_id',$lang_id)->where('meaning_id',$meaning->id)->first();
                if ($meaning_text_obj) {
                    $meaning_texts[$meaning->id][$lang_text] = $meaning_text_obj->meaning_text;
                }
            }
        }   
        
        return view('dict.lemma.edit_examples')
                  ->with(array(
                               'back_to_url'    => '/dict/lemma/'.$lemma->id,
                               'lemma'          => $lemma, 
                               'meanings'        => $lemma->meanings,
                               'meaning_texts'  => $meaning_texts,
                               'args_by_get'    => $this->args_by_get,
                               'url_args'       => $this->url_args,
                              )
                        );
    }

    /**
     * Shows the form for editing of text example for all lemma meanings connected with this sentence.
     *
     * @param  int  $id - ID of lemma
     * @param  int  $sentence_id - ID of example
     * @return \Illuminate\Http\Response
     */
    public function editExample(Request $request, $id, $example_id)
    {
        $lemma = Lemma::find($id);
        
        list($sentence, $meanings, $meaning_texts) = 
                Text::preparationForExampleEdit($example_id);
        
        if ($sentence == NULL) {
            return Redirect::to('/dict/lemma/'.($lemma->id).($this->args_by_get))
                       ->withError(\Lang::get('messages.invalid_id'));            
        } else {
            return view('dict.lemma.edit_example')
                      ->with(array(
                                   'back_to_url'    => '/dict/lemma/'.$lemma->id,
                                   'id'             => $lemma->id, 
                                   'meanings'       => $meanings,
                                   'meaning_texts'  => $meaning_texts,
                                   'route'          => array('lemma.update.examples', $id),
                                   'sentence'       => $sentence,
                                   'args_by_get'    => $this->args_by_get,
                                   'url_args'       => $this->url_args,
                                  )
                            );            
        }
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
       // https://laravel.com/api/5.1/Illuminate/Database/Eloquent/Model.html#method_touch

        $lemma= Lemma::findOrFail($id);
        
        $this->validate($request, [
            'lemma'  => 'required|max:255',
            'lang_id'=> 'required|numeric',
//            'pos_id' => 'numeric',
        ]);
        
        // LEMMA UPDATING
        $lemma->lemma = trim($request->lemma);
        $lemma->lang_id = (int)$request->lang_id;
        $lemma->pos_id = (int)$request->pos_id;
        $lemma->reflexive = (int)$request->reflexive;
        if ($lemma->pos_id != 11) { // is not verb
            $lemma->reflexive = 0;
        }
        $lemma->updated_at = date('Y-m-d H:i:s');
        $lemma->save();
        
        $lemma->createDictionaryWordforms($request->wordforms);    
        // MEANINGS UPDATING
        // existing meanings
        Meaning::updateLemmaMeanings($request->ex_meanings);

        // new meanings, i.e. meanings created by user in form now
        Meaning::storeLemmaMeanings($request->new_meanings, $id);
                
        return Redirect::to('/dict/lemma/'.($lemma->id).($this->args_by_get))
                       ->withSuccess(\Lang::get('messages.updated_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateWordforms(Request $request, $id)
    {
//dd($request->lang_wordforms);        
        $dialect_id = $request->dialect_id;
        $lemma= Lemma::findOrFail($id);
//phpinfo();
//dd($request->empty_wordforms);        
        // WORDFORMS UPDATING
        //remove all records from table lemma_wordform
        if (!(int)$dialect_id) {
            $dialect_id = NULL;
        }
//        if ($dialect_id) {
        $lemma-> wordforms()->wherePivot('dialect_id',$dialect_id)->detach();
//        }
        //add wordforms from full table of gramsets
        Wordform::storeLemmaWordformGramsets($request->lang_wordforms, $lemma);
        //add wordforms without gramsets
        Wordform::storeLemmaWordformsEmpty($request->empty_wordforms, $lemma, $dialect_id);
//exit(0);  

        // updates links with text examples
        $lemma->updateTextLinks();
                
        return Redirect::to('/dict/lemma/'.($lemma->id).($this->args_by_get))
                       ->withSuccess(\Lang::get('messages.updated_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateExamples(Request $request, $id)
    {
        Text::updateExamples($request['relevance']);
        return Redirect::to($request['back_to_url'].($this->args_by_get))
                       ->withSuccess(\Lang::get('messages.updated_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $error = false;
        $status_code = 200;
        $result =[];
        if($id != "" && $id > 0) {
            try{
                $lemma = Lemma::find($id);
                if($lemma){
                    $lemma_title = $lemma->lemma;
                    
                    //remove all records from table lemma_wordform
                    $lemma-> wordforms()->detach();
                    
                    $meanings = $lemma->meanings;
                    
                    foreach ($meanings as $meaning) {
                        DB::table('meaning_relation')
                          ->where('meaning2_id',$meaning->id)->delete();

                        DB::table('meaning_translation')
                          ->where('meaning2_id',$meaning->id)->delete();

                        DB::table('meaning_text')
                          ->where('meaning_id',$meaning->id)->delete();

                        $meaning_texts = $meaning->meaningTexts;
                        foreach ($meaning_texts as $meaning_text) {
                            $meaning_text -> delete();
                        }
                        $meaning -> delete();
                    }

                    $lemma->delete();
                    $result['message'] = \Lang::get('dict.lemma_removed', ['name'=>$lemma_title]);
                }
                else{
                    $error = true;
                    $result['error_message'] = \Lang::get('messages.record_not_exists');
                }
          }catch(\Exception $ex){
                    $error = true;
                    $status_code = $ex->getCode();
                    $result['error_code'] = $ex->getCode();
                    $result['error_message'] = $ex->getMessage();
                }
        }else{
            $error =true;
            $status_code = 400;
            $result['message']='Request data is empty';
        }
        
        if ($error) {
                return Redirect::to('/dict/lemma/'.($this->args_by_get))
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/dict/lemma/'.($this->args_by_get))
                  ->withSuccess($result['message']);
        }
        
 //       return response()->json(['error' => $error,'response'=>$result],$status_code)
  //                       ->setCallback(\Request::input('callback'));
    }
    
    /**
     * Shows history of lemma.
     *
     * @param  int  $id - ID of lemma
     * @return \Illuminate\Http\Response
     */
    public function history($id)
    {
        $lemma = Lemma::find($id);
        if (!$lemma) {
            return Redirect::to('/dict/lemma/'.($this->args_by_get))
                           ->withErrors(\Lang::get('messages.record_not_exists'));
        }
//dd($lemma->revisionHistory);        
        return view('dict.lemma.history')
                  ->with(['lemma' => $lemma,
                               'args_by_get'    => $this->args_by_get,
                               'url_args'       => $this->url_args,
                         ]);
    }
    
    /** Gets list of longest lemmas, 
     * gets first N lemmas sorted by length.
     *  
     */
    public function sortedByLength(Request $request)
    {
        $limit_num = (int)$request->input('limit_num');
        $page = (int)$request->input('page');

        if (!$page) {
            $page = 1;
        }
                
        if ($limit_num<=0) {
            $limit_num = 10;
        } elseif ($limit_num>1000) {
            $limit_num = 1000;
        }           
        
        //$lemmas = Lemma::orderBy(char_length('lemma'), 'desc')
        $builder = Lemma::select(DB::raw('*, char_length(lemma) as char_length'));
        
        $numAll = $builder->count();
        
        $lemmas = $builder->orderBy('char_length', 'desc')
//                ->take($limit_num)->get();
                          ->paginate($limit_num);
                
        return view('dict.lemma.sorted_by_length')
                  ->with(array('limit_num' => $limit_num,
                               'page'=>$page,
                               'lemmas' => $lemmas,
                               'numAll' => $numAll
                              )
                        );
    }
    
    
    /** Gets list of all semantic relations, 
     *  
     */
    public function relation(Request $request)
    {
        $lemmas = Lemma::select(DB::raw('lemmas.id as lemma1_id, lemmas.lemma as lemma1, relation_id, meaning1_id, meaning2_id'))
                       ->join('meanings', 'lemmas.id', '=', 'meanings.lemma_id')
                       ->join('meaning_relation', 'meanings.id', '=', 'meaning_relation.meaning1_id');        

        if ($this->url_args['search_lemma']) {
            $lemmas = $lemmas->where('lemma','like', $this->url_args['search_lemma']);
        } 

        foreach (['lang','pos','relation'] as $var) {
            if ($this->url_args['search_'.$var]) {
                $lemmas = $lemmas->where($var.'_id',$this->url_args['search_'.$var]);
            }
        } 
         
        $numAll = $lemmas->count();
        $lemmas = $lemmas->orderBy('lemma')->paginate($this->url_args['limit_num']);

        $pos_values = PartOfSpeech::getList();//getGroupedListWithQuantity('lemmas');
        
        $lang_values = Lang::getList();//getListWithQuantity('lemmas');
        
        $relation_values = Relation::getList();//getListWithQuantity('lemmas');
        
        return view('dict.lemma.relation')
                  ->with([
                            'lemmas'          => $lemmas,
                            'lang_values'     => $lang_values,
                            'numAll'          => $numAll,
                            'pos_values'      => $pos_values,
                            'relation_values' => $relation_values,
                            'args_by_get'     => $this->args_by_get,
                            'url_args'        => $this->url_args,
                          ]
                        );
    }
        
    public function omonyms(Request $request) {
        $lemmas = Lemma::groupBy('lemma','pos_id','lang_id')
                       ->havingRaw('count(*) > 1');
        
        if ($this->url_args['search_lemma']) {
            $lemmas = $lemmas->where('lemma','like', $this->url_args['search_lemma']);
        } 

        foreach (['lang','pos'] as $var) {
            if ($this->url_args['search_'.$var]) {
                $lemmas = $lemmas->where($var.'_id',$this->url_args['search_'.$var]);
            }
        } 
         
        $ids = [];
        foreach($lemmas->get() as $lemma) {
            $omonyms = Lemma::select('id')
                            ->where('lemma','like',$lemma->lemma)
                            ->where('lang_id',$lemma->lang_id)
                            ->where('pos_id',$lemma->pos_id)->get();
            foreach ($omonyms as $omonym) {
                $ids[] = $omonym->id;
            }
        }

        $lemmas = Lemma::whereIn('id',$ids);
        $numAll = $lemmas->count();
        $lemmas = $lemmas->orderBy('lemma')->paginate($this->url_args['limit_num']);

        $pos_values = PartOfSpeech::getList();        
        $lang_values = Lang::getList();
        
        return view('dict.lemma.omonyms')
                  ->with([
                            'lemmas'          => $lemmas,
                            'lang_values'     => $lang_values,
                            'numAll'          => $numAll,
                            'pos_values'      => $pos_values,
                            'args_by_get'     => $this->args_by_get,
                            'url_args'        => $this->url_args,
                          ]
                        );
    }

    /**
     * Gets list of relations for drop down list in JSON format
     * Test url: /dict/lemma/meanings_list?lang_id=1&pos_id=1&lemma_id=2810
     * 
     * @return JSON response
     */
    public function meaningsList(Request $request)
    {
        $search_lemma = '%'.$request->input('q').'%';
        $lang_id = (int)$request->input('lang_id');
        $pos_id = (int)$request->input('pos_id');
        $lemma_id = (int)$request->input('lemma_id');

        $all_meanings = [];
        $lemmas = Lemma::where('lang_id',$lang_id)
                       ->where('pos_id',$pos_id)
                       ->where('id','<>',$lemma_id)
                       ->where('lemma','like', $search_lemma)
                       ->orderBy('lemma')->get();
        foreach ($lemmas as $lem) {
            foreach ($lem->meanings as $meaning) {
                $all_meanings[]=['id'  => $meaning->id, 
                                 'text'=> $lem->lemma .' ('.$meaning->getMultilangMeaningTextsString().')'];
            }
        }  

        return Response::json($all_meanings);
    }
    
    public function newLemmaList(Request $request)
    {
        $limit = (int)$request->input('limit');
        if ($limit) {
            $portion = $limit;
            $view = 'new_list';
        } else {
            $portion = 100;
            $view = 'full_new_list';
        }
        $lemmas = Lemma::lastCreatedLemmas($portion);
                                
        return view('dict.lemma.'.$view)
                  ->with(['new_lemmas' => $lemmas,
                          'limit' => $limit
                         ]);
    }
    
    public function updatedLemmaList(Request $request)
    {
        $limit = (int)$request->input('limit');
        if ($limit) {
            $portion = $limit;
            $view = 'updated_list';
        } else {
            $portion = 100;
            $view = 'full_updated_list';
        }
        $lemmas = Lemma::lastUpdatedLemmas($portion);
                                
        return view('dict.lemma.'.$view)
                  ->with(['last_updated_lemmas'=>$lemmas,
                          'limit' => $limit
                         ]);
    }
    
    
    /** Copy vepsian.{lemma and translation_lemma} to vepkar.lemmas
     * + temp column vepkar.lemmas.temp_translation_lemma_id
     */
/*    
    public function tempInsertVepsianLemmas()
    {
        $lemmas = DB::connection('vepsian')->table('lemma')->orderBy('id')->get();
 
     
        DB::connection('mysql')->table('meaning_texts')->delete();
        DB::connection('mysql')->statement('ALTER TABLE meaning_texts AUTO_INCREMENT = 1');
        
        DB::connection('mysql')->table('meanings')->delete();
        DB::connection('mysql')->statement('ALTER TABLE meanings AUTO_INCREMENT = 1');

        DB::connection('mysql')->table('lemmas')->delete();
        DB::connection('mysql')->statement('ALTER TABLE lemmas AUTO_INCREMENT = 1');
        
        foreach ($lemmas as $lemma) {
            DB::connection('mysql')->table('lemmas')->insert([
                    'id' => $lemma->id,
                    'lemma' => $lemma->lemma,
                    'lang_id' => 1,
                    'pos_id' => $lemma->pos_id,
                    'created_at' => $lemma -> modified,
                    'updated_at' => $lemma -> modified
                ]
            );
        }
         
    }
 */    
}
