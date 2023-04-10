<?php

namespace App\Http\Controllers;

use LaravelLocalization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Models\Corpus\Text;
use App\Models\Corpus\Word;

use App\Models\Dict\Dialect;
use App\Models\Dict\Lemma;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the start page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search_w = $request->input('search_w');
        $limit = 7;
        $locale = LaravelLocalization::getCurrentLocale();
        $total_lemmas = Lemma::count();
        $total_texts = Text::count();
        $total_words = Word::count();
        $total_dialects = Dialect::count();
        $lemmas_choice = \Lang::choice('blob.choice_articles',$total_lemmas, [], $locale);
        $texts_choice = \Lang::choice('blob.choice_texts',$total_texts, [], $locale);        
        $words_choice = \Lang::choice('blob.choice_words',$total_words, [], $locale);        
        $video = Text::videoForStart();
        $locale = LaravelLocalization::getCurrentLocale();
        return view('welcome',
                    compact('lemmas_choice', 'limit', 'locale', 'search_w', 
                            'texts_choice', 'total_dialects', 'total_lemmas', 
                            'total_texts', 'total_words', 'video', 'words_choice'));
    }   

    public function page($page) {
        return view('page.'.$page);        
    }
    
    public function help($section, $page) {
        return view('help.'.$section.'.'.$page);        
    }
    
    public function simpleSearch(Request $request) {
        $search_w = $request->input('search_w');
        $search_by_dict = (int)$request->input('search_by_dict');
        $search_by_corpus = (int)$request->input('search_by_corpus');
        
        if ($search_w) {
            if ($search_by_dict && !$search_by_corpus) {
                return Redirect::to(route('lemma.simple_search',['search_w'=>$search_w]));
            } elseif (!$search_by_dict && $search_by_corpus) {
                return Redirect::to(route('text.simple_search',['search_w'=>$search_w]));
            }
        }
        
        if (!$search_by_dict && !$search_by_corpus) {
            $search_by_dict=$search_by_corpus=1;
        }
        $lemma_total = Lemma::simpleSearch($search_w)->count();
        $text_total = Text::simpleSearch($search_w)->count();
        return view('simple_search', 
                compact('search_w', 'search_by_dict', 'search_by_corpus', 
                        'lemma_total', 'text_total'));        
    }
    
}
