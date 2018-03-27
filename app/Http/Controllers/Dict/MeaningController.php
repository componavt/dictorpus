<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use DB;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Dict\Relation;

class MeaningController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:ref.edit,/dict/meaning/', 
                ['only' => ['create','store','edit','update','destroy',
                            'createRelation']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createRelation($id,$relation_id)
    {
        $relation_text = Relation::find($relation_id)->name;
        return view('dict.lemma._form_new_relation')
                  ->with(array('meaning_id' => $id,
                               'relation_id' => $relation_id,
                               'relation_text' => $relation_text
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
    
    
    /** 
     * (1) Copy vepsian.{meaning} to vepkar.meanings (without meaning_text)
     * (2) Copy vepsian.{meaning.meaning_text, translation_lemma} to vepkar.meaning_texts
     */
/*    
    public function tempInsertVepsianMeanings()
    {
        $meanings = DB::connection('vepsian')->table('meaning')->orderBy('id')->get();
 
     
        DB::connection('mysql')->table('meaning_texts')->delete();
        DB::connection('mysql')->statement('ALTER TABLE meaning_texts AUTO_INCREMENT = 1');
        
        DB::connection('mysql')->table('meanings')->delete();
        DB::connection('mysql')->statement('ALTER TABLE meanings AUTO_INCREMENT = 1');
        
        foreach ($meanings as $meaning):
            DB::connection('mysql')->table('meanings')->insert([
                    'id' => $meaning->id,
                    'lemma_id' => $meaning->lemma_id,
                    'meaning_n' => $meaning->meaning_n,
                    'created_at' => $meaning -> modified,
                    'updated_at' => $meaning -> modified
                ]
            );
            
            if ($meaning->meaning_text) {
                DB::connection('mysql')->table('meaning_texts')->insert([
                        'meaning_id' => $meaning->id,
                        'lang_id' => 1,
                        'meaning_text' => $meaning->meaning_text,
                        'created_at' => $meaning -> modified,
                        'updated_at' => $meaning -> modified
                    ]
                );
            }
            
            $translations = DB::connection('vepsian')->table('translation')
                             -> where('meaning_id',$meaning->id)->get();
            if (!$translations) {
                continue;
            }       
            
            foreach ($translations as $translation) {
                $translation_lemma_id = $translation-> translation_lemma_id;

                $translation_lemma = DB::connection('vepsian')
                                       ->table('translation_lemma')
                                       ->where('id',$translation_lemma_id)->first();
                if (!$translation_lemma) {
                    continue;
                } 

                if ($translation_lemma->lemma) {
                    DB::connection('mysql')->table('meaning_texts')->insert([
                            'meaning_id' => $meaning->id,
                            'lang_id' => $translation_lemma->lang_id,
                            'meaning_text' => $translation_lemma->lemma,
                            'created_at' => $meaning -> modified,
                            'updated_at' => $meaning -> modified
                        ]
                    );
                }
            }       
        endforeach;
    }
 */    
    
    
}
