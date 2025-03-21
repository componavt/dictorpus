<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;
use DB;

//use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Library\Grammatic;
use App\Library\Str;
use App\Library\Predictor;

use App\Models\Corpus\Sentence;
use App\Models\Corpus\Text;
use App\Models\Corpus\Word;

use App\Models\Dict\Dialect;
use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\LemmaWordform;
use App\Models\Dict\PartOfSpeech;
use App\Models\Dict\Wordform;

use App\Models\User;

class WordController extends Controller
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
                                     'updateMeaningLinks', 'updateWordBlock']]);
        $this->url_args = Word::urlArgs($request);  
        
        $this->args_by_get = search_values_by_URL($this->url_args);
    }
    
    /**
     * SQL: select lower(word) as l_word, count(*) as frequency from words where text_id in (select id from texts where lang_id=1) group by word order by frequency DESC, l_word LIMIT 30;
     * SQL: select word, count(*) as frequency from words where text_id in (select id from texts where lang_id=1) group by word order by frequency DESC, word LIMIT 30;
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function frequencyDict(Request $request) {
//        $start = microtime(true);
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        if ($url_args['search_lang']) {
            $lang_id = $url_args['search_lang'];
            $words = Word::whereIn('text_id', function($query) use ($lang_id){
                                $query->select('id')->from('texts')
                                      ->where('lang_id',$lang_id);
                        })
                  ->groupBy('word')
                  ->latest(DB::raw('count(word)'));
                        
            if ($url_args['search_word']) {
                $words = $words->where('word','like',$url_args['search_word']);
            } 

            if ($url_args['search_dialect']) {
                $words = $words->whereIn('text_id', function ($q) use ($url_args) {
                    $q->select('text_id')->from('dialect_text')
                      ->whereDialectId($url_args['search_dialect']); 
                });
            } 

            if ($url_args['search_linked']=="1") {
                $words = $words->whereIn('id', function ($q) {
                    $q->select('word_id')->from('meaning_text');
                });
            } elseif ($url_args['search_linked']=="2") {
                $words = $words->whereNotIn('id', function ($q) {
                    $q->select('word_id')->from('meaning_text');
                });
            } 
//dd(to_sql($words));        
            $words = $words
//                    ->take($url_args['limit_num'])
                    ->select('word',DB::raw('count(word) as frequency'))
                    ->paginate($url_args['limit_num']);
//                    ->get(['word',DB::raw('count(word) as frequency')]);
        } else {
            $words = NULL;
        }
        $lang_values = Lang::getList();
        $dialect_values = Dialect::getList();
//dd($words);        
        return view('corpus.word.freq_dict',
                compact('dialect_values', 'lang_values', 'words',/* 'start',*/
                        'args_by_get', 'url_args'));
    }
    
    public function updateMeaningLinks() {
        $is_all_checked = false;
        while (!$is_all_checked) {
            $word = Word::where('checked',0)
                    //->where('text_id',1)
                    ->first();
//dd($word);            
            if ($word) {
                // save old checked links
                $checked_relevances =  Word::checkedMeaningRelevances(
                        $word->text_id, $word->w_id, $word->word);  
                // delete all links
                DB::statement("DELETE FROM meaning_text WHERE text_id=".$word->text_id." and w_id=".$word->w_id);
                // search new links
                $word->setMeanings($checked_relevances);
                $word->checked=1;
                $word->save();   
            } else {
                $is_all_checked = true;
            }
        }
        print 'done';
    }
    
    /**
     * Calls by AJAX, 
     * adds 
     * /corpus/word/add_example/<text_id>_<w_id>_<wordform_id>_<gramset_id>
     * /corpus/word/load_word_block/<text_id>_<w_id>
     * /corpus/word/load_word_block/3154_1
     * 
     * @param type $example_id
     * @return string
     */
    public function loadWordBlock($text_id, $w_id)
    {        
        if (User::checkAccess('dict.edit')) {
            $word = Word::whereTextId($text_id)->whereWId($w_id)->first();
// 2020-10-24 пока отключим, чтобы быстрее работало...            
//            $word->updateMeaningAndWordformText();
// 2020-11-20 добавим условие, потому что сильно тормозит
            $text = Text::find($text_id);            
            if (!$text->wordforms()->where('w_id', $w_id)->count()) {
                $word->updateWordformText();
            }
        }
        return Word::createWordBlock((int)$text_id, (int)$w_id);
    }
    
    /**
     * Calls by AJAX from lexical grammatic search, 
     * @return string
     */
    public function loadLemmaBlock($text_id, $w_id)
    {        
//        $word = Word::whereTextId($text_id)->whereWId($w_id)->first();
        $text = Text::find((int)$text_id);
        if (!$text) {return;}
        return $text->createLemmaBlock((int)$w_id);
    }
    
    /**
     * Calls by AJAX from lexical grammatic search, 
     * @return string
     */
    public function loadUnlinkedLemmaBlock(Request $request)
    {        
        $word = $request->input('word');
        $lang_id = (int)$request->input('lang_id');
        if (!$word || !$lang_id) {return;}
        $lemmas = Lemma::whereLangId($lang_id)
                    ->where(function ($query) use ($word) {
                        $query->whereIn('id', function ($q) use ($word) {
                            $q->select('lemma_id')->from('lemma_wordform')
                              ->where('wordform_for_search', 'like', $word);
                        })->orWhere('lemma_for_search', 'like', $word);
                    })->get();

        $wordform_ids = LemmaWordform::whereLangId($lang_id)
                            ->where('wordform_for_search', 'like', $word)
                            ->pluck('wordform_id')->toArray();
        return Word::lemmaBlock($word, null, $lemmas, null, $wordform_ids);
    }
    
    /**
     * Calls by AJAX, 
     * adds 
     * /corpus/word/add_example/<text_id>_<w_id>_<wordform_id>_<gramset_id>
     * /corpus/word/load_word_block/<text_id>_<w_id>
     * /corpus/word/load_word_block/3154_1
     * 
     * @param type $example_id
     * @return string
     */
    public function updateWordBlock($text_id, $w_id)
    {        
        $word = Word::whereTextId($text_id)->whereWId($w_id)->first();
        $word->updateMeaningAndWordformText(true);
        $word->updateWordformText(true);
        return Word::createWordBlock((int)$text_id, (int)$w_id);
    }
    
    /**
     * /corpus/word/edit/1_179?word=gor’o-¦gor’kija
     * s_id=20
     * 
     * @param int $text_id
     * @param int $w_id
     * @param Request $request
     */
    public function edit($text_id, $w_id, Request $request) {
        $word = trim($request->input('word'));
        $cyr_word = trim($request->input('cyr_word'));
        
        $word_obj = Word::whereTextId($text_id)
                        ->whereWId($w_id)->first();
        if (!$word_obj) { return; }
        
        $word_obj->splitInSentence($word, $cyr_word);        
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
        if (!$word || !$word->s_id) {
            return;
        }
        return Text::createWordCheckedBlock($meaning_id, $text_id, $word->s_id, $w_id);
    }

    /**
     * Calls by AJAX, 
     * adds 
     * /corpus/word/add_example/<text_id>_<w_id>_<wordform_id>_<gramset_id>
     * 
     * @param type $example_id
     * @return string
     */
    public function addGramset($id)
    {
        if (!preg_match("/^(\d+)\_(\d+)_(\d+)_(\d+)$/",$id,$regs)) {
            return null;
        }        
        $wordform = Wordform::find($regs[3]);
//dd($wordform->id);    
        $wordform->updateTextWordformLinks($regs[1],$regs[2],$regs[4]);
        return Word::createWordBlock($regs[1],$regs[2]);
    }
    
    
    public function lemmaGramsetPrediction(Request $request) {
        $uword = $request->uword;
        $lang_id = (int)$request->lang_id;
        
        if (!$uword || !in_array($lang_id, Lang::projectLangIDs())) {
            return;
        }

        $exist_lemmas = $prediction = [];        
        foreach (Predictor::lemmaGramsetByAnalog($uword, $lang_id) as $l_p_g => $count) {
            list($lemma, $pos_id, $gramset_id) = preg_split("/\_/", $l_p_g);
            $item = ['lemma'=>$lemma,
                     'pos'=>PartOfSpeech::getNameById($pos_id),
                     'gramset'=>Gramset::getStringByID($gramset_id),
                     'proc'=>Str::intToProc($count)];
            $lemmas = Lemma::whereLangId($lang_id)
                           ->where('lemma', 'like', $lemma)
                           ->wherePosId($pos_id);
            if ($lemmas->count()) {
                foreach ($lemmas->get() as $lemma_obj) {
                    $exist_lemmas[$lemma_obj->id] = $item;
                    foreach ($lemma_obj->meanings as $meaning) {
                        $exist_lemmas[$lemma_obj->id]['meanings'][$lemma_obj->id.'_'.$meaning->id.'_'.$gramset_id]
                                = $meaning->getMultilangMeaningTextsStringLocale();
                    }
                }
            } else {
                $prediction[$l_p_g] = $item;
            }
        }
        
        return view('dict.wordform._prediction', compact('exist_lemmas', 'prediction'));
    }
}
