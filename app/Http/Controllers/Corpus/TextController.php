<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use DB;
use LaravelLocalization;
use Response;

use App\Models\Dict\Dialect;
use App\Models\Dict\Lang;

use App\Models\Corpus\Corpus;
use App\Models\Corpus\Event;
use App\Models\Corpus\Genre;
use App\Models\Corpus\Informant;
use App\Models\Corpus\Place;
use App\Models\Corpus\Recorder;
use App\Models\Corpus\Source;
use App\Models\Corpus\Text;
use App\Models\Corpus\Transtext;

class TextController extends Controller
{
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
                                     'markupAllEmptyTextXML','markupAllTexts']]);
        $this->url_args = [
                    'limit_num'       => (int)$request->input('limit_num'),
                    'page'            => (int)$request->input('page'),
                    'search_corpus'   => (int)$request->input('search_corpus'),
                    'search_dialect'  => (int)$request->input('search_dialect'),
                    'search_lang'     => (int)$request->input('search_lang'),
                    'search_title'    => $request->input('search_title'),
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
            $texts = $texts->where('corpus_id',$this->url_args['search_corpus']);
        } 

        $search_dialect = $this->url_args['search_dialect'];
        if ($search_dialect) {
            $texts = $texts->whereIn('id',function($query) use ($search_dialect){
                        $query->select('text_id')
                        ->from("dialect_text")
                        ->where('dialect_id',$search_dialect);
                    });
        } 

        if ($search_dialect || !$this->url_args['search_lang']) {
            $dialect = Dialect::find($search_dialect);
            if ($dialect) {
                $this->url_args['search_lang'] = $dialect->lang_id;
            }
        }
        
        if ($this->url_args['search_lang']) {
            $texts = $texts->where('lang_id',$this->url_args['search_lang']);
        } 

        $numAll = $texts->count();

        $texts = $texts->paginate($this->url_args['limit_num']);
        
        $corpus_values = Corpus::getListWithQuantity('texts');
        
        //$lang_values = Lang::getList();
        $lang_values = Lang::getListWithQuantity('texts');
        
        $dialect_values = Dialect::getList();

        return view('corpus.text.index')
                    ->with([
                        'dialect_values' => $dialect_values,
                        'texts' => $texts,
                        'lang_values' => $lang_values,
                        'corpus_values' => $corpus_values,
                        'numAll' => $numAll,
                        'args_by_get'    => $this->args_by_get,
                        'url_args'       => $this->url_args,
                    ]);
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
        
        return view('corpus.text.create')
                  ->with(['lang_values' => $lang_values,
                          'corpus_values' => $corpus_values,
                          'informant_values' => $informant_values,
                          'place_values' => $place_values,
                          'recorder_values' => $recorder_values,
                          'dialect_values' => $dialect_values,
                          'genre_values' => $genre_values,
                         ]);
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
        ]);
        
        $text = Text::create($request->only('corpus_id','lang_id','title','text')); //,'source_id','event_id',
        
        Transtext::storeTranstext($request->only('transtext_lang_id','transtext_title','transtext_text'), 
                                                  $text);
        Event::storeEvent($request->only('event_informant_id','event_place_id','event_date'), 
                                                  $text);
        Source::storeSource($request->only('source_title', 'source_author', 'source_year', 'source_ieeh_archive_number1', 'source_ieeh_archive_number2', 'source_pages', 'source_comment'), 
                                                  $text);       
        if ($text->event) {
            $text->event->recorders()->attach($request->event_recorders);
        }

        $text->dialects()->attach($request->dialects);
        $text->genres()->attach($request->genres);

        $redirect = Redirect::to('/corpus/text/'.($text->id))
                       ->withSuccess(\Lang::get('messages.created_success'));

        if ($request->text) {
            $error_message = $text->markup();
            if ($error_message) {
                $redirect = $redirect->withErrors($error_message);
            }
        }

        $text->push();

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
        
        return view('corpus.text.show')
                  ->with(['text'=>$text,
                          'labels'=>join(', ',$labels)]);
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
        $informant_values = [NULL => ''] + Informant::getList();
        $place_values = [NULL => ''] + Place::getList();

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
                          'informant_values' => $informant_values,
                          'place_values' => $place_values,
                          'recorder_values' => $recorder_values,
                          'recorder_value' => $recorder_value,
                          'dialect_values' => $dialect_values,
                          'dialect_value' => $dialect_value,
                          'genre_values' => $genre_values,
                          'genre_value' => $genre_value,
                         ]);
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
        
        $text = Text::with('transtext','event','source')->get()->find($id);
        $old_text = $text->text;
        $text->fill($request->only('corpus_id','lang_id','title','text','text_xml'));

        Transtext::storeTranstext($request->only('transtext_lang_id','transtext_title','transtext_text','transtext_text_xml'), 
                                                  $text);
        Event::storeEvent($request->only('event_informant_id','event_place_id','event_date'), 
                                                  $text);
        Source::storeSource($request->only('source_title', 'source_author', 'source_year', 'source_ieeh_archive_number1', 'source_ieeh_archive_number2', 'source_pages', 'source_comment'), 
                                                  $text);
        
        if ($text->event) {
            $text-> event->recorders()->detach();
            $text-> event->recorders()->attach($request->event_recorders);
        }
        
        $text->dialects()->detach();
        $text->dialects()->attach($request->dialects);

        $text->genres()->detach();
        $text->genres()->attach($request->genres);

        $redirect = Redirect::to('/corpus/text/'.($text->id))
                       ->withSuccess(\Lang::get('messages.updated_success'));

        if ($request->text && $old_text != $request->text || $request->text && !$text->text_xml) {
            $error_message = $text->markup();
            if ($error_message) {
                $redirect = $redirect->withErrors($error_message);
            }
        }

        $text->push();        
         
        return $redirect;
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
                return Redirect::to('/corpus/text/')
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/corpus/text/')
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
        $lang_id = (int)$request->input('lang_id');
//        $lemma_id = (int)$request->input('lemma_id');

        $list = [];
        $dialects = Dialect::where('lang_id',$lang_id)
                       ->where(function($q) use ($dialect_name){
                            $q->where('name_en','like', $dialect_name)
                              ->orWhere('name_ru','like', $dialect_name);
                         })->orderBy('name_'.$locale)->get();
                         
        foreach ($dialects as $dialect) {
            $list[]=['id'  => $dialect->id, 
                     'text'=> $dialect->name];
        }  

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
                  ->with(['text' => $text]);
    }

    /**
     * Markup all texts and transtexts
     */
    public function markupAllTexts()
    {
        $texts = Text::all();
        foreach ($texts as $text) {
            $message_error = $text->markup();
            print "<p>$message_error</p>";
            $text->save();            
        }
        
        $texts = Transtext::all();
        foreach ($texts as $text) {
            $text->markup();
            $text->save();            
        }
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
