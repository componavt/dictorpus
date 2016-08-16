<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use DB;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Dict\Lemma;
use App\Models\Dict\Lang;
use App\Models\Dict\PartOfSpeech;

class LemmaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit_num = $request->input('limit_num');

        if ($limit_num<=0) {
            $limit_num = 10;
        } elseif ($limit_num>1000) {
            $limit_num = 1000;
        }      

        
        $limit_num = (int)$limit_num;
        $lemmas = Lemma::orderBy('lemma')->take($limit_num)->get();        

// print "<pre>";        
        if ($lemmas) {
            foreach ($lemmas as $lemma) { 
                $lang_obj = Lang::where('id', $lemma->lang_id)->first();
                if($lang_obj) {
                    $lemma->lang = $lang_obj->getNameAttribute();
                } else {
                    $lemma->lang = '';
                }
                
                $pos_obj = //$lemma -> pos();
                        PartOfSpeech::where('id', $lemma->pos_id)->first();
                // var_dump($pos_obj);
                if ($pos_obj) {
                    $lemma->pos = //$lemma->pos_id;
$pos_obj->getNameAttribute();
                } else {
                    $lemma->pos = '';
                }
            }            
        }
        return view('dict.lemma.index')
                  ->with(array('limit_num' => $limit_num,
                               'lemmas' => $lemmas,
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
        
        //select * from lemmas order by char_length(lemma) DESC limit 10;
        //$lemmas = Lemma::orderBy(char_length('lemma'), 'desc')
        //               ->take($limit_num)->get();
        $lemmas = DB::select('select * from lemmas order by char_length(lemma) '
                           . 'DESC limit :limit', ['limit'=>$limit_num]);
         
        if ($lemmas) {
            foreach ($lemmas as $lemma) { 
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
            }            
        }
        return view('dict.lemma.sorted_by_length')
                  ->with(array('limit_num' => $limit_num,
                               'lemmas' => $lemmas,
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
 
     
        //DB::connection('mysql')->table('lemmas')->truncate();
        
        foreach ($lemmas as $lemma) {
            DB::connection('mysql')->table('lemmas')->insert([
                    'id' => $lemma->id,
                    'lemma' => $lemma->lemma,
                    'lang_id' => 1,
                    'pos_id' => $lemma->pos_id,
                    'created_at' => $lemma -> modified,
                    'updated_at' => $lemma -> modified,
                    'temp_translation_lemma_id' => 0
                ]
            );
        }
         
        //DB::connection('mysql')->table('lemmas')->delete('delete from lemmas where id>2932');
        $trans_lemmas = DB::connection('vepsian')->table('translation_lemma')->orderBy('id')->get();

        foreach ($trans_lemmas as $trans_lemma) {
            $translation = DB::connection('vepsian')->table('translation')-> where('translation_lemma_id',$trans_lemma->id)->first();
            if (!$translation) {
                continue;
            }
            $meaning_id = $translation->meaning_id;
            
            $meaning = DB::connection('vepsian')->table('meaning')-> where('id',$meaning_id)->first();
            if (!$meaning) {
                continue;
            }
            $lemma_id = $meaning->lemma_id;
            
            $lemma = DB::connection('vepsian')->table('lemma')-> where('id',$lemma_id)->first();
            if (!$lemma) {
                continue;
            }
            $pos_id = $lemma->pos_id;
            
            DB::connection('mysql')->table('lemmas')->insert([
                    'lemma' => $trans_lemma->lemma,
                    'lang_id' => $trans_lemma->lang_id,
                    'pos_id' => $pos_id,
                    'temp_translation_lemma_id' => $trans_lemma->id
                ]
            );
        
        }
 
//        return view("home");
    }
     */
}
