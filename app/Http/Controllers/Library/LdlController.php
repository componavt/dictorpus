<?php

namespace App\Http\Controllers\Library;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use LaravelLocalization;
use Illuminate\Support\Facades\Redirect;

//use App\Library\Ldl;

use App\Models\Dict\Concept;
//use App\Models\Dict\ConceptCategory;
use App\Models\Dict\Label;
//use App\Models\Dict\Lemma;
//use App\Models\Dict\PartOfSpeech;
//use App\Models\Dict\Relation;

class LdlController extends Controller
{
    public $url_args=[];
    public $args_by_get='';
    
    public function __construct(Request $request)
    {        
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
            ];
        $this->url_args['limit_num'] = 5;
        $this->args_by_get = search_values_by_URL($this->url_args);
    }
    
    public function index(Request $request)
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
/*        $page = $request->input('page');
        if ($page != 'help') {
            $page = 'welcome';
        }
        $locale = LaravelLocalization::getCurrentLocale();
 */
        $label_id = Label::LDLLabel;

        $alphabet = Concept::whereIn('id', function($q) {
                            $q->select('concept_id')->from('concept_meaning')
                              ->whereIn('meaning_id', function ($q2) {
                                  $q2->select('id')->from('meanings')
                                     ->whereIn('lemma_id', Label::ldlLemmas());
                              });
                        })
                         ->selectRaw('substr(text_ru,1,1) as letter')
                         ->groupBy('letter')
                         ->orderBy('letter')
                         ->get();
dd($alphabet);                        
/*
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
*/
        return view('ldl.index',
                compact('alphabet', 'args_by_get', 'url_args'));
    }
/*    
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
    }*/
}
