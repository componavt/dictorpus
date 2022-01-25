<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

//use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Corpus\Sentence;
use App\Models\Corpus\SentenceTranslation;

use App\Models\Dict\Lang;

class SentenceTranslationController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // permission= corpus.edit, redirect failed users to /corpus/sentence/, authorized actions list:
        $this->middleware('auth:corpus.edit,/corpus/sentence/', 
                         ['only' => ['create','store','edit','update','destroy']]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(int $sentence_id, int $lang_id)
    {
        $lang_name = Lang::getNameByID($lang_id);
        if (!$lang_name) {
            return;
        }
        
        $sentence = Sentence::find($sentence_id);
        if (!$sentence) {
            return;
        }
        
        if (SentenceTranslation::getByLangId($sentence_id, $lang_id)) {
            return;
        }
                
        return view('corpus.sentence.translation._create', 
                compact('sentence_id', 'lang_id', 'lang_name'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(int $sentence_id, int $lang_id, Request $request)
    {
        $text = $request->input('text');
        if (!$text) { return; }
        
        $lang_name = Lang::getNameByID($lang_id);
        if (!$lang_name) { return; }
        
        $sentence = Sentence::find($sentence_id);
        if (!$sentence) { return; }
        
        $translation = SentenceTranslation::getByLangId($sentence_id, $lang_id);
//var_dump($sentence_id);        
        if (!$translation) {
            $translation = SentenceTranslation::create(['sentence_id'=>$sentence_id, 'lang_id'=>$lang_id, 'text'=>$request->input('text')]);
        }
//var_dump($sentence_id);        
//dd($translation->sentence_id);        
        return view('corpus.sentence.translation.view', 
                compact('translation', 'lang_name'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $sentence_id, int $lang_id, Request $request)
    {
        $translation = SentenceTranslation::getByLangId($sentence_id, $lang_id);
        if (!$translation) { return; }
        $translation_text = $translation->text;
        
        $lang_name = Lang::getNameByID($lang_id);
        if (!$lang_name) { return; }
               
        $action = 'update';
        
        return view('corpus.sentence.translation._form_create_edit', 
                compact('action', 'sentence_id', 'lang_id', 'lang_name', 'translation_text'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(int $sentence_id, int $lang_id, Request $request)
    {
        $text = $request->input('text');
        
        $lang_name = Lang::getNameByID($lang_id);
        if (!$lang_name) { return; }
        
        $translation = SentenceTranslation::getByLangId($sentence_id, $lang_id);
        if (!$translation) { return; }
        
        if (!$text) {
            $translation->delete();
            return;
        }
        $translation->text = $text;
        $translation->save();
        
        return view('corpus.sentence.translation.view', 
                compact('translation', 'lang_name'));
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
}
