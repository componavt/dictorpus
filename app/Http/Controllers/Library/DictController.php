<?php

namespace App\Http\Controllers\Library;

use Illuminate\Http\Request;

//use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Library\Grammatic;
use App\Library\Str;

use App\Models\Dict\Dialect;
use App\Models\Dict\Example;
use App\Models\Dict\Label;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\LemmaFeature;
use App\Models\Dict\Meaning;
use App\Models\Dict\MeaningText;
use App\Models\Dict\PartOfSpeech;
use App\Models\Dict\Synset;
use App\Models\Dict\Syntype;

class DictController extends Controller
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
        $this->middleware('auth:dict.edit,/');
        
        $this->url_args = Str::urlArgs($request) + 
            ['search_pos'      => (int)$request->input('search_pos'),
             'search_status'   => (int)$request->input('search_status'),
             'search_lang'   => (int)$request->input('search_lang'),
             'search_lemma'    => $request->input('search_lemma'),
            ];
        
        $this->args_by_get = search_values_by_URL($this->url_args);
    }
    
    // отбираем из словаря ливвиковские леммы, у которых нет ещё метки "для мильтимедийного словаря"
    // и слова, для которых есть примеры в корпусе новописьменных текстов
    // 
    // проверка на "чужие леммы", вдруг не то попалось
    // select count(*) from label_lemma where lemma_id in (select id from lemmas where lang_id not in (5)) and label_id=3;
    public function multiSelect() {
        ini_set('max_execution_time', 7200);
        ini_set('memory_limit', '512M');
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        $lang_id=5; // livvic
        $dialect_id=44; // New written Livvic
        $label_id = Label::OlodictLabel; // for multimedia dictionary
        
/*        $lemmas = Lemma::whereLangId($lang_id)
                       ->whereNotIn('id', function ($q) use ($label_id) {
                           $q->select('lemma_id')->from('label_lemma')
                             ->whereLabelId($label_id);
                       }) 
                       ->whereIn('id', function ($q) use ($dialect_id) {
                           $q->select('lemma_id')->from('meanings')
                             ->whereIn('id', function ($q2) use ($dialect_id) {
                               $q2->select('meaning_id')->from('meaning_text')
                                  ->whereIn('text_id', function ($q3) use ($dialect_id) {
                                      $q3->select('text_id')->from('dialect_text')
                                         ->whereDialectId($dialect_id);
                                  });
                             });
                       })->get();
        foreach ($lemmas as $lemma) {
//print "<p>".$lemma->id."</p>";            
            $lemma->labels()->attach([$label_id]);
        }               */
        
/*        $examples = DB::table('meaning_text')->whereRelevance(10)->get();
//dd($examples);        
        foreach ($examples as $example) {
            $sentence = Sentence::whereTextId($example->text_id)
                                ->whereSId($example->s_id)->first();
            $fragment = $sentence->fragments()->first();
            if ($fragment) {
                $fragment->w_id = $example->w_id;
                $fragment->save();
            }
            $translation = $sentence->translations()->first();
            if ($translation) {
                $translation->w_id = $example->w_id;
                $translation->save();
            }
        }
        print "done";*/
        // update label_lemma set status=1  where lemma_id in (select id from lemmas where status=1);
        $lemmas = Lemma::whereLangId($lang_id)
                ->whereNotIn('lemmas.id', function ($q) use ($label_id) {
                    $q->select('lemma_id')->from('label_lemma')
                      ->whereLabelId($label_id);
                })->whereIn('id', function ($q) use ($dialect_id) {
                    $q->select('lemma_id')->from('meanings')
                      ->whereIn('id', function ($q2) use ($dialect_id) {
                        $q2->select('meaning_id')->from('meaning_text')
                           ->whereIn('text_id', function ($q3) use ($dialect_id) {
                               $q3->select('text_id')->from('dialect_text')
                                  ->whereDialectId($dialect_id);
                           });
                      });
                });

        if ($url_args['search_lemma']) {
            $lemmas->where('lemma', 'like', $url_args['search_lemma']);
        } 
               
        if ($url_args['search_pos']) {
            $lemmas->wherePosId($url_args['search_pos']);
        } 
               
        if ($url_args['search_status'] == 3) {
            $lemmas->whereIn('id', function ($q) {
                $q->select('lemma_id')->from('audio_lemma');
            });            
        }
        
        $lemma_coll = $lemmas->get();
        $lemmas = [];
        foreach ($lemma_coll as $lemma) {
            $lemmas[$lemma->id] = [
                'lemma'=>$lemma->lemma, 
                'pos_name'=>$lemma->pos->name, 
                'frequency'=>$lemma->getFrequencyInCorpus()];
        }
        $pos_values = [NULL=>'']+PartOfSpeech::getList();
        
        return view('service.dict.multi.select',
                compact('label_id', 'lemmas', 'pos_values', 
                        'args_by_get', 'url_args'));
    }
    
    public function multiView(Request $request) {
        ini_set('max_execution_time', 7200);
        ini_set('memory_limit', '512M');
        $lang_id=5; // livvic
        $label_id = Label::OlodictLabel; // for multimedia dictionary
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        $lemmas = Lemma::whereLangId($lang_id)
                ->whereIn('lemmas.id', function ($q) use ($label_id, $url_args) {
                    $q->select('lemma_id')->from('label_lemma')
                      ->whereLabelId($label_id);
                    if (in_array($url_args['search_status'], [1,2])) {
                        $q->whereStatus($url_args['search_status']==1 ? 1 : 0);
                    } 
                });
        // леммы с метками, отсортированные по частоте употребления в корпусе
/*        $lemmas = Lemma::selectFromMeaningText()
            ->join('parts_of_speech','parts_of_speech.id','=','lemmas.pos_id')
            ->whereLangId($lang_id)
            ->whereIn('lemmas.id', function ($q) use ($label_id, $url_args) {
                $q->select('lemma_id')->from('label_lemma')
                  ->whereLabelId($label_id);
                if ($url_args['search_status']) {
                    $q->whereStatus($url_args['search_status']==1 ? 1 : 0);
                } 
            })
            ->groupBy('lemma_id', 'word_id')
            ->latest(DB::raw('count(*)'));
*/
        if ($url_args['search_lemma']) {
            $lemmas->where('lemma', 'like', $url_args['search_lemma']);
        } 
               
        if ($url_args['search_pos']) {
            $lemmas->wherePosId($url_args['search_pos']);
        } 
               
        if ($url_args['search_status'] == 3) {
            $lemmas->whereIn('id', function ($q) {
                $q->select('lemma_id')->from('audio_lemma');
            });            
        }
        
/*        $lemma_coll = $lemmas->get();//['lemma', 'lemma_id', 'parts_of_speech.name_'.$locale.' as pos_name', 'status']);
        $lemmas = [];
        foreach ($lemma_coll as $lemma) {
//dd($lemma);            
            $lemmas[$lemma->id] = [
                'lemma'=>$lemma->lemma, 
                'pos_name'=>$lemma->pos->name, 
                'concepts' => $lemma->conceptNames(),
//                'frequency'=>$lemma->getFrequencyInCorpus(), 
                'audios'=>Audio::getUrlsByLemmaId($lemma->id), 
                'status'=>$lemma->labelStatus($label_id)];
        }*/
//        $lemmas=$lemmas->sortByDesc('frequency');
//dd($lemmas[10603]);        
        $numAll = $lemmas->count();
        $lemmas = $lemmas->orderBy('lemma')->paginate($url_args['limit_num']);
        $pos_values = [NULL=>'']+PartOfSpeech::getList();
        
        return view('service.dict.multi.index',
                compact('label_id', 'lemmas', 'numAll', 'pos_values',
                        'args_by_get', 'url_args'));
    }
    
    public function schoolSelect() {
        /*
        $lang_id=5; // livvic
        $dialect_id=44; // New written Livvic
        $label_id = 4; // for school dictionary
        
        $lemmas = Lemma::whereLangId($lang_id)
                       ->whereNotIn('id', function ($q) use ($label_id) {
                           $q->select('lemma_id')->from('label_lemma')
                             ->whereLabelId($label_id);
                       })->orderBy('id')
                               //->take(10)
                       ->get();
        foreach ($lemmas as $lemma) {
//print "<p>".$lemma->id."</p>";            
            $lemma->labels()->attach([$label_id]);
            foreach ($lemma->meanings as $meaning) {
                $meaning->labels()->attach([$label_id]);
            }
        } */                      
    }
    
    public function schoolView(Request $request) {
//        ini_set('max_execution_time', 7200);
//        ini_set('memory_limit', '512M');
        $lang_id=5; // livvic
        $label_id = 4; // for school dictionary
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        $lemmas = Lemma::whereLangId($lang_id)
            ->whereIn('id', function ($q) use ($label_id, $url_args) {
                $q->select('lemma_id')->from('label_lemma')
                  ->whereLabelId($label_id);
                if (in_array($url_args['search_status'], [1,2])) {
                    $q->whereStatus($url_args['search_status']==1 ? 1 : 0);
                } 
            })
//            ->orderBy('lemma');
            ->orderByRaw('lower(lemma)');

        if ($url_args['search_pos']) {
            $lemmas->wherePosId($url_args['search_pos']);
        } 
        
        if ($url_args['search_status'] == 3) {
            $lemmas->whereIn('id', function ($q) {
                $q->select('lemma_id')->from('audio_lemma');
            });            
        }
        
        $numAll = $lemmas->count();
        $lemmas = $lemmas->paginate($url_args['limit_num']);
        $pos_values = [NULL=>'']+PartOfSpeech::getList();
        
        return view('service.dict.school.index',
                compact('label_id', 'lemmas', 'numAll', 
                        'pos_values', 'args_by_get', 'url_args'));
    }
    
    public function zaikovSelect() {
        ini_set('max_execution_time', 7200);
        ini_set('memory_limit', '512M');
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        $lang_id=4; // proper
        $label_id = Label::ZaikovLabel; // for Zaikov dictionary
        
        $lemmas = Lemma::whereLangId($lang_id)
                       ->whereNotIn('id', function ($q) use ($label_id) {
                           $q->select('lemma_id')->from('label_lemma')
                             ->whereLabelId($label_id);
                       })->orderBy('lemma');
//                       ->take(10);
                       
        if ($url_args['search_pos']) {
            $lemmas->wherePosId($url_args['search_pos']);
        } 
               
        if ($url_args['search_lemma']) {
            $lemmas->where(function ($query) use ($url_args) {
                        Lemma::searchLemmas($query, $url_args['search_lemma']);
                       });
        } 
        
        if ($url_args['search_status'] == 3) {
            $lemmas->whereIn('id', function ($q) {
                $q->select('lemma_id')->from('audio_lemma');
            });            
        }
        
        $lemma_coll = $lemmas->get();
        $lemmas = [];
        foreach ($lemma_coll as $lemma) {
            $lemmas[$lemma->id] = [
                'lemma'=>$lemma->lemma, 
                'pos_name'=>$lemma->pos->name];
        }
        $pos_values = [NULL=>'']+PartOfSpeech::getList();
        
        return view('service.dict.zaikov.select',
                compact('label_id', 'lemmas', 'pos_values', 
                        'args_by_get', 'url_args'));
 
/*        foreach ($lemmas as $lemma) {
//print "<p>".$lemma->id."</p>";            
            $lemma->labels()->attach([$label_id]);
            foreach ($lemma->meanings as $meaning) {
                $meaning->labels()->attach([$label_id]);
            }
        }  */                   
    }
    
    public function zaikovView(Request $request) {
        $lang_id=4; // proper
        $dialect_id = 46;
        $label_id = Label::ZaikovLabel; // for Zaikov dictionary
        
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        $lemmas = Lemma::whereLangId($lang_id)
            ->whereIn('id', function ($q) use ($label_id, $url_args) {
                $q->select('lemma_id')->from('label_lemma')
                  ->whereLabelId($label_id);
                if (in_array($url_args['search_status'], [1,2])) {
                    $q->whereStatus($url_args['search_status']==1 ? 1 : 0);
                } 
            })
//            ->orderBy('lemma');
            ->orderBy('lemma_for_search');
//            ->orderByRaw('lower(lemma)');

        if ($url_args['search_pos']) {
            $lemmas->wherePosId($url_args['search_pos']);
        } 
        
        if ($url_args['search_lemma']) {
            $lemmas->where(function ($query) use ($url_args) {
                        Lemma::searchLemmas($query, $url_args['search_lemma']);
                       });
        } 
        
        if ($url_args['search_status'] == 3) {
            $lemmas->whereIn('id', function ($q) {
                $q->select('lemma_id')->from('audio_lemma');
            });            
        }
        
        $numAll = $lemmas->count();
        $lemmas = $lemmas->paginate($url_args['limit_num']);
        $pos_values = [NULL=>'']+PartOfSpeech::getList();
        $langs_for_meaning = array_slice(Lang::getListWithPriority(),0,1,true);
        $dialect_values = Dialect::getList($lang_id);
        $label_values = Label::getList();
        $total_meanings = 2;
        
        return view('service.dict.zaikov.index',
                compact('dialect_id', 'dialect_values', 'label_id', 
                        'label_values', 'lang_id', 'langs_for_meaning', 
                        'lemmas', 'numAll', 'pos_values', 'total_meanings', 
                        'args_by_get', 'url_args'));
    }
    
    public function createMeaning($lemma_id, Request $request)
    {
        $lemma = Lemma::find($lemma_id);
        if (!$lemma) {return;}
        $label_id = (int)$request->label_id;
        return view('service.dict.meaning._create', 
                compact('label_id', 'lemma'));
    }
    
    public function storeMeaning($lemma_id, Request $request)
    {
        $lemma = Lemma::find($lemma_id);
        if (!$lemma) {return;}
        $label_id = (int)$request->label_id;
        
        $meaning_obj = Meaning::storeLemmaMeaning($lemma->id, $lemma->getNewMeaningN(), [Lang::getIDByCode('ru') => $request->meaning]);
        $lemma->updateTextLinks();
        $meaning_obj->labels()->attach([$label_id]);
        Example::store($meaning_obj->id, $request->all());
        
        return view('service.dict.lemma._meanings', 
                compact('label_id', 'lemma'));
    }
    
    /**
     * Store a newly created resource in storage.
     * 
     * URL: /service/dict/lemma/store?lang_id=4&lemma=täh&meaning=test1&pos_id=5&label_id=5
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeLemma(Request $request)
    {
        $dialect_id = 46;
        $this->validate($request, [
            'lemma'  => 'required|max:255',
            'lang_id'=> 'required|numeric',
            'pos_id' => 'numeric',
            'label_id' => 'numeric', 
        ]);
        
        $label_id = $request->label_id;
        if (!$label_id) {
            return;
        } 
        
        $lemma = Lemma::storeLemma($request->all());
        LemmaFeature::store($lemma->id, $request);
        $lemma->labels()->attach([$label_id]);
        
        $meanings = (array)$request->meanings;
        
        for ($i=0; $i<sizeof($meanings); $i++)  {     
            if (!$meanings[$i]['meaning_text']) { continue; }
            $meaning = Meaning::storeLemmaMeaning($lemma->id, $i+1, [Lang::getIDByCode('ru') => $meanings[$i]['meaning_text']]);
            $meaning->labels()->attach([$label_id]);
            Example::store($meaning->id, $meanings[$i]);
        }
        $lemma->updateTextLinks();
        
        if ($label_id == Label::ZaikovLabel) {
            return view('service.dict.zaikov._row', 
                    compact('dialect_id', 'label_id', 'lemma'));
        }
    }
    
    public function wordforms($lemma_id, Request $request)
    {
        $dialect_id = (int)$request->input('dialect_id');
        $lemma= Lemma::find((int)$lemma_id);
        if (!$lemma) {
            return;
        }
        $wordforms = $lemma->wordformsForTable($dialect_id);
        return view('service.dict.wordforms._all', compact('lemma', 'wordforms'));
    }
    
/*    public function editLemma($lemma_id)
    {
        $lemma= Lemma::find((int)$lemma_id);
        if (!$lemma) {
            return;
        }
        $phrase_values = $lemma->phraseLemmas->pluck('lemma', 'id')->toArray();
        $dialect_values = Dialect::getList($lemma->lang_id);
        $pos_values = ['NULL'=>''] + PartOfSpeech::getGroupedList(); 
        return view('service.dict.lemma._edit', 
                compact('dialect_values', 'lemma', 'phrase_values', 'pos_values'));
    }*/
    
    public function updateLemma($id, Request $request)
    {
//dd($request->all());        
        $this->validate($request, [
            'lemma'  => 'required|max:255',
            'pos_id' => 'numeric',
        ]);
        $lemma= Lemma::find((int)$id);
        if (!$lemma) {
            return;
        }
        $data = $request->all();
        $data['lang_id'] = $lemma->lang_id;
        $lemma->updateLemma($data);
        $lemma->updateTextLinks();
        
        return $lemma->zaikovTemplate();
    }
    
    public function storeLabel ($meaning_id, Request $request) {
        $meaning = Meaning::find((int)$meaning_id);
        $data = $request->all(); 
        if ($data['name_ru']) {
            $label = Label::store($data);
            $label_id = $label->id;
        } else {
            $label_id = (int)$request->label_id;            
        }
        if (!$meaning) {
            return;
        }
        $meaning->labels()->syncWithoutDetaching([$label_id]);
        return view('service.dict.label._index', compact('meaning'));
    }
    
    public function removeVisibleLabel ($meaning_id, $label_id) {
        $meaning = Meaning::find((int)$meaning_id);
        if (!$meaning) {
            return;
        }
        $meaning->labels()->detach($label_id);
        return view('service.dict.label._index', compact('meaning'));        
    }
    
    public function createPhrase ($meaning_id) {
        return view('service.dict.lemma._create_phrase', compact('meaning_id'));                
    }
    
    public function storePhrase ($meaning_id, Request $request) {
        if (!$request->phrase) {
            return;
        }
        $meaning = Meaning::find((int)$meaning_id);
        if (!$meaning) {
            return;
        }
//        $lang_id = $meaning->lemma->lang_id;
        $lemma_p = Lemma::whereLemma($request->phrase)
                        ->wherePosId(PartOfSpeech::PhraseId)
                        ->whereLangId($meaning->lemma->lang_id)
                        ->first();
        if (!$lemma_p) {
            $lemma_p = Lemma::store($request->phrase, 
                    PartOfSpeech::PhraseId, $meaning->lemma->lang_id);
            $meaning_p = Meaning::create([
                'meaning_n' => 1,
                'lemma_id' => $lemma_p->id
            ]);
            $meaning_text = MeaningText::create([
                'meaning_id' => $meaning_p->id,
                'lang_id' => 2,
                'meaning_text' => $request->phrase_ru
            ]);
        }
        if (!$meaning->phrases()->wherePivot('lemma_id',$lemma_p->id)->count()) {
            $meaning->phrases()->attach($lemma_p->id);
        }
        if (!$meaning->lemma->phrases()->wherePivot('phrase_id',$lemma_p->id)->count()) {
            $meaning->lemma->phrases()->attach($lemma_p->id);
        }
        return view('service.dict.meaning._phrases', compact('meaning'));                
    }
    
    public function editPhrase ($id) {
        $lemma= Lemma::find((int)$id);
        if (!$lemma) {
            return;
        }
        $phrase_id = $lemma->id;
        $func = 'updatePhrase('.$phrase_id.')';
        $phrase = $lemma->lemma;
        $phrase_ru = $lemma->meanings ? $lemma->meanings[0]->getMeaningTextByLangCode('ru') : '';
        return view('service.dict.lemma._create_edit_phrase', 
                compact('phrase', 'phrase_id', 'phrase_ru', 'func'));                
    }
// TODO: проверить удаление леммы-фразы    
    public function updatePhrase ($id, Request $request) {
        $lemma = Lemma::find((int)$id);
        if (!$lemma || !$request->phrase) {
            return;
        }
        $lemma->lemma = $request->phrase;
        $lemma->lemma_for_search = Grammatic::changeLetters($lemma->lemma, $lemma->lang_id);
        $lemma->save();
        
        $meaning_text = $lemma->getMeaningText(1, 'ru');
        if (!$meaning_text) {
            return;
        }
        $meaning_text->meaning_text = $request->phrase_ru;
        $meaning_text->save();
        return view('service.dict.meaning._phrase', ['phrase'=>$lemma]);                
    }
    
    /* отбираем все людиковские слова, связанные понятиями */
    public function ldlSelect() {        
        ini_set('max_execution_time', 7200);
        ini_set('memory_limit', '512M');
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        $lang_id=6; // ludic
        $label_id = Label::LDLLabel; 
        
        $lemmas = Lemma::whereLangId($lang_id)
                       ->whereIn('id', function ($q) { // есть значения, привязанные к понятиям
                           $q->select('lemma_id')->from('meanings')
                             ->whereIn('id', function ($q2) {
                                 $q2->select('meaning_id')->from('concept_meaning');
                             });
                       })->whereNotIn('id', function ($q) use ($label_id) { // еще не имеющие метку
                           $q->select('lemma_id')->from('label_lemma')
                             ->whereLabelId($label_id);
                       })->orderBy('id')
                       //->take(10)
                       ->get();
        foreach ($lemmas as $lemma) {
            $lemma->labels()->attach([$label_id]);
        }                       
/*        
        if ($url_args['search_pos']) {
            $lemmas->wherePosId($url_args['search_pos']);
        } 
               
        if ($url_args['search_status'] == 3) {
            $lemmas->whereIn('id', function ($q) {
                $q->select('lemma_id')->from('audio_lemma');
            });            
        }
        
        $lemma_coll = $lemmas->get();
        $lemmas = [];
        foreach ($lemma_coll as $lemma) {
            $lemmas[$lemma->id] = [
                'lemma'=>$lemma->lemma, 
                'pos_name'=>$lemma->pos->name, 
                'frequency'=>$lemma->getFrequencyInCorpus()];
        }
        $pos_values = [NULL=>'']+PartOfSpeech::getList();
        
        return view('service.dict.multi.select',
                compact('label_id', 'lemmas', 'pos_values', 
                        'args_by_get', 'url_args'));
*/ 
    }
        
    public function ldlView(Request $request) {
//        ini_set('max_execution_time', 7200);
//        ini_set('memory_limit', '512M');
        
        $lang_id=6; // ludic
        $label_id = Label::LDLLabel; 
        
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        $lemmas = Lemma::whereLangId($lang_id)
                ->whereIn('lemmas.id', function ($q) use ($label_id, $url_args) {
                    $q->select('lemma_id')->from('label_lemma')
                      ->whereLabelId($label_id);
                    if (in_array($url_args['search_status'], [1,2])) {
                        $q->whereStatus($url_args['search_status']==1 ? 1 : 0);
                    } 
                });
                
        if ($url_args['search_pos']) {
            $lemmas->wherePosId($url_args['search_pos']);
        } 
               
        if ($url_args['search_status'] == 3) {
            $lemmas->whereIn('id', function ($q) {
                $q->select('lemma_id')->from('audio_lemma');
            });            
        }
        
        $numAll = $lemmas->count();
        $lemmas = $lemmas->orderBy('lemma')->paginate($url_args['limit_num']);
        $pos_values = [NULL=>'']+PartOfSpeech::getList();
        
        return view('service.dict.ldl.index',
                compact('label_id', 'lemmas', 'numAll', 'pos_values',
                        'args_by_get', 'url_args'));
    }
    
    public function synsetsView(Request $request) {
        $url_args = $this->url_args;
        if (empty($url_args['search_lang'])) {
            $url_args['search_lang'] = 5; // livvic
        }
/*        if (empty($url_args['search_pos'])) {
            $url_args['search_pos'] = 1; // adjective
        }*/
        $args_by_get = search_values_by_URL($url_args);
       
//        $new_set = Synset::findSynset($url_args['search_lang'], $url_args['search_pos']);
        $new_set_founded = Synset::newSetFounded($url_args['search_lang'], $url_args['search_pos']);
        
        $synsets = Synset::whereLangId($url_args['search_lang']);
                
        if (!empty($url_args['search_pos'])) {
            $synsets->wherePosId($url_args['search_pos']);
        } 
               
        if (!empty($url_args['search_status'])) {
            $synsets->whereStatus($url_args['search_status']==1 ? 1 : 0);
        }
        
        $numAll = $synsets->count();
        $synsets = $synsets->orderBy('id')->paginate($url_args['limit_num']);

        $lang_values = Lang::getProjectList();
        $pos_values = [NULL=>'']+PartOfSpeech::getList();
        $status_values = trans('dict.output_checked_or_not');
        unset($status_values[3]);
        
        return view('service.dict.synsets.index',
                compact('lang_values', 'new_set_founded', 'numAll', 'pos_values',
                        'status_values', 'synsets', 'args_by_get', 'url_args'));
    }
    
    public function findNewSynset(Request $request) {
        $url_args = $this->url_args;
        if (empty($url_args['search_lang'])) {
            $url_args['search_lang'] = 5; // livvic
        }
        $args_by_get = search_values_by_URL($url_args);
        
        $synsets = Synset::findSynsets($url_args['search_lang'], $url_args['search_pos']);        
//dd($synsets);        
        
        $lang = Lang::findOrFail($url_args['search_lang']);
        $syntype_values = Syntype::getList(1);
//dd($new_set, $core, $periphery);        
        return view('service.dict.synsets.find_new',
                compact('lang', 'synsets', 'syntype_values', 
                        'args_by_get', 'url_args'));
    }
}

    
