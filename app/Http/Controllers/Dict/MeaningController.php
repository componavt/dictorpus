<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
//use DB;

//use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\User;

use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\Meaning;
use App\Models\Dict\Relation;

use App\Models\Corpus\MeaningTextRel;

class MeaningController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:dict.add,/dict/meaning/', 
                ['only' => ['create', 'store']]);
        $this->middleware('auth:dict.edit,/dict/meaning/', 
                ['except' => ['create', 'store', 'index', 'loadExamples', 'reloadExamples', 'loadMoreExamples', 'photo']]);
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
     * Shows the form for creating a new resource.
     * 
     * Called by ajax request
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $count = (int)$request->input('count');
        $meaning_n = (int)$request->input('meaning_n');
//        $langs_for_meaning = Lang::getListInterface();
        $langs_for_meaning = Lang::getListForMeaning();
                                
        return view('dict.meaning.form._create')
                  ->with(array('count' => $count,
                               'new_meaning_n' => $meaning_n,
                               'langs_for_meaning' => $langs_for_meaning
                              )
                        );
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
                  ->with(['meaning_id' => $id,
                          'relation_id' => $relation_id,
                          'relation_text' => $relation_text                           
                         ]);
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
     * /dict/meaning/example/add/1418_5_59_3093
     * 
     * @param type $example_id
     * @return string
     */
    public function addExample($example_id, $relevance)
    {
        if ($relevance != 10) {
            $relevance = 5;
        }
        MeaningTextRel::updateExamples([$example_id=>$relevance]);
        return '<span class="glyphicon glyphicon-star relevance-'.$relevance.'"></span>';
    }

    /**
     * test: /dict/meaning/examples/reload/23813
     * 
     * @param INT $id
     * @return \Illuminate\Http\Response
     */
    public function loadExamples (int $id, Request $request) {
        $limit = 5;
        $start = (int)$request->input('start');
        $update_examples = (int)$request->input('update_examples');
        $meaning = Meaning::find($id);
        if (!$meaning) {
            return NULL;
        }
        
        if (User::checkAccess('dict.edit') && ($update_examples || !$meaning->texts()->count())) {
            $meaning->reloadExamples();
        }
        
        $sentence_count = $meaning->countSentences(false);
        $sentence_total = $meaning->countSentences(true);
        $sentences = $meaning->sentences(false, $limit, $start);
        $count=1+$start;   
//dd($sentences);        
        return view('dict.lemma.example.all', 
                compact('meaning', 'limit', 'start', 'count',
                        'sentence_count', 'sentence_total', 'sentences')); 
    }

    public function loadMoreExamples (int $id, Request $request) {
        $limit = 5;
        $start = (int)$request->input('start');
        $meaning = Meaning::find($id);
        if (!$meaning) {
            return NULL;
        }
        
        $sentence_count = $meaning->countSentences(false);
        $sentences = $meaning->sentences(false, $limit, $start);
        $count=1+$start;   
        
        return view('dict.lemma.example._limit', 
                compact('meaning', 'limit', 'start', 'count',
                        'sentence_count', 'sentences')); 
    }
    
    public function removeLabel(int $meaning_id, int $label_id) {
        $meaning = Meaning::find($meaning_id);
        if ($meaning) {
            $meaning->labels()->detach($label_id);
            return 1;
        }
        return 0;
    }
    
    public function photo(int $meaning_id) {
        $meaning = Meaning::find($meaning_id);
        if (!$meaning->concepts()->count() || !$meaning->concepts[0]->wiki_photo) {
            return;
        }
        $photo = $meaning->photoInfo();
        if (!$photo) {
            return view('dict.concept._photo_reload', with(['obj'=>'meaning', 'id'=>$meaning_id,
                'url'=>'/dict/meaning/'.$meaning_id.'/photo']));
        }
        return view('dict.concept._photo_preview', compact('photo'));
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
