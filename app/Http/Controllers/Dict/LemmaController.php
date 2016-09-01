<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use DB;
use Gate;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Models\User;

use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\Meaning;
use App\Models\Dict\MeaningText;
use App\Models\Dict\PartOfSpeech;
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
        $this->middleware('auth:dict.edit,/dict/lemma/', ['only' => 'create','store','edit','update','destroy']);
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
                               'lemmas' => $lemmas,
                               'lemma_name' => $lemma_name,
                               'lang_values' => $lang_values,
                               'lang_id'=>$lang_id,
                               'pos_values' => $pos_values,
                               'pos_id'=>$pos_id,
                               'numAll' => $numAll,
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

        return view('dict.lemma.show')
                  ->with(['lemma'=>$lemma,
                          'meaning_texts' => $meaning_texts,
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
                                
        return view('dict.lemma.edit')
                  ->with(array('lemma' => $lemma,
                               'lang_values' => $lang_values,
                               'pos_values' => $pos_values,
                               'gramset_values' => $gramset_values,
                               'langs_for_meaning' => $langs_for_meaning,
                                'new_meaning_n' => $new_meaning_n
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
        if($request->lang_wordforms && is_array($request->lang_wordforms)) {
            foreach($request->lang_wordforms as $gramset_id=>$wordform_text) {
                if ($wordform_text) {
                    $wordform_obj = Wordform::firstOrCreate(['wordform'=>$wordform_text]);
                    $lemma-> wordforms()->attach($wordform_obj->id, ['gramset_id'=>$gramset_id, 'dialect_id'=>NULL]);
                }
            }
        }
 // add check-out for dublicates wordforms (several gramsets, one of them NULL) in lemma_wordform
 // must not records with (the_same_lemma, the_same_wordform, the_same_dialect, some_gramset) 
 //                   and (the_same_lemma, the_same_wordform, the_same_dialect, NULL) 
        //add wordforms without gramsets
        if($request->empty_wordforms && is_array($request->empty_wordforms)) {
            foreach($request->empty_wordforms as $wordform_info) {
                if ($wordform_info['wordform']) {
                    $wordform_obj = Wordform::firstOrCreate(['wordform'=>$wordform_info['wordform']]);
                    $lemma-> wordforms()->attach($wordform_obj->id, ['gramset_id'=>$wordform_info['gramset'], 'dialect_id'=>'NULL']);
                }
            }
        }
               
//$wordform_ids = array(1,2,3)        
//$lemma->wordforms()->attach($wordform_ids) 
 
        // MEANINGS UPDATING
        // existing meanings
        Meaning::updateLemmaMeanings($request->ex_meanings);
        /*
        if ($request->ex_meanings && is_array($request->ex_meanings)) {
            foreach ($request->ex_meanings as $meaning_id => $meaning) {
                $meaning_obj = Meaning::find($meaning_id);
                
                foreach ($meaning['meaning_text'] as $lang=>$meaning_text) {   
                    if ($meaning_text) {
                        $meaning_text_obj = MeaningText::firstOrCreate(['meaning_id' => $meaning_id, 'lang_id' => $lang]);
                        $meaning_text_obj -> meaning_text = $meaning_text;
                        $meaning_text_obj -> save(); 
                    } else {
                        // delete if meaning_text exists in DB but it's empty in form
                        $meaning_text_obj = MeaningText::where('meaning_id',$meaning_id)->where('lang_id',$lang)->first();
                        if ($meaning_text_obj) {
                            $meaning_text_obj -> delete();
                        }    
                    }
                }
                
                if ($meaning_obj->meaningTexts()->count()) { // is meaning has any meaning texts
                    $meaning_obj -> meaning_n = $meaning['meaning_n'];
                    $meaning_obj -> save();                    
                } else {
                    $meaning_obj -> delete();
                }
            }
        }*/

        // new meanings, i.e. meanings created by user in form now
        Meaning::storeLemmaMeanings($request->new_meanings, $id);
               
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
                    $result['error_message'] = \Lang::get('record_not_exists');
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
    
    
    /** Gets list of longest lemmas, 
     * gets first N lemmas sorted by length.
     *  
     */
    public function sortedByLength(Request $request)
    {
        $limit_num = (int)$request->input('limit_num');
        
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
                               'lemmas' => $lemmas,
                               'numAll' => $numAll
                              )
                        );
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
