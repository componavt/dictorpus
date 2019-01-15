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
                    'search_informant'=> $request->input('search_informant'),
                    'search_lang'     => (array)$request->input('search_lang'),
                    'search_recorder' => $request->input('search_recorder'),
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
       
        $this->args_by_get = Lang::searchValuesByURL($this->url_args);
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
        return view('corpus.text.history')
                  ->with(['text' => $text,
                        'args_by_get'    => $this->args_by_get,
                        'url_args'       => $this->url_args,
                          ]);
    }

    /**
     * Markup all texts and transtexts
     * update texts set checked=0;
     * select count(*) from texts where checked=0;
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
        
        $word = Word::getByTextWid($text_id, $w_id);
       
        if (!$word || !$word->sentence_id) {
            return;
        }
        
        $sentence = Text::extractSentence($text_id, $word->sentence_id, $w_id);            
                                
        return view('dict.lemma.show.example_sentence')
                ->with(['sentence'=>$sentence,'relevance'=>'', 'count'=>'']);
    }
    
    /*
     * vepkar-20190129-vep
     */
    public function exportToCONLL() {//Request $request
        $date = Carbon::now();
        $date_now = $date->toDateString();
                //isoFormat('YYYYMMDD');
        foreach ([1, 4, 5, 6] as $lang_id) {
//            $lang_id = 1;
            $lang = Lang::find($lang_id);
            $filename = 'export/conll/vepkar-'.$date_now.'-'.$lang->code.'.txt';
//    dd($filename);        
            Storage::disk('public')->put($filename, "# ".$lang->name);
            //exportLangTextsToCONLL($lang_id);
            $texts = Text::where('lang_id',$lang_id)
                    //->whereNotNull('transtext_id')
                    //->take(1)
                    ->get();
            foreach ($texts as $text) {
//dd($text);                
                Storage::disk('public')->append($filename, $text->toCONLL());
            }
            print  '<p><a href="'.Storage::url($filename).'">'.$lang->name.'</a>';            
        }
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
