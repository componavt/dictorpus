<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

use App\Models\Corpus\Author;
use App\Models\Corpus\Collection;
use App\Models\Corpus\Corpus;
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
    public function index()
    {
        return view('corpus.collection.index');
    }

    public function show($id, Request $request)
    {
        $id = (int)$id;
        $collection = Collection::find($id);
        $for_print = (int)($request->for_print);

        if (Collection::isCollectionId($id)) {
            if (Collection::isCollectionByAuthor($id)) {
                $author_id = Collection::getCollectionAuthors($id);
                $author = Author::find($author_id);
                return view(
                    'corpus.collection.' . $id . '.index',
                    compact('author', 'id')
                );
            } elseif (Collection::isCollectionByGenre($id)) {
                list($dialects, $genres, $lang_ids, $langs, $text_count) = Collection::getDataForCollectionByGenre($id);
                return view(
                    'corpus.collection.' . $id . '.index',
                    compact(
                        'dialects',
                        'for_print',
                        'genres',
                        'collection',
                        'lang_ids',
                        'langs',
                        'text_count'
                    )
                );
            } elseif (Collection::isCollectionByCorpuses($id)) {
                list($corpuses, $text_count) = Collection::getDataForCollectionByCorpuses($id);
                return view(
                    'corpus.collection.' . $id . '.index',
                    compact(
                        'for_print',
                        'collection',
                        'corpuses',
                        'id',
                        'text_count'
                    )
                );
            } elseif ($id == 7) {
                return Redirect::to('/corpus/monument');
            }
        }
        return Redirect::to('/corpus/collection');
    }

    public function runeTopics(Request $request)
    {
        $for_print = (int)($request->for_print);
        $genres = Genre::where('parent_id', Collection::getCollectionGenres(2))
            ->orderBy('sequence_number')->get();
        $genre_arr = Genre::find(Collection::getCollectionGenres(2))
            ->getSubGenreIds();
        $lang_id = Collection::getCollectionLangs(2);
        $dialects = Dialect::whereIn('lang_id', $lang_id)->get();
        return view(
            'corpus.collection.2.topics',
            compact('dialects', 'for_print', 'genres', 'lang_id')
        );
    }

    public function texts(int $id, Request $request)
    {
        $for_print = (int)($request->for_print);
        $plot_id = (int)($request->plot_id);
        $plot = Plot::find($plot_id);
        $corpus_id = (int)($request->corpus_id);
        $corpus = Corpus::find($corpus_id);

        $collection = Collection::find($id);
        if (!$collection) {
            return;
        }
        $lang_ids = $collection->getLangIds();
        $page_titles = [];
        $texts = Text::whereIn('lang_id', $lang_ids);
        if ($corpus) {
            $page_titles[] = trans('corpus.corpus') . ': ' . $corpus->name;
            $texts->whereIn('id', function ($q) use ($corpus_id) {
                $q->select('text_id')->from('corpus_text')
                    ->where('corpus_id', $corpus_id);
            });
        }
        if ($plot) {
            $page_titles[] = trans('corpus.plot') . ': ' . $plot->name;
            $texts->whereIn('id', function ($q) use ($plot_id) {
                $q->select('text_id')->from('plot_text')
                    ->where('plot_id', $plot_id);
            });
        }
        $texts = $texts->get()->sortBy('year');
        $page_title = join('<br>', $page_titles);
        $url_args = '?search_collection=' . $id . '&search_plot=' . $plot_id . '&for_print=' . $for_print;
        return view(
            'corpus.collection.' . $id . '.texts',
            compact('collection', 'for_print', 'page_title', 'texts', 'url_args')
        );
    }

    public function runesForTopic($topic_id, Request $request)
    {
        $for_print = (int)($request->for_print);
        $topic = Topic::find($topic_id);
        $plot = Plot::find($request->plot_id);
        if (!$topic || !$plot) {
            return;
        }
        $collection = Collection::find(2);
        if (!$collection) {
            return;
        }
        $lang_ids = $collection->getLangIds();
        $texts = $topic->textsForPlot($plot->id)->whereIn('lang_id', $lang_ids)->get()->sortBy('year');
        $page_title = trans('corpus.plot') . ': ' . $plot->name . '<br>' . trans('corpus.topic') . ': ' . $topic->name;
        $url_args = '?search_collection=2&search_topic=' . $topic->id . '&for_print=' . $for_print;
        $back_link = ['/corpus/collection/2/topics', trans('collection.topic_index')];
        return view(
            'corpus.collection.2.texts',
            compact('back_link', 'collection', 'for_print', 'page_title', 'texts', 'url_args')
        );
    }

    public function predictionTextsForCycle($cycle_id)
    {
        $cycle = Cycle::find($cycle_id);
        $lang_id = Collection::getCollectionLangs(3);
        $texts = $cycle->texts()->whereIn('lang_id', $lang_id)->get();
        $page_title = trans('corpus.cycle') . ': ' . $cycle->name;
        $url_args = '?search_collection=3&search_cycle=' . $cycle->id;
        return view(
            'corpus.collection.3.texts',
            compact('page_title', 'texts', 'url_args')
        );
    }

    public function karelianRunes()
    {
        return Redirect::to('/corpus/collection/2');
    }

    public function karelianLegends()
    {
        return Redirect::to('/corpus/collection/3');
    }

    public function predictionMotives()
    {
        $genre_id = Collection::getCollectionGenres(3);
        //        $lang_id = Collection::getCollectionLangs(3);
        $motypes = Motype::whereGenreId($genre_id)->orderBy('code')->get();
        return view(
            'corpus.collection.3.motives',
            compact('motypes')
        );
    }

    public function predictionTextsForMotive($motive_id)
    {
        $motive = Motive::find($motive_id);
        $lang_id = Collection::getCollectionLangs(3);
        $texts = $motive->texts()->whereIn('lang_id', $lang_id)->get();
        $page_title = trans('corpus.motive') . ': ' . $motive->full_name;
        $url_args = '?search_collection=3&search_motive=' . $motive->id;
        $back_link = ['/corpus/collection/3/motives', trans('collection.motive_index')];
        return view(
            'corpus.collection.3.texts',
            compact('back_link', 'page_title', 'texts', 'url_args')
        );
    }
}
