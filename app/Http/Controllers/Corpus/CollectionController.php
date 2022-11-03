<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

use App\Models\Corpus\Collection;
use App\Models\Corpus\Cycle;
use App\Models\Corpus\Genre;

use App\Models\Dict\Dialect;

class CollectionController extends Controller
{
    public function index() {
        return view('corpus.collection.index');
    }
    
    public function show($id) {
        if (Collection::isCollectionId($id)) {
            if ($id == 3) {
                $genres = [Genre::find(Collection::getCollectionGenres($id))];
            } else {
                $genres = Genre::where('parent_id', Collection::getCollectionGenres($id))
                           ->orderBy('sequence_number')->get();
            }
            $lang_id = Collection::getCollectionLangs($id);
            $dialects = Dialect::whereIn('lang_id', $lang_id)->get();
            return view('corpus.collection.'.$id.'.index',
                    compact('dialects', 'genres', 'id', 'lang_id'));
        }
        return Redirect::to('/corpus/collection');
    }
    
    public function predictionShow($cycle_id) {
        $cycle = Cycle::find($cycle_id);
        $lang_id = Collection::getCollectionLangs(3);
        $texts = $cycle->texts()->whereIn('lang_id', $lang_id)->get();
        return view('corpus.collection.3.by_cycle',
                compact('cycle', 'texts'));
        
    }
    
    public function karelianRunes() {
        return Redirect::to('/corpus/collection/2');
    }
    
    public function karelianLegends() {
        return Redirect::to('/corpus/collection/3');
    }
    
}
