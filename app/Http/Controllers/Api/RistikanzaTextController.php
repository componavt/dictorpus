<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Controller;
//use Illuminate\Support\Facades\Response;
//use Illuminate\Support\Facades\DB;

use App\Api\RistikanzaText;

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
    protected $bibleCorpus = 2;
    protected $folkloreCorpus = 4;
    protected $monumentsCorpus = 12;

    public function ethnographic(Request $request)
    {
        $url_args = Text::urlArgs($request);
        $url_args['search_corpus'] = [$this->ethnographicCorpus];

        return response()->json(RistikanzaText::getTexts($url_args));
    }

    public function folklore(Request $request)
    {
        $url_args = Text::urlArgs($request);
        $url_args['search_corpus'] = [$this->folkloreCorpus];

        return response()->json(RistikanzaText::getTexts($url_args));
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

        return response()->json(RistikanzaText::textData($text));
    }

    public function formValues(Request $request)
    {
        $corpus_id = (int)$request->input('corpus_id');
        $genre_id = (int)$request->input('genre_id');

        return response()->json([
            'author_values' => [NULL => ''] + Author::getList(),
            'corpus_values' => Corpus::getList(),
            'dialect_values' => Dialect::getList(),
            'district_values' => District::getList(),
            //'genre_values' => Genre::getList($corpusIds),
            'informant_values' => [NULL => ''] + Informant::getList(),
            'lang_values' => Lang::getProjectList(),
            'place_values' => Place::getList(false),
            'plot_values' => Plot::getList($genre_id, $corpus_id),
            'recorder_values' => [NULL => ''] + Recorder::getList(),
            'region_values' => [NULL => ''] + Region::getList(),
            'sort_values' => Text::sortList(),
            'topic_values' => Topic::getList($genre_id, $corpus_id),
        ]);
    }

    public function dialects(Request $request)
    {
        $locale = app()->getLocale();

        $langId = $request->input('lang_id');

        if ($langId !== null && !is_array($langId)) {
            $request->merge([
                'lang_id' => [$langId],
            ]);
        }

        $validator = Validator::make($request->all(), [
            'q' => 'sometimes|string|max:255',
            'lang_id' => 'sometimes|array',
            'lang_id.*' => 'integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $params = $request->only(['q', 'lang_id']);

        $q = trim((string) ($params['q'] ?? ''));
        $name = $q === '' ? null : '%' . $q . '%';

        $langIds = array_remove_null($params['lang_id'] ?? []);

        $query = Dialect::query();

        if ($name !== null) {
            $query->where(function ($query) use ($name) {
                $query->where('name_en', 'like', $name)
                    ->orWhere('name_ru', 'like', $name);
            });
        }

        if (sizeof($langIds)) {
            $query->whereIn('lang_id', $langIds);
        }

        $dialects = $query
            ->orderBy('name_' . $locale)
            ->limit(50)
            ->get()
            ->map(function ($dialect) {
                return [
                    'id' => $dialect->id,
                    'text' => $dialect->name,
                ];
            })
            ->values()
            ->all();

        return response()->json($dialects);
    }

    public function districts(Request $request)
    {
        $locale = app()->getLocale();

        $regionId = $request->input('region_id');

        if ($regionId !== null && !is_array($regionId)) {
            $request->merge([
                'region_id' => [$regionId],
            ]);
        }

        $validator = Validator::make($request->all(), [
            'q' => 'sometimes|string|max:255',
            'region_id' => 'sometimes|array',
            'region_id.*' => 'integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $params = $request->only(['q', 'region_id']);

        $q = trim((string) ($params['q'] ?? ''));
        $name = $q === '' ? null : '%' . $q . '%';

        $regionIds = array_remove_null($params['region_id'] ?? []);

        $query = District::query();

        if ($name !== null) {
            $query->where(function ($query) use ($name) {
                $query->where('name_en', 'like', $name)
                    ->orWhere('name_ru', 'like', $name);
            });
        }

        if (sizeof($regionIds)) {
            $query->whereIn('region_id', $regionIds);
        }

        $districts = $query
            ->orderBy('name_' . $locale)
            ->limit(50)
            ->get()
            ->map(function ($district) {
                return [
                    'id' => $district->id,
                    'text' => $district->name,
                ];
            })
            ->values()
            ->all();

        return response()->json($districts);
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

    public function places(Request $request)
    {
        $locale = app()->getLocale();

        $regionId = $request->input('region_id');
        $districtId = $request->input('district_id');

        if ($regionId !== null && !is_array($regionId)) {
            $request->merge([
                'region_id' => [$regionId],
            ]);
        }

        if ($districtId !== null && !is_array($districtId)) {
            $request->merge([
                'district_id' => [$districtId],
            ]);
        }

        $validator = Validator::make($request->all(), [
            'q' => 'sometimes|string|max:255',
            'region_id' => 'sometimes|array',
            'region_id.*' => 'integer|min:1',
            'district_id' => 'sometimes|array',
            'district_id.*' => 'integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $params = $request->only(['q', 'region_id', 'district_id']);

        $q = trim((string) ($params['q'] ?? ''));
        $name = $q === '' ? null : '%' . $q . '%';

        $regionIds = array_remove_null($params['region_id'] ?? []);
        $districtIds = array_remove_null($params['district_id'] ?? []);

        $query = Place::query();

        if ($name !== null) {
            $query->where(function ($query) use ($name) {
                $query->where('name_en', 'like', $name)
                    ->orWhere('name_ru', 'like', $name);
            });
        }

        if (sizeof($regionIds)) {
            $query->whereIn('region_id', $regionIds);
        }

        if (sizeof($districtIds)) {
            $query->whereIn('district_id', $districtIds);
        }

        /*Log::debug('Ristikanza API places', [
            'sql' => to_sql($query),
        ]);*/

        $places = $query
            ->orderBy('name_' . $locale)
            ->limit(50)
            ->get()
            ->map(function ($place) {
                return [
                    'id' => $place->id,
                    'text' => $place->name,
                ];
            })
            ->values()
            ->all();

        return response()->json($places);
    }

    public function topics(Request $request)
    {
        $corpusId = $request->input('corpus_id');
        if ($corpusId !== null && !is_array($corpusId)) {
            $request->merge([
                'corpus_id' => [$corpusId],
            ]);
        }

        $genreId = $request->input('genre_id');
        if ($genreId !== null && !is_array($genreId)) {
            $request->merge([
                'genre_id' => [$genreId],
            ]);
        }

        $plotId = $request->input('plot_id');
        if ($plotId !== null && !is_array($plotId)) {
            $request->merge([
                'plot_id' => [$plotId],
            ]);
        }

        $validator = Validator::make($request->all(), [
            'q' => 'sometimes|string|max:255',
            'corpus_id' => 'sometimes|array',
            'corpus_id.*' => 'integer|min:1',
            'genre_id' => 'sometimes|array',
            'genre_id.*' => 'integer|min:1',
            'plot_id' => 'sometimes|array',
            'plot_id.*' => 'integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $params = $request->only(['q', 'corpus_id', 'genre_id']);

        $q = trim((string) ($params['q'] ?? ''));
        $name = $q === '' ? null : '%' . $q . '%';

        $corpusIds = array_remove_null($params['corpus_id'] ?? []);
        $plotIds = array_remove_null($params['plot_id'] ?? []);
        $genreIds = array_remove_null($params['genre_id'] ?? []);

        $query = Topic::query();

        if ($name !== null) {
            $query->where(function ($query) use ($name) {
                $query->where('name_en', 'like', $name)
                    ->orWhere('name_ru', 'like', $name);
            });
        }

        if (sizeof($corpusIds) || sizeof($genreIds) || sizeof($plotIds)) {
            $query->whereIn('id', function ($q) use ($corpusIds, $genreIds, $plotIds) {
                $q->select('topic_id')->from('plot_topic');
                if (sizeof($plotIds)) {
                    $q->whereIn('plot_id', $plotIds);
                }
                if (sizeof($corpusIds)) {
                    $q->whereIn('plot_id', function ($q1) use ($corpusIds, $genreIds) {
                        $q1->select('id')->from('plots')
                            ->whereIn('genre_id', function ($q2) use ($corpusIds, $genreIds) {
                                $q2->select('id')->from('genres');
                                if (sizeof($genreIds)) {
                                    $q2->whereIn('id', $genreIds);
                                }
                                if (sizeof($corpusIds)) {
                                    $q2->whereIn('corpus_id', $corpusIds);
                                }
                            });
                    });
                }
            });
        }

        $topics = $query
            ->orderBy('sequence_number')
            ->limit(50)
            ->get()
            ->map(function ($topic) {
                return [
                    'id' => $topic->id,
                    'text' => $topic->name,
                ];
            })
            ->values()
            ->all();

        return response()->json($topics);
    }

    public function plots(Request $request)
    {
        $corpusId = $request->input('corpus_id');
        if ($corpusId !== null && !is_array($corpusId)) {
            $request->merge([
                'corpus_id' => [$corpusId],
            ]);
        }

        $genreId = $request->input('genre_id');
        if ($genreId !== null && !is_array($genreId)) {
            $request->merge([
                'genre_id' => [$genreId],
            ]);
        }

        $validator = Validator::make($request->all(), [
            'q' => 'sometimes|string|max:255',
            'corpus_id' => 'sometimes|array',
            'corpus_id.*' => 'integer|min:1',
            'genre_id' => 'sometimes|array',
            'genre_id.*' => 'integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $params = $request->only(['q', 'corpus_id', 'genre_id']);

        $q = trim((string) ($params['q'] ?? ''));
        $name = $q === '' ? null : '%' . $q . '%';

        $corpusIds = array_remove_null($params['corpus_id'] ?? []);
        $genreIds = array_remove_null($params['genre_id'] ?? []);

        $query = Plot::query();

        if ($name !== null) {
            $query->where(function ($query) use ($name) {
                $query->where('name_en', 'like', $name)
                    ->orWhere('name_ru', 'like', $name);
            });
        }

        if (sizeof($corpusIds) || sizeof($genreIds)) {
            $query->whereIn('genre_id', function ($q2) use ($corpusIds, $genreIds) {
                $q2->select('id')->from('genres');
                if (sizeof($genreIds)) {
                    $q2->whereIn('id', $genreIds);
                }
                if (sizeof($corpusIds)) {
                    $q2->whereIn('corpus_id', $corpusIds);
                }
            });
        }
        Log::debug('Ristikanza API locale', [
            'genre_id' => $request->input('genre_id'),
            'params' => $params,
            'genreIds' => $genreIds,
            'sql' => to_sql($query),

        ]);
        $plots = $query
            ->orderBy('sequence_number')
            ->limit(50)
            ->get()
            ->map(function ($plot) {
                return [
                    'id' => $plot->id,
                    'text' => $plot->name,
                ];
            })
            ->values()
            ->all();

        return response()->json($plots);
    }

    public function folkloreGenres()
    {
        $locale = app()->getLocale();

        $parent_genres = [19, 52, 93];
        $genres = [];

        foreach ($parent_genres as $parent_id) {
            $parent = Genre::find($parent_id);
            if (!$parent) {
                continue;
            }
            $genres[$parent_id]['name'] = $parent->{'name_pl_' . $locale};

            $res = Genre::whereParentId($parent_id)->orderBy('sequence_number');
            $genres[$parent_id]['genres'] = $res->count() ? $res->pluck('name_pl_' . $locale, 'id')->toArray() : [];
        }

        return response()->json($genres);
    }
}
