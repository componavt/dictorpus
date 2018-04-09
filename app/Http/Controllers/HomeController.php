<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

use App\Models\Dict\Lemma;
use App\Models\Corpus\Text;

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
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $limit = 3;
        $new_lemmas = Lemma::lastCreatedLemmas($limit);
        $last_updated_lemmas = Lemma::lastUpdatedLemmas($limit);
        $new_texts = Text::lastCreatedTexts($limit);
        $last_updated_texts = Text::lastUpdatedTexts($limit);
        
        return view('welcome')->with([
                                        'new_lemmas'=>$new_lemmas,
                                        'last_updated_lemmas'=>$last_updated_lemmas,
                                        'new_texts'=>$new_texts,
                                        'last_updated_texts'=>$last_updated_texts,
                                     ]);
    }
}
