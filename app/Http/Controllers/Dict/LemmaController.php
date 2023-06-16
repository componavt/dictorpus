<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use DB;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Response;
use LaravelLocalization;

use App\Models\User;

use App\Library\Grammatic;

use App\Models\Corpus\MeaningTextRel;
use App\Models\Corpus\Place;
use App\Models\Corpus\SentenceFragment;
use App\Models\Corpus\SentenceTranslation;

use App\Models\Dict\Concept;
use App\Models\Dict\ConceptCategory;
use App\Models\Dict\Dialect;
use App\Models\Dict\Gramset;
use App\Models\Dict\Label;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\LemmaFeature;
use App\Models\Dict\Meaning;
use App\Models\Dict\MeaningText;
use App\Models\Dict\PartOfSpeech;
use App\Models\Dict\Relation;

class LemmaController extends Controller
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
        // permission= dict.edit, redirect failed users to /dict/lemma/, authorized actions list:
        $this->middleware('auth:dict.add,/dict/lemma/', 
                ['only' => ['create','store', 'createPhonetic']]);
        $this->middleware('auth:dict.edit,/dict/lemma/', 
                ['only' => ['edit','update','destroy',
                            'editExample', 'removeExample',
                            'editExamples','updateExamples',
                            'storeSimple', 'store',
                            'createWordform', 'updateWordformFromText',
                            'editWordforms','updateWordforms', 'checkWordforms',
                            'reloadStemAffixByWordforms'
                    ]]);
        
        $this->url_args = Lemma::urlArgs($request);  
        
        $this->args_by_get = search_values_by_URL($this->url_args);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * 
     */
    public function index()
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        $lemmas = Lemma::search($url_args);
//dd($lemmas->toSql());        
        $numAll = $lemmas->count();

        $lemmas = $lemmas->paginate($url_args['limit_num']);         
//dd($lemmas);        
        $pos_values = [NULL=>'']+PartOfSpeech::getGroupedListWithQuantity('lemmas');
        
        //$lang_values = Lang::getList();
        $lang_values = [NULL=>'']+Lang::getListWithQuantity('lemmas', !user_dict_edit());
        $gramset_values = Gramset::getList($url_args['search_pos'],$url_args['search_lang'],true);
        $dialect_values = Dialect::getList($url_args['search_lang']);
        $concept_category_values = [NULL=>'']+ConceptCategory::getList();
        $concept_values = [NULL=>'']+Concept::getList($url_args['search_concept_category'], $url_args['search_pos']);
        $not_changeable_pos_list = PartOfSpeech::notChangeablePOSIdList();
//dd($url_args['search_concept'], $concept_values);
        return view('dict.lemma.index',
                compact('concept_values', 'concept_category_values', 'dialect_values', 
                        'gramset_values', 'lang_values', 'lemmas', 'numAll',
                        'not_changeable_pos_list', 'pos_values', 'args_by_get', 'url_args'));
    }

    public function simpleSearch(Request $request) {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        $lemmas = Lemma::simpleSearch($url_args['search_w']);
        $numAll = $lemmas->count();
        $lemmas = $lemmas->orderBy('lemma')->paginate($url_args['limit_num']);         
        
        return view('dict.lemma.search.simple', 
                compact('lemmas', 'numAll', 'args_by_get', 'url_args'));        
    }
    
    /**
     * Search lemmas by wordforms with grammatical attributes
     * 
     * @return \Illuminate\Http\Response
     */
    public function byWordforms()
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        $lemmas = Lemma::searchByWordformGrams($url_args);

        $numAll = $lemmas->count();

        $lemmas = $lemmas->paginate($url_args['limit_num']);         

        $pos_values = PartOfSpeech::getChangeableListWithQuantity('lemmas');
        
        $lang_values = Lang::getListWithQuantity('lemmas',true);
        
        $gramset_values = [NULL=>'']+Gramset::getList($url_args['search_pos'],$url_args['search_lang'],true);
        $dialect_values = $url_args['search_lang'] ? [NULL=>'']+Dialect::getList($url_args['search_lang']): [];
                
        return view('dict.lemma.by_wordforms',
                compact('dialect_values', 'gramset_values', 'lang_values', 
                        'lemmas', 'pos_values', 'numAll', 'args_by_get', 'url_args'));
    }

    public function wordformGramForm(Request $request)
    {
        $count = (int)$request->input('count');
        $lang_id = (int)$request->input('lang_id');
        $pos_id = (int)$request->input('pos_id');
        $gramset_values = $pos_id ? [NULL=>'']+Gramset::getList($pos_id,$lang_id,true): [];
                                
        return view('dict.lemma.search._wordform_gram_form',
                 compact('count', 'gramset_values'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        $phrase_values = (array)$request->input('phrase_lemmas');
        $pos_id = (int)$request->input('pos_id');
        if (!$pos_id) {
            $pos_id = PartOfSpeech::getIDByCode('Noun');
        }

        $lang_id = User::userLangID();
//        $dialect_id = User::userDialectID();
        $wordform_dialect_value = User::userDialects();
//var_dump($dialect_value);        
        $new_meaning_n = 1;
        
        $pos_values = PartOfSpeech::getGroupedList();   
        $lang_values = Lang::getList();
//        $langs_for_meaning = Lang::getListInterface();
        $langs_for_meaning = Lang::getListForMeaning();
                
        $dialect_values = $lang_id ? Dialect::getList($lang_id) : Dialect::getList(); //['NULL'=>'']+
//dd($dialect_values);        
        return view('dict.lemma.create',
                  compact('wordform_dialect_value', 'dialect_values', 'langs_for_meaning', 
                          'lang_id', 'lang_values', 'new_meaning_n', 'phrase_values', 
                          'pos_id', 'pos_values', 'args_by_get', 'url_args'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//dd($request->all());        
        $this->validate($request, [
            'lemma'  => 'required|max:255',
            'lang_id'=> 'required|numeric',
//            'pos_id' => 'numeric',
        ]);
        
        $lemma = Lemma::storeLemma($request->all());

        LemmaFeature::store($lemma->id, $request);
        $lemma->storePhrase($request->phrase);
//        $lemma->storeDialects($request->dialects);
        
        Meaning::storeLemmaMeanings($request->new_meanings, $lemma->id);
        
//        $lemma->updateTextLinks();

        return Redirect::to('/dict/lemma/'.($lemma->id).($this->args_by_get))
            ->withSuccess(\Lang::get('messages.created_success'));        
    }

    public function createPhonetic($id, Request $request) {
        $old_lemma = Lemma::find($id);
        if (!$old_lemma || !$request->new_lemma) {
            return Redirect::to('/dict/lemma/'.($id).($this->args_by_get ? $this->args_by_get. '&' : '?'))
                           ->withError("Can't create empty lemma");        
        }
        $data = $old_lemma->dataForCopy($request->new_lemma);
        list($new_lemma, $wordforms, $stem, $affix, $gramset_wordforms, $stems) 
                = Grammatic::parseLemmaField($data);
        $lemma = Lemma::store($new_lemma, $old_lemma->pos_id, $old_lemma->lang_id);
        $lemma->storeAddition($wordforms, $stem, $affix, $gramset_wordforms, $data, $data['wordform_dialect_id'], $stems);      
        Meaning::storeLemmaMeanings($data['new_meanings'], $lemma->id);
        return Redirect::to('/dict/lemma/'.($lemma->id).($this->args_by_get ? $this->args_by_get. '&' : '?').'update_text_links=1')
                       ->withSuccess(\Lang::get('messages.created_success'));        
    }

    /**
     * Store a newly created resource in storage.
     * 
     * URL: /dict/lemma/store_simple?lang_id=4&lemma=täh&meaning=test1&pos_id=5
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeSimple(Request $request)
    {
        $this->validate($request, [
            'lemma'  => 'required|max:255',
            'lang_id'=> 'required|numeric',
            'pos_id' => 'numeric',
//            'label_id' => 'nullable|numeric', // nullable неизвестен
        ]);
        
//        $lemma = Lemma::storeLemma($request->all());
//dd($request->all());
//        LemmaFeature::store($lemma->id, $request);

        for ($i=0; $i<2; $i++) {
            $meaning_text = $request->{'meaning'. ($i>0 ? $i : '')}; 
            if ($meaning_text) {
                $new_meanings[$i]=['meaning_n' => $i+1,
                                  'meaning_text' =>
                                    [Lang::getIDByCode('ru') => $meaning_text]];    
            }
        }
        Meaning::storeLemmaMeanings($new_meanings, $lemma->id);
        $lemma->updateTextLinks();
        
        if ($request->label_id) {
            $label_id = $request->label_id;
            $lemma->labels()->attach([$label_id]);
            foreach ($lemma->meanings as $meaning) {
                $meaning->labels()->attach([$label_id]);
            }
            if ($label_id == Label::ZaikovLabel) {
                return view('service.dict.zaikov._row', 
                        compact('label_id', 'lemma'));
            }
        }
        
        return $lemma->id;        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $lemma = Lemma::find($id);
        if (!$lemma)
            return Redirect::to('/dict/lemma/'.($this->args_by_get))
                           ->withErrors('error.no_lemma');

        $update_text_links = (int)$request->update_text_links;

        $langs_for_meaning = Lang::getListWithPriority($lemma->lang_id);
//dd($relations);
        $meanings = $lemma->meanings;
        $meaning_texts = $lemma->getMeaningTexts();
        $meaning_relations = $lemma->getMeaningRelations();
        $translation_values = $lemma->getMeaningTranslations();
                  
        $dialect_values = Dialect::getList($lemma->lang_id);
        $phrases = $lemma->phrases->sortBy('lemma');
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        $user = User::currentUser();
        $informant_id = $user ? $user->informant_id : NULL;
        
        return view('dict.lemma.show',
                  compact('dialect_values', 'informant_id', 'lemma', 'meaning_texts',
                          'meaning_relations', 'phrases', 'translation_values',
                          'update_text_links', 'args_by_get', 'url_args'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        $lemma= Lemma::findOrFail((int)$id);
        if (!$lemma) {
            return Redirect::to('/dict/lemma/'.($this->args_by_get))
                           ->withErrors('error.no_lemma');
        }
        $pos_values = ['NULL'=>''] + PartOfSpeech::getGroupedList(); 
        $lang_values = Lang::getList();
//        $gramset_values = ['NULL'=>'']+Gramset::getList($lemma->pos_id,$lemma->lang_id,true);
        $langs_for_meaning = Lang::getListWithPriority($lemma->lang_id);
        $new_meaning_n = $lemma->getNewMeaningN();
        $relation_values = Relation::getList();

        $all_meanings = [];
        $lemmas = Lemma::where('lang_id',$lemma->lang_id)
                       ->where('pos_id',$lemma->pos_id)
                       ->where('id','<>',$lemma->id) 
                       ->orderBy('lemma')->get();
        foreach ($lemmas as $lem) {
            foreach ($lem->meanings as $meaning) {
                $all_meanings[$meaning->id] = $lem->lemma .' ('.$meaning->getMultilangMeaningTextsString().')';
            }
        }  

        $relation_meanings = 
        $meaning_relation_values = 
        $translation_values = [];
        
        foreach ($lemma->meanings as $meaning) {
            foreach ($meaning->meaningRelations as $meaning_relation) {
                $relation_id = $meaning_relation->pivot->relation_id;
                $meaning2_id = $meaning_relation->pivot->meaning2_id;
                $relation_meanings[$meaning->id][$relation_id][] = $meaning2_id;
                if (isset($all_meanings[$meaning2_id])) {
                    $meaning_relation_values[$meaning2_id] = $all_meanings[$meaning2_id];
                }
            }
            
            foreach (array_keys($langs_for_meaning) as $l_id) {
                $translation_values[$meaning->id][$l_id] = [];
                $meaning_translations = $meaning->translations()->wherePivot('lang_id',$l_id)->get();
                if ($meaning_translations) {
                    foreach ($meaning_translations as $meaning_translation) {
                        $meaning2_id = $meaning_translation->pivot->meaning2_id; 
                        $meaning2_obj = Meaning::find($meaning2_id);
                        if ($meaning2_obj) {
                            $translation_values[$meaning->id][$l_id][$meaning2_id] 
                                = $meaning2_obj->getLemmaMultilangMeaningTextsString();
                        }
                    }
                }
            }
        }   
            
        $all_meanings = $meaning_relation_values;
        $phrase_values = $lemma->phraseLemmas->pluck('lemma', 'id')->toArray();
        $wordform_dialect_value = User::userDialects();
        
        $lemma_variants = $lemma->variants->pluck('lemma', 'id')->toArray();
        $dialect_values = Dialect::getList($lemma->lang_id);
        $dialects_value = !$lemma->dialects ? []
                                    : $lemma->dialects->pluck('id')->toArray();
        $concept_values = Concept::getList(NULL, $lemma->pos_id !=PartOfSpeech::getIDByCode('PHRASE') ? $lemma->pos_id : NULL); //[NULL=>'']+
        $place_values = Place::getListByLang($lemma->lang_id);
        
        return view('dict.lemma.edit',
                    compact('all_meanings', 'concept_values', 'lang_values', 'wordform_dialect_value', 
                            'dialect_values', 'langs_for_meaning', 'lemma', 
                            'lemma_variants', 'new_meaning_n', 'dialects_value',
                            'phrase_values', 'pos_values', 'relation_values', 
                            'relation_meanings', 'translation_values', 'place_values',
                            'args_by_get', 'url_args'));
    }

    /**
     * Shows the form for editing of lemma's text examples.
     *
     * @param  int  $id - ID of lemma
     * @return \Illuminate\Http\Response
     */
    public function editExamples(Request $request, $id)
    {
        $lemma = Lemma::find($id);
        if (!$lemma) {
            return Redirect::to('/dict/lemma/'.($this->args_by_get))
                           ->withErrors('error.no_lemma');
        }
        
        $langs_for_meaning = Lang::getListWithPriority($lemma->lang_id);
        $meanings = $lemma->meanings;
        $meaning_texts = [];
          
        foreach ($meanings as $meaning) {
            foreach ($langs_for_meaning as $lang_id => $lang_text) {
                $meaning_text_obj = MeaningText::where('lang_id',$lang_id)->where('meaning_id',$meaning->id)->first();
                if ($meaning_text_obj) {
                    $meaning_texts[$meaning->id][$lang_text] = $meaning_text_obj->meaning_text;
                }
            }
        }   
        
        return view('dict.lemma.edit_examples')
                  ->with(array(
                               'back_to_url'    => '/dict/lemma/'.$lemma->id,
                               'lemma'          => $lemma, 
                               'meanings'        => $lemma->meanings,
                               'meaning_texts'  => $meaning_texts,
                               'args_by_get'    => $this->args_by_get,
                               'url_args'       => $this->url_args,
                              )
                        );
    }

    /**
     * Shows the form for editing of text example for all lemma meanings connected with this sentence.
     *
     * @param  int  $id - ID of lemma
     * @param  int  $example_id - ID of example
     * @return \Illuminate\Http\Response
     */
    public function editExample(Request $request, $id, $example_id)
    {
        $lemma = Lemma::find($id);
        if (!$lemma) {
            return Redirect::to('/dict/lemma/'.($this->args_by_get))
                           ->withErrors('error.no_lemma');
        }
        
        list($sentence, $meanings, $meaning_texts, $w_id) = 
                MeaningTextRel::preparationForExampleEdit($example_id);
        
        if ($sentence == NULL) {
            return Redirect::to('/dict/lemma/'.($lemma->id).($this->args_by_get))
                       ->withError(\Lang::get('messages.invalid_id'));            
        } 
            
        $translations = SentenceTranslation::whereSentenceId($sentence['sent_obj']->id)
                        ->whereWId($sentence['w_id'])->get();
        $fragment = SentenceFragment::getBySW($sentence['sent_obj']->id, $w_id);
        
        $back_to_url = '/dict/lemma/'.$lemma->id;
        $route = array('lemma.update.examples', $id);
        
        $langs_for_meaning = Lang::getListForMeaning();
        foreach ($translations as $translation) {
            unset($langs_for_meaning[$translation->lang_id]);
        }
        
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        return view('dict.lemma.example.edit',
                  compact('back_to_url', 'fragment', 'id', 'langs_for_meaning', 'lemma',
                          'meaning_texts', 'meanings', 'route', 'sentence', 
                          'translations', 'args_by_get', 'url_args'));            
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
       // https://laravel.com/api/5.1/Illuminate/Database/Eloquent/Model.html#method_touch
        $lemma= Lemma::find($id);
        if (!$lemma) {
            return Redirect::to('/dict/lemma/'.($this->args_by_get))
                           ->withErrors('error.no_lemma');
        }
        
        $this->validate($request, [
            'lemma'  => 'required|max:255',
            'lang_id'=> 'required|numeric',
//            'pos_id' => 'numeric',
        ]);
//dd($request->all());        
        $lemma->updateLemma($request->all());
        
        // MEANINGS UPDATING
        // existing meanings
        Meaning::updateLemmaMeanings($request->ex_meanings);

        // new meanings, i.e. meanings created by user in form now
        Meaning::storeLemmaMeanings($request->new_meanings, $id);
        
        //$lemma->updateTextLinks();
                
        return Redirect::to('/dict/lemma/'.($lemma->id).($this->args_by_get ? $this->args_by_get. '&' : '?').'update_text_links=1')
                       ->withSuccess(\Lang::get('messages.updated_success'));
    }

    /**
     * /dict/lemma/remove/example/5177_1511_1_8
     * 
     * @param type $example_id
     * @return int
     */
    public function removeExample($example_id)
    {
        if (preg_match("/^(\d+)\_(\d+)_(\d+)_(\d+)$/",$example_id,$regs)) {
            DB::statement('UPDATE meaning_text SET relevance=0'. 
                          ' WHERE meaning_id='.(int)$regs[1].
                          ' AND text_id='.(int)$regs[2].
                          ' AND s_id='.(int)$regs[3].
                          ' AND w_id='.(int)$regs[4]);            
            return 1;
        }
        return 0;
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
                $lemma = Lemma::find($id);
                if($lemma){
                    $result['message'] = \Lang::get('dict.lemma_removed', ['name'=>$lemma->lemma]);
                    $lemma->remove();
                } else{
                    $error = true;
                    $result['error_message'] = \Lang::get('messages.record_not_exists');
                }
          }catch(\Exception $ex){
                    $error = true;
//                    $status_code = $ex->getCode();
                    $result['error_code'] = $ex->getCode();
                    $result['error_message'] = $ex->getMessage();
                }
        } else{
            $error =true;
//            $status_code = 400;
            $result['message']='Request data is empty';
        }
        
        if ($error) {
            return Redirect::to('/dict/lemma/'.($this->args_by_get))
                           ->withErrors($result['error_message']);
        }
        return Redirect::to('/dict/lemma/'.($this->args_by_get))
              ->withSuccess($result['message']);
    }
    
    /**
     * Shows history of lemma.
     *
     * @param  int  $id - ID of lemma
     * @return \Illuminate\Http\Response
     */
    public function history($id)
    {
        $lemma = Lemma::find($id);
        if (!$lemma) {
            return Redirect::to('/dict/lemma/'.($this->args_by_get))
                           ->withErrors('error.no_lemma');
        }
//dd($lemma->revisionHistory);        
        return view('dict.lemma.history')
                  ->with(['lemma' => $lemma,
                               'args_by_get'    => $this->args_by_get,
                               'url_args'       => $this->url_args,
                         ]);
    }
    
    /** Gets list of longest lemmas, 
     * gets first N lemmas sorted by length.
     *  
     */
    public function sortedByLength()
    {
        $builder = Lemma::select(DB::raw('*, char_length(lemma) as char_length'))
                ->whereRaw('char_length(lemma) >10');
        
        $numAll = $builder->count();
        
        $lemmas = $builder->latest('char_length')
                          ->paginate($this->url_args['limit_num']);
                
        return view('dict.lemma.sorted_by_length')
                  ->with(array('lemmas'      => $lemmas,
                               'numAll'      => $numAll,
                               'args_by_get' => $this->args_by_get,
                               'url_args'    => $this->url_args,
                              )
                        );
    }
    
    
    /** Gets list of all semantic relations, 
     *  
     */
    public function relation()
    {
        $lemmas = Lemma::select(DB::raw('lemmas.id as lemma1_id, lemmas.lemma as lemma1, relation_id, meaning1_id, meaning2_id'))
                       ->join('meanings', 'lemmas.id', '=', 'meanings.lemma_id')
                       ->join('meaning_relation', 'meanings.id', '=', 'meaning_relation.meaning1_id');        

        if ($this->url_args['search_lemma']) {
            $lemmas = $lemmas->where('lemma','like', $this->url_args['search_lemma']);
        } 

        foreach (['lang','pos','relation'] as $var) {
            if ($this->url_args['search_'.$var]) {
                $lemmas = $lemmas->where($var.'_id',$this->url_args['search_'.$var]);
            }
        } 
         
        $numAll = $lemmas->count();
        $lemmas = $lemmas->orderBy('lemma')->paginate($this->url_args['limit_num']);

        $pos_values = PartOfSpeech::getList();//getGroupedListWithQuantity('lemmas');
        
        $lang_values = Lang::getList();//getListWithQuantity('lemmas');
        
        $relation_values = Relation::getList();//getListWithQuantity('lemmas');
        
        return view('dict.lemma.relation')
                  ->with([
                            'lemmas'          => $lemmas,
                            'lang_values'     => $lang_values,
                            'numAll'          => $numAll,
                            'pos_values'      => $pos_values,
                            'relation_values' => $relation_values,
                            'args_by_get'     => $this->args_by_get,
                            'url_args'        => $this->url_args,
                          ]
                        );
    }
        
    public function omonyms(Request $request) {
        $lemmas = Lemma::groupBy('lemma','pos_id','lang_id')
                       ->havingRaw('count(*) > 1');
        
        if ($this->url_args['search_lemma']) {
            $lemmas = $lemmas->where('lemma','like', $this->url_args['search_lemma']);
        } 

        foreach (['lang','pos'] as $var) {
            if ($this->url_args['search_'.$var]) {
                $lemmas = $lemmas->where($var.'_id',$this->url_args['search_'.$var]);
            }
        } 
         
        $ids = [];
        foreach($lemmas->get() as $lemma) {
            $omonyms = Lemma::where('lemma','like',$lemma->lemma)
                            ->where('lang_id',$lemma->lang_id)
                            ->where('pos_id',$lemma->pos_id)->get(['id']);
            foreach ($omonyms as $omonym) {
                $ids[] = $omonym->id;
            }
        }

        $lemmas = Lemma::whereIn('id',$ids);
        $numAll = $lemmas->count();
        $lemmas = $lemmas->orderBy('lemma')->paginate($this->url_args['limit_num']);

        $pos_values = PartOfSpeech::getList();        
        $lang_values = Lang::getList();
        
        return view('dict.lemma.omonyms')
                  ->with([
                            'lemmas'          => $lemmas,
                            'lang_values'     => $lang_values,
                            'numAll'          => $numAll,
                            'pos_values'      => $pos_values,
                            'args_by_get'     => $this->args_by_get,
                            'url_args'        => $this->url_args,
                          ]
                        );
    }

    /**
     * Gets list of relations for drop down list in JSON format
     * Test url: /dict/lemma/meanings_list?lang_id=1&pos_id=1&lemma_id=2810
     * 
     * @return JSON response
     */
    public function meaningsList(Request $request)
    {
        $search_lemma = '%'.$request->input('q').'%';
        $lang_id = (int)$request->input('lang_id');
        $pos_id = (int)$request->input('pos_id');
        $lemma_id = (int)$request->input('lemma_id');

        $all_meanings = [];
        $lemmas = Lemma::where('lemma','like', $search_lemma)
                       ->where('lang_id',$lang_id);
        if ($pos_id) {
            $lemmas = $lemmas->whereIn('pos_id',[$pos_id, PartOfSpeech::getPhraseID()]);
        }
        if ($lemma_id) {
            $lemmas = $lemmas->where('id','<>',$lemma_id);
        }
        $lemmas = $lemmas->orderBy('lemma')->get();
        foreach ($lemmas as $lem) {
            foreach ($lem->meanings as $meaning) {
                $all_meanings[]=['id'  => $meaning->id, 
                                 'text'=> $lem->lemma .' ('.$meaning->getMultilangMeaningTextsString().')'];
            }
        }  

        return Response::json($all_meanings);
    }
    
    /**
     * Gets list of phrase lemmas for drop down list in JSON format
     * Test url: /dict/lemma/phrase_list?lang_id=5
     * 
     * @return JSON response
     */
    public function listWithPosMeaning(Request $request)
    {
        $limit = 1000;
        $search_lemma = $request->input('q').'%';
        $lang_id = (int)$request->input('lang_id');
        $list = [];
        
        $lemmas = Lemma::where('lang_id',$lang_id)
                       ->where('pos_id','<>',PartOfSpeech::getPhraseID())
                       ->where('lemma','like', $search_lemma)
                       ->take($limit)
                       ->orderBy('lemma')->get();
        
        foreach($lemmas as $lemma) {
            $list[] = ['id'  => $lemma->id, 
                       'text'=> $lemma->lemma. ' ('.$lemma->pos->name.') '.$lemma->phraseMeaning()];
        }

        return Response::json($list);
    }
    
    /**
     * Gets list of lemmas for drop down list in JSON format
     * Test url: /dict/lemma/list?lang_id=5
     * 
     * @return JSON response
     */
    public function lemmaLangList(Request $request)
    {
        $limit = 1000;
//        $search_lemma = '%'.$request->input('q').'%';
        $search_lemma = $request->input('q').'%';
        $lang_id = (int)$request->input('lang_id');
        $list = [];
        
        $lemmas = Lemma::where('lang_id',$lang_id)
                       ->where('lemma','like', $search_lemma)
                       ->take($limit)
                       ->orderBy('lemma')->get();
        
        foreach($lemmas as $lemma) {
            $list[] = ['id'  => $lemma->id, 
                       'text'=> $lemma->lemma. ' ('.$lemma->pos->name.')'];
//                       'text'=> $lemma->lemma. ' ('.$lemma->pos->name.') '.$lemma->phraseMeaning()];
        }

        return Response::json($list);
    }
    
    public function fullNewList(Request $request)
    {
        $portion = 1000;
        $lemmas = Lemma::lastCreated($portion)
                    ->groupBy(function ($item, $key) {
                        return (string)$item['created_at']->formatLocalized(trans('main.date_format'));
                    });
        if (!$lemmas) {            
            return Redirect::to('/');
        }
        return view('dict.lemma.list.full_new')
              ->with(['new_lemmas' => $lemmas]);
    }
    
    /**
     * /dict/lemma/limited_new_list
     * @param Request $request
     * @return Response
     */
    public function limitedNewList(Request $request)
    {
        $limit = (int)$request->input('limit');
        $lemmas = Lemma::lastCreated($limit);
        if ($lemmas) {                       
            return view('dict.lemma.list.limited_new')
                      ->with(['new_lemmas' => $lemmas]);
        }
    }
    
    public function fullUpdatedList(Request $request)
    {
        $portion = 100;
        $lemmas = Lemma::lastUpdated($portion,1);                                
        if (!$lemmas) {            
            return Redirect::to('/');
        }
        return view('dict.lemma.list.full_updated')
                  ->with(['last_updated_lemmas'=>$lemmas]);
    }
    
    /**
     * /dict/lemma/limited_updated_list
     * @param Request $request
     * @return Response
     */
    public function limitedUpdatedList(Request $request)
    {
        $limit = (int)$request->input('limit');
        $lemmas = Lemma::lastUpdated($limit);
//dd($lemmas);                                
        if ($lemmas) {                       
            return view('dict.lemma.list.limited_updated')
                      ->with(['last_updated_lemmas'=>$lemmas]);
        }
    }
    
    
    /**
     * Display a page with list of phrases and lemmas which constitutes these lemmas.
     */
    public function phrases()
    {
        $lemmas = Lemma::where('pos_id', PartOfSpeech::getPhraseID())->orderBy('lemma');
        
        if ($this->url_args['search_lemma']) {
            $lemmas = $lemmas->where('lemma','like', $this->url_args['search_lemma']);
        } 

        if ($this->url_args['search_lang']) {
            $lemmas = $lemmas->where('lang_id',$this->url_args['search_lang']);
        } 
         
        if ($this->url_args['search_meaning']) {
            $search_meaning = $this->url_args['search_meaning'];
            $lemmas = $lemmas->whereIn('id',function($query) use ($search_meaning){
                                    $query->select('lemma_id')
                                    ->from('meanings')
                                    ->whereIn('id',function($query) use ($search_meaning){
                                        $query->select('meaning_id')
                                        ->from('meaning_texts')
                                        ->where('meaning_text','like', $search_meaning);
                                    });
                                });
        } 


        $lemmas = $lemmas->groupBy('lemmas.id')
                         ->with(['meanings'=> function ($query) {
                                    $query->orderBy('meaning_n');
                                }]);
        $numAll = $lemmas->get()->count();
        $lemmas = $lemmas->paginate($this->url_args['limit_num']);         

        $pos_values = PartOfSpeech::getGroupedListWithQuantity('lemmas');
        $lang_values = Lang::getListWithQuantity('lemmas');

        return view('dict.lemma.phrases')
                  ->with(array(
                               'lang_values'    => $lang_values,
                               'lemmas'         => $lemmas,
                               'numAll'         => $numAll,
                               'args_by_get'    => $this->args_by_get,
                               'url_args'       => $this->url_args,
                              )
                        );
    }
    
    /**
     * Заново выделить неизменяемую часть по существующим словоформам
     * и обновить аффиксы у словоформ
     * Re-highlight the unchangeble part by existing word forms
     * and update affixes of wordforms
     * 
     * @param Int $id
     * @return string
     */
    function reloadStemAffixByWordforms($id) {
        $lemma = Lemma::find($id);
        if (!$lemma) {
            return Redirect::to('/dict/lemma/'.($this->args_by_get))
                           ->withErrors('error.no_lemma');
        }
        
        list($max_stem, $affix) = $lemma->getStemAffixByWordforms();
        $lemma_stem = preg_replace("/\|\|/", '', $lemma->reverseLemma->stem);
        if ($max_stem!=$lemma_stem || $affix!=$lemma->reverseLemma->affix) {
            $lemma->reverseLemma->stem = $max_stem;
            $lemma->reverseLemma->affix = $affix;
            $lemma->reverseLemma->save();
        }
        
        $lemma->updateWordformAffixes(true);
        
        return $lemma->dictForm();
    }
    
    /**
     * SQL: select lemmas.id as lem_id, lemma, count(*) as frequency from lemmas, meaning_text, meanings where meaning_text.meaning_id=meanings.id and meanings.lemma_id=lemmas.id and relevance>0 group by lem_id order by frequency DESC;
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function frequencyInTexts(Request $request) {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        $locale = LaravelLocalization::getCurrentLocale();
        
        $lemmas_for_lang = Lemma::selectFromMeaningText()
                                ->groupBy('lang_id')
                                ->latest('frequency')
                                ->get(['lang_id', DB::raw('count(*) as frequency')]);
        $lang_values = [];
        foreach ($lemmas_for_lang as $lemma) {
            $lang_values[$lemma->lang_id] = $lemma->lang->name ." (".number_format($lemma->frequency, 0, '', ' ').")";
        }
//dd($lemmas_for_lang);
        $lemmas_for_pos = Lemma::selectFromMeaningText()
                               ->whereNotNull('pos_id')
                               ->groupBy('pos_id')
                               ->latest('frequency')
                               ->get(['pos_id', DB::raw('count(*) as frequency')]);
        $pos_values = [NULL=>''];
        foreach ($lemmas_for_pos as $lemma) {
            $pos_values[$lemma->pos_id] = $lemma->pos->name ." (".number_format($lemma->frequency, 0, '', ' ').")";
        }

        if ($url_args['search_lang']) {
            $lemmas = Lemma::selectFromMeaningText($url_args['search_dialect'])
                           ->join('parts_of_speech','parts_of_speech.id','=','lemmas.pos_id')
                           ->whereLangId($url_args['search_lang'])
                           ->groupBy('lemma_id', 'word_id')
                           ->latest('lemma');
                        
            if ($url_args['search_pos']) {
                $lemmas = $lemmas->wherePosId($url_args['search_pos']);
            } 
            
//dd($lemmas->toSql());
            $lemma_coll = $lemmas->get(['lemma', 'lemma_id', 'parts_of_speech.name_'.$locale.' as pos_name']);
            $lemmas = [];
            foreach ($lemma_coll as $lemma) {
                if (isset($lemmas[$lemma->lemma_id])) {
                    $lemmas[$lemma->lemma_id]['frequency'] = 1+$lemmas[$lemma->lemma_id]['frequency'];
                    continue;
                }
                $lemmas[$lemma->lemma_id] = [
                    'lemma'=>$lemma->lemma, 
                    'pos_name'=>$lemma->pos_name, 
                    'frequency'=>1];
            }
        } else {
            $lemmas = NULL;
        }
        $dialect_values = [NULL=>'']+Dialect::getList();
                
        return view('corpus.text.frequency.lemmas',
                compact('dialect_values', 'lang_values', 'lemmas', 'pos_values', 
                        'args_by_get', 'url_args'));
    }
    
    public function getWordformTotal($id) {
        $lemma= Lemma::findOrFail((int)$id);
        $lemma->updateWordformTotal();
        return "(".$lemma->wordform_total.")";
    }
    
    public function setStatus(int $id, int $label_id, int $status) {
        DB::statement("UPDATE label_lemma set status='$status' where"
                    . " lemma_id='$id' and label_id='$label_id'");
        return Lemma::findOrFail($id)->labelStatus($label_id);
    }
    
    public function removeLabel(int $id, int $label_id) {
        DB::statement("DELETE from label_lemma where"
                    . " lemma_id='$id' and label_id='$label_id'");
        return;
    }
    
    public function addLabel(int $id, int $label_id) {
        $lemma = Lemma::find($id);
//        DB::statement("INSERT INTO label_lemma (lemma_id, label_id, status)"
//                    . " VALUES ('$id', '$label_id', 0)");
        if ($lemma) {
            $lemma->labels()->attach([$label_id]);
            foreach ($lemma->meanings as $meaning) {
                $meaning->labels()->attach([$label_id]);
            }
        }
        return;
    }
    
}
