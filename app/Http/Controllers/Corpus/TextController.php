<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB;
use LaravelLocalization;
use Response;
use Storage;

use App\Library\Grammatic;
use App\Library\Str;

use App\Models\Corpus\Corpus;
use App\Models\Corpus\Event;
use App\Models\Corpus\Genre;
use App\Models\Corpus\Informant;
use App\Models\Corpus\Place;
use App\Models\Corpus\Recorder;
use App\Models\Corpus\Source;
use App\Models\Corpus\Text;
use App\Models\Corpus\Transtext;
use App\Models\Corpus\Word;

use App\Models\Dict\Dialect;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\Meaning;
use App\Models\Dict\PartOfSpeech;

class TextController extends Controller
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
        // permission= corpus.edit, redirect failed users to /corpus/text/, authorized actions list:
        $this->middleware('auth:corpus.edit,/corpus/text/', 
                         ['only' => ['create','store','edit','update','destroy',
                                     'addExample', 'editExample', 'updateExamples', 
                                     'markupText',
                                     'markupAllEmptyTextXML','markupAllTexts']]);
        $this->url_args = [
                    'limit_num'       => (int)$request->input('limit_num'),
                    'page'            => (int)$request->input('page'),
                    'search_birth_place' => $request->input('search_birth_place'),
                    'search_corpus'   => (array)$request->input('search_corpus'),
                    'search_dialect'  => (array)$request->input('search_dialect'),
                    'search_informant'=> $request->input('search_informant'),
                    'search_lang'     => (array)$request->input('search_lang'),
                    'search_place'    => $request->input('search_place'),
                    'search_recorder' => $request->input('search_recorder'),
                    'search_sentence' => (int)$request->input('search_sentence'),
                    'search_title'    => $request->input('search_title'),
                    'search_word'     => $request->input('search_word'),
                    'search_text'     => $request->input('search_text'),
                ];
        
        if (!$this->url_args['page']) {
            $this->url_args['page'] = 1;
        }
        
        if ($this->url_args['limit_num']<=0) {
            $this->url_args['limit_num'] = 10;
        } elseif ($this->url_args['limit_num']>1000) {
            $this->url_args['limit_num'] = 1000;
        }   
       
        $this->args_by_get = Str::searchValuesByURL($this->url_args);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {        
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        if (isset($url_args['search_dialect'][0]) && !$url_args['search_lang']) {
            $dialect = Dialect::find($url_args['search_dialect'][0]);
            if ($dialect) {
                $url_args['search_lang'] = [$dialect->lang_id];
            }
        }
        
        $texts = Text::search($url_args);

        $numAll = $texts->count();

        $texts = $texts->paginate($this->url_args['limit_num']);
        
        $corpus_values = Corpus::getListWithQuantity('texts');

        //$lang_values = Lang::getList();
        $lang_values = Lang::getListWithQuantity('texts');
        
        $dialect_values = Dialect::getList();
        $informant_values = [NULL => ''] + Informant::getList();
        $recorder_values = [NULL => ''] + Recorder::getList();

        return view('corpus.text.index',
                compact('corpus_values', 'dialect_values', 'informant_values', 
                        'lang_values', 'recorder_values', 'texts', 'numAll', 
                        'args_by_get', 'url_args'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $lang_values = Lang::getList();
        $corpus_values = Corpus::getList();
        $informant_values = [NULL => ''] + Informant::getList();
        $place_values = [NULL => ''] + Place::getList();
        $recorder_values = Recorder::getList();
        $dialect_values = Dialect::getList();
        $genre_values = Genre::getList();        
//dd($dialect_values);        
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        return view('corpus.text.create',
                  compact('corpus_values', 'dialect_values', 'genre_values',
                          'informant_values', 'lang_values', 'place_values',
                          'recorder_values', 'args_by_get', 'url_args'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title'  => 'required|max:255',
            'text'=> 'required',
            'corpus_id' => 'required|numeric',
            'lang_id' => 'required|numeric',
            'transtext.title'  => 'max:255',
            'transtext.lang_id' => 'numeric',
            'event_date' => 'numeric',
        ]);
        $request['text'] = Text::process($request['text']);
        $request['transtext_text'] = Text::process($request['transtext_text']);

        $text = Text::create($request->only('corpus_id','lang_id','title')); //,'source_id','event_id',
        $text->text = $text->processTextBeforeSaving($request->text);

        $error_message = $text -> storeAdditionInfo($request);

        $redirect = Redirect::to('/corpus/text/'.($text->id).($this->args_by_get));
        if ($error_message) {
            $redirect = $redirect->withErrors($error_message);
        } else {
            $redirect = $redirect->withSuccess(\Lang::get('messages.created_success'));
        }         
        return $redirect;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $text = Text::find($id);
       
        if (!$text) {
            return Redirect::to('/corpus/text/')
                           ->withErrors(\Lang::get('corpus.text_not_found',['id'=>$id]));            
        }
        
        $labels = [];
        
        foreach ($text->dialects as $dialect) {
            $labels[] = $dialect->name;
        }

        foreach ($text->genres as $genre) {
            $labels[] = $genre->name;
        }
        $labels = join(', ',$labels);
        $pos_values = PartOfSpeech::getGroupedList();   
        $langs_for_meaning = array_slice(Lang::getListWithPriority(),0,1,true);
        $pos_id = PartOfSpeech::getIDByCode('Noun');
        $dialect_values = Dialect::getList($text->lang_id);
        $text_dialects = $text->dialects;
        $dialect_value = isset($text_dialects[0]->id) ? $text_dialects[0]->id: 0;    
//dd($dialect_value);        
//        $dialect_value = isset($dialect_values[0]) ? $dialect_values[0]: 0;        
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        return view('corpus.text.show',
                  compact('dialect_value', 'dialect_values', 'labels', 'text', 'args_by_get', 'url_args', 
                          'pos_values', 'pos_id', 'langs_for_meaning'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $text = Text::with('transtext')->find($id); //,'event','source'
        
        $lang_values = Lang::getList();
        $corpus_values = Corpus::getList();
//        $informant_values = [NULL => ''] + Informant::getList();
        $place_values = [NULL => ''] + Place::getList();

        $informant_values = Informant::getList();
        $informant_value = $text->informantValue();

        $recorder_values = Recorder::getList();
        $recorder_value = $text->recorderValue();

        $dialect_values = Dialect::getList();
        $dialect_value = $text->dialectValue();
//dd($dialect_value);        

        $genre_values = Genre::getList();        
        $genre_value = $text->genreValue();

        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        return view('corpus.text.edit',
                compact('corpus_values', 'dialect_value', 'dialect_values',
                        'genre_value', 'genre_values', 'informant_value',
                        'informant_values','lang_values', 'place_values','text',
                        'recorder_value', 'recorder_values', 'args_by_get', 'url_args'
                        ));
    }

    /**
     * Shows the form for editing of text example for all lemma meanings connected with this sentence.
     *
     * @param  int  $id - ID of lemma
     * @param  int  $example_id - ID of example
     * @return \Illuminate\Http\Response
     */
    public function editExample($id, $example_id)
    {
        //$text = Text::find($id);
        list($sentence, $meanings, $meaning_texts) = 
                Text::preparationForExampleEdit($id.'_'.$example_id);
        
        if ($sentence == NULL) {
            return Redirect::to('/corpus/text/'.$id.($this->args_by_get))
                       ->withError(\Lang::get('messages.invalid_id'));            
        } else {
            return view('dict.lemma.edit_example')
                      ->with(array(
                                   'back_to_url'    => '/corpus/text/'.$id,
                                   'id'             => $id, 
                                   'meanings'       => $meanings,
                                   'meaning_texts'  => $meaning_texts,
                                   'route'          => array('text.update.examples', $id),
                                   'sentence'       => $sentence,
                                   'args_by_get'    => $this->args_by_get,
                                   'url_args'       => $this->url_args,
                                  )
                            );            
        }
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
        $this->validate($request, [
            'title'  => 'required|max:255',
            'text'=> 'required',
            'corpus_id' => 'required|numeric',
            'lang_id' => 'required|numeric',
            'transtext.title'  => 'max:255',
            'transtext.lang_id' => 'numeric',
//            'event_date' => 'numeric',
        ]);

        $error_message = Text::updateByID($request, $id);
        
        $redirect = Redirect::to('/corpus/text/'.$id.($this->args_by_get));
        if ($error_message) {
            return $redirect->withErrors($error_message);
        } else {
            return $redirect->withSuccess(\Lang::get('messages.updated_success'));
        }         
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateExamples(Request $request, $id)
    {
        Text::updateExamples($request['relevance']);
        return Redirect::to($request['back_to_url'].($this->args_by_get))
                       ->withSuccess(\Lang::get('messages.updated_success'));
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $error = false;
//        $status_code = 200;
        $result =[];
        if($id != "" && $id > 0) {
            try{
                $text = Text::find($id);
                if($text){
                    $text_title = Text::removeAll($text);
                    $result['message'] = \Lang::get('corpus.text_removed', ['name'=>$text_title]);
                }
                else{
                    $error = true;
                    $result['error_message'] = \Lang::get('messages.record_not_exists');
                }
          }catch(\Exception $ex){
                    $error = true;
 //                   $status_code = $ex->getCode();
                    $result['error_code'] = $ex->getCode();
                    $result['error_message'] = $ex->getMessage();
                }
        }else{
            $error =true;
//            $status_code = 400;
            $result['message']='Request data is empty';
        }
        
        if ($error) {
                return Redirect::to('/corpus/text/'.($this->args_by_get))
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/corpus/text/'.($this->args_by_get))
                  ->withSuccess($result['message']);
        }
    }
    
    /**
     * Gets list of dialects for drop down list in JSON format
     * Test url: /dict/dialect/list?lang_id=1
     * 
     * @return JSON response
     */
/*    public function dialectList(Request $request)
    {
        $dialect_name = '%'.$request->input('q').'%';
        $lang_ids = (array)$request->input('lang_id');
//        $lemma_id = (int)$request->input('lemma_id');

        $list = [];
        $dialects = Dialect::whereIn('lang_id',$lang_ids)
                       ->where(function($q) use ($dialect_name){
                            $q->where('name_en','like', $dialect_name)
                              ->orWhere('name_ru','like', $dialect_name);
                         })->orderBy('sequence_number')->get();
                         
        foreach ($dialects as $dialect) {
            $list[]=['id'  => $dialect->id, 
                     'text'=> $dialect->name];
        }  
//dd(sizeof($dialects));
        return Response::json($list);
    }

    /**
     * Shows history of text.
     *
     * @param  int  $id - ID of text
     * @return \Illuminate\Http\Response
     */
    public function history($id)
    {
        $text = Text::find($id);
        if (!$text) {
            return Redirect::to('/corpus/text/')
                           ->withErrors(\Lang::get('messages.record_not_exists'));
        }
        return view('corpus.text.history')
                  ->with(['text' => $text,
                        'args_by_get'    => $this->args_by_get,
                        'url_args'       => $this->url_args,
                          ]);
    }

    /**
     * Markup all texts and transtexts
     * /corpus/text/markup_all_texts
     * 
     * update texts set checked=0;
     * select count(*) from texts where checked=0;
     * 
     * update texts set checked=0 where lang_id=4;
     * select count(*) from texts where checked=0 and lang_id=4;
     */
    public function markupAllTexts()
    {
        $is_all_checked = false;
        while (!$is_all_checked) {
            $text = Text::where('checked',0)->first();
            if ($text) {
                $message_error = $text->markup($text->text_xml);
                print "<p>$message_error</p>";
                $text->checked=1;
                $text->save();   
            } else {
                $is_all_checked = true;
            }
        }
/*
        $texts = Transtext::all();
        foreach ($texts as $text) {
            $text->markup();
            $text->save();            
        }
 * 
 */
    }
     
    /**
     * Markup xml of text and transtext
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markupText(Int $id)
    {
        $text = Text::find($id);
        $message_error = $text->markup($text->text_xml);
        if ($message_error) {
            return Redirect::to('/corpus/text/'.($text->id))
                           ->withErrors($message_error);        
        }
        $text->save();            
        
        $transText = Transtext::find($text->transtext_id);
        if ($transText) {
            $transText->markup();
            $transText->save(); 
        }
        
        return Redirect::to('/corpus/text/'.($text->id).($this->args_by_get))
                       ->withSuccess(\Lang::get('messages.updated_success'));        
    }
     
    
    /**
     * Markup all texts and transtexts with empty text_xml
     */
    public function markupAllEmptyTextXML()
    {
        $texts = Text::where('text_xml',NULL)->orWhere('text_xml','like','')->get();
        foreach ($texts as $text) {
            $message_error = $text->markup();
            print "<p>$message_error</p>";
            $text->save();            
        }
        
        $transtexts = Transtext::where('text_xml',NULL)->orWhere('text_xml','like','')->get();
        foreach ($transtexts as $text) {
            $text->markup();
            $text->save();            
        }
    }
    
    public function fullNewList()
    {
        $portion = 100;
        $texts = Text::lastCreated($portion)
                    ->groupBy(function ($item, $key) {
                        return (string)$item['created_at']->formatLocalized(trans('main.date_format'));
                    });
        if (!$texts) {            
            return Redirect::to('/');
        }
        return view('corpus.text.list.full_new')
              ->with(['new_texts' => $texts]);
    }
    
    /**
     * /corpus/text/limited_new_list
     * @param Request $request
     * @return Response
     */
    public function limitedNewList(Request $request)
    {
        $limit = (int)$request->input('limit');
        $texts = Text::lastCreated($limit);
        if ($texts) {                       
            return view('corpus.text.list.limited_new')
                      ->with(['new_texts' => $texts]);
        }
    }
    
    public function fullUpdatedList()
    {
        $portion = 100;
        $texts = Text::lastUpdated($portion,1);                                
        if (!$texts) {            
            return Redirect::to('/');
        }
        return view('corpus.text.list.full_updated')
                  ->with(['last_updated_texts'=>$texts]);
    }
    
    /**
     * /corpus/text/limited_updated_list
     * @param Request $request
     * @return Response
     */
    public function limitedUpdatedList(Request $request)
    {
        $limit = (int)$request->input('limit');
        $texts = Text::lastUpdated($limit);
//dd($texts);                                
        if ($texts) {                       
            return view('corpus.text.list.limited_updated')
                      ->with(['last_updated_texts'=>$texts]);
        }
    }
    public function newTextList(Request $request)
    {
        $limit = (int)$request->input('limit');
        if ($limit) {
            $portion = $limit;
            $view = 'new_list';
        } else {
            $portion = 100;
            $view = 'full_new_list';
        }
        $texts = Text::lastCreatedTexts($portion);
                                
        return view('corpus.text.'.$view)
                  ->with(['new_texts' => $texts,
                          'limit' => $limit
                         ]);
    }
    
    public function updatedTextList(Request $request)
    {
        $limit = (int)$request->input('limit');
        if ($limit) {
            $portion = $limit;
            $view = 'updated_list';
        } else {
            $portion = 100;
            $view = 'full_updated_list';
        }
        $texts = Text::lastUpdatedTexts($portion);
                                
        return view('corpus.text.'.$view)
                  ->with(['last_updated_texts'=>$texts,
                          'limit' => $limit
                         ]);
    }
    
    /**
     * Calls by AJAX, 
     * adds 
     * /corpus/text/add/example/4254_1500_11_92
     * 
     * @param type $example_id
     * @return string
     */
    public function addExample($example_id)
    {
        Text::updateExamples([$example_id=>5]);
        $str = '';
        if (preg_match("/^(\d+)\_(\d+)_(\d+)_(\d+)$/",$example_id,$regs)) {
            $str = Text::createWordCheckedBlock($regs[1],$regs[2],$regs[3],$regs[4]);
        }
        return $str;
    }

    /**
     * Shows the sentence with a highlighted word.
     * Receives text ID and word ID (local number in the text),
     * finds sentence in the text, parses xml
     * 
     * Called by ajax request
     * /corpus/text/sentence?text_id=1548&w_id=4
     *
     * @return \Illuminate\Http\Response
     */
    public function showWordInSentence(Request $request)
    {
        $text_id = (int)$request->input('text_id');
        $w_id = (int)$request->input('w_id');
        if (!$text_id || !$w_id) {
            return;
        }
        
        $word = Word::getByTextWid($text_id, $w_id);
       
        if (!$word || !$word->sentence_id) {
            return;
        }
        
        $sentence = Text::extractSentence($text_id, $word->sentence_id, $w_id);            
                                
        return view('dict.lemma.show.example_sentence')
                ->with(['sentence'=>$sentence,'relevance'=>'', 'count'=>'']);
    }
    
    /**
     * SQL: select lower(word) as l_word, count(*) as frequency from words where text_id in (select id from texts where lang_id=1) group by word order by frequency DESC, l_word LIMIT 30;
     * SQL: select word, count(*) as frequency from words where text_id in (select id from texts where lang_id=1) group by word order by frequency DESC, word LIMIT 30;
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function frequencySymbols(Request $request) {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        if ($url_args['search_lang']) {
            $symbols = Text::countFrequencySymbols($url_args['search_lang']);
        } else {
            $symbols = NULL;
        }
        $lang_values = Lang::getList();
        
        return view('corpus.text.frequency.symbols',
                compact('lang_values', 'symbols', 'args_by_get', 'url_args'));
    } 
}
