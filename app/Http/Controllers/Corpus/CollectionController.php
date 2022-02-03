<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

use App\Models\Corpus\Collection;
use App\Models\Corpus\Genre;

use App\Models\Dict\Dialect;

class CollectionController extends Controller
{
    public function index() {
        return view('corpus.collection.index');
    }
    
    public function show($id) {
        if (Collection::isCollectionId($id)) {
            $genres = Genre::where('parent_id', Collection::getCollectionGenres($id))
                           ->orderBy('sequence_number')->get();
            $lang_id = Collection::getCollectionLangs($id);
            $dialects = Dialect::whereIn('lang_id', $lang_id)->get();
            return view('corpus.collection.'.$id.'.index',
                    compact('dialects', 'genres', 'id', 'lang_id'));
        }
        return Redirect::to('/corpus/collection');
    }
}
