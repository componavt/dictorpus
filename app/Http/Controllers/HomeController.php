<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

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
        $total_lemmas = Lemma::totalCount();
        $total_texts = Text::totalCount();
        $total_dialects = Dialect::totalCount();
        return view('welcome')
                ->with(['limit'=>$limit,
                        'total_dialects' => $total_dialects,
                        'total_lemmas' => $total_lemmas,
                        'total_texts' => $total_texts,
                       ]);
    }
    
    /**
     * @return \Illuminate\Http\Response
     */
    public function stats()
    {
        $total_lemmas = Lemma::totalCount();
        $total_texts = Text::totalCount();
        $total_dialects = Dialect::totalCount();
        return view('page.stats')
                ->with([
                        'total_lemmas' => $total_lemmas,
                        'total_texts' => $total_texts,
                       ]);
    }
}
