<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Dict\Gram;
use App\Models\Dict\GramCategory;

class GramController extends Controller
{
    /**
     * Show the list of grammatical attributes.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gram_categories = GramCategory::all();
        // $gram_categories = GramCategory::select('*')->orderBy('id')->get();
        
        $grams = array();
        
        foreach ($gram_categories as $gc) {         //   id is gram_category_id
            $grams[$gc->name] = Gram::getByCategory($gc->id);
        }

        return view('dict.gram.index')->with(array('grams' => $grams));
    }

}
