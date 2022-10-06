<?php

namespace App\Http\Controllers\Library;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use LaravelLocalization;

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
                'search_concept_category'  => $request->input('search_concept_category'),
                'search_concept'  => (int)$request->input('search_concept'),
                'search_gram'    => $request->input('search_gram'),
                'search_lemma'    => $request->input('search_lemma'),
                'search_letter'    => $request->input('search_letter'),
                'search_meaning'    => $request->input('search_meaning'),
                'search_pos'    => (int)$request->input('search_pos'),
                'search_word'    => $request->input('search_word'),
                'with_audios'    => (int)$request->input('with_audios'),
                'with_template'    => (int)$request->input('with_template'),
                'limit_num' => 10
            ];
        
/*        $url_args = $this->url_args;
        if ($url_args['search_lemma']) {
            if (!$url_args['search_letter']) {
                $url_args['search_letter'] = mb_substr($url_args['search_lemma'], 0, 1);
            }
        }
        
        $this->url_args = $url_args;*/
        $this->args_by_get = search_values_by_URL($this->url_args);
    }
    
    public function index(/*Request $request*/)
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
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
        $concept_category_values = Olodict::conceptCategoryList();
        $concept_values = [NULL=>'']+Olodict::conceptList($url_args['search_concept_category'], $url_args['search_pos']);
        $relations = Relation::getList();//orderBy('sequence_number')->get();

        return view('olodict.index',
                compact('alphabet', 'concept_category_values', 'concept_values', 
                        'dialect_id', 'gram_list', 'label_id', 'lemma_list', 
                        'lemmas_total', 'lemmas', 'locale', 'pos_values',
                        'relations', 'args_by_get', 'url_args'));
    }
    
    public function lemmaList()
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        $locale = LaravelLocalization::getCurrentLocale();
        
        $lemma_list = Olodict::lemmaList($url_args);
        $lemmas_total = sizeof($lemma_list->get());
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
        return view('olodict.help');
    }
    
    public function abbr() {
        return view('olodict.abbr');
    }
}
