<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use DB;
use Gate;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Response;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

use App\Library\Grammatic;
use App\Models\User;

use App\Models\Dict\Dialect;
use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\LemmaFeature;
use App\Models\Dict\Meaning;
use App\Models\Dict\MeaningText;
use App\Models\Dict\PartOfSpeech;
use App\Models\Dict\Relation;
use App\Models\Dict\Wordform;
use App\Models\Corpus\Text;
use App\Models\Corpus\Word;

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
        $this->middleware('auth:dict.edit,/dict/lemma/', 
                          ['only' => ['create','store','edit','update','destroy',
                                      'editExample', 'removeExample',
                                      'editExamples','updateExamples', 
                                      'createMeaning', 'storeSimple',
                                      'createWordform', 'updateWordformFromText',
                                      'editWordforms','updateWordforms']]);
        
        $this->url_args = [
                    'limit_num'       => (int)$request->input('limit_num'),
                    'page'            => (int)$request->input('page'),
                    'search_gramset'  => (int)$request->input('search_gramset'),
                    'search_id'       => (int)$request->input('search_id'),
                    'search_lang'     => (int)$request->input('search_lang'),
                    'search_lemma'    => $request->input('search_lemma'),
                    'search_meaning'  => $request->input('search_meaning'),
                    'search_pos'      => (int)$request->input('search_pos'),
                    'search_relation' => (int)$request->input('search_relation'),
                    'search_wordform' => $request->input('search_wordform'),
                ];
        
        if (!$this->url_args['page']) {
            $this->url_args['page'] = 1;
        }
        
        if (!$this->url_args['search_id']) {
            $this->url_args['search_id'] = NULL;
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
     * 
     */
    public function index()
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        $lemmas = Lemma::search($url_args);
        $numAll = $lemmas->count();
//dd($lemmas->toSql()); 
        $lemmas = $lemmas->paginate($this->url_args['limit_num']);         
//dd($lemmas);        
        $pos_values = PartOfSpeech::getGroupedListWithQuantity('lemmas');
        
        //$lang_values = Lang::getList();
        $lang_values = Lang::getListWithQuantity('lemmas');
        $gramset_values = Gramset::getList($this->url_args['search_pos'],$this->url_args['search_lang'],true);

        return view('dict.lemma.index',
                compact('gramset_values', 'lang_values', 'lemmas', 'numAll',
                        'pos_values', 'args_by_get', 'url_args'));
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
        $dialect_value = User::userDialects();
//var_dump($dialect_value);        
        $new_meaning_n = 1;
        
        $pos_values = PartOfSpeech::getGroupedList();   
        $lang_values = Lang::getList();
        $langs_for_meaning = Lang::getListWithPriority();
        $dialect_values = $lang_id ? Dialect::getList($lang_id) : Dialect::getList(); //['NULL'=>'']+
//dd($dialect_values);        
        return view('dict.lemma.create',
                  compact('dialect_value', 'dialect_values', 'langs_for_meaning', 
                          'lang_id', 'lang_values', 'new_meaning_n', 'phrase_values', 
                          'pos_id', 'pos_values', 'args_by_get', 'url_args'));
    }

    /**
     * Shows the form for creating a new resource.
     * 
     * Called by ajax request
     *
     * @return \Illuminate\Http\Response
     */
    public function createMeaning(Request $request)
    {
        $count = (int)$request->input('count');
        $meaning_n = (int)$request->input('meaning_n');
//        $langs_for_meaning = Lang::getList();
        $langs_for_meaning = Lang::getListWithPriority();
                                
        return view('dict.lemma._form_create_meaning')
                  ->with(array('count' => $count,
                               'new_meaning_n' => $meaning_n,
                               'langs_for_meaning' => $langs_for_meaning
                              )
                        );
    }

    /**
     * Shows the form fields for creating a new wordform.
     * 
     * Called by ajax request
     * /dict/lemma/wordform/create?lemma_id=10603&text_id=1548
     *
     * @return \Illuminate\Http\Response
     */
    public function createWordform(Request $request)
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

        $gramset_values = ['NULL'=>'']+Gramset::getList($lemma->pos_id,$lemma->lang_id,true);
        $dialect_values = Dialect::getList($lemma->lang_id); //['NULL'=>'']+
        $meaning_values = Meaning::getList($lemma_id);
        
        $pos_name = $lemma->pos->name;
        $dialect_value = $text->dialectValue();
        
        return view('dict.lemma._form_create_wordform_fields')
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
        $this->validate($request, [
            'lemma'  => 'required|max:255',
            'lang_id'=> 'required|numeric',
//            'pos_id' => 'numeric',
        ]);
        
        $data = $request->all();
/*        if ($data['pos_id'] != 11) { // is not verb
            $data['reflexive'] = 0;
        }*/
        list($data['lemma'], $wordforms, $stem, $inflexion, $gramset_wordforms) 
                = Lemma::parseLemmaField($data);
        $request->replace($data);
        
        $lemma = Lemma::create($request->only('lemma','lang_id','pos_id')); //,'reflexive'
        $lemma->lemma_for_search = Grammatic::toSearchForm($lemma->lemma);
        $lemma->save();

        $lemma->createDictionaryWordforms($wordforms, $request->plur_tan);
        $lemma->storePhrase($request->phrase);
        $lemma->storeReverseLemma($stem, $inflexion);
        $lemma->storeWordformsFromTemplate($gramset_wordforms, $request->dialect_id); // а если диалектов нет?
            
        Meaning::storeLemmaMeanings($request->new_meanings, $lemma->id);
        
//        $lemma->updateTextLinks();

        return Redirect::to('/dict/lemma/'.($lemma->id).($this->args_by_get))
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
        ]);
        
        $data = $request->all();
        list($data['lemma'], $data['wordforms'], $stem, $inflexion) 
                = Lemma::parseLemmaField(trim($data['lemma']));
        $request->replace($data);
        
        $lemma = Lemma::create($request->only('lemma','lang_id','pos_id'));
        $lemma->lemma_for_search = Grammatic::toSearchForm($lemma->lemma);
        $lemma->save();

        $lemma->createDictionaryWordforms($request->wordforms, $request->plur_tan);
        $lemma->storeReverseLemma($stem, $inflexion);
        $new_meanings[0]=['meaning_n' => 1,
                          'meaning_text' =>
                            [Lang::getIDByCode('ru') => $request->meaning]];    
        Meaning::storeLemmaMeanings($new_meanings, $lemma->id);
        
        $lemma->updateTextLinks();

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
        $relations = Relation::getList();
//dd($relations);
        $meanings = $lemma->meanings;
        $meaning_texts = 
        $meaning_relations = 
        $translation_values = [];
          
        foreach ($meanings as $meaning) {
            foreach ($langs_for_meaning as $lang_id => $lang_text) {
                $meaning_text_obj = MeaningText::where('lang_id',$lang_id)->where('meaning_id',$meaning->id)->first();
                if ($meaning_text_obj) {
                    $meaning_texts[$meaning->id][$lang_text] = $meaning_text_obj->meaning_text;
                }
            }

            $relation_meanings = $meaning->meaningRelations;
            if ($relation_meanings) {
                foreach ($relation_meanings as $relation_meaning) {
                    $meaning2_id = $relation_meaning->pivot->meaning2_id;
                    $relation_id = $relation_meaning->pivot->relation_id;
                    $relation_text = $relations[$relation_id];
                    $relation_meaning_obj = Meaning::find($meaning2_id);
                    $relation_lemma_obj = $relation_meaning_obj->lemma;
                    $relation_lemma = $relation_lemma_obj->lemma;
                    $meaning_relations[$meaning->id][$relation_text][$relation_lemma_obj->id]  
                            = ['lemma' => $relation_lemma,
                               'meaning' => $relation_meaning_obj->getMultilangMeaningTextsString()];
                }
            }
            
            foreach ($langs_for_meaning as $l_id => $lang_text) {
                $meaning_translations = $meaning->translations()->wherePivot('lang_id',$l_id)->get();
                if ($meaning_translations) {
                    foreach ($meaning_translations as $meaning_translation) {
                        $meaning2_id = $meaning_translation->pivot->meaning2_id; 
                        $meaning2_obj = Meaning::find($meaning2_id);
                        $translation_lemma_obj = $meaning2_obj->lemma;
                        $translation_lemma = $translation_lemma_obj->lemma;
                        $translation_values[$meaning->id][$lang_text][$translation_lemma_obj->id] 
                            = ['lemma' => $translation_lemma,
                               'meaning' => $meaning2_obj->getMultilangMeaningTextsString()];
                    }
                }
            }
        }   
        
        $dialect_values = Dialect::getList($lemma->lang_id);
        return view('dict.lemma.show')
                  ->with([
                          'dialect_values'    => $dialect_values,
                          'lemma'             => $lemma,
                          'meaning_texts'     => $meaning_texts,
                          'meaning_relations' => $meaning_relations,
                          'translation_values'=> $translation_values,
                          'update_text_links' => $update_text_links,
                          'args_by_get'       => $this->args_by_get,
                          'url_args'          => $this->url_args,
            ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        $lemma = Lemma::find($id);
        
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
        $dialect_value = User::userDialects();
        $dialect_values = Dialect::getList($lemma->lang_id);
        
        $lemma_value = $lemma->reverseLemma && $lemma->reverseLemma->inflexion 
                     ? $lemma->reverseLemma->stem.'|'.$lemma->reverseLemma->inflexion 
                     : $lemma->lemma;
        
        return view('dict.lemma.edit',
                    compact('all_meanings', 'lang_values', 'dialect_value', 
                            'dialect_values', 'langs_for_meaning', 
                            'lemma', 'lemma_value', 'new_meaning_n', 'phrase_values',
                            'pos_values', 'relation_values', 'relation_meanings',
                            'translation_values', 'args_by_get', 'url_args'));
    }

    /**
     * Shows the form for editing of lemma's wordforms.
     *
     * @param  int  $id - ID of lemma
     * @return \Illuminate\Http\Response
     */
    public function editWordforms(Request $request, $id)
    {
        $dialect_id = (int)$request->dialect_id;
        $dialect_name = Dialect::getNameByID($dialect_id);
        
        if (!$dialect_id) {
            $dialect_id = NULL;
        }

        $lemma = Lemma::find($id);
//dd($lemma->wordformsWithAllGramsets($dialect_id));        
        $gramset_values = ['NULL'=>'']+Gramset::getGroupedList($lemma->pos_id,$lemma->lang_id,true);
//        $gramset_values = ['NULL'=>'']+Gramset::getList($lemma->pos_id,$lemma->lang_id,true);
        $dialect_values = ['NULL'=>'']+Dialect::getList($lemma->lang_id);
        return view('dict.lemma.edit_wordforms')
                  ->with(array(
                               'dialect_id' => $dialect_id,
                               'dialect_name' => $dialect_name,
                               'dialect_values' => $dialect_values,
                               'gramset_values' => $gramset_values,
                               'lemma' => $lemma,                      
                               'args_by_get'    => $this->args_by_get,
                               'url_args'       => $this->url_args,
                              )
                        );
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
     * @param  int  $sentence_id - ID of example
     * @return \Illuminate\Http\Response
     */
    public function editExample(Request $request, $id, $example_id)
    {
        $lemma = Lemma::find($id);
        
        list($sentence, $meanings, $meaning_texts) = 
                Text::preparationForExampleEdit($example_id);
        
        if ($sentence == NULL) {
            return Redirect::to('/dict/lemma/'.($lemma->id).($this->args_by_get))
                       ->withError(\Lang::get('messages.invalid_id'));            
        } else {
            return view('dict.lemma.edit_example')
                      ->with(array(
                                   'back_to_url'    => '/dict/lemma/'.$lemma->id,
                                   'id'             => $lemma->id, 
                                   'meanings'       => $meanings,
                                   'meaning_texts'  => $meaning_texts,
                                   'route'          => array('lemma.update.examples', $id),
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
       // https://laravel.com/api/5.1/Illuminate/Database/Eloquent/Model.html#method_touch
        $lemma= Lemma::findOrFail($id);
        
        $this->validate($request, [
            'lemma'  => 'required|max:255',
            'lang_id'=> 'required|numeric',
//            'pos_id' => 'numeric',
        ]);
        
        // LEMMA UPDATING
//dd($request->all());        
        list($new_lemma, $wordforms_list, $stem, $inflexion, $gramset_wordforms) 
                = Lemma::parseLemmaField($request->all());
        
        $lemma->lemma = $new_lemma;
        $lemma->lemma_for_search = Grammatic::toSearchForm($new_lemma);
        $lemma->lang_id = (int)$request->lang_id;
        $lemma->pos_id = (int)$request->pos_id;
        $lemma->updated_at = date('Y-m-d H:i:s');
        $lemma->save();
        
        LemmaFeature::store($lemma->id, $request);
        
        $lemma->createDictionaryWordforms($wordforms_list);    
        $lemma->storePhrase($request->phrase);
        $lemma->storeReverseLemma($stem, $inflexion);
        // MEANINGS UPDATING
        // existing meanings
        Meaning::updateLemmaMeanings($request->ex_meanings);
        $lemma->storeWordformsFromTemplate($gramset_wordforms, $request->dialect_id); // а если диалектов нет?

        // new meanings, i.e. meanings created by user in form now
        Meaning::storeLemmaMeanings($request->new_meanings, $id);
        
        //$lemma->updateTextLinks();
                
        return Redirect::to('/dict/lemma/'.($lemma->id).($this->args_by_get).'&update_text_links=1')
                       ->withSuccess(\Lang::get('messages.updated_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateWordforms(Request $request, $id)
    {
        $dialect_id = $request->dialect_id;
        $lemma= Lemma::findOrFail($id);
        // WORDFORMS UPDATING
        //remove all records from table lemma_wordform
        if (!(int)$dialect_id) {
            $dialect_id = NULL;
        }
        $lemma-> wordforms()->wherePivot('dialect_id',$dialect_id)->detach();
        //add wordforms from full table of gramsets
        $lemma-> storeWordformGramsets($request->lang_wordforms, $request->lang_wordforms_dialect);
        //add wordforms without gramsets
        $lemma-> storeWordformsEmpty($request->empty_wordforms, $dialect_id);

        // updates links with text examples
//        $lemma->updateTextLinks();
                
        
        return Redirect::to('/dict/lemma/'.($lemma->id).($this->args_by_get).($this->args_by_get ? '&' : '?').'update_text_links=1')
                       ->withSuccess(\Lang::get('messages.updated_success'));
    }

    /**
     * Saves links 1)text's word with lemma's meaning 
     *             2)wordform with gramset and dialects
     * 
     * /dict/lemma/wordform/update?lemma_id=10603&text_id=1548&w_id=7&meaning_id=11660&gramset_id=34&dialects[]=44&dialects[]=34
     * @param Request $request
     * @return Null
     */
    public function updateWordformFromText(Request $request)
    {
        $lemma_id = (int)$request->input('lemma_id');
        $text_id = (int)$request->input('text_id'); 
        $w_id = (int)$request->input('w_id'); 
        
        if (!$lemma_id || !$text_id || !$w_id) {
            return;
        }

        $lemma = Lemma::find($lemma_id);
        $text = Text::find($text_id);
        $word = Word::getByTextWid($text_id, $w_id);
        
        if (!$lemma || !$text || !$word || !$word->sentence_id) { return; }
        
        $meaning_id = $request->input('meaning_id'); 
        $gramset_id = $request->input('gramset_id'); 
        $dialects = (array)$request->input('dialects'); 
        
        $wordform = $request->input('wordform'); 
        if (!$wordform) {
            $wordform = $word -> word;
        }
        
        $text->addLinkWithMeaning($lemma, $meaning_id, $w_id, $word);
        $lemma->addWordformFromText($wordform, $gramset_id, $dialects, $text_id, $w_id);
        return 1;            
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
                          ' AND sentence_id='.(int)$regs[3].
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
                $lemma = Lemma::find($id);
                if($lemma){
                    $result['message'] = \Lang::get('dict.lemma_removed', ['name'=>$lemma->lemma]);
                    $lemma-> remove();
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
        } else {
            return Redirect::to('/dict/lemma/'.($this->args_by_get))
                  ->withSuccess($result['message']);
        }
        
 //       return response()->json(['error' => $error,'response'=>$result],$status_code)
  //                       ->setCallback(\Request::input('callback'));
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
                           ->withErrors(\Lang::get('messages.record_not_exists'));
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
        $builder = Lemma::select(DB::raw('*, char_length(lemma) as char_length'));
        
        $numAll = $builder->count();
        
        $lemmas = $builder->orderBy('char_length', 'desc')
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
            $omonyms = Lemma::select('id')
                            ->where('lemma','like',$lemma->lemma)
                            ->where('lang_id',$lemma->lang_id)
                            ->where('pos_id',$lemma->pos_id)->get();
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
            $lemmas = $lemmas->where('pos_id',$pos_id);
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
    public function phraseList(Request $request)
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * 
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
    
    /*
     * split wordforms such as pieksäh/pieksähes on two wordforms
     * and link meanings of lemma with sentences
     * 
     * select * from meaning_text where meaning_id in (select id from meanings where lemma_id in (select lemma_id from lemma_wordform where wordform_id=8755))
     */
    /*
    public function tmpSplitWordforms() {
        $wordforms = Wordform::where('wordform','like','%/%')->take(100)->get();
        foreach($wordforms as $wordform) {
print "<br><br>".$wordform->id. "=".$wordform->wordform;  
//exit(0);
            $lemmas = $wordform->lemmas()->withPivot('gramset_id')
                      ->withPivot('dialect_id')->get();
            foreach ($lemmas as $lemma) {
                $gramset_id = $lemma->pivot->gramset_id;
                $dialect_id = $lemma->pivot->dialect_id;
                foreach (preg_split("/\//",$wordform->wordform) as $word) {
                    $wordform_obj = Wordform::firstOrCreate(['wordform'=>trim($word)]);
                    $exist_wordforms = $lemma->wordforms()
                                             ->wherePivot('gramset_id',$gramset_id)
                                             ->wherePivot('dialect_id',$dialect_id)
                                             ->wherePivot('wordform_id',$wordform_obj->id);
                    if (!$exist_wordforms->count()) {
                        $lemma->wordforms()->attach($wordform_obj->id, 
                               ['gramset_id'=>$gramset_id, 'dialect_id'=>$dialect_id]);
                    }
print "<br>".$wordform_obj->id.'='.$wordform_obj->wordform."; lemma: ".$lemma->id."=".$lemma->lemma;                    
                    $wordform_obj->updateTextLinks($lemma);
                }
                $wordform->lemmas()->wherePivot('gramset_id',$gramset_id)
                         ->wherePivot('dialect_id',$dialect_id)
                         ->detach();
            }
            $wordform->delete();
        }
    }*/
    
    public function tmpMoveReflexive() {
        $lemmas=Lemma::where('reflexive',1)->get();
       
        foreach ($lemmas as $lemma) {
            LemmaFeature::create(['id'=>$lemma->id,
                'reflexive'=>1]);
        }
        print 'done.';
    }
    
    
    /** Copy vepsian.{lemma and translation_lemma} to vepkar.lemmas
     * + temp column vepkar.lemmas.temp_translation_lemma_id
     */
/*    
    public function tempInsertVepsianLemmas()
    {
        $lemmas = DB::connection('vepsian')->table('lemma')->orderBy('id')->get();
 
     
        DB::connection('mysql')->table('meaning_texts')->delete();
        DB::connection('mysql')->statement('ALTER TABLE meaning_texts AUTO_INCREMENT = 1');
        
        DB::connection('mysql')->table('meanings')->delete();
        DB::connection('mysql')->statement('ALTER TABLE meanings AUTO_INCREMENT = 1');

        DB::connection('mysql')->table('lemmas')->delete();
        DB::connection('mysql')->statement('ALTER TABLE lemmas AUTO_INCREMENT = 1');
        
        foreach ($lemmas as $lemma) {
            DB::connection('mysql')->table('lemmas')->insert([
                    'id' => $lemma->id,
                    'lemma' => $lemma->lemma,
                    'lang_id' => 1,
                    'pos_id' => $lemma->pos_id,
                    'created_at' => $lemma -> modified,
                    'updated_at' => $lemma -> modified
                ]
            );
        }
         
    }
 */    
}
