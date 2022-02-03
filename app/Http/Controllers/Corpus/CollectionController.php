<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

use App\Models\Corpus\Genre;

use App\Models\Dict\Dialect;

class CollectionController extends Controller
{
    public function index() {
        return view('corpus.collection.index');
    }
    
    public function show($id) {
        $collect_genres = [1=>19, 2=>66];
        $langs = [1=>[1], 2=>[4,5,6]];
        
        if (in_array($id, [1, 2])) {
            $genres = Genre::where('parent_id',$collect_genres[$id])->get();
            $lang_id = $langs[$id];
            $dialects = Dialect::whereIn('lang_id', $lang_id)->get();
            return view('corpus.collection.'.$id.'.index',
                    compact('dialects', 'genres', 'id', 'lang_id'));
        }
        return Redirect::to('/corpus/collection');
    }
}
