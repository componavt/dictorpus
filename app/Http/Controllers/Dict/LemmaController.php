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
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // permission= dict.edit, redirect failed users to /dict/lemma/, authorized actions list:
        $this->middleware('auth:dict.edit,/dict/lemma/', ['only' => ['create','store','edit','update','destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * 
     */
    public function index(Request $request)
    {
        $lemma_name = $request->input('lemma_name');
        $limit_num = (int)$request->input('limit_num');
        $lang_id = (int)$request->input('lang_id');
        $pos_id = (int)$request->input('pos_id');
        $page = (int)$request->input('page');
        $search_id = (int)$request->input('search_id');

        if (!$page) {
            $page = 1;
        }
        
        if (!$search_id) {
            $search_id = NULL;
        }
        
        if ($limit_num<=0) {
            $limit_num = 10;
        } elseif ($limit_num>1000) {
            $limit_num = 1000;
        }   
        
        $lemmas = Lemma::orderBy('lemma');
        
        if ($lemma_name) {
            $lemmas = $lemmas->where('lemma','like', $lemma_name);
        } 

        if ($lang_id) {
            $lemmas = $lemmas->where('lang_id',$lang_id);
        } 
         
        if ($pos_id) {
            $lemmas = $lemmas->where('pos_id',$pos_id);
        } 
         
        if ($search_id) {
            $lemmas = $lemmas->where('id',$search_id);
        } 

        $numAll = $lemmas->count();

        $lemmas = $lemmas
                //->take($limit_num)
                       ->with(['meanings'=> function ($query) {
                                    $query->orderBy('meaning_n');
                                }])
//                       ->simplePaginate($limit_num);         
                       ->paginate($limit_num);         
                                        /*->get();*/
        
        $pos_values = PartOfSpeech::getGroupedListWithQuantity('lemmas');
        
        //$lang_values = Lang::getList();
        $lang_values = Lang::getListWithQuantity('lemmas');
                                
        return view('dict.lemma.index')
                  ->with(array('limit_num' => $limit_num,
                               'lemma_name' => $lemma_name,
                               'lang_id'=>$lang_id,
                               'pos_id'=>$pos_id,
                               'page'=>$page,
                               'lemmas' => $lemmas,
                               'lang_values' => $lang_values,
                               'pos_values' => $pos_values,
                               'numAll' => $numAll,
                               'search_id'=>$search_id,
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
                  ->with(array('lang_values' => $lang_values,
                               'pos_values' => $pos_values,
                               'langs_for_meaning' => $lang_values,
                               'new_meaning_n' => $new_meaning_n
                              )
                        );
    }

    /**
     * Show the form for creating a new resource.
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
    
        return Redirect::to('/dict/lemma/'.($lemma->id))
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

        return view('dict.lemma.show')
                  ->with(['lemma'=>$lemma,
                          'meaning_texts' => $meaning_texts,
                          'meaning_relations' => $meaning_relations,
                          'translation_values' => $translation_values
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
        $gramset_values = ['NULL'=>'']+Gramset::getList($lemma->pos_id);
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
                $meaning_relation_values[$meaning2_id] = $all_meanings[$meaning2_id];
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
                               'gramset_values' => $gramset_values,
                               'langs_for_meaning' => $langs_for_meaning,
                               'new_meaning_n' => $new_meaning_n,
                               'all_meanings' => $meaning_relation_values,//$all_meanings,
                               'relation_values' => $relation_values,
                               'relation_meanings' => $relation_meanings,
                               'translation_values' => $translation_values
                              )
                        );
    }

    /**
     * Shows the form for editing of lemma's wordforms.
     *
     * @param  int  $id - ID of lemma
     * @return \Illuminate\Http\Response
     */
    public function editWordforms($id)
    {
        $lemma = Lemma::find($id);
        
        $pos_values = ['NULL'=>''] + PartOfSpeech::getGroupedList(); 
        $gramset_values = ['NULL'=>'']+Gramset::getList($lemma->pos_id);

        return view('dict.lemma.edit_wordforms')
                  ->with(array('lemma' => $lemma,
                               'gramset_values' => $gramset_values,
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
        
        // WORDFORMS UPDATING
        //remove all records from table lemma_wordform
        $lemma-> wordforms()->detach();
       
        //add wordforms from full table of gramsets
        Wordform::storeLemmaWordformGramsets($request->lang_wordforms, $lemma);

        //add wordforms without gramsets
        Wordform::storeLemmaWordformsEmpty($request->empty_wordforms, $lemma);
  
        // MEANINGS UPDATING
        // existing meanings
        Meaning::updateLemmaMeanings($request->ex_meanings);

        // new meanings, i.e. meanings created by user in form now
        Meaning::storeLemmaMeanings($request->new_meanings, $id);
               
        return Redirect::to('/dict/lemma/'.($lemma->id))
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
       // https://laravel.com/api/5.1/Illuminate/Database/Eloquent/Model.html#method_touch
        $lemma= Lemma::findOrFail($id);
        
        // WORDFORMS UPDATING
        //remove all records from table lemma_wordform
        $lemma-> wordforms()->detach();
       
        //add wordforms from full table of gramsets
        Wordform::storeLemmaWordformGramsets($request->lang_wordforms, $lemma);

        //add wordforms without gramsets
        Wordform::storeLemmaWordformsEmpty($request->empty_wordforms, $lemma);
  
        return Redirect::to('/dict/lemma/'.($lemma->id))
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
                        $meaning->meaningRelations()->detach();

                        DB::table('meaning_translation')
                          ->where('meaning2_id',$meaning->id)->delete();
                        $meaning->translations()->detach();

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
                return Redirect::to('/dict/lemma/')
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/dict/lemma/')
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
            return Redirect::to('/dict/lemma/')
                           ->withErrors(\Lang::get('messages.record_not_exists'));
        }
//dd($lemma->revisionHistory);        
        return view('dict.lemma.history')
                  ->with(['lemma' => $lemma]);
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
        $lemma_name = $request->input('lemma_name');
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
        if ($lemma_name) {
            $lemmas = $lemmas->where('lemma','like', $lemma_name);
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
                               'lemma_name' => $lemma_name,
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
        $lemma_name = '%'.$request->input('q').'%';
        $lang_id = (int)$request->input('lang_id');
        $pos_id = (int)$request->input('pos_id');
        $lemma_id = (int)$request->input('lemma_id');

        $all_meanings = [];
        $lemmas = Lemma::where('lang_id',$lang_id)
                       ->where('pos_id',$pos_id)
                       ->where('id','<>',$lemma_id)
                       ->where('lemma','like', $lemma_name)
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
