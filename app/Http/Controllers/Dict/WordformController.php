<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use DB;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

//use App\Models\Dict\Gramset;
use App\Models\Dict\Dialect;
use App\Models\Dict\Lemma;
use App\Models\Dict\Lang;
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
        $this->middleware('auth:ref.edit,/dict/wordform/', ['only' => ['create','store','edit','update','destroy']]);
        
        $this->url_args = [
                    'limit_num'       => (int)$request->input('limit_num'),
                    'page'            => (int)$request->input('page'),
                    'search_dialect'  => (int)$request->input('search_dialect'),
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
        $wordforms = Wordform::orderBy('wordform');
        
        if ($this->url_args['search_wordform']) {
            $wordforms = $wordforms->where('wordform','like', $this->url_args['search_wordform']);
        } 

        $search_lang = $this->url_args['search_lang'];
        $search_pos = $this->url_args['search_pos'];
        $search_dialect = $this->url_args['search_dialect'];

        if ($search_dialect || !$search_lang) {
            $dialect = Dialect::find($search_dialect);
            if ($dialect) {
                $search_lang = $this->url_args['search_lang'] =
                        $dialect->lang_id;
            }
        }
//        if ($search_lang || $search_pos || $search_dialect) {
            $wordforms = $wordforms->join('lemma_wordform', 'wordforms.id', '=', 'lemma_wordform.wordform_id');
//        }
        
        if ($search_lang) {
            $wordforms = $wordforms->whereIn('lemma_id',function($query) use ($search_lang){
                        $query->select('id')
                        ->from(with(new Lemma)->getTable())
                        ->where('lang_id', $search_lang);
                    });
        } 
         
        if ($search_pos) {
            $wordforms = $wordforms->whereIn('lemma_id',function($query) use ($search_pos){
                        $query->select('id')
                        ->from(with(new Lemma)->getTable())
                        ->where('pos_id',$search_pos);
                    });
        } 
         
        if ($search_dialect) {
            $wordforms = $wordforms->where('dialect_id',$search_dialect);
                    
/*            $wordforms = $wordforms->whereIn('id',function($query) use ($search_dialect){
                        $query->select('wordform_id')
                        ->from("lemma_wordform")
                        ->where('dialect_id',$search_dialect);
                    }); */
        } 
//dd($wordforms->toSql());        
         
        $numAll = $wordforms->count();
        
        $wordforms = $wordforms->paginate($this->url_args['limit_num']);
                //take($limit_num)->get();
        
        foreach ($wordforms as $wordform) {
            $lemmas = [];
            foreach ($wordform->lemmas as $lemma) {
               if (!$search_lang || $lemma->lang_id == $search_lang) {
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
 
        return view('dict.wordform.index')
                  ->with([
                      'dialect_values' => $dialect_values,
                      'lang_values' => $lang_values,
                      'numAll' => $numAll,
                      'pos_values' => $pos_values,
                      'wordforms' => $wordforms,
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
        return Redirect::to('/dict/wordform/');
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
        $wordform->wordform = trim($request->wordform);
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
 *
 */  
    
    public function tempCheckWordformsWithSpaces() {
//print "<pre>";        
        $wordforms = Wordform::where('wordform','like','% %')
//                ->where('id','>',51739)
                ->orderBy('id')->get();//take(10)->
        $count = 1;
        foreach ($wordforms as $wordform) {
            print "<p>".$count++.') '.$wordform->id.', '.$wordform->wordform;
            $wordform_id = $wordform->id;
            if ($wordform->lemmas()->count()) { 
                $trim_word = trim($wordform->wordform);
                if ($trim_word != $wordform->wordform) {
                    $wordform->wordform = $trim_word;
                    $wordform->save();                    
                    print "<br>Wordform saved";
                }
                $words = preg_split("/\s+/",$trim_word);
                if (sizeof($words)<2) { continue; }

                $langs = $wordform->langsArr();
                //print "<br>";
                //print_r($words);
                $word_coll = Word::where('word','like',$words[sizeof($words)-1])
                        ->whereIn('text_id',function($query) use ($langs){
                                $query->select('id')
                                ->from(with(new Text)->getTable())
                                ->whereIn('lang_id',$langs);
                            })->get();
                if (!sizeof($word_coll)) { continue; }        
                print "<br><span style='color:red'>BINGO!</span>: ".sizeof($word_coll);
                foreach ($word_coll as $last_word) {
                    $founded = true;
                    $word_founded=[$last_word->w_id => $last_word->word];
                    $curr_word = $last_word;
                    $i=sizeof($words)-2;
                    while ($founded && $i>=0) {
                        $curr_word = $curr_word->leftNeighbor();
                        if (!$curr_word) { 
                            $founded = false;
                            continue;                            
                        }
                        $word_founded[$curr_word->w_id] = $curr_word->word;
                        if ($curr_word->word != $words[$i]) {
                            $founded = false;
                            continue;
                        }
                        $i--;
                    }
                    ksort($word_founded);
                    if ($founded) {
                        print "<br><span style='color:red'>FOUNDED: </span>";
                        print $last_word->text_id.' | '.join(',',array_keys($word_founded));
                        $last_word->text->mergeWords($word_founded);
                    }
                }
            } else {
                $wordform->delete();
                print "<br>Wordform deleted";
            }
            print "</p>";
        }
    }    
}

// a lemma and wordform related by more than once
// select lemma_id, wordforms.wordform as wordform, count(*) as count from lemma_wordform,wordforms where wordforms.id=lemma_wordform.wordform_id group by lemma_id, wordform having count>1;
// select wordforms.wordform as wordform, count(*) as count from lemma_wordform, wordforms where wordforms.id=lemma_wordform.wordform_id group by wordform having count>1 order by count;