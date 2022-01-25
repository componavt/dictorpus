<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

//use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Corpus\Sentence;
use App\Models\Corpus\SentenceFragment;

class SentenceFragmentController extends Controller
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
/*    public function create(int $id, Request $request)
    {
        $lang_id = (int)$request->input('lang_id');
        $lang_name = Lang::getNameByID($lang_id);
        if (!$lang_name) {
            return;
        }
        
        $sentence = Sentence::find($id);
        if (!$sentence) {
            return;
        }
        
        if (SentenceFragment::whereId($id)->whereLangId($lang_id)->count()>0) {
            return;
        }
        
        $action = 'create';
        
        return view('corpus.sentence.fragment._form_create_edit', 
                compact('action', 'id', 'lang_id', 'lang_name'));
    }*/

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
/*    public function store(int $id, Request $request)
    {
        $text = $request->input('text');
        if (!$text) { return; }
        
        $lang_id = (int)$request->input('lang_id');
        $lang_name = Lang::getNameByID($lang_id);
        if (!$lang_name) { return; }
        
        $sentence = Sentence::find($id);
        if (!$sentence) { return; }
        
        $fragment = SentenceFragment::whereId($id)->whereLangId($lang_id)->first();
        if (!$fragment) {
            $fragment = SentenceFragment::create(['id'=>$id, 'lang_id'=>$lang_id, 'text'=>$request->input('text')]);
        }
        return view('corpus.sentence.fragment.view', 
                compact('fragment', 'lang_name'));
    }*/

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $sentence = Sentence::find($id);
        if (!$sentence) { return; }
        
        $fragment = SentenceFragment::find($id);
        $fragment_text = $fragment ? $fragment->text_xml : $sentence->text_xml;
        
        return view('corpus.sentence.fragment._form_create_edit', 
                compact('id', 'fragment_text'));
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
        $text_xml = $request->input('text_xml');
        
        $fragment = SentenceFragment::find($id);
        if ($fragment) {
            if (!$text_xml) {        
                $fragment->delete();
                $fragment = null;
            } else {
                $fragment->text_xml = $text_xml;
                $fragment->save();
            }
        } elseif (!$text_xml) {
            return;
        } else {    
            $fragment = SentenceFragment::create(['id'=>$id, 'text_xml' => $text_xml]);
        }
        
        return view('dict.lemma.example._fragment', 
                compact('fragment', 'id'));
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
