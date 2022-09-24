<?php

namespace App\Http\Controllers\Library;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use LaravelLocalization;

use App\Library\Olodict;

use App\Models\Dict\Label;
use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;

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
                'search_gram'    => $request->input('search_gram'),
                'search_lemma'    => $request->input('search_lemma'),
                'search_letter'    => $request->input('search_letter'),
                'search_meaning'    => $request->input('search_meaning'),
                'search_pos'    => (int)$request->input('search_pos'),
                'search_template'    => $request->input('search_template'),
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

        return view('olodict.index',
                compact('alphabet', 'dialect_id', 'gram_list', 'lemma_list', 
                        'lemmas_total', 'lemmas', 'locale', 'pos_values',
                        'args_by_get', 'url_args'));
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
}
