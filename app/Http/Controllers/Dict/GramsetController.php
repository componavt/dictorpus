<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
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
}
