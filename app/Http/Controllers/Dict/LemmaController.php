<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use DB;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Dict\Lemma;
use App\Models\Dict\Lang;
use App\Models\Dict\PartOfSpeech;
use App\Models\Dict\Meaning;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class LemmaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit_num = (int)$request->input('limit_num');
        $lang_id = (int)$request->input('lang_id');
        $pos_id = (int)$request->input('pos_id');

        if ($limit_num<=0) {
            $limit_num = 10;
        } elseif ($limit_num>1000) {
            $limit_num = 1000;
        }      
        
        $lemmas = Lemma::orderBy('lemma');
        
        if ($lang_id) {
            $lemmas = $lemmas->where('lang_id',$lang_id);
        } 
         
        if ($pos_id) {
            $lemmas = $lemmas->where('pos_id',$pos_id);
        } 
         
         $lemmas = $lemmas->take($limit_num)
                       ->with(['meanings'=> function ($query) {
                                    $query->orderBy('meaning_n');
                                }])->get();
        
        $pos_values = PartOfSpeech::getGroupedList();   
        $lang_values = Lang::getList();
                                
        return view('dict.lemma.index')
                  ->with(array('limit_num' => $limit_num,
                               'lemmas' => $lemmas,
                               'lang_values' => $lang_values,
                               'lang_id'=>$lang_id,
                               'pos_values' => $pos_values,
                               'pos_id'=>$pos_id
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
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $lemma_obj = Lemma::find($id);
               
        return view('dict.lemma.show')->with(['lemma'=>$lemma_obj]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!User::checkAccess('dict.edit'))
            return Redirect::to('/')
                    ->withErrors(\Lang::get('error.permission_denied'));
        print 'Прилетели';
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
        //               ->take($limit_num)->get();
        $lemmas = DB::select('select * from lemmas order by char_length(lemma) '
                           . 'DESC limit :limit', ['limit'=>$limit_num]);
         
        $out_lemmas = array();
        if ($lemmas) {
            foreach ($lemmas as $lemma) { 
                $out_lemmas[] = Lemma::find($lemma->id);
            }
 /*               
                $lang_obj = Lang::where('id', $lemma->lang_id)->first();
                if($lang_obj) {
                    $lemma->lang = $lang_obj->getNameAttribute();
                } else {
                    $lemma->lang = '';
                }
                
                $pos_obj = PartOfSpeech::where('id', $lemma->pos_id)->first();
                if ($pos_obj) {
                    $lemma->pos = $pos_obj->getNameAttribute();
                } else {
                    $lemma->pos = '';
                }
            }  */          
        }
        return view('dict.lemma.sorted_by_length')
                  ->with(array('limit_num' => $limit_num,
                               'lemmas' => $out_lemmas,
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
