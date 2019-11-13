<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use DB;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

use App\Library\Grammatic;

use App\Models\Dict\Dialect;
use App\Models\Dict\Gramset;
use App\Models\Dict\Lemma;
use App\Models\Dict\Lang;
use App\Models\Dict\Meaning;
use App\Models\Dict\PartOfSpeech;
use App\Models\Dict\Wordform;
use App\Models\Corpus\Text;
use App\Models\Corpus\Word;

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
        
        $this->url_args = [
                    'limit_num'       => (int)$request->input('limit_num'),
                    'page'            => (int)$request->input('page'),
                    'search_dialect'  => (int)$request->input('search_dialect'),
                    'search_gramset'  => (int)$request->input('search_gramset'),
                    'search_lang'     => (int)$request->input('search_lang'),
                    'search_pos'      => (int)$request->input('search_pos'),
                    'search_wordform' => $request->input('search_wordform'),
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
        
       $wordforms = Wordform::search($url_args);
        $numAll = $wordforms->count();
        
        $wordforms = $wordforms->paginate($url_args['limit_num']);
                //take($limit_num)->get();
        
        foreach ($wordforms as $wordform) {
            $lemmas = [];
            foreach ($wordform->lemmas as $lemma) {
               if (!$url_args['search_lang'] || $lemma->lang_id == $url_args['search_lang']) {
                   $lemmas[$lemma->id] = $lemma;
               } 
            }
            $wordform['lemmas'] = array_values($lemmas);
        }

        $pos_values = PartOfSpeech::getGroupedList();
//        $pos_values = PartOfSpeech::getGroupedListWithQuantity('wordforms');
       
        $lang_values = Lang::getList();
        //$lang_values = Lang::getListWithQuantity('wordforms');
                                
        $dialect_values = Dialect::getList();
        $gramset_values = Gramset::getList($url_args['search_pos'],$url_args['search_lang'],true);

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
        $dialect_values = Dialect::getList($lemma->lang_id); //['NULL'=>'']+
        $meaning_values = Meaning::getList($lemma_id);
        
        $pos_name = $lemma->pos->name;
        $dialect_value = $text->dialectValue();
        
        return view('dict.wordform._form_create_fields')
                  ->with(['dialect_value'=>$dialect_value,
                          'dialect_values' => $dialect_values,
                          'gramset_values' => $gramset_values,
                          'meaning_values' => $meaning_values,
                          'pos_name'=>$pos_name]);
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
        
        // LEMMA UPDATING
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
    
    /** 
     * (1) Copy vepsian.wordform to vepkar.wordforms (without dublicates)
     * (2) Copy vepsian.lemma_gram_wordform to vepkar.lemma_wordform
     */
/*    public function tempInsertVepsianWordform()
    {
        $lemma_wordfoms = DB::connection('vepsian')
                            ->table('lemma_gram_wordform')
                            ->orderBy('lemma_id','wordform_id')
                            //->take(1)
                            ->get();
 
        DB::connection('mysql')->table('lemma_wordform')->delete();

        DB::connection('mysql')->table('wordforms')->delete();
        DB::connection('mysql')->statement('ALTER TABLE wordforms AUTO_INCREMENT = 1');
        
        
        foreach ($lemma_wordfoms as $lemma_wordform):
            $veps_wordform = DB::connection('vepsian')
                            ->table('wordform')
                            ->find($lemma_wordform->wordform_id);
            $wordform = Wordform::firstOrNew(['wordform' => $veps_wordform->wordform]); 
            $wordform->updated_at = $veps_wordform->modified;
            $wordform->created_at = $veps_wordform->modified;
            $wordform->save();
            
            if ($lemma_wordform->gram_set_id === 0) {
                $lemma_wordform->gram_set_id = NULL;
            }
            
            DB::connection('mysql')->table('lemma_wordform')->insert([
                    'lemma_id' => $lemma_wordform->lemma_id,
                    'wordform_id' => $wordform->id,
                    'gramset_id' => $lemma_wordform->gram_set_id,
                    //'created_at' => $wordform->updated_at,
                    //'updated_at' => $wordform->created_at
                ]
            );
                
        endforeach;
     }
    
    public function tempCheckWordformsWithSpaces(Request $request) {
//print "<pre>";        
        $id = $request->id;
        $wordforms = Wordform::where('wordform','like','% %');
        if ($id) {
            $wordforms = $wordforms->where('id','>',$id);
        }
        $wordforms = $wordforms->orderBy('id')->get();//take(10)->
        $count = 1;
        foreach ($wordforms as $wordform) {
            print "<p>".$count++.') '.$wordform->id.', '.$wordform->wordform;
            if ($wordform->lemmas()->count()) { 
                print $wordform->trimWord() ? '<br>Wordform saved' : '';
                $wordform->checkWordformWithSpaces(1);
            } else {
                $wordform->delete();
                print "<br>Wordform deleted";
            }
            print "</p>";
        }
    }    
    
    public function tmpFixNegativeVepsVerbForms() {
        $lang_id = 1;
        $gramsets = [70, 71, 72, 73, 78, 79, 80, 81, 82, 83, 84, 85, 50, 74, 76, 77, 116, 117, 118, 119, 120, 121];
        $dialect_id=43;
        foreach ($gramsets as $gramset_id) {
            $negation = Grammatic::negativeForm($gramset_id, $lang_id);
            $lemmas = Lemma::where('lang_id', $lang_id)
                    ->whereIn('id', function($query) use ($dialect_id, $gramset_id) {
                        $query->select('lemma_id')->from('lemma_wordform')
                              ->where('gramset_id',$gramset_id)
                              ->where('dialect_id',$dialect_id);
                    })->where('id','<>',828)->where('id','<>',652)
                    ->orderBy('lemma')->get();
            $count = 1;
            foreach($lemmas as $lemma) {
                foreach ($lemma->wordforms()->wherePivot('gramset_id', $gramset_id)->get() as $wordform) {
                    if (preg_match("/^".$negation."/", $wordform->wordform)) { continue; }
                    $new_wordform = $negation.$wordform->wordform;
                    print "<p>".$count++.'. '.$lemma->id.'. '.$new_wordform;
                    $lemma->wordforms()
                          ->wherePivot('wordform_id',$wordform->id)
                          ->wherePivot('gramset_id',$gramset_id)
                          ->wherePivot('dialect_id',$dialect_id)
                          ->detach();                    
                    $lemma->addWordform($new_wordform, $gramset_id, $dialect_id); 
                }
            }
        }
    }
 *
 */  
}

// a lemma and wordform related by more than once
// select lemma_id, wordforms.wordform as wordform, count(*) as count from lemma_wordform,wordforms where wordforms.id=lemma_wordform.wordform_id group by lemma_id, wordform having count>1;
// select wordforms.wordform as wordform, count(*) as count from lemma_wordform, wordforms where wordforms.id=lemma_wordform.wordform_id group by wordform having count>1 order by count;