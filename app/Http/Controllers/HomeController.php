<?php

namespace App\Http\Controllers;

use LaravelLocalization;

use App\Models\Corpus\Text;
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
    public function index()
    {
        $limit = 3;
        $locale = LaravelLocalization::getCurrentLocale();
        $total_lemmas = Lemma::count();
        $total_texts = Text::count();
        $total_dialects = Dialect::count();
        $lemmas_choice = \Lang::choice('blob.choice_articles',$total_lemmas, [], $locale);
        $texts_choice = \Lang::choice('blob.choice_texts',$total_texts, [], $locale);        
        $video = Text::videoForStart();
        return view('welcome',
                    compact('lemmas_choice', 'limit', 'texts_choice', 'total_dialects', 
                            'total_lemmas', 'total_texts', 'video'));
    }   

    public function page($page) {
        return view('page.'.$page);        
    }
    
    public function help($section, $page) {
        return view('help.'.$section.'.'.$page);        
    }
}
