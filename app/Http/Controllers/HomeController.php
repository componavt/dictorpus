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
        $new_texts = Text::lastCreatedTexts($limit);
        $last_updated_texts = Text::lastUpdatedTexts($limit);
        
        return view('welcome')->with([
                                        'limit'=>$limit,
                                        'new_texts'=>$new_texts,
                                        'last_updated_texts'=>$last_updated_texts,
                                     ]);
    }
}
