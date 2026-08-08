<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
//use Illuminate\Support\Facades\Log;

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
    protected $biblicalCorpus = 2;
    protected $folkloreCorpus = 4;
    protected $monumentsCorpus = 12;
    protected $enabledCorpuses = [2, 4, 12, 15];


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
            'url_args' => $url_args,
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

        return response()->json(['data'=>$this->textData($text)]);
    }

    protected function textData(Text $text): array
    {
        $lang_id = $text->lang_id;
        $locale = app()->getLocale();
        $authors = [];
        foreach ($text->authors as $author) {
            $name = $author->getNameByLang($lang_id);
            $authors[$author->id] = $name ? $name : $author->name;
        }
        
        $informants = $recorders = [];
        $event_place = $event_date = '';
        if ($text->event) {
            $informants = $text->event->informants()->pluck('name_'.$locale,'id')->toArray();
            $recorders = $text->event->recorders()->pluck('name_'.$locale,'id')->toArray();
            $event_place = $text->event->place->placeString();
            $event_date = $text->event->date;
        }
        
        $source = [];
        if ($text->source) {
            $source['book'] = $text->source->bookToString();
            if ($text->source->ieeh_archive_number1) {
                $source['number'] = '№'.$text->source->ieeh_archive_number1;
                if ($text->source->ieeh_archive_number2) {
                    $source['number'] .= '/'.$text->source->ieeh_archive_number2;
                }
            }
            $source['comment'] = $text->source->comment;           
        }
        
        $cyrtext = $transtext = [];
        if ($text->cyrtext) {
            $cyrtext['title'] = $text->cyrtext->title;
            $cyrtext['text'] = $text->cyrtext->text_xml 
                ? str_replace("<s id=\"", "<s class=\"cyr_sentence\" id=\"cyrtext_s",
                        str_replace("<w id=\"","<w class=\"cyr_word\" id=\"cyr_w_", 
                            mb_ereg_replace('[¦^]', '', $text->cyrtext->text_xml)))
                : nl2br(mb_ereg_replace('[¦^]', '', $text->cyrtext->text));
        }
        if ($text->transtext) {
            $transtext['title'] = $text->transtext->title;
            $transtext['authors'] = $text->transtext->authorsToString();
            $transtext['lang'] = $text->transtext->lang ? $text->transtext->lang->short : null;
            $transtext['text'] = $text->transtext->text_xml 
                ? str_replace("<s id=\"","<s class=\"trans_sentence\" id=\"transtext_s", 
                        mb_ereg_replace('[¦^]', '', $text->transtext->text_xml)) 
                : nl2br(mb_ereg_replace('[¦^]', '', $text->transtext->text));
        }
        $original_text = str_replace("<s id=\"","<s class=\"sentence\" id=\"text_s", 
                        mb_ereg_replace('[¦^]', '', $text->textFromStructure()));
        
        $photos = [];
        foreach ($text->getMedia() as $photo) {
            $photos[] = [
                'src' => $photo->getUrl('thumb'),
                'big' => $photo->getUrl(''),
                'title' => $photo->name
            ];
        }
        
        $audiotexts = [];
        foreach ($text->audiotexts as $audiotext) {
            $audiotexts[] = $audiotext->url();
        }
        
        return [
            'id' => $text->id,
            'title' => $text->title,
            'lang' => $text->lang ? $text->lang->short : null,
            'dialects' => $text->dialects()->pluck('name_'.$locale,'id')->toArray(),
            'authors' => $authors,
            'corpuses' => $text->corpuses()->whereIn('id', $this->enabledCorpuses)->pluck('name_'.$locale,'id')->toArray(),
            'genres' => $text->genres()->pluck('name_'.$locale,'id')->toArray(),
            'plots' => $text->plots()->pluck('name_'.$locale,'id')->toArray(),
            'topics' => $text->topics()->pluck('name_'.$locale,'id')->toArray(),
            'cycles' => $text->cycles()->pluck('name_'.$locale,'id')->toArray(),
            'motives' => $text->motives()->pluck('name_'.$locale,'id')->toArray(),
            'event_place' => $event_place,
            'event_date' => $event_date,
            'informants' => $informants,
            'recorders' => $recorders,
            'source' => $source,
            'mentioned_places' => $text->placesToString(),
            'comment' => $text->comment,
            'text' => $original_text,
            'transtext' => $transtext,
            'cyrtext' => $cyrtext,
            'photos' => $photos,
            'audiotexts' => $audiotexts,
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
