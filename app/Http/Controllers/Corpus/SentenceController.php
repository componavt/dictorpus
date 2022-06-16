<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

//use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Library\Str;

use App\Models\Corpus\Corpus;
use App\Models\Corpus\Genre;
use App\Models\Corpus\Sentence;
use App\Models\Corpus\Text;

use App\Models\Dict\Dialect;
use App\Models\Dict\Gram;
use App\Models\Dict\Lang;
use App\Models\Dict\PartOfSpeech;

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
        $pos_values = PartOfSpeech::getListForCorpus();
        $gram_values = Gram::getListForCorpus();
//dd($gram_values);        
        return view('corpus.sentence.index',
                compact('corpus_values', 'dialect_values', 'genre_values', 'gram_values',
                        'lang_values', 'pos_values', 'args_by_get', 'url_args'));
    }

    /**
     *
     * @return \Illuminate\Http\Response
     */
    public function results()
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        $url_args['words'] = Sentence::preparedWordsForSearch($url_args['search_words']);
//dd($url_args['words']);        
        $search_query=Sentence::searchQueryToString($url_args);
//dd($search_query);        
        $entry_number = $numAll = 0;
        $texts = null;
        if (!sizeof($url_args['words'])) {
            $refine = true; // отправляем уточнить запрос, без слов искать не будем
        } else {
            $refine = false;
            list($entry_number, $sentence_builder) = Sentence::entryNumber($url_args); // считаем количество вхождений
//dd($sentence_builder->get());            
            if ($entry_number>0) {
//                $texts = Text::searchWithSentences($url_args); // выбираем тексты
                $texts = Text::whereIn('id', $sentence_builder->pluck('t1.text_id'));
                $numAll = $texts->count();
                $texts = $texts->paginate($this->url_args['limit_num']);
                $text_sentences =[];
                foreach ($sentence_builder->get() as $sentence) {
                    $text_sentences[$sentence->text_id][] = $sentence->s_id;
                }
            }
        }      
        return view('corpus.sentence.results',
                compact('texts', 'numAll', 'entry_number', 'refine',
                        'search_query', 'text_sentences', 'args_by_get', 'url_args'));
    }

    public function wordGramForm(Request $request)
    {
        $count = (int)$request->input('count');
                                
        return view('corpus.sentence._search_word_form',
                 compact('count'));
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
    public function show($id, Request $request)
    {
        $sentence = Sentence::findOrfail($id);
        $with_left_context = (int)$request->input('with_left_context');
        $with_right_context = (int)$request->input('with_right_context');
        $for_view = true;
        return view('corpus.sentence.show', 
                compact('sentence', 'for_view', 'with_left_context', 'with_right_context'));
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
            $error_message = $text->updateMeaningAndWordformText($sentence, $text_xml);
            if ($error_message) {
                return $error_message;
            }
        }
        $with_edit = true;
        return view('corpus.sentence.show', compact('text', 'sentence', 'with_edit'));
    }

    public function markup($id)
    {
        $sentence = Sentence::findOrfail($id);        
        $text = $sentence->text;
        
        $text_xml = $sentence->text_xml;
        $error_message = $text->updateMeaningAndWordformText($sentence, $text_xml, true);
        if ($error_message) {
            return $error_message;
        }
        $sentence_xml = $sentence->text_xml; 
        $with_edit = true;
        return view('corpus.sentence.show', compact('text', 'sentence', 'with_edit'));
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
