<?php

namespace App\Http\Controllers\Library;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Library\Olodict;

use App\Models\Dict\Label;
use App\Models\Dict\Lemma;

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
                'search_pos'    => (int)$request->input('search_pos'),
                'search_with_audio'    => (int)$request->input('search_with_audio'),
                'limit_num' => 20
            ];
        
        $url_args = $this->url_args;
        if ($url_args['search_lemma']) {
            if (!$url_args['search_letter']) {
                $url_args['search_letter'] = mb_substr($url_args['search_lemma'], 0, 1);
            }
            if (!$url_args['search_gram']) {
                $url_args['search_gram'] = mb_substr($url_args['search_lemma'], 0, 3);
            }
        }
        
        $this->url_args = $url_args;
        $this->args_by_get = search_values_by_URL($this->url_args);
    }
    
    public function index(/*Request $request*/)
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        $alphabet = Lemma::whereIn('id', Label::checkedOloLemmas())
                         ->selectRaw('substr(lemma_for_search,1,1) as letter')
                         ->groupBy('letter')
                         ->orderBy('letter')
                         ->get();
//dd($alphabet);                        
//        $first_letter = $alphabet[0]->letter;
        
                        
//        $first_trigram = $trigrams[0]->gram;
        $lemma_list = Olodict::lemmaList($url_args);
        $gram_list = Olodict::gramLinks($url_args['search_letter']);
        $lemmas = Olodict::search($url_args);
        
        return view('olodict.index',
                compact('alphabet', 'gram_list', 'lemma_list', 'lemmas', 'args_by_get', 'url_args'));
    }
    
    public function lemmaList()
    {
        $url_args = $this->url_args;
        $lemma_list = Olodict::lemmaList($url_args);
                        
        return view('olodict._lemma_list',
                compact('lemma_list', 'url_args'));
    }
    
    public function gramLinks(string $letter)
    {
        $url_args = $this->url_args;
        $gram_list = Olodict::gramLinks($letter);
                        
        return view('olodict._gram_links',
                compact('gram_list', 'url_args'));
    }
    
    public function lemmas() {
        $url_args = $this->url_args;
        $lemmas = Olodict::search($url_args);
        return view('olodict._lemmas',
                compact('lemmas', 'url_args'));
    }
}
