<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

//use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Library\Str;

use App\Models\Corpus\Corpus;
use App\Models\Corpus\Genre;
use App\Models\Corpus\Sentence;

use App\Models\Dict\Dialect;
use App\Models\Dict\Lang;

class SentenceController extends Controller
{
    public $url_args=[];
    public $args_by_get='';
    
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // permission= corpus.edit, redirect failed users to /corpus/sentence/, authorized actions list:
        $this->middleware('auth:corpus.edit,/corpus/sentence/', 
                         ['only' => ['create','store','edit','update','destroy','markup']]);
        $this->url_args = Sentence::urlArgs($request);  
        
        $this->args_by_get = Str::searchValuesByURL($this->url_args);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        $corpus_values = Corpus::getListWithQuantity('texts');
        $lang_values = Lang::getListWithQuantity('texts');        
        $dialect_values = Dialect::getList();
        $genre_values = Genre::getList();
        
        return view('corpus.sentence.index',
                compact('corpus_values', 'dialect_values', 'genre_values', 
                        'lang_values', 'args_by_get', 'url_args'));
    }

    /**
     *
     * @return \Illuminate\Http\Response
     */
    public function results()
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        $texts = Sentence::search($url_args);

        $numAll = $texts->count();

        $texts = $texts->paginate($this->url_args['limit_num']);
        
        return view('corpus.sentence.results',
                compact('texts', 'numAll', 'args_by_get', 'url_args'));
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
    public function edit(int $id)
    {
        $sentence = Sentence::findOrfail($id);
        return view('corpus.sentence.edit', compact('sentence'));
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
        $sentence = Sentence::findOrfail($id);        
        $text = $sentence->text;
        
        $text_xml = $request->input('text_xml');
        if ($text_xml && $sentence->text_xml != $text_xml) {
            $sentence->text_xml = $text_xml;
            $sentence->save();
            $error_message = $text->updateMeaningAndWordformText($sentence->s_id, $text_xml);
            if ($error_message) {
                return $error_message;
            }
        }
        return view('corpus.sentence.show', compact('sentence', 'text'));
    }

    public function markup($id)
    {
        $sentence = Sentence::findOrfail($id);        
        $text = $sentence->text;
        
        $text_xml = $sentence->text_xml;
        $error_message = $text->updateMeaningAndWordformText($sentence->s_id, $text_xml, true);
        if ($error_message) {
            return $error_message;
        }
        return view('corpus.sentence.show', compact('sentence', 'text'));
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
