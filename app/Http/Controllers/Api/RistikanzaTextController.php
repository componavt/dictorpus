<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Controller;
//use Illuminate\Support\Facades\Response;
//use Illuminate\Support\Facades\DB;

use App\Models\Corpus\Author;
use App\Models\Corpus\Corpus;
use App\Models\Corpus\District;
use App\Models\Corpus\Genre;
use App\Models\Corpus\Informant;
use App\Models\Corpus\Place;
use App\Models\Corpus\Plot;
use App\Models\Corpus\Recorder;
use App\Models\Corpus\Region;
use App\Models\Corpus\Text;
use App\Models\Corpus\Topic;

use App\Models\Dict\Dialect;
use App\Models\Dict\Lang;

class RistikanzaTextController extends Controller
{
    protected $ethnographicCorpus = 15;

    public function ethnographic(Request $request)
    {
        $url_args = Text::urlArgs($request);
        $url_args['search_corpus'] = [$this->ethnographicCorpus];

        $texts = Text::search($url_args)
            ->with([
                'dialects',
                'lang',
                'transtext'
            ]);
        $n_records = $texts->count();

        $texts = $texts->paginate(
            $url_args['limit_num']
        );
        /*Log::debug('Ristikanza API locale', [
            'app_locale' => app()->getLocale(),
            'accept_language' => $request->header('Accept-Language'),
            'title' => $texts->first()->title ?? null,
        ]);*/

        $items = $texts->getCollection()
            ->map(function ($text) {
                return [
                    'id' => $text->id,
                    'author' => $text->authorsToString(),
                    'title' => $text->title,
                    'lang' => $text->lang ? $text->lang->name : '',
                    'dialect' => $text->dialectsToArray(),
                    'trans_author' => $text->transtext ? $text->transtext->authorsToString() : '',
                    'trans_title' => $text->transtext ? $text->transtext->title : '',
                ];
            })
            ->values()
            ->all();

        return response()->json([
            'data' => $items,
            'current_page' => $texts->currentPage(),
            'last_page' => $texts->lastPage(),
            'per_page' => $texts->perPage(),
            'total' => $texts->total()
        ]);
    }

    public function show(int $id)
    {
        $text = Text::query()
            ->whereId($id)
            ->with([
                'lang',
                'authors',
                'corpuses'
            ])
            ->firstOrFail();

        return response()->json($this->textData($text));
    }

    protected function textData(Text $text): array
    {
        $lang_id = $text->lang_id;
        return [
            'id' => $text->id,
            'title' => $text->title,
            'lang' => optional($text->lang)->short,

            /*            'authors' => $text->authors
                ->map(function ($author) use ($lang_id) {
                    $name = $author->getNameByLang($lang_id);
                    return [
                        'id' => $author->id,
                        'name' => $name ? $name : $author->name,
                    ];
                })
                ->values()
                ->all(),

            'corpuses' => $text->corpuses
                ->map(function ($corpus) use ($lang_id) {
                    return [
                        'id' => $corpus->id,
                        'name' => $corpus->name,
                    ];
                })
                ->values()
                ->all(),*/
            'authors' => $text->authors
                //                ->pluck('name', 'id')
                ->mapWithKeys(function ($author) {
                    return [
                        $author->id => $author->name,
                    ];
                })
                ->all(),

            'corpuses' => $text->corpuses
                //                ->pluck('name', 'id')
                ->mapWithKeys(function ($corpus) {
                    return [
                        $corpus->id => $corpus->name,
                    ];
                })
                ->all(),

        ];
    }

    public function formValues()
    {
        return response()->json([
            'author_values' => [NULL => ''] + Author::getList(),
            'corpus_values' => Corpus::getList(),
            'dialect_values' => Dialect::getList(),
            'district_values' => District::getList(),
            'genre_values' => Genre::getList(),
            'informant_values' => [NULL => ''] + Informant::getList(),
            'lang_values' => Lang::getProjectList(),
            'place_values' => Place::getList(false),
            'plot_values' => Plot::getList(),
            'recorder_values' => [NULL => ''] + Recorder::getList(),
            'region_values' => [NULL => ''] + Region::getList(),
            'topic_values' => Topic::getList(),
        ]);
    }

    public function genres(Request $request)
    {
        $locale = app()->getLocale();

        $params = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'corpus_ids' =>  ['nullable', 'array'],
            'corpus_ids.*' => ['integer', 'min:1'],
        ]);

        $name = '%' . trim((string)($params['q'] ?? '')) . '%';
        $corpus_ids = array_remove_null($params['corpus_ids'] ?? []);

        $genres = Genre::query()
            ->when($name !== '', function ($query) use ($name) {
                $query->where('name_en',  'like',  $name)
                    ->orWhere('name_ru', 'like',  $name);
            })
            ->when(sizeof($corpus_ids), function ($query) use ($corpus_ids) {
                $query->whereIn('id', function ($q) use ($corpus_ids) {
                    $q->select('genre_id')->from('corpus_genre')
                        ->whereIn('corpus_id', $corpus_ids);
                });
            })
            ->orderBy('name_' . $locale)
            ->limit(50)
            ->get()
            ->map(function ($genre) {
                return [
                    'id' => $genre->id,
                    'text' => $genre->name,
                ];
            })
            ->values()
            ->all();

        return response()->json($genres);
    }
}
