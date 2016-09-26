<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Dict\Gram;
use App\Models\Dict\GramCategory;
use App\Models\Dict\PartOfSpeech;

class GramsetController extends Controller
{
    //
    
     /**
     * Show the list of gramsets.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $pos_id = (int)$request->input('pos_id');
        
        $pos_obj = PartOfSpeech::find($pos_id);
 
        $gramsets = NULL;
        if ($pos_obj) {
            $gramsets = $pos_obj->gramsets()->get();
        }
        
        $pos_values = PartOfSpeech::getGroupedListWithQuantity('gramsets');
                
        return view('dict.gramset.index')
                ->with(array('pos_id'=>$pos_id, 
                             'pos_values' => $pos_values, 
                             'gramsets' => $gramsets));
    }   

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pos_values = PartOfSpeech::getGroupedList();
        
        $grams = [];
        
        foreach (GramCategory::all() as $gc) {         //   id is gram_category_id
            $grams[$gc->name_en] = ['name'=> $gc->name,
                                    'grams' => [NULL=>''] + Gram::getList($gc->id)];
        }

        return view('dict.gramset.create')
                  ->with(['grams' => $grams,
                          'pos_values'=>$pos_values
                         ]);
    }

}
