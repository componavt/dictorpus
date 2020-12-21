<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use DB;

//use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

use App\Library\Grammatic;
use App\Library\Str;

use App\Models\Dict\Dialect;
use App\Models\Dict\Gramset;
use App\Models\Dict\Lemma;
use App\Models\Dict\Lang;
use App\Models\Dict\Meaning;
use App\Models\Dict\PartOfSpeech;
use App\Models\Dict\Wordform;
use App\Models\Corpus\Text;

class WordformController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('auth:dict.edit,/dict/wordform/', 
                ['except'=>['show', 'withMultipleLemmas', 'index']]);
//                ['only' => ['create','store','edit','update','destroy','tmpFixNegativeVepsVerbForms']]);
        
        $this->url_args = Wordform::urlArgs($request);  
        
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
        
        $wordforms = Wordform::search($url_args);
        $numAll = $wordforms->count();
        
        $wordforms = $wordforms->paginate($url_args['limit_num']);
                //take($limit_num)->get();
        
        $lang_id = $url_args['search_lang'];
        $pos_id = $url_args['search_pos'];
        foreach ($wordforms as $wordform) {
            $lemmas = [];
//dd($wordform);            
//dd($url_args['search_gramset']);            
            foreach ($wordform->lemmas as $lemma) {
//dd($lemma->pivot);                
               if ((!$lang_id || $lemma->lang_id == $lang_id) 
                       && (!$pos_id || $lemma->pos_id == $pos_id)
                  && (!$url_args['search_gramset'] || $lemma->pivot->gramset_id == $url_args['search_gramset'])
                  && (!$url_args['search_dialect'] || $lemma->pivot->dialect_id == $url_args['search_dialect'])
                   ){
                   $lemmas[$lemma->id] = $lemma;
               } 
            }
            $wordform['lemmas'] = array_values($lemmas);
        }

        $pos_values = PartOfSpeech::getGroupedList();
//        $pos_values = PartOfSpeech::getGroupedListWithQuantity('wordforms');
       
        $lang_values = Lang::getList();
        //$lang_values = Lang::getListWithQuantity('wordforms');
                                
        //$dialect_values = Dialect::getList();
        $dialect_values = $lang_id ? [NULL=>'']+Dialect::getList($lang_id): [];
        $gramset_values = $pos_id ? [NULL=>'']+Gramset::getList($pos_id,$lang_id,true): [];

        return view('dict.wordform.index',
                compact('dialect_values', 'gramset_values', 'lang_values', 'numAll',
                        'pos_values', 'wordforms', 'args_by_get', 'url_args'));
    }
    
    /**
     * Shows the form fields for creating a new wordform.
     * 
     * Called by ajax request
     * /dict/wordform/create?lemma_id=10603&text_id=1548
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $lemma_id = (int)$request->input('lemma_id');
        $text_id = (int)$request->input('text_id'); 
        if (!$lemma_id || !$text_id) {
            return;
        }
        
        $lemma = Lemma::find($lemma_id);
        $text = Text::find($text_id);
        if (!$lemma || !$text) {
            return;
        }

        $gramset_values = ['NULL'=>'']+Gramset::getGroupedList($lemma->pos_id,$lemma->lang_id,true);
        $meaning_values = Meaning::getList($lemma_id);
        
        $pos_name = $lemma->pos->name;
        $dialect_values = Dialect::getList($lemma->lang_id); //['NULL'=>'']+
        $dialect_value = $text->dialectValue();
        
        return view('dict.wordform._form_create_fields',
                  compact('dialect_value', 'dialect_values', 'gramset_values',
                          'meaning_values', 'pos_name'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return Redirect::to('/dict/wordform/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Redirect::to('/dict/wordform/');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $wordform = Wordform::find($id);
        return view('dict.wordform.edit')
                ->with(['wordform'=>$wordform,
                        'args_by_get'    => $this->args_by_get,
                        'url_args'       => $this->url_args,
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
        $wordform = Wordform::find($id);
        $this->validate($request, [
            'wordform'  => 'required|max:255',
        ]);
        
        // Wordform UPDATING
        $trim_word = Grammatic::toRightForm($request->wordform);        
        $wordform->wordform = trim($trim_word);
        $wordform->wordform_for_search = Grammatic::toSearchForm($trim_word);
        $wordform->save();
        
        return Redirect::to('/dict/wordform/'.($this->args_by_get))
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
        return Redirect::to('/dict/wordform/');
    }
    
    
    /** Lists wordforms associated with more than one lemma.
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function withMultipleLemmas()
    {
//select wordforms.wordform as wordform, count(*) as count from lemma_wordform, wordforms where 
//wordforms.id=lemma_wordform.wordform_id and lemma_id in (select id from lemmas where lang_id=1) 
//group by wordform having count>1 order by count;
        $builder = DB::table('wordforms')->
                     join('lemma_wordform', 'wordforms.id', '=', 'lemma_wordform.wordform_id')
                     ->select(DB::raw('wordform_id, count(*) as count'))
                     ->groupBy('wordform_id')
                     ->having('count', '>', 1);
        
        if ($this->url_args['search_wordform']) {
            $builder = $builder->where('wordform','like', $this->url_args['search_wordform']);
        } 

        $search_lang = $this->url_args['search_lang'];
        if ($search_lang) {
            $builder = $builder->whereIn('lemma_id',function($query) use ($search_lang){
                        $query->select('id')
                        ->from(with(new Lemma)->getTable())
                        ->where('lang_id', $search_lang);
                    });
        } 
         
        $builder = $builder->orderBy('count', 'DESC')
                           ->orderBy('wordform');
//                ->with('lemmas');
  /*      $builder = $builder->with(['wordforms'=> function ($query) {
                                    $query->orderBy('wordform');
                                }]);*/

        $wordforms = [];

        foreach ($builder->get() as $wordform) {
            $wordform_obj = Wordform::find($wordform->wordform_id);            
//dd($wordform_obj->lemmas);
            $lemmas = [];
            foreach ($wordform_obj->lemmas as $lemma) {
               if (!$search_lang || $lemma->lang_id == $search_lang) {
                   $lemmas[$lemma->id] = $lemma;
               } 
            }
            if (sizeof($lemmas)>1) {
                $wordform_obj['lemmas'] = array_values($lemmas);
                $wordforms[] = $wordform_obj;
            }
        }
 //dd($wordforms);       
        $lang_values = Lang::getList();
                                
        return view('dict.wordform.with_multiple_lemmas')
                  ->with([
                        'lang_values' => $lang_values,
                        'wordforms' => $wordforms,
                        'args_by_get'    => $this->args_by_get,
                        'url_args'       => $this->url_args,
                         ]);
    }
}

// a lemma and wordform related by more than once
// select lemma_id, wordforms.wordform as wordform, count(*) as count from lemma_wordform,wordforms where wordforms.id=lemma_wordform.wordform_id group by lemma_id, wordform having count>1;
// select wordforms.wordform as wordform, count(*) as count from lemma_wordform, wordforms where wordforms.id=lemma_wordform.wordform_id group by wordform having count>1 order by count;