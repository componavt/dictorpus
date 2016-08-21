<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Dict\Dialect;

class DialectController extends Controller
{
    /**
     * Show the list of dialects.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $dialects = Dialect::orderBy('lang_id','id')->get();

        return view('dict.dialect.index')->with(array('dialects' => $dialects));
    }
}
