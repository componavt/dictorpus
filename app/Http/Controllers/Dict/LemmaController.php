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
        $numAll = $lemmas->get()->count();

        $lemmas = $lemmas->paginate($this->url_args['limit_num']);         
        
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
        $new_meaning_n = 1;
                
        return view('dict.lemma.create')
                  ->with(array(
                               'langs_for_meaning' => $lang_values,
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
        $langs_for_meaning = Lang::getList();
                                
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
        
        $lemma = Lemma::create($request->only('lemma','lang_id','pos_id'));
        
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

        return view('dict.lemma.edit_wordforms')
                  ->with(array(
                               'dialect_id' => $dialect_id,
                               'dialect_name' => $dialect_name,
                               'gramset_values' => $gramset_values,
                               'lemma' => $lemma,                      
                               'args_by_get'    => $this->args_by_get,
                               'url_args'       => $this->url_args,
                              )
                        );
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
        $lemma->lemma = $request->lemma;
        $lemma->lang_id = $request->lang_id;
        $lemma->pos_id = $request->pos_id;
        $lemma->save();
        
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
        $lemma-> wordforms()->wherePivot('dialect_id',$dialect_id)->detach();
       
        //add wordforms from full table of gramsets
        Wordform::storeLemmaWordformGramsets($request->lang_wordforms, $lemma);
        //add wordforms without gramsets
        Wordform::storeLemmaWordformsEmpty($request->empty_wordforms, $lemma);
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
        foreach ($request['relevance'] as $key => $value) {
            if (preg_match("/^(\d+)\_(\d+)_(\d+)_(\d+)$/",$key,$regs)) {
                DB::statement('UPDATE meaning_text SET relevance='.(int)$value.
                              ' WHERE meaning_id='.(int)$regs[1].
                              ' AND text_id='.(int)$regs[2].
                              ' AND sentence_id='.(int)$regs[3].
                              ' AND w_id='.(int)$regs[4]);
            }
        }
        $lemma=Lemma::find($id);
        $meanings = [];
        foreach ($lemma->meanings as $meaning) {
            $meanings[] = $meaning->id;            
        }
        $meanings = array_unique($meanings);
        
        foreach ($meanings as $meaning_id) {
            $sentences = DB::table('meaning_text')
                    ->where('meaning_id',$meaning_id)
                    ->where('relevance',1)
                    ->get(['text_id','sentence_id','w_id']);
            foreach ($sentences as $sentence) {
                $exists_positive_rel = DB::table('meaning_text')
                        -> where('text_id',$sentence->text_id)
                        -> where('sentence_id',$sentence->sentence_id)
                        -> where('w_id',$sentence->w_id)
                        -> whereIn('meaning_id',$meanings)
                        -> where ('relevance','>',1);
                if ($exists_positive_rel->count() > 0) {
                    DB::statement('UPDATE meaning_text SET relevance=0'
                                 .' WHERE meaning_id='.(int)$meaning_id
                                 .' AND text_id='.(int)$sentence->text_id
                                 .' AND sentence_id='.(int)$sentence->sentence_id
                                 .' AND w_id='.(int)$sentence->w_id);
                    
                }
                
            }
        }
        return Redirect::to('/dict/lemma/'.$id.($this->args_by_get))
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
        $search_lemma = $request->input('search_lemma');
        $limit_num = (int)$request->input('limit_num');
        $lang_id = (int)$request->input('lang_id');
        $pos_id = (int)$request->input('pos_id');
        $relation_id = (int)$request->input('relation_id');
        $page = (int)$request->input('page');

        if (!$page) {
            $page = 1;
        }
        
        if ($limit_num<=0) {
            $limit_num = 10;
        } elseif ($limit_num>1000) {
            $limit_num = 1000;
        }   
        
        $lemmas = Lemma::select(DB::raw('lemmas.id as lemma1_id, lemmas.lemma as lemma1, relation_id, meaning1_id, meaning2_id'))
                       ->join('meanings', 'lemmas.id', '=', 'meanings.lemma_id')
                       ->join('meaning_relation', 'meanings.id', '=', 'meaning_relation.meaning1_id');        
        if ($search_lemma) {
            $lemmas = $lemmas->where('lemma','like', $search_lemma);
        } 

        if ($lang_id) {
            $lemmas = $lemmas->where('lang_id',$lang_id);
        } 
         
        if ($pos_id) {
            $lemmas = $lemmas->where('pos_id',$pos_id);
        } 
                
        if ($relation_id) {
            $lemmas = $lemmas->where('relation_id',$relation_id);
        } 
        
        $numAll = $lemmas->count();
        $lemmas = $lemmas->orderBy('lemma')->paginate($limit_num);

        $pos_values = PartOfSpeech::getList();//getGroupedListWithQuantity('lemmas');
        
        $lang_values = Lang::getList();//getListWithQuantity('lemmas');
        
        $relation_values = Relation::getList();//getListWithQuantity('lemmas');
        
        return view('dict.lemma.relation')
                  ->with(array('limit_num' => $limit_num,
                               'search_lemma' => $search_lemma,
                               'lang_id'=>$lang_id,
                               'pos_id'=>$pos_id,
                               'relation_id'=>$relation_id,
                               'page'=>$page,
                               'lemmas' => $lemmas,
                               'lang_values' => $lang_values,
                               'pos_values' => $pos_values,
                               'relation_values' => $relation_values,
                               'numAll' => $numAll,
                              )
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
