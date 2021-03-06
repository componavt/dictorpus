<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;
use DB;

//use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Library\Str;
use App\Library\Predictor;

use App\Models\Corpus\Word;
use App\Models\Corpus\Text;

use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
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
        $this->url_args = [
                    'limit_num'       => (int)$request->input('limit_num'),
                    'page'            => (int)$request->input('page'),
                    'search_lang'     => (int)$request->input('search_lang'),
                    'search_word'     => $request->input('search_word'),
                    'search_linked'   => $request->input('search_linked'),
                ];
        
        if (!$this->url_args['page']) {
            $this->url_args['page'] = 1;
        }
        
        if ($this->url_args['limit_num']<=0) {
            $this->url_args['limit_num'] = 100;
        } elseif ($this->url_args['limit_num']>1000) {
            $this->url_args['limit_num'] = 1000;
        }   
       
        $this->args_by_get = Str::searchValuesByURL($this->url_args);
    }
    
    /**
     * SQL: select lower(word) as l_word, count(*) as frequency from words where text_id in (select id from texts where lang_id=1) group by word order by frequency DESC, l_word LIMIT 30;
     * SQL: select word, count(*) as frequency from words where text_id in (select id from texts where lang_id=1) group by word order by frequency DESC, word LIMIT 30;
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function frequencyDict(Request $request) {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        if ($url_args['search_lang']) {
            $lang_id = $url_args['search_lang'];
//            $words = Word::select(DB::raw('lower(word) as l_word'),DB::raw('count(word) as frequency'))
            $words = Word::select('word',DB::raw('count(word) as frequency'))
                   ->whereIn('text_id', function($query) use ($lang_id){
                                $query->select('id')->from('texts')
                                      ->where('lang_id',$lang_id);
                        })
                  ->groupBy('word')
                  ->orderBy(DB::raw('count(word)'), 'DESC');
                        
            if ($url_args['search_word']) {
                $words = $words->where('word','like',$url_args['search_word']);
            } 

//var_dump($words->toSql());        
            $words = $words 
//                    ->take($this->url_args['limit_num'])
                    ->take(1000)
                    ->get();
        } else {
            $words = NULL;
        }
        $lang_values = Lang::getList();
        
        return view('corpus.word.freq_dict',
                compact('lang_values', 'words', 'args_by_get', 'url_args'));
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
//        return Word::createGramsetBlock($regs[1],$regs[2]);
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
