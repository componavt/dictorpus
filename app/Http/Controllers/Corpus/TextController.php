<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

use App\Models\Dict\Lang;

use App\Models\Corpus\Corpus;
use App\Models\Corpus\Informant;
use App\Models\Corpus\Place;
use App\Models\Corpus\Recorder;
use App\Models\Corpus\Text;
use App\Models\Corpus\Transtext;

class TextController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // permission= dict.edit, redirect failed users to /dict/lemma/, authorized actions list:
        $this->middleware('auth:corpus.edit,/corpus/text/', ['only' => 'create','store','edit','update','destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $text_title = $request->input('text_title');
        $limit_num = (int)$request->input('limit_num');
        $lang_id = (int)$request->input('lang_id');
        $corpus_id = (int)$request->input('corpus_id');
        $page = (int)$request->input('page');

        if (!$page) {
            $page = 1;
        }
        
        if ($limit_num<=0) {
            $limit_num = 10;
        } elseif ($limit_num>1000) {
            $limit_num = 1000;
        }   
        
        // select * from `texts` where (`transtext_id` in (select `id` from `transtexts` where `title` = '%nitid_') or `title` like '%nitid_') and `lang_id` = '1' order by `title` asc limit 10 offset 0
        // select texts by title from texts and translation texts
        $texts = Text::orderBy('title');

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

        if ($lang_id) {
            $texts = $texts->where('lang_id',$lang_id);
        } 

        if ($corpus_id) {
            $texts = $texts->where('corpus_id',$corpus_id);
        } 

        $numAll = $texts->count();

        $texts = $texts->paginate($limit_num);
        
        $corpus_values = Corpus::getListWithQuantity('texts');
        
        //$lang_values = Lang::getList();
        $lang_values = Lang::getListWithQuantity('texts');

        return view('corpus.text.index')
                    ->with(['texts' => $texts,
                            'limit_num' => $limit_num,
                            'text_title' => $text_title,
                            'lang_id'=>$lang_id,
                            'corpus_id'=>$corpus_id,
                            'page'=>$page,
                            'lang_values' => $lang_values,
                            'corpus_values' => $corpus_values,
                            'numAll' => $numAll,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        $text = Text::find($id);
        
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
        $informant_values = Informant::getList();
        $place_values = [NULL => ''] + Place::getList();
        $recorder_values = Recorder::getList();
        
        $recorder_value = [];
        if ($text->event && $text->event->recorders) {
            $recorders = $text->event->recorders;
            foreach ($recorders as $recorder) {
                $recorder_value[] = $recorder->id;
            }
        }
//var_dump($recorder_value);
        return view('corpus.text.edit')
                  ->with(['text' => $text,
                          'lang_values' => $lang_values,
                          'corpus_values' => $corpus_values,
                          'informant_values' => $informant_values,
                          'place_values' => $place_values,
                          'recorder_values' => $recorder_values,
                          'recorder_value' => $recorder_value
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
        $text = Text::with('transtext')->find($id); //,'event','source'
        $text->fill($request->only('corpus_id','lang_id','title','text')); //,'source_id','event_id'
        $text->transtext->fill(only('transtext.lang_id','transtext.title','transtext.text'));
        $text->push();
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
}
