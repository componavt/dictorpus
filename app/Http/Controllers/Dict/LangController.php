<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Dict\Lang;

class LangController extends Controller
{
    /**
     * Show the list of languages.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $langs = Lang::orderBy('name_en')->get();
        $total_count = Lang::count();

        return view('dict.lang.index')->with(array('languages' => $langs,
                                                   'total_count' => $total_count));
    }
}
