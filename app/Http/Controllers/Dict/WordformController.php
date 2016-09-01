<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use DB;

use App\Http\Requests;
use App\Http\Controllers\Controller;

//use App\Models\Dict\Gramset;
use App\Models\Dict\Lemma;
use App\Models\Dict\Lang;
use App\Models\Dict\PartOfSpeech;
use App\Models\Dict\Wordform;

class WordformController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $wordform_name = $request->input('wordform_name');
        $limit_num = (int)$request->input('limit_num');
        $lang_id = (int)$request->input('lang_id');
        $pos_id = (int)$request->input('pos_id');
        $page = (int)$request->input('page');

        if (!$page) {
            $page = 1;
        }
        
        if ($limit_num<=0) {
            $limit_num = 10;
        } elseif ($limit_num>1000) {
            $limit_num = 1000;
        }      
        
        $wordforms = Wordform::orderBy('wordform');
        
        if ($wordform_name) {
            $wordforms = $wordforms->where('wordform','like', $wordform_name);
        } 

        if ($lang_id) {
            $wordforms = $wordforms->where('lang_id',$lang_id);
        } 
         
        if ($pos_id) {
            $wordforms = $wordforms->where('pos_id',$pos_id);
        } 
         
        $numAll = $wordforms->count();
        
        $wordforms = $wordforms->paginate($limit_num);
                //take($limit_num)->get();
        /*
                       ->with(['meanings'=> function ($query) {
                                    $query->orderBy('meaning_n');
                                }])*/
        
        $pos_values = PartOfSpeech::getGroupedList();
//        $pos_values = PartOfSpeech::getGroupedListWithQuantity('wordforms');
       
        $lang_values = Lang::getList();
        //$lang_values = Lang::getListWithQuantity('wordforms');
                                
 
        return view('dict.wordform.index')
                  ->with(array('limit_num' => $limit_num,
                               'wordforms' => $wordforms,
                               'wordform_name' => $wordform_name,
                               'page'=>$page,
                               'lang_values' => $lang_values,
                               'lang_id'=>$lang_id,
                               'pos_values' => $pos_values,
                               'pos_id'=>$pos_id,
                               'numAll' => $numAll
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
    
    /** Lists wordforms associated with more than one lemma.
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function withMultipleLemmas(Request $request)
    {
        $wordform_name = $request->input('wordform_name');
        $lang_id = (int)$request->input('lang_id');

//select wordforms.wordform as wordform, count(*) as count from lemma_wordform, wordforms where 
//wordforms.id=lemma_wordform.wordform_id and lemma_id in (select id from lemmas where lang_id=1) 
//group by wordform having count>1 order by count;
        $builder = DB::table('wordforms')
                     ->join('lemma_wordform', 'wordforms.id', '=', 'lemma_wordform.wordform_id')
                     ->select(DB::raw('wordform_id, count(*) as count'))
                     ->groupBy('wordform_id')
                     ->having('count', '>', 1);
        
        if ($wordform_name) {
            $builder = $builder->where('wordform','like', $wordform_name);
        } 

        if ($lang_id) {
            $builder = $builder->whereIn('lemma_id',function($query) use ($lang_id){
                        $query->select('id')
                        ->from(with(new Lemma)->getTable())
                        ->where('lang_id', $lang_id);
                    });
        } 
         
        $builder = $builder->orderBy('count', 'DESC')
                           ->orderBy('wordform');
  /*      $builder = $builder->with(['wordforms'=> function ($query) {
                                    $query->orderBy('wordform');
                                }]);*/
        $wordforms = $builder->get();
           
        $lang_values = Lang::getList();
                                
        return view('dict.wordform.with_multiple_lemmas')
                  ->with(array(
                               'wordforms' => $wordforms,
                               'wordform_name' => $wordform_name,
                               'lang_values' => $lang_values,
                               'lang_id'=>$lang_id,
                               )
                        );
    }
    
    /** 
     * (1) Copy vepsian.wordform to vepkar.wordforms (without dublicates)
     * (2) Copy vepsian.lemma_gram_wordform to vepkar.lemma_wordform
     */
/*    public function tempInsertVepsianWordform()
    {
        $lemma_wordfoms = DB::connection('vepsian')
                            ->table('lemma_gram_wordform')
                            ->orderBy('lemma_id','wordform_id')
                            //->take(1)
                            ->get();
 
        DB::connection('mysql')->table('lemma_wordform')->delete();

        DB::connection('mysql')->table('wordforms')->delete();
        DB::connection('mysql')->statement('ALTER TABLE wordforms AUTO_INCREMENT = 1');
        
        
        foreach ($lemma_wordfoms as $lemma_wordform):
            $veps_wordform = DB::connection('vepsian')
                            ->table('wordform')
                            ->find($lemma_wordform->wordform_id);
            $wordform = Wordform::firstOrNew(['wordform' => $veps_wordform->wordform]); 
            $wordform->updated_at = $veps_wordform->modified;
            $wordform->created_at = $veps_wordform->modified;
            $wordform->save();
            
            if ($lemma_wordform->gram_set_id === 0) {
                $lemma_wordform->gram_set_id = NULL;
            }
            
            DB::connection('mysql')->table('lemma_wordform')->insert([
                    'lemma_id' => $lemma_wordform->lemma_id,
                    'wordform_id' => $wordform->id,
                    'gramset_id' => $lemma_wordform->gram_set_id,
                    //'created_at' => $wordform->updated_at,
                    //'updated_at' => $wordform->created_at
                ]
            );
                
        endforeach;
     }
 *
 */  
}

// a lemma and word form related by more than once
// select lemma_id, wordforms.wordform as wordform, count(*) as count from lemma_wordform,wordforms where wordforms.id=lemma_wordform.wordform_id group by lemma_id, wordform having count>1;
// select wordforms.wordform as wordform, count(*) as count from lemma_wordform, wordforms where wordforms.id=lemma_wordform.wordform_id group by wordform having count>1 order by count;