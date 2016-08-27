<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use DB;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Dict\Gramset;
use App\Models\Dict\Lemma;
use App\Models\Dict\Lang;
use App\Models\Dict\Meaning;
use App\Models\Dict\PartOfSpeech;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class LemmaController extends Controller
{
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
        $pos_values = PartOfSpeech::getGroupedList();   
        $lang_values = Lang::getList();
                                
        return view('dict.lemma.create')
                  ->with(array('lang_values' => $lang_values,
                               'pos_values' => $pos_values,
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
            'pos_id' => 'numeric',
        ]);
        
        $lemma= new Lemma;
        $lemma->lemma = $request->lemma;
        $lemma->lang_id = $request->lang_id;
        $lemma->pos_id = $request->pos_id;
        $lemma->save();
	
//        return redirect('/dict/lemma/'.($lemma->id));    
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
               
        return view('dict.lemma.show')->with(['lemma'=>$lemma]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    /*    if (!User::checkAccess('dict.edit'))
            return Redirect::to('/')
                    ->withErrors(\Lang::get('error.permission_denied'));*/
        $lemma = Lemma::find($id);
        
        $pos_values = PartOfSpeech::getGroupedList(); 
        //$pos_values = PartOfSpeech::getGroupedListWithQuantity('lemmas');
        $lang_values = Lang::getList();
        $gramset_values = ['NULL'=>'']+Gramset::getList($lemma->pos_id);
                                
        return view('dict.lemma.edit')
                  ->with(array('lemma' => $lemma,
                               'lang_values' => $lang_values,
                               'pos_values' => $pos_values,
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
        $this->validate($request, [
            'lemma'  => 'required|max:255',
            'lang_id'=> 'required|numeric',
            'pos_id' => 'numeric',
        ]);
        
        $lemma= Lemma::find($id);
        $lemma->lemma = $request->lemma;
        $lemma->lang_id = $request->lang_id;
        $lemma->pos_id = $request->pos_id;
        $lemma->save();
	
//        return redirect('/dict/lemma/'.($lemma->id));
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
        //
    }
    
    
    /** Gets list of longest lemmas, 
     * gets first N lemmas sorted by length.
     *  
     */
    public function sortedByLength(Request $request)
    {
    /**
     * @var int $numAll Total number of lemmas
     *
     */
        $limit_num = (int)$request->input('limit_num');
        
        if ($limit_num<=0) {
            $limit_num = 10;
        } elseif ($limit_num>1000) {
            $limit_num = 1000;
        }           
        
        //$lemmas = Lemma::orderBy(char_length('lemma'), 'desc')
        //               ->take($limit_num)->get();
        /*
        $lemmas = DB::select('select * from lemmas order by char_length(lemma) '
                           . 'DESC limit :limit', ['limit'=>$limit_num]);
         
        $out_lemmas = array();
        if ($lemmas) {
            foreach ($lemmas as $lemma) { 
                $out_lemmas[] = Lemma::find($lemma->id);
            }
        }
         * 
         */
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
