<?php

namespace App\Http\Controllers\Library;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use LaravelLocalization;
use Illuminate\Support\Facades\Redirect;

use App\Library\Olodict;

//use App\Models\Dict\Concept;
//use App\Models\Dict\ConceptCategory;
use App\Models\Dict\Label;
use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;
use App\Models\Dict\Relation;

class OlodictController extends Controller
{
    public $url_args=[];
    public $args_by_get='';
    
    public function __construct(Request $request)
    {
        // permission= dict.edit, redirect failed users to /dict/lemma/, authorized actions list:
/*        $this->middleware('auth:dict.edit,/audio', 
                          ['except'=>['index']]);*/
        
        $this->url_args = url_args($request) + 
            [
                'by_alpha'  => (int)$request->input('by_alpha'),
                'search_concept_category'  => $request->input('search_concept_category'),
                'search_concept'  => (int)$request->input('search_concept'),
                'search_gram'    => $request->input('search_gram'),
                'search_lemma'    => $request->input('search_lemma'),
                'search_letter'    => $request->input('search_letter'),
                'search_meaning'    => $request->input('search_meaning'),
                'search_pos'    => $request->input('search_pos'),
                'search_word'    => $request->input('search_word'),
                'with_audios'    => (int)$request->input('with_audios'),
                'with_photos'    => (int)$request->input('with_photos'),
                'with_template'    => (int)$request->input('with_template'),
                'limit_num' => 5
            ];
        $this->url_args['limit_num'] = 5;
//dd($this->url_args['by_alpha']);        
/*        $url_args = $this->url_args;
        if ($url_args['search_lemma']) {
            if (!$url_args['search_letter']) {
                $url_args['search_letter'] = mb_substr($url_args['search_lemma'], 0, 1);
            }
        }
        
        $this->url_args = $url_args;*/
        $this->args_by_get = search_values_by_URL($this->url_args);
    }
    
    public function index(Request $request)
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        $page = $request->input('page');
        if ($page != 'help') {
            $page = 'welcome';
        }
        $locale = LaravelLocalization::getCurrentLocale();
        $label_id = Label::OlodictLabel;

        $alphabet = Lemma::whereIn('id', Label::checkedOloLemmas())
                         ->selectRaw('substr(lemma_for_search,1,1) as letter')
                         ->groupBy('letter')
                         ->orderBy('letter')
                         ->get();

        $lemma_list = Olodict::lemmaList($url_args);
        $lemmas_total = sizeof($lemma_list->get());
        $lemma_list = $lemma_list->paginate($url_args['limit_num']);
        
        $gram_list = Olodict::gramLinks($url_args['search_letter']);
        $lemmas = Olodict::search($url_args);
        $dialect_id = Olodict::Dialect;
        $pos_values = PartOfSpeech::getListForOlodict();
        $concept_category_values = Olodict::conceptCategoryList(false);
        $concept_values = [NULL=>'']+Olodict::conceptList($url_args['search_concept_category'], $url_args['search_pos']);
        $relations = Relation::getList();//orderBy('sequence_number')->get();

        return view('olodict.index',
                compact('alphabet', 'concept_category_values', 'concept_values', 
                        'dialect_id', 'gram_list', 'label_id', 'lemma_list', 
                        'lemmas_total', 'lemmas', 'locale', 'page', 'pos_values',
                        'relations', 'args_by_get', 'url_args'));
    }
    
    public function lemmaList()
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
//dd($url_args['by_alpha']);
        $locale = LaravelLocalization::getCurrentLocale();
        
        $lemma_list = Olodict::lemmaList($url_args);
        $lemmas_total = sizeof($lemma_list->get());
//dd($lemmas_total);        
        $lemma_list = $lemma_list->paginate($url_args['limit_num']);
        
        return view('olodict._lemma_list',
                compact('lemma_list', 'lemmas_total', 'locale', 
                        'args_by_get', 'url_args'));
    }
    
    public function gramLinks(string $letter)
    {
        $url_args = $this->url_args;
        $locale = LaravelLocalization::getCurrentLocale();
        $gram_list = Olodict::gramLinks($letter);
                        
        return view('olodict._gram_links',
                compact('gram_list', 'locale', 'url_args'));
    }
    
    public function lemmas() {
        $url_args = $this->url_args;
        $lemmas = Olodict::search($url_args);
        return view('olodict._lemmas',
                compact('lemmas', 'url_args'));
    }
    
    public function help() {
        $locale = LaravelLocalization::getCurrentLocale();
        return Redirect::to('/'.$locale.'/olodict?page=help');
    }
    
    public function abbr() {
        return view('olodict.abbr');
    }
    
    public function stats() {
        $dialect_id = Olodict::Dialect;
        $symbols = 0;
        $meanings_c = 0;
        $sentences_c = 0;
        $wordforms_c = 0;
        
        $lemmas = Lemma::whereIn('id', Label::checkedOloLemmas())->get();
        $lemmas_c = sizeof($lemmas);
        foreach ($lemmas as $lemma) {
            $symbols += mb_strlen($lemma->lemma); 
            $meanings = $lemma->meaningsWithBestExamples();
            $meanings_c += sizeof($meanings);
            foreach ($meanings as $meaning) {
                foreach($meaning->meaningTexts()->pluck('meaning_text')->toArray() as $meaning_text) {
                    $symbols += mb_strlen($meaning_text);                     
                } 
//dd($lemma->lemma, $meaning->sentences(false, '', 0, 10)); 
                $sentences = $meaning->sentences(false, '', 0, 10);
                $sentences_c += sizeof($sentences);
                foreach ($sentences as $sent) {
                    $symbols += mb_strlen($sent['s']);                                         
                    $symbols += mb_strlen($sent['trans_s']);                                         
                }
            }
            foreach ($lemma->wordformsByDialect($dialect_id) as $wordform) {
                $wordforms_c += 1;
                $symbols += mb_strlen($wordform->wordform);                     
            }
        }
//dd($lemmas);        
        return view('olodict.stats',
                compact('lemmas_c', 'meanings_c', 'sentences_c', 'symbols', 'wordforms_c'));
    }
}
