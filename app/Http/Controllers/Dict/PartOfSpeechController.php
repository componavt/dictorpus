<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Dict\PartOfSpeech;

class PartOfSpeechController extends Controller
{
    /**
     * Show the list of parts of speech.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $categories = PartOfSpeech::select('category')->groupBy('category')->orderBy('category')->get();
        
        $pos_category = array();
        
        foreach ($categories as $row) {
            $pos_category[$row->category] = PartOfSpeech::getByCategory($row->category);
        }

        return view('dict.pos.index')->with(array('pos_category' => $pos_category));
    }
}
