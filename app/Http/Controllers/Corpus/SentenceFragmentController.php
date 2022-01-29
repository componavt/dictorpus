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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $sentence_id, int $w_id)
    {
        $sentence = Sentence::find($sentence_id);
        if (!$sentence) { return; }
        
        $fragment = SentenceFragment::getBySW($sentence_id, $w_id);
        $fragment_text = $fragment ? $fragment->text_xml : $sentence->text_xml;
        
        return view('corpus.sentence.fragment._form_create_edit', 
                compact('sentence_id', 'w_id', 'fragment_text'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $sentence_id, int $w_id)
    {
        $text_xml = $request->input('text_xml');
        
        $fragment = SentenceFragment::getBySW($sentence_id, $w_id);
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
            $fragment = SentenceFragment::create(['sentence_id'=>$sentence_id, 
                                    'w_id'=>$w_id, 'text_xml' => $text_xml]);
        }        
        return view('dict.lemma.example._fragment', 
                compact('fragment', 'sentence_id', 'w_id'));
    }
}
