<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
//use Illuminate\Support\Facades\Log;

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
            'sort_values' => Text::sortList(),
            'topic_values' => Topic::getList(),
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
        $locale = app()->getLocale();

        $corpusId = $request->input('corpus_id');

        if ($corpusId !== null && !is_array($corpusId)) {
            $request->merge([
                'corpus_id' => [$corpusId],
            ]);
        }

        $validator = Validator::make($request->all(), [
            'q' => 'sometimes|string|max:255',
            'corpus_id' => 'sometimes|array',
            'corpus_id.*' => 'integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $params = $request->only(['q', 'corpus_id']);

        $q = trim((string) ($params['q'] ?? ''));
        $name = $q === '' ? null : '%' . $q . '%';

        $corpusIds = array_remove_null($params['corpus_id'] ?? []);

        $query = Topic::query();

        if ($name !== null) {
            $query->where(function ($query) use ($name) {
                $query->where('name_en', 'like', $name)
                    ->orWhere('name_ru', 'like', $name);
            });
        }

        if (sizeof($corpusIds)) {
            $query->whereIn('id', function ($q) use ($corpusIds) {
                $q->select('topic_id')->from('plot_topic')
                    ->whereIn('plot_id', function ($q1) use ($corpusIds) {
                        $q1->select('id')->from('plots')
                            ->whereIn('genre_id', function ($q2) use ($corpusIds) {
                                $q2->select('id')->from('genres')
                                    ->whereIn('corpus_id', $corpusIds);
                            });
                    });
            });
        }

        $topics = $query
            ->orderBy('name_' . $locale)
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
}
