<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use DB;
use LaravelLocalization;
use Response;

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
use App\Models\Dict\Meaning;

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
                    'search_corpus'   => (array)$request->input('search_corpus'),
                    'search_dialect'  => (array)$request->input('search_dialect'),
                    'search_lang'     => (array)$request->input('search_lang'),
                    'search_title'    => $request->input('search_title'),
                    'search_word'    => $request->input('search_word'),
                ];
        
        if (!$this->url_args['page']) {
            $this->url_args['page'] = 1;
        }
        
        if ($this->url_args['limit_num']<=0) {
            $this->url_args['limit_num'] = 10;
        } elseif ($this->url_args['limit_num']>1000) {
            $this->url_args['limit_num'] = 1000;
        }   
       
        $this->args_by_get = Lang::searchValuesByURL($this->url_args);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        // select * from `texts` where (`transtext_id` in (select `id` from `transtexts` where `title` = '%nitid_') or `title` like '%nitid_') and `lang_id` = '1' order by `title` asc limit 10 offset 0
        // select texts by title from texts and translation texts
        $texts = Text::orderBy('title');

        $text_title = $this->url_args['search_title'];
        if ($text_title) {
            $texts = $texts->where(function($q) use ($text_title){
                            $q->whereIn('transtext_id',function($query) use ($text_title){
                                $query->select('id')
                                ->from(with(new Transtext)->getTable())
                                ->where('title','like', $text_title);
                            })->orWhere('title','like', $text_title);
                    });
                           //->whereOr('transtexts.title','like', $text_title);
        } 

        if ($this->url_args['search_corpus']) {
            $texts = $texts->whereIn('corpus_id',$this->url_args['search_corpus']);
        } 

        $search_dialect = $this->url_args['search_dialect'];
        if (sizeof($search_dialect)) {
            $texts = $texts->whereIn('id',function($query) use ($search_dialect){
                        $query->select('text_id')
                        ->from("dialect_text")
                        ->whereIn('dialect_id',$search_dialect);
                    });
        } 

        if (isset($search_dialect[0]) && !$this->url_args['search_lang']) {
            $dialect = Dialect::find($search_dialect[0]);
            if ($dialect) {
                $this->url_args['search_lang'] = [$dialect->lang_id];
            }
        }
        
        if (sizeof($this->url_args['search_lang'])) {
            $texts = $texts->whereIn('lang_id',$this->url_args['search_lang']);
        } 

        $search_word = $this->url_args['search_word'];
        if ($search_word) {
            $texts = $texts->whereIn('id',function($query) use ($search_word){
                                $query->select('text_id')
                                ->from('words')
                                ->where('word','like', $search_word);
                            });
        } 

        $numAll = $texts->count();

        $texts = $texts->paginate($this->url_args['limit_num']);
        
        $corpus_values = Corpus::getListWithQuantity('texts');

        //$lang_values = Lang::getList();
        $lang_values = Lang::getListWithQuantity('texts');
        
        $dialect_values = Dialect::getList();

        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        return view('corpus.text.index',
                compact('corpus_values', 'dialect_values', 'lang_values',
                        'texts', 'numAll', 'args_by_get', 'url_args'));
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

        $text = Text::create($request->only('corpus_id','lang_id','title','text')); //,'source_id','event_id',

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
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        return view('corpus.text.show',
                  compact('labels', 'text', 'args_by_get', 'url_args'));
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

        $genre_values = Genre::getList();        
        $genre_value = $text->genreValue();

        return view('corpus.text.edit')
                  ->with(['text' => $text,
                          'lang_values' => $lang_values,
                          'corpus_values' => $corpus_values,
                          'informant_value' => $informant_value,
                          'informant_values' => $informant_values,
                          'place_values' => $place_values,
                          'recorder_values' => $recorder_values,
                          'recorder_value' => $recorder_value,
                          'dialect_values' => $dialect_values,
                          'dialect_value' => $dialect_value,
                          'genre_values' => $genre_values,
                          'genre_value' => $genre_value,
                        'args_by_get'    => $this->args_by_get,
                        'url_args'       => $this->url_args,
                         ]);
    }

    /**
     * Shows the form for editing of text example for all lemma meanings connected with this sentence.
     *
     * @param  int  $id - ID of lemma
     * @param  int  $sentence_id - ID of example
     * @return \Illuminate\Http\Response
     */
    public function editExample(Request $request, $id, $example_id)
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
            'event_date' => 'numeric',
        ]);
        $request['text'] = Text::process($request['text']);
        $request['transtext_text'] = Text::process($request['transtext_text']);
        
        $text = Text::with('transtext','event','source')->get()->find($id);
        $old_text = $text->text;

        $text->fill($request->only('corpus_id','lang_id','title','text','text_xml'));
        $text->updated_at = date('Y-m-d H:i:s');
        $text->save();
        
        $error_message = $text -> storeAdditionInfo($request, $old_text);

        $redirect = Redirect::to('/corpus/text/'.($text->id).($this->args_by_get));
        if ($error_message) {
            $redirect = $redirect->withErrors($error_message);
        } else {
            $redirect = $redirect->withSuccess(\Lang::get('messages.updated_success'));
        }         
        return $redirect;
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
        $status_code = 200;
        $result =[];
        if($id != "" && $id > 0) {
            try{
                $text = Text::find($id);
                if($text){
                    $text_title = $text->title;
                    
                    $transtext_id = $text->transtext_id;
                    $event_id = $text->event_id;
                    $source_id = $text->source_id;
                    
                    $text->dialects()->detach();
                    $text->genres()->detach();
                    $text->meanings()->detach();

                    $text->words()->delete();
                    $text->video()->delete();
                    
                    $text->delete();

                    //remove transtext if exists and don't link with other texts
                    if ($transtext_id && !Text::where('id','<>',$id)
                                              ->where('transtext_id',$transtext_id)
                                              ->count()) {
                        Transtext::find($transtext_id)->delete();
                    }

                    //remove event if exists and don't link with other texts
                    if ($event_id && !Text::where('id','<>',$id)
                                              ->where('event_id',$event_id)
                                              ->count()) {
                        $event = Event::find($event_id);
                        if ($event) {
                            $event->recorders()->detach();
                            $event->delete();
                        }
                    }

                    //remove source if exists and don't link with other texts
                    if ($source_id && !Text::where('id','<>',$id)
                                           ->where('source_id',$source_id)
                                           ->count()) {
                        Source::find($source_id)->delete();
                    }

                    $result['message'] = \Lang::get('corpus.text_removed', ['name'=>$text_title]);
                }
                else{
                    $error = true;
                    $result['error_message'] = \Lang::get('messages.record_not_exists');
                }
          }catch(\Exception $ex){
                    $error = true;
                    $status_code = $ex->getCode();
                    $result['error_code'] = $ex->getCode();
                    $result['error_message'] = $ex->getMessage();
                }
        }else{
            $error =true;
            $status_code = 400;
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
     * Test url: /corpus/text/dialect_list?lang_id=1
     * 
     * @return JSON response
     */
    public function dialectList(Request $request)
    {
        $locale = LaravelLocalization::getCurrentLocale();

        $dialect_name = '%'.$request->input('q').'%';
        $lang_ids = (array)$request->input('lang_id');
//        $lemma_id = (int)$request->input('lemma_id');

        $list = [];
        $dialects = Dialect::whereIn('lang_id',$lang_ids)
                       ->where(function($q) use ($dialect_name){
                            $q->where('name_en','like', $dialect_name)
                              ->orWhere('name_ru','like', $dialect_name);
                         })->orderBy('name_'.$locale)->get();
                         
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
//dd($lemma->revisionHistory);        
        return view('corpus.text.history')
                  ->with(['text' => $text,
                        'args_by_get'    => $this->args_by_get,
                        'url_args'       => $this->url_args,
                          ]);
    }

    /**
     * Markup all texts and transtexts
     */
    public function markupAllTexts()
    {
        $is_all_checked = false;
        while (!$is_all_checked) {
            $text = Text::where('checked',0)->first();
//dd($text);            
            if ($text) {
                $message_error = $text->markup($text->text_xml);
                print "<p>$message_error</p>";
                $text->checked=1;
                $text->save();   
//dd($text->updated_at);                
            } else {
                $is_all_checked = true;
            }
        }
/*            
        $texts = Text::all();
        foreach ($texts as $text) {
            $message_error = $text->markup();
            print "<p>$message_error</p>";
            $text->save();            
        }
*/

    $texts = Transtext::all();
    foreach ($texts as $text) {
        $text->markup();
        $text->save();            
    }
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
                           ->withError($message_error);        
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
        
        $texts = Transtext::where('text_xml',NULL)->orWhere('text_xml','like','')->get();
        foreach ($texts as $text) {
            $text->markup();
            $text->save();            
        }
    }
    
    public function fullNewList(Request $request)
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
    
    public function fullUpdatedList(Request $request)
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
     * /corpus/text/word/create_checked_block
     * 
     * @param Request $request
     * @return string
     */
    public function getWordCheckedBlock(Request $request)
    {
        $meaning_id = (int)$request->input('meaning_id');
        $text_id = (int)$request->input('text_id'); 
        $w_id = (int)$request->input('w_id'); 
        $word = Word::where('text_id',$text_id)
                    ->where('w_id',$w_id)->first();
        if (!$word || !$word->sentence_id) {
            return;
        }
        return Text::createWordCheckedBlock($meaning_id, $text_id, $word->sentence_id, $w_id);
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
        
        $word = Word::where('text_id',$text_id)
                        ->where('w_id',$w_id)->first();
       
        if (!$word || !$word->sentence_id) {
            return;
        }
        
        $sentence = Text::extractSentence($text_id, $word->sentence_id, $w_id);            
                                
        return view('dict.lemma.show.example_sentence')
                ->with(['sentence'=>$sentence,'relevance'=>'', 'count'=>'']);
    }
    
//select count(*) from words where (word like '%Ü%' COLLATE utf8_bin OR word like '%ü%' COLLATE utf8_bin OR word like '%w%') and text_id in (SELECT id from texts where lang_id=5);
/*
    public function tmpProcessOldLetters() {
        $lang_id=5;
        $words = Word::whereRaw("(word like '%Ü%' COLLATE utf8_bin OR word like '%ü%' COLLATE utf8_bin OR word like '%w%')"
                . " and text_id in (SELECT id from texts where lang_id=5)")  // only livvic texts
                     ->take(1000)->get();
//dd($words->toSql());        
        foreach ($words as $word) {
//dd($word->word);            
            $new_word = Word::changeLetters($word->word);
            $new_word_l = strtolower($new_word);
            if ($new_word != $word->word) {
//dd($word->text_id);        
print "<p>".$word->word;        
                DB::statement("DELETE FROM meaning_text WHERE word_id=".$word->id);
                $wordform_q = "(SELECT id from wordforms where wordform like '$new_word' or wordform like '$new_word_l')";
                $lemma_q = "(SELECT lemma_id FROM lemma_wordform WHERE wordform_id in $wordform_q)";
                $meanings = Meaning::whereRaw("lemma_id in (SELECT id from lemmas where lang_id=$lang_id and (lemma like '$new_word' or lemma like '$new_word_l' or id in $lemma_q))")
                                   ->get();    
//dd($meanings);    
                foreach ($meanings as $meaning) {
                    $meaning->texts()->attach($word->text_id,
                            ['sentence_id'=>$word->sentence_id,
                             'word_id'=>$word->id,
                             'w_id'=>$word->w_id,
                             'relevance'=>1]);
                    
                }
                $word->word = $new_word;
                $word->save();
            }
//                        $word_for_DB = Word::changeLetters($word_for_DB);
        }
        
    }
*/
    /*    public function tempStripSlashes()
    {
        $texts = Text::all();
        foreach ($texts as $text) {
            $text->title = stripslashes($text->title);
            $text->text = stripslashes($text->text);
            $text->save();            
        }
        
    }
 * 
 */
    
    /*    
    public function tempInsertVepsianText()
    {
        DB::connection('mysql')->table('texts')->delete();
       
        DB::connection('mysql')->table('transtexts')->delete();

        $veps_texts = DB::connection('vepsian')
                            ->table('text')
                            ->where('lang_id',2)
                            ->orderBy('id')
                            //->take(1)
                            ->get();
        
        foreach ($veps_texts as $veps_text):
            $text = new Transtext;
            $text->id = $veps_text->id;
            $text->lang_id = $veps_text->lang_id;
            $text->title = $veps_text->title;
            $text->text = $veps_text->text;
            $text->updated_at = $veps_text->modified;
            $text->created_at = $veps_text->modified;
            $text->save();            
        endforeach;

        $veps_texts = DB::connection('vepsian')
                            ->table('text')
                            ->where('lang_id',1)
                            ->orderBy('id')
                            //->take(1)
                            ->get();
 
        foreach ($veps_texts as $veps_text):
            $text = new Text;
            $text->id = $veps_text->id;
            $text->corpus_id = $veps_text->corpus_id;
            $text->lang_id = $veps_text->lang_id;
            $text->title = $veps_text->title;
            $text->text = $veps_text->text;
            $text->source_id = $veps_text->source_id;
            $text->event_id = $veps_text->event_id;
            $text->updated_at = $veps_text->modified;
            $text->created_at = $veps_text->modified;

            $transtext = DB::connection('vepsian')
                            ->table('text_pair')
                            ->where('text1_id',$text->id)
                            ->first();
            if ($transtext) {
                $text->transtext_id = $transtext->text2_id;
            }
            $text->save();            
        endforeach;
     }
 */
/*
    public function tempInsertVepsianDialectText()
    {
        DB::connection('mysql')->table('dialect_text')->delete();
       
        $veps_texts = DB::connection('vepsian')
                            ->table('text_label')
                            ->join('text','text.id','=','text_label.text_id')
                            ->where('label_id','<',6)
                            ->where('lang_id',1)
                            ->orderBy('text_id')
                            //->take(1)
                            ->get();
        
        foreach ($veps_texts as $veps_text):
            DB::connection('mysql')->table('dialect_text')
                                   ->insert(['dialect_id'=>$veps_text->label_id,
                                             'text_id'=>$veps_text->text_id]);
        endforeach;
     }
/*    
    public function tempInsertVepsianGenreText()
    {
        DB::connection('mysql')->table('dialect_text')->delete();
       
        $veps_texts = DB::connection('vepsian')
                            ->table('text_label')
                            ->join('text','text.id','=','text_label.text_id')
                            ->where('label_id','>',5)
                            ->where('lang_id',1)
                            ->orderBy('text_id')
                            //->take(1)
                            ->get();
        
        foreach ($veps_texts as $veps_text):
            DB::connection('mysql')->table('genre_text')
                                   ->insert(['genre_id'=>$veps_text->label_id,
                                             'text_id'=>$veps_text->text_id]);
        endforeach;
     }
 * 
 */
     // select text1_id,text2_id,t1.event_id,t2.event_id  from text_pair, text as t1, text as t2 where t2.lang_id=2 and t2.event_id is not null and text_pair.text1_id=t1.id and text_pair.text2_id=t2.id;
     // select text1_id,text2_id,text.event_id from text_pair,text where text.lang_id=2 and text.event_id is not null and text_pair.text2_id=text.id;
}
