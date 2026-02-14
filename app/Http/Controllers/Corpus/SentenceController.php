<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

//use App\Http\Requests;
use App\Http\Controllers\Controller;

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
        
        $this->args_by_get = search_values_by_URL($this->url_args);
    }

    /**
     * Лексико-грамматический поиск
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
     * Результат лексико-грамматического поиска
     *
     * @return \Illuminate\Http\Response
     */
    public function results(Request $request)
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        $script_start = microtime(true);

        $url_args['words'] = Sentence::preparedWordsForSearch($url_args['search_words']);
//dd($url_args['words']);        
        $search_query=Sentence::searchQueryToString($url_args);
        $entry_number = $numAll = 0;
        $text_sentences =[];
        $texts = null;
        if (!sizeof($url_args['words'])) {
            $refine = $sentences = true; // отправляем уточнить запрос, без слов искать не будем
        } else {
            $refine = false;
            list($entry_number, $sentences, $is_limited) = Sentence::entryNumber($url_args); // считаем количество вхождений
//dd($entry_number);   
//dd(to_sql($sentences));
//dd($sentences);
//dd($sentences->pluck('text1_id')->unique()->get());
            if ($entry_number>0) {
                $texts = Text::whereIn('id', $sentences->pluck('text1_id')); //->unique()
                $numAll = $texts->count();
//dd($numAll);                
                $texts = $texts->paginate($this->url_args['limit_num']);
//dd($texts);       
                foreach($texts as $text) {                
                    foreach ($sentences->where('text1_id', $text->id) as $sentence) {
//dd(($sentences->where('text_id', $text->id)->get()));                        
                        $sentence_id = $sentence->sentence1_id;

                        if (!isset($text_sentences[$text->id]['sentences'][$sentence_id])) {
                            $text_sentences[$text->id]['sentences'][$sentence_id] = Sentence::find($sentence_id);
                        }

                        for ($i = 1; $i <= sizeof($url_args['words']); $i++) {
                            $text_sentences[$text->id]['words'][] = $sentence->{'w'.$i.'_id'};
                        }
                    }
                }
            }
//dd($text_sentences);            
        }      
        return view('corpus.sentence.results',
                compact('entry_number', 'is_limited', 'numAll', 'refine', 'script_start',
                        'texts', 'search_query', 'sentences', 'args_by_get', 'url_args'));
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
        list($meanings_by_wid, $gramsets_by_wid, $wordforms, $words_with_important_examples) 
                = $sentence->text->meaningsGramsetsByWid($sentence->words()->pluck('w_id'));
//dd($meanings_by_wid, $gramsets_by_wid, $wordforms, $words_with_important_examples);        
        return view('corpus.sentence.show', 
                compact('sentence', 'for_view', 'with_left_context', 'with_right_context',
                        'meanings_by_wid', 'gramsets_by_wid', 'wordforms', 'words_with_important_examples'));
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
        list($meanings_by_wid, $gramsets_by_wid, $wordforms, $words_with_important_examples) 
                = $sentence->text->meaningsGramsetsByWid($sentence->words()->pluck('w_id'));
        return view('corpus.sentence.show', 
                compact('text', 'sentence', 'with_edit', 'meanings_by_wid', 
                        'gramsets_by_wid', 'wordforms', 'words_with_important_examples'));
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
