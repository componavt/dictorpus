<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

use App\Models\Corpus\Author;
use App\Models\Corpus\Collection;
use App\Models\Corpus\Cycle;
use App\Models\Corpus\Genre;
use App\Models\Corpus\Motive;
use App\Models\Corpus\Motype;
use App\Models\Corpus\Plot;
use App\Models\Corpus\Text;
use App\Models\Corpus\Topic;

use App\Models\Dict\Dialect;
use App\Models\Dict\Lang;

class CollectionController extends Controller
{
    public function index() {
        return view('corpus.collection.index');
    }
    
    public function show($id, Request $request) {
        $id = (int)$id;
        $for_print = (int)($request->for_print);

        if (Collection::isCollectionId($id)) {
            if (Collection::isCollectionByAuthor($id)) {
                $author_id = Collection::getCollectionAuthors($id);
                $author = Author::find($author_id);
                $text_count = Text::whereIn('id', function ($q) use ($author_id) {
                                    $q->select('text_id')->from('author_text')
                                      ->whereIn('author_id', [$author_id]);
                                })->count();
                return view('corpus.collection.'.$id.'.index',
                        compact('author', 'id', 'text_count'));
                
            } elseif (Collection::isCollectionByGenre($id)) {
                $lang_ids = Collection::getCollectionLangs($id);
                $langs = Lang::whereIn('id', $lang_ids)->orderBy('id')->get();
                if ($id == 3) {
                    $genre_arr = [Collection::getCollectionGenres($id)];
                    $genres = [Genre::find($genre_arr[0])];
                } else {
                    $genres = Genre::where('parent_id', Collection::getCollectionGenres($id))
                               ->orderBy('sequence_number')->get();
                    $genre_arr = Genre::find(Collection::getCollectionGenres($id))
                            ->getSubGenreIds();
                }
                $text_count = Text::whereIn('lang_id', $lang_ids)
                                  ->whereIn('id', function ($q) use ($genre_arr) {
                                    $q->select('text_id')->from('genre_text')
                                      ->whereIn('genre_id', $genre_arr);
                                })->count();
                $dialects = [];
                if ($id == 1) {
                    $dialects = Dialect::whereIn('lang_id', $lang_ids)->get();
                } elseif ($id == 6) {
                    foreach ($genres as $genre) {
                        foreach ($langs as $lang) {
                        $dials = Dialect::where('lang_id', $lang->id)->get();
                            foreach ($dials as $dialect) {
                                $texts = $dialect->textsByGenre($genre->id)->sortBy('title');
                                if (!count($texts)) {
                                    continue;
                                }
                                $dialects[$genre->id]['langs'][$lang->id]['dialects'][$dialect->id] = ['dialect'=>$dialect, 'texts'=>$texts];
                            }
                            $dialects[$genre->id]['langs'][$lang->id]['lang_text_count'] = 
                                    Text::where('lang_id', $lang->id)
                                      ->whereIn('id', function ($q) use ($genre) {
                                        $q->select('text_id')->from('genre_text')
                                          ->where('genre_id', $genre->id);
                                    })->count();
                        } 
                        $dialects[$genre->id]['genre_text_count'] = 
                                Text::whereIn('lang_id', $lang_ids)
                                  ->whereIn('id', function ($q) use ($genre) {
                                    $q->select('text_id')->from('genre_text')
                                      ->where('genre_id', $genre->id);
                                })->count();
                    }
//dd($dialects);                    
                }
                return view('corpus.collection.'.$id.'.index',
                        compact('dialects', 'for_print', 'genres', 'id', 'lang_ids', 
                                'langs', 'text_count'));
            } elseif ($id == 7) {
                Redirect::to('/corpus/monument');
            }
        }
        return Redirect::to('/corpus/collection');
    }
    
    public function runeTopics(Request $request) {
        $for_print = (int)($request->for_print);
        $genres = Genre::where('parent_id', Collection::getCollectionGenres(2))
                   ->orderBy('sequence_number')->get();
        $genre_arr = Genre::find(Collection::getCollectionGenres(2))
                ->getSubGenreIds();
        $lang_id = Collection::getCollectionLangs(2);
        $dialects = Dialect::whereIn('lang_id', $lang_id)->get();
        return view('corpus.collection.2.topics',
                compact('dialects', 'for_print', 'genres', 'lang_id'));        
    }
    
    public function runesForPlot($plot_id, Request $request) {
        $for_print = (int)($request->for_print);
        $plot = Plot::find($plot_id);
        $lang_id = Collection::getCollectionLangs(2);
        $texts = $plot->texts()->whereIn('lang_id', $lang_id)->get()->sortBy('year');
        $page_title = trans('corpus.plot'). ': '. $plot->name;
        $url_args = '?search_collection=2&search_plot='.$plot->id.'&for_print='.$for_print;
        return view('corpus.collection.2.texts',
                compact('for_print', 'lang_id', 'page_title', 'texts', 'url_args'));        
    }
    
    public function runesForTopic($topic_id, Request $request) {
        $for_print = (int)($request->for_print);
        $topic = Topic::find($topic_id);
        $plot = Plot::find($request->plot_id);
        if (!$topic || !$plot) {
            return;
        }
        $lang_id = Collection::getCollectionLangs(2);
        $texts = $topic->textsForPlot($plot->id)->whereIn('lang_id', $lang_id)->get()->sortBy('year');
        $page_title = trans('corpus.plot'). ': '. $plot->name.'<br>'.trans('corpus.topic'). ': '. $topic->name;
        $url_args = '?search_collection=2&search_topic='.$topic->id.'&for_print='.$for_print;
        $back_link = ['/corpus/collection/2/topics', trans('collection.topic_index')];
        return view('corpus.collection.2.texts',
                compact('back_link', 'for_print', 'page_title', 'texts', 'url_args'));        
    }
    
    public function predictionTextsForCycle($cycle_id) {
        $cycle = Cycle::find($cycle_id);
        $lang_id = Collection::getCollectionLangs(3);
        $texts = $cycle->texts()->whereIn('lang_id', $lang_id)->get();
        $page_title = trans('corpus.cycle'). ': '. $cycle->name;
        $url_args = '?search_collection=3&search_cycle='.$cycle->id;
        return view('corpus.collection.3.texts',
                compact('page_title', 'texts', 'url_args'));
        
    }
    
    public function karelianRunes() {
        return Redirect::to('/corpus/collection/2');
    }
    
    public function karelianLegends() {
        return Redirect::to('/corpus/collection/3');
    }
    
    public function predictionMotives() {
        $genre_id = Collection::getCollectionGenres(3);
//        $lang_id = Collection::getCollectionLangs(3);
        $motypes = Motype::whereGenreId($genre_id)->orderBy('code')->get();
        return view('corpus.collection.3.motives',
                compact('motypes'));        
    }
    
    public function predictionTextsForMotive($motive_id) {
        $motive = Motive::find($motive_id);
        $lang_id = Collection::getCollectionLangs(3);
        $texts = $motive->texts()->whereIn('lang_id', $lang_id)->get();
        $page_title = trans('corpus.motive'). ': '. $motive->full_name;
        $url_args = '?search_collection=3&search_motive='.$motive->id;
        $back_link = ['/corpus/collection/3/motives', trans('collection.motive_index')];
        return view('corpus.collection.3.texts',
                compact('back_link', 'page_title', 'texts', 'url_args'));
        
    }
}
