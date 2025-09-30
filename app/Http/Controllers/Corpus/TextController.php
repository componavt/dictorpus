<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Response;
use DB;

use App\Models\Corpus\Author;
use App\Models\Corpus\Corpus;
use App\Models\Corpus\Cycle;
use App\Models\Corpus\District;
use App\Models\Corpus\Genre;
use App\Models\Corpus\Informant;
use App\Models\Corpus\MeaningTextRel;
use App\Models\Corpus\Motive;
use App\Models\Corpus\Place;
use App\Models\Corpus\Plot;
use App\Models\Corpus\Recorder;
use App\Models\Corpus\Region;
use App\Models\Corpus\Sentence;
use App\Models\Corpus\SentenceFragment;
use App\Models\Corpus\SentenceTranslation;
use App\Models\Corpus\Text;
use App\Models\Corpus\Topic;
use App\Models\Corpus\Transtext;
use App\Models\Corpus\Word;

use App\Models\Dict\Dialect;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
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
                         ['only' => ['create','store','edit','update','destroy', 'stats',
                                     'addExample', 'checkSentences', 'editExample', 'updateExamples', 
                                     'editSentences', 'photos', 'updatePhotos', 'deletePhoto',                                     
                                     'markupText', 'markupAllEmptyTextXML','markupAllTexts', 'concordance']]);
        $this->url_args = Text::urlArgs($request);  
        $this->url_args_w = remove_empty($this->url_args);
        $this->args_by_get = search_values_by_URL($this->url_args_w);
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
        $url_args_w = $this->url_args_w;

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
        $lang_values = Lang::getListWithQuantity('texts', true);        
        $dialect_values = Dialect::getList();
        $informant_values = [NULL => ''] + Informant::getList();
        $recorder_values = [NULL => ''] + Recorder::getList();
        $author_values = [NULL => ''] + Author::getList();
        $genre_values = Genre::getList();
        $plot_values = Plot::getList();
        $topic_values = Topic::getList();
        $region_values = [NULL => ''] + Region::getList();
        $district_values = District::getList();
        $place_values = Place::getList(false);

        return view('corpus.text.index',
                compact('author_values', 'corpus_values', 'dialect_values', 
                        'district_values', 'genre_values', 'informant_values', 
                        'lang_values', 'recorder_values', 'region_values', 
                        'place_values', 'plot_values', 'texts', 'topic_values', 
                        'numAll', 'args_by_get', 'url_args', 'url_args_w'));
    }

    public function simpleSearch() {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        $url_args_w = remove_empty($url_args);
        
        $texts = Text::simpleSearch($url_args['search_w']);
        $numAll = $texts->count();
        $texts = $texts->orderBy('id', 'desc')->paginate($url_args['limit_num']);         
        
        return view('corpus.text.search.simple', 
                compact('texts', 'numAll', 'args_by_get', 'url_args', 'url_args_w'));        
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
        $cycle_genre_id = Genre::LEGEND_ID;
        $cycle_values = Cycle::getList();
        $motive_values = Motive::getList();
        $informant_values = [NULL => ''] + Informant::getList();
        $place_values = [NULL => ''] + Place::getList();
        $recorder_values = Recorder::getList();
        $dialect_values = Dialect::getList();
        $genre_values = Genre::getList();        
        $plot_values = Plot::getList();
        $topic_values = [NULL => ''] + Topic::getList();

        $region_values = Region::getList();
        $district_values = District::getList();
        
        $author_values = Author::getList();
        $project_langs=Lang::projectLangs(); 
        
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        return view('corpus.text.create',
                  compact('author_values', 'corpus_values', 'cycle_genre_id', 
                          'cycle_values', 'dialect_values', 
                          'district_values', 'genre_values', 'informant_values', 
                          'lang_values', 'motive_values', 'place_values', 'plot_values', 
                          'project_langs', 'recorder_values', 'region_values', 
                          'topic_values', 'args_by_get', 'url_args'));
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
            'corpuses' => 'required|array',
            'lang_id' => 'required|numeric',
            'transtext.title'  => 'max:255',
            'transtext.lang_id' => 'numeric',
            'event_date' => 'numeric',
            'cyrtext.title'  => 'max:255',
        ]);

        $text = Text::create($request->only('corpus_id','lang_id','title', 'comment')); //,'source_id','event_id',
        $request['text'] = Text::process($request['text']);
        $text->text = $text->processTextBeforeSaving($request->text);

        $error_message = $text -> storeAdditionInfo($request);

        $redirect = Redirect::to('/corpus/text/'.($text->id).'/check_sentence'.($this->args_by_get));
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
    public function show($id, Request $request)
    {
        $for_print = (int)($request->for_print);
        $text = Text::find($id);
       
        if (!$text) {
            return Redirect::to('/corpus/text/')
                           ->withErrors(\Lang::get('corpus.text_not_found',['id'=>$id]));            
        }
        
        $pos_values = PartOfSpeech::getGroupedList();   
        $langs_for_meaning = array_slice(Lang::getListWithPriority(),0,1,true);
        $pos_id = PartOfSpeech::getIDByCode('Noun');
        $dialect_values = Dialect::getList($text->lang_id);
        $dialect_value = $text->dialectValue();
        $photos = $text->getMedia();
        
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        list($meanings_by_wid, $gramsets_by_wid, $wordforms) = $text->meaningsGramsetsByWid();
       
        if ($for_print) {
            return view('corpus.text.show_print',
                      compact('dialect_value', 'dialect_values', 'for_print', 'gramsets_by_wid', 
                              'langs_for_meaning', 'meanings_by_wid',  'photos', 'pos_id', 'pos_values', 
                              'text', 'wordforms', 'args_by_get', 'url_args'));            
        }
        return view('corpus.text.show',
                  compact('dialect_value', 'dialect_values', 'gramsets_by_wid',
                          'langs_for_meaning', 'meanings_by_wid',  'photos', 'pos_id', 'pos_values', 
                          'text', 'wordforms', 'args_by_get', 'url_args'));
    }
    
    public function editSentences($id) {
        $text = Text::find($id);       
        if (!$text) {
            return Redirect::to('/corpus/text/')
                           ->withErrors(\Lang::get('corpus.text_not_found',['id'=>$id]));            
        }
        $sentences = Sentence::whereTextId($id)->orderBy('s_id')->get();

        $dialect_value = $text->dialectValue();
        $dialect_values = Dialect::getList($text->lang_id);
        $langs_for_meaning = array_slice(Lang::getListWithPriority(),0,1,true);
        $pos_id = PartOfSpeech::getIDByCode('Noun');
        $pos_values = PartOfSpeech::getGroupedList();   
        
        $trans_sentences = $text->transtext ? $text->transtext->getSentencesFromXML() : [];
        
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        $url_args['wblock_preloaded'] = true;
        
        list($meanings_by_wid, $gramsets_by_wid, $wordforms) = $text->meaningsGramsetsByWid();
//dd($meanings_by_wid);        
        return view('corpus.text.sentences',
                compact('dialect_value', 'dialect_values', 'gramsets_by_wid', 
                        'langs_for_meaning', 'meanings_by_wid',
                        'pos_id', 'pos_values', 'sentences', 'text', 
                        'trans_sentences', 'wordforms', 'args_by_get', 'url_args'));
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
        
        $cycle_values = Cycle::getList();
        $cycle_value = $text->cycleValue();
        $cycle_genre_id = Genre::LEGEND_ID;
        $motive_values = Motive::getList();
        
        $place_values = [NULL => ''] + Place::getList();

        $informant_values = Informant::getList();
        $informant_value = $text->informantValue();

        $recorder_values = Recorder::getList();

        $dialect_values = Dialect::getList();

        $genre_values = Genre::getList($text->corpus_id);        
        
        $plot_values = Plot::getList();
        $plot_value = $text->plotValue();
        
        $topic_values = [NULL => ''] + Topic::getList();
        $topic_value = $text->topicValueWithNumber();//topicValue();
//dd($topic_value);
        $region_values = Region::getList();
        $district_values = District::getList();

        $author_values = Author::getList();
        $author_value = $text->authorValue();
        $trans_author_value = $text->transtext ? $text->transtext->authorValue() : null;
        $project_langs=Lang::projectLangs(); 
                
        $readonly = ($text->meanings()->wherePivot('relevance','<>',1)->count()) ? true : false;
        
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        return view('corpus.text.edit',
                compact('author_value', 'author_values', 'corpus_values', 
                        'cycle_genre_id', 'cycle_value', 'cycle_values', 
                        'dialect_values', 'district_values', 
                        'genre_values', 'informant_value',
                        'informant_values','lang_values', 'motive_values', 'place_values', 
                        'plot_value', 'plot_values', 'project_langs', 'readonly', 
                        'recorder_values', 'region_values', 
                        'text', 'topic_value', 'topic_values', 'trans_author_value', 
                        'args_by_get', 'url_args'));
    }

    public function checkSentences($id) {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        $text = Text::find($id);       
        if (!$text) {
            return Redirect::to('/corpus/text/')
                           ->withErrors(\Lang::get('corpus.text_not_found',['id'=>$id]));            
        }
        list($text->text_structure, $sentences) = Text::markupText($text->text, true, true);
        $trans_sentences = !empty($text->transtext) ? $text->transtext->getSentencesFromXML() : [];
        $cyr_sentences = !empty($text->cyrtext) ? $text->cyrtext->getSentencesFromXML(true) : [];
        $total = max(sizeof($sentences), sizeof($trans_sentences), sizeof($cyr_sentences));
//dd($sentences, $trans_sentences, $cyr_sentences, $total);        
       
        return view('corpus.text.check_sentences',
                compact('cyr_sentences', 'sentences', 'text', 'total', 'trans_sentences', 
                        'args_by_get', 'url_args'));
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
        list($sentence, $meanings, $meaning_texts) = 
            MeaningTextRel::preparationForExampleEdit($id.'_'.$example_id);
//dd($sentence);        
        if ($sentence == NULL) {
            return Redirect::to('/corpus/text/'.$id.($this->args_by_get))
                       ->withError(\Lang::get('messages.invalid_id'));            
        } 
        $text = Text::find($id);
        $pos_values = PartOfSpeech::getGroupedList();   
        $langs_for_meaning = array_slice(Lang::getListWithPriority(),0,1,true);
        $pos_id = PartOfSpeech::getIDByCode('Noun');
        $text_dialects = $text->dialects;
        $dialect_value = $text_dialects[0]->id ?? 0;
        $dialect_values = Dialect::getList($text->lang_id);

        $back_to_url = '/corpus/text/'.$id;
        $route = ['text.update.examples', $id];
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
            
        $translations = SentenceTranslation::whereSentenceId($sentence['sent_obj']->id)
                        ->whereWId($sentence['w_id'])->get();
        $fragment = SentenceFragment::getBySW($sentence['sent_obj']->id, $sentence['w_id']);
        
        return view('dict.lemma.example.edit',
                  compact('back_to_url', 'dialect_value', 'dialect_values', 
                          'fragment', 'langs_for_meaning', 'meanings', 
                          'meaning_texts', 'pos_id', 'pos_values', 'route', 
                          'sentence', 'text', 'translations', 'args_by_get', 'url_args')
                        );            
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
            'corpuses' => 'required|array',
            'lang_id' => 'required|numeric',
            'transtext.title'  => 'max:255',
            'transtext.lang_id' => 'numeric',
            'cyrtext.title'  => 'max:255',
//            'new_file' => 'mimetypes:audio/mp3',
//            'event_date' => 'numeric',
        ]);
//dd($request->topics);
        $error_message = Text::updateByID($request, $id);
        $redirect = Redirect::to('/corpus/text/'.$id. (empty($request->to_makeup) ? '' : '/check_sentence').$this->args_by_get);
        
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
        MeaningTextRel::updateExamples($request['relevance']);
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
                    $text_title = $text->title;
                    $text->remove();
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
                $message_error = $text->markup();
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
    public function markupText(int $id)
    {
        $text = Text::find($id);
        $message_error = $text->markup();
        if ($message_error) {
            return Redirect::to('/corpus/text/'.($text->id))
                           ->withErrors($message_error);        
        }
        $text->save();            
        
/*        $transText = Transtext::find($text->transtext_id);
        if ($transText) {
            $transText->markup();
            $transText->save(); 
        }*/
        
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
     * /corpus/text/add_example/4254_1500_11_92
     * 
     * @param type $example_id
     * @return string
     */
    public function addExample($example_id)
    {
        MeaningTextRel::updateExamples([$example_id=>5]);
//        $str = '';
        if (preg_match("/^(\d+)\_(\d+)_(\d+)_(\d+)$/",$example_id,$regs)) {
            return Word::createWordBlock($regs[2],$regs[4]);
//            return Text::createWordCheckedBlock($regs[1],$regs[2],$regs[3],$regs[4]);
        }
//        return $str;
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
        $with_cyrilic = (int)$request->input('with_cyrilic');
        
        $word = Word::getByTextWid($text_id, $w_id);
       
        if (!$word || !$word->s_id) {
            return;
        }
        
        $sentence = Text::extractSentence($text_id, $word->s_id, $w_id); 
        $relevance = $count = '';
        
        return view('dict.lemma.example.sentence', 
                compact('count', 'relevance', 'sentence', 'text_id', 'with_cyrilic'));
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
    
    public function speechCorpus()
    {        
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        $url_args['with_audio'] = true;
        
        if (isset($url_args['search_dialect'][0]) && !$url_args['search_lang']) {
            $dialect = Dialect::find($url_args['search_dialect'][0]);
            if ($dialect) {
                $url_args['search_lang'] = [$dialect->lang_id];
            }
        }
        
        $texts = Text::search($url_args);

        $numAll = $texts->count();

        $texts = $texts->paginate($this->url_args['limit_num']);
        
//        $lang_values = Lang::getProjectList();
        $lang_values = Lang::getListWithQuantity('audiotexts', true);
        
        $dialect_values = Dialect::getList();
        $informant_values = [NULL => ''] + Informant::getList();
        $recorder_values = [NULL => ''] + Recorder::getList();
        $author_values = [NULL => ''] + Author::getList();
        $genre_values = Genre::getList();
        $plot_values = Plot::getList();
        $topic_values = Topic::getList();
        $region_values = [NULL => ''] + Region::getList();
        $district_values = District::getList();
        $place_values = Place::getList(false);

        return view('corpus.text.speech_corpus',
                compact('author_values', 'dialect_values', 
                        'district_values', 'genre_values', 'informant_values', 
                        'lang_values', 'recorder_values', 'region_values', 
                        'place_values', 'plot_values', 'texts', 'topic_values', 
                        'numAll', 'args_by_get', 'url_args'));
    } 
    
    public function spellchecking()
    {        
        $lang_values = Lang::getProjectList();
        return view('corpus.text.spellchecking', compact('lang_values'));
    }
    
    public function analysSpellchecking(Request $request)
    {        
        $text = $request->input('text');
        $lang_id = (int)$request->input('lang_id');
        return Text::spellchecking($text, $lang_id);
    }
    
    public function stats($id) {
        $args_by_get = $this->args_by_get;
        $text = Text::find($id);
        $totalWords = $text->words()->count();
//dd(to_sql($text->markedWords()));        
        $markedWords = $text->markedWords()->count();
        $markedWordsToAll = round(100 * $markedWords / $totalWords);
        
        $checked_words = $text->markedWords('checked')->count();
        $checkedWordsToMarked = round(100 * $checked_words / $markedWords);
//dd($totalWords);        
        $lemmas = Lemma::whereIn('id', function ($q) use ($id) {
                            $q->select('lemma_id')->from('meanings')
                              ->whereIn('id', function ($q2) use ($id) {
                                $q2->select('meaning_id')->from('meaning_text')
                                  ->whereTextId($id)
                                  ->where('relevance', '<>', 0);
                             });
                         });
//dd($lemmas)                         
        $totalLemmas = $lemmas->count();
        $lemmas_by_pos = [];
        foreach ($lemmas->groupBy('pos_id')->orderBy('count', 'DESC')->selectRaw('pos_id, count(*) as count')->get() as $row) {
            $pos = PartOfSpeech::find($row->pos_id);
            $lemmas_by_pos[$pos->name] = ['count'=>$row->count, '%'=>round(100 * $row->count / $totalLemmas)];
        }
        return view('corpus.text.stats', 
                compact('checked_words', 'checkedWordsToMarked', 'lemmas_by_pos', 
                        'markedWords', 'markedWordsToAll', 'text', 'totalLemmas', 
                        'totalWords', 'args_by_get'));
    }
    
    public function photos(int $id) {
        $text = Text::find($id);
        if (!$text) {
            return Redirect::to('/corpus/text/')
                           ->withErrors(\Lang::get('corpus.text_not_found',['id'=>$id]));            
        }
        
        $args_by_get = $this->args_by_get;
        $photos = $text->getMedia();
        
        return view('corpus.text.photos', 
                compact('text', 'photos', 'args_by_get'));
    }    
    
    public function updatePhotos(int $id, Request $request)
    {
        $text = Text::find($id);
        if (!$text) {
            return Redirect::to('/corpus/text/')
                           ->withErrors(\Lang::get('corpus.text_not_found',['id'=>$id]));            
        }
        if ($request->photo) {
            foreach ($request->photo as $photo_id=>$photo) {
                $media = $text->getMedia()->where('id', $photo_id)->first();
                if (!$media) {
                    continue;
                }
                if (!empty($request->{'file_'.$photo_id})) {
                    $text->uploadPhoto('file_'.$photo_id, $photo['title']);
                    $text->deleteMedia($media->id);
                    continue;
                }
                $media->name = $photo['title'];
                $media->save();
            }
        }
        
        if (!empty($request->new_file)) {
            $text->uploadPhoto('new_file', $request->new_title);
//            $text->uploadPhoto($request->file('new_file'), $request->new_title);
        }
        return Redirect::to('/corpus/text/'.$id.'/photos'.($this->args_by_get))
                    ->withSuccess('Фотографии изменены.');
    }
    
    public function deletePhoto(int $id, int $photo_id)
    {
        $text = Text::find($id);
        if (!$text) {
            return Redirect::to('/corpus/text/')
                           ->withErrors(\Lang::get('corpus.text_not_found',['id'=>$id]));            
        }
        $media = $text->getMedia()->where('id', $photo_id)->first();
        if (!$media) {
            return Redirect::to('/corpus/text/'.$id.'/photos'.($this->args_by_get))
                        ->withError('Нет такого изображения');            
        }
        $text->deleteMedia($media->id);
        return Redirect::to('/corpus/text/'.$id.'/photos'.($this->args_by_get))
                    ->withSuccess('Фотография удалена.');
    }
    
    public function concordance(Text $text)
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
       
        $concordance=$text->concordance();
        
        $unchecked_count = Word::whereTextId($text->id)
                ->whereIn('id', function ($q) use($text) {
                    $q->select('word_id')->from('meaning_text')
                      ->whereTextId($text->id);
                })->whereNotIn('id', function ($q) use($text) {
                    $q->select('word_id')->from('meaning_text')
                      ->whereTextId($text->id)
                      ->where('relevance', '>', 1);
                })->count();
        
        return view('corpus.text.concordance', 
                compact('text', 'concordance', 'unchecked_count', 'args_by_get', 'url_args'));
    } 
   
}
