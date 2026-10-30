<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use App\Library\MeaningExampleFilterInput;
use App\Library\MeaningExampleFilterValues;
use App\Library\MeaningExampleFormatter;
use App\Library\MeaningExampleWordData;
use App\Models\User;
use App\Models\Corpus\MeaningTextRel;
use App\Models\Corpus\SentenceFragment;
use App\Models\Corpus\TextWordform;
use App\Models\Corpus\Word;
use App\Models\Dict\Gramset;
use App\Models\Dict\Meaning;
use App\Models\Dict\Wordform;

use App\Library\MeaningExampleSearch;

class MeaningExampleController extends Controller
{
    protected $review_context = [];
    protected $synset_id = null;
    protected $examples_return = '';
    protected $recorded_partner_ids = [];

    /**
     * Показывает найденные предложения без изменения корпусных связей.
     */
    public function index($meaning_id, Request $request)
    {
        $source = Meaning::with([
            'lemma.lang',
            'lemma.pos',
            'meaningTexts.lang',
        ])->findOrFail($meaning_id);

        $filter_values = (new MeaningExampleFilterValues())->load($source);
        $extra_filters = (new MeaningExampleFilterInput())->read($request->all(), $filter_values);
        $selected_filters = $extra_filters['options'] ?? ['source_gramset_ids' => [], 'corpus_ids' => []];

        $synset_id = $this->exampleSynsetId($request, $source);
        $this->synset_id = $synset_id;

        $input = [
            'distance_from' => $request->input('distance_from', -1),
            'distance_to' => $request->input('distance_to', 1),
            'partner_meaning_id' => $request->input('partner_meaning_id', ''),
            'only_checked' => $request->input('only_checked', 0),
            'same_gramset' => $request->input('same_gramset', 1),
            'source_only_checked' => $request->input('source_only_checked', $request->input('only_checked', 0)),
            'per_page' => $request->input('per_page', 20),
            'page' => $request->input('page', 1),
            'whole_sentence' => $request->input('whole_sentence', 0),
        ];

        $validator = Validator::make(
            $input,
            [
                'distance_from' => 'required|integer',
                'distance_to' => 'required|integer',
                'partner_meaning_id' => 'integer|min:1',
                'only_checked' => 'required|boolean',
                'same_gramset' => 'required|boolean',
                'source_only_checked' => 'required|boolean',
                'per_page' => 'required|integer|min:1|max:100',
                'page' => 'required|integer|min:1|max:1000000',
                'whole_sentence' => 'required|boolean',
            ],
            [],
            [
                'distance_from' => trans('meaning_examples.distance_from'),
                'distance_to' => trans('meaning_examples.distance_to'),
                'partner_meaning_id' => trans('meaning_examples.partner'),
                'only_checked' => trans('meaning_examples.only_checked'),
                'same_gramset' => trans('meaning_examples.same_gramset'),
                'per_page' => trans('meaning_examples.per_page'),
                'page' => trans('meaning_examples.page'),
            ]
        );

        $partner = null;
        $sentences = null;

        if (
            !$validator->fails() && $input['partner_meaning_id'] !== '' && $input['partner_meaning_id'] !== null
        ) {
            $partner = Meaning::with(['lemma.pos', 'meaningTexts.lang'])->find((int) $input['partner_meaning_id']);

            if (!$partner) {
                $validator->errors()->add('partner_meaning_id', trans('meaning_examples.invalid_partner'));
            } elseif ((int) $partner->lemma->lang_id !== (int) $source->lemma->lang_id) {
                $validator->errors()->add(
                    'partner_meaning_id',
                    trans('meaning_examples.different_language')
                );
            }
        }

        if ($validator->errors()->count() === 0 && (int) $input['distance_from'] > (int) $input['distance_to']) {
            $validator->errors()->add('distance_from', trans('meaning_examples.invalid_distance'));
        }

        $search_errors = $validator->errors();

        foreach ($extra_filters['errors']->getMessages() as $field => $messages) {
            foreach ($messages as $message) {
                $search_errors->add($field, $message);
            }
        }

        if ($search_errors->count() === 0) {
            $threshold = (int) $input['only_checked'];

            $search = new MeaningExampleSearch(
                $source,
                $partner,
                [
                    'corpus_ids' => $selected_filters['corpus_ids'],
                    'distance_from' => (int) $input['distance_from'],
                    'distance_to' => (int) $input['distance_to'],
                    'partner_threshold' => $threshold,
                    'same_gramset' => (bool) (int) $input['same_gramset'],
                    'source_gramset_ids' => $selected_filters['source_gramset_ids'],
                    'source_threshold' => (int) $input['source_only_checked'],
                    'whole_sentence' => (bool) (int) $input['whole_sentence'],
                ]
            );

            $sentences = $search->paginateSentences((int) $input['per_page'], (int) $input['page']);

            $sentences->appends([
                'distance_from' => (int) $input['distance_from'],
                'distance_to' => (int) $input['distance_to'],
                'partner_meaning_id' => $partner ? $partner->id : '',
                'only_checked' => $threshold,
                'same_gramset' => (int) $input['same_gramset'],
                'source_only_checked' => (int) $input['source_only_checked'],
                'per_page' => (int) $input['per_page'],
                'synset_id' => $synset_id,
                'whole_sentence' => (int) $input['whole_sentence'],
                'source_gramset_ids' => $selected_filters['source_gramset_ids'],
                'corpus_ids' => $selected_filters['corpus_ids'],
            ]);
        }

        $only_checked = !empty($input['only_checked']);
        $same_gramset = !empty($input['same_gramset']);
        $source_only_checked = !empty($input['source_only_checked']);
        $whole_sentence = !empty($input['whole_sentence']);

        $per_page = $sentences ? $sentences->perPage() : 20;

        $distance_from = is_scalar($input['distance_from']) ? (string) $input['distance_from'] : '';
        $distance_to = is_scalar($input['distance_to']) ? (string) $input['distance_to'] : '';

        $partner_text = $partner ? $this->partnerOptionText($partner) : '';

        $lemma = $source->lemma;
        $meaning_labels = $source->showShortLabels();
        $back_to_url = LaravelLocalization::localizeURL('/dict/lemma/' . $lemma->id) . '#meaning-' . $source->id;

        $examples_return = '';

        if ($sentences) {
            $examples_return = '/dict/meaning/' . $source->id . '/examples/search?'
                . http_build_query([
                    'distance_from' => (int) $input['distance_from'],
                    'distance_to' => (int) $input['distance_to'],
                    'only_checked' => (int) $input['only_checked'],
                    'page' => $sentences->currentPage(),
                    'partner_meaning_id' => $partner ? $partner->id : '',
                    'per_page' => $sentences->perPage(),
                    'same_gramset' => (int) $input['same_gramset'],
                    'source_only_checked' => (int) $input['source_only_checked'],
                    'synset_id' => $synset_id,
                    'whole_sentence' => (int) $input['whole_sentence'],
                    'source_gramset_ids' => $selected_filters['source_gramset_ids'],
                    'corpus_ids' => $selected_filters['corpus_ids'],
                ]);
        }

        $this->examples_return = $examples_return;
        $word_blocks = [];

        if ($sentences && count($sentences)) {
            $word_blocks = $this->preloadWordBlocks(
                $source,
                $sentences->items(),
                [
                    'distance_from' => (int) $input['distance_from'],
                    'distance_to' => (int) $input['distance_to'],
                ]
            );
        }

        return view(
            'dict.meaning.examples.search',
            compact(
                'back_to_url',
                'distance_from',
                'distance_to',
                'examples_return',
                'lemma',
                'meaning_labels',
                'only_checked',
                'partner',
                'partner_text',
                'per_page',
                'same_gramset',
                'search_errors',
                'sentences',
                'source',
                'source_only_checked',
                'synset_id',
                'whole_sentence',
                'word_blocks',
                'filter_values',
                'selected_filters',
            )
        );
    }

    /**
     * Возвращает блок проверки конкретного употребления.
     */
    public function wordBlock($meaning_id, $text_id, $w_id, Request $request)
    {
        list($source, $word, $interval) = $this->reviewWord($meaning_id, $text_id, $w_id, $request);

        return $this->renderWordBlock($source, $word, $interval);
    }

    /**
     * Подтверждает неподтверждённое значение: source получает 7, остальные 5.
     */
    public function confirmMeaning($meaning_id, $text_id, $w_id, Request $request)
    {
        if (!User::checkAccess('corpus.edit')) {
            abort(403);
        }

        $this->validate($request, [
            'meaning_id' => 'required|integer|min:1',
        ]);

        list($source, $word, $interval) = $this->reviewWord($meaning_id, $text_id, $w_id, $request);
        $this->assertReviewWritable();
        $selected_id = (int) $request->input('meaning_id');

        DB::transaction(function () use ($source, $word, $selected_id) {
            Word::where('id', $word->id)->lockForUpdate()->first();

            $links = DB::table('meaning_text')
                ->where('text_id', $word->text_id)
                ->where('w_id', $word->w_id)
                ->orderBy('meaning_id')
                ->lockForUpdate()
                ->get();

            $available = false;

            foreach ($links as $link) {
                if ((int) $link->relevance > 1) {
                    return;
                }

                if (
                    (int) $link->meaning_id === $selected_id
                    && (int) $link->relevance === 1
                ) {
                    $available = true;
                }
            }

            if (!$available) {
                abort(409);
            }

            $relevance = 5;

            MeaningTextRel::updateExample(
                $relevance,
                $selected_id,
                (int) $word->text_id,
                (int) $word->w_id
            );
        });

        return $this->renderWordBlock($source, $word, $interval);
    }

    /**
     * Подтверждает существующую неподтверждённую привязку грамсета.
     */
    public function confirmGramset($meaning_id, $text_id, $w_id, Request $request)
    {
        if (!User::checkAccess('corpus.edit')) {
            abort(403);
        }

        $this->validate($request, [
            'gramset_id' => 'required|integer|min:1',
            'wordform_id' => 'required|integer|min:1',
        ]);

        list($source, $word, $interval) = $this->reviewWord($meaning_id, $text_id, $w_id, $request);
        $this->assertReviewWritable();

        $gramset_id = (int) $request->input('gramset_id');
        $wordform_id = (int) $request->input('wordform_id');

        DB::transaction(function () use ($word, $gramset_id, $wordform_id) {
            Word::where('id', $word->id)->lockForUpdate()->first();

            $checked_meaning_id = DB::table('meaning_text')
                ->where('text_id', $word->text_id)
                ->where('w_id', $word->w_id)
                ->where('relevance', '>', 1)
                ->value('meaning_id');

            if ($checked_meaning_id) {
                TextWordform::updateWordformLinksAfterCheckExample((int) $word->text_id, (int) $word->w_id, (int) $checked_meaning_id);
            }

            $links = DB::table('text_wordform')
                ->where('text_id', $word->text_id)
                ->where('w_id', $word->w_id)
                ->orderBy('wordform_id')
                ->orderBy('gramset_id')
                ->lockForUpdate()
                ->get();

            $available = false;

            foreach ($links as $link) {
                if ((int) $link->relevance > 1) {
                    return;
                }

                if (
                    (int) $link->wordform_id === $wordform_id
                    && (int) $link->gramset_id === $gramset_id
                    && (int) $link->relevance === 1
                ) {
                    $available = true;
                }
            }

            if (!$available) {
                abort(409);
            }

            Wordform::findOrFail($wordform_id)
                ->updateTextWordformLinks(
                    (int) $word->text_id,
                    (int) $word->w_id,
                    $gramset_id
                );
        });

        return $this->renderWordBlock($source, $word, $interval);
    }

    /**
     * Проверяет интервал, загружает источник и употребление того же языка.
     */
    protected function reviewWord($meaning_id, $text_id, $w_id, Request $request)
    {
        $this->validate($request, ['examples_return' => 'string|max:4096']);
        $this->examples_return = (string) $request->input('examples_return', '');

        $interval = [
            'distance_from' => $request->input('distance_from', -1),
            'distance_to' => $request->input('distance_to', 1),
            'whole_sentence' => $request->input('whole_sentence', 0),
        ];

        $validator = Validator::make(
            $interval,
            [
                'distance_from' => 'required|integer',
                'distance_to' => 'required|integer',
                'whole_sentence' => 'required|boolean',
            ],
            [],
            [
                'distance_from' => trans('meaning_examples.distance_from'),
                'distance_to' => trans('meaning_examples.distance_to'),
            ]
        );

        if ($validator->fails()) {
            $this->throwValidationException($request, $validator);
        }

        $interval = array_map('intval', $interval);

        if ($interval['distance_from'] > $interval['distance_to']) {
            abort(422, trans('meaning_examples.invalid_distance'));
        }

        $source = Meaning::with('lemma')->findOrFail($meaning_id);
        $this->synset_id = $this->exampleSynsetId($request, $source);

        $word = Word::with('text')
            ->where('text_id', $text_id)
            ->where('w_id', $w_id)
            ->firstOrFail();

        if (
            !$word->text
            || (int) $word->text->lang_id !== (int) $source->lemma->lang_id
        ) {
            abort(404);
        }

        $this->review_context = $this->reviewContext(
            $source,
            $word,
            $request
        );

        return [$source, $word, $interval];
    }

    /**
     * Собирает блок из текущих смысловых и морфологических привязок.
     */
    protected function renderWordBlock(Meaning $source, Word $word, array $interval, array $prepared = [])
    {
        $data = isset($prepared['data'])
            ? $prepared['data']
            : (new MeaningExampleWordData())->load(
                (int) $word->text_id,
                [(int) $word->w_id]
            );

        $review_context = isset($prepared['context'])
            ? $prepared['context']
            : $this->review_context;

        $word_meanings = $data['meanings'][$word->w_id] ?? [];
        $word_gramsets = $data['gramsets'][$word->w_id] ?? [];
        $wordforms = $data['wordforms'];

        $meaning_checked = !empty($word_meanings['checked']);
        $gramset_checked = !empty($word_gramsets['checked']);

        $recorded_partner_ids = $this->recordedPartnerIds($source);

        $collocation_recorded = $meaning_checked && isset($recorded_partner_ids[(int) $word_meanings['checked']->id]);

        $meaning_values = $meaning_checked
            ? [$word_meanings['checked']]
            : ($word_meanings['unchecked'] ?? []);

        $gramset_values = $gramset_checked
            ? [$word_gramsets['checked']]
            : ($word_gramsets['unchecked'] ?? []);

        $has_gramsets = count($gramset_values) > 0;

        $fully_checked = $meaning_checked
            && ($gramset_checked || !$has_gramsets);

        $read_only = !empty($review_context['read_only']);
        $can_edit = User::checkAccess('corpus.edit') && !$read_only;

        $can_reset = $can_edit && (!$meaning_checked || (int) $word_meanings['checked']->relevance !== 10);

        $source_relevance = array_key_exists('source_relevance', $prepared)
            ? (int) $prepared['source_relevance']
            : (int) DB::table('meaning_text')
                ->where('meaning_id', $source->id)
                ->where('text_id', $word->text_id)
                ->where('w_id', $review_context['source_w_id'])
                ->value('relevance');

        $examples_return = '';

        if ($this->examples_return !== '') {
            $return_parts = explode('#', $this->examples_return, 2);
            $path_parts = explode('?', $return_parts[0], 2);

            $return_query = [];
            parse_str($path_parts[1] ?? '', $return_query);

            if (!empty($review_context['original'])) {
                $return_query['open_original'] = 1;
            } else {
                unset($return_query['open_original']);
            }

            $examples_return = $path_parts[0]
                . ($return_query ? '?' . http_build_query($return_query) : '')
                . '#sentence-' . $source->id
                . '_' . $word->text_id
                . '_' . $word->s_id
                . '_' . $review_context['source_w_id'];
        }

        $add_collocation_url = $read_only ? null
            : $this->collocationEditorUrl(
                $source,
                $word,
                $word_meanings['checked'] ?? null,
                $interval,
                $examples_return
            );

        $delete_collocation_url = null;

        if ($collocation_recorded && !$read_only && User::checkAccess('dict.edit')) {
            $partner_id = (int) $word_meanings['checked']->id;
            $collocation_id = $recorded_partner_ids[$partner_id];

            $query = ['edit' => $collocation_id];

            if ($this->synset_id !== null) {
                $query['synset_id'] = $this->synset_id;
            }

            if ($examples_return !== '') {
                $query['examples_return'] = $examples_return;
            }

            $delete_collocation_url = LaravelLocalization::localizeURL('/dict/meaning/' . $source->id . '/collocations')
                . '?' . http_build_query($query) . '#collocation-' . $collocation_id;
        }

        return view(
            'dict.meaning.examples._word_block',
            compact(
                'add_collocation_url',
                'can_edit',
                'can_reset',
                'collocation_recorded',
                'delete_collocation_url',
                'fully_checked',
                'gramset_checked',
                'gramset_values',
                'meaning_checked',
                'meaning_values',
                'read_only',
                'source',
                'source_relevance',
                'word',
                'wordforms',
            )
        );
    }

    /**
     * Сбрасывает выбор значения и грамсета, кроме примеров с оценкой 10.
     */
    public function resetWord($meaning_id, $text_id, $w_id, Request $request)
    {
        if (!User::checkAccess('corpus.edit')) {
            abort(403);
        }

        list($source, $word, $interval) = $this->reviewWord($meaning_id, $text_id, $w_id, $request);
        $this->assertReviewWritable();
        DB::transaction(function () use ($word) {
            Word::where('id', $word->id)->lockForUpdate()->first();

            $links = DB::table('meaning_text')
                ->where('text_id', $word->text_id)
                ->where('w_id', $word->w_id)
                ->orderBy('meaning_id')
                ->lockForUpdate()
                ->get();

            foreach ($links as $link) {
                if ((int) $link->relevance === 10) {
                    abort(403);
                }
            }

            $word->updateMeaningAndWordformText(true);
        });

        return $this->renderWordBlock($source, $word, $interval);
    }

    /**
     * Возвращает страницу значений соседних слов с учётом фильтров формы.
     */
    public function partners($meaning_id, Request $request)
    {
        $input = $this->partnerSearchInput($request);

        $this->validate($request, ['source_only_checked' => 'boolean']);
        $source_threshold = (int) $request->input('source_only_checked', $input['only_checked']);

        $source = Meaning::with('lemma')->findOrFail($meaning_id);

        $filter_values = (new MeaningExampleFilterValues())->load($source);
        $extra_filters = (new MeaningExampleFilterInput())->read($request->all(), $filter_values);

        if ($extra_filters['errors']->count()) {
            abort(422, $extra_filters['errors']->first());
        }

        $search = new MeaningExampleSearch(
            $source,
            null,
            [
                'distance_from' => $input['distance_from'],
                'distance_to' => $input['distance_to'],
                'partner_threshold' => $input['only_checked'],
                'same_gramset' => (bool) $input['same_gramset'],
                'source_gramset_ids' => $extra_filters['options']['source_gramset_ids'],
                'corpus_ids' => $extra_filters['options']['corpus_ids'],
                'source_threshold' => $source_threshold,
                'whole_sentence' => (bool) (int) $input['whole_sentence'],
            ]
        );

        $query = $search->partnerMeaningsQuery();

        if ($input['q'] !== '') {
            $query->where('l2.lemma', 'like', '%' . $input['q'] . '%')
                ->orderByRaw('CASE WHEN l2.lemma = ? THEN 0 WHEN l2.lemma LIKE ? THEN 1 ELSE 2 END', [$input['q'], $input['q'] . '%']);
        }

        $page_size = 25;

        $rows = $query
            ->orderBy('l2.lemma')
            ->orderBy('l2.id')
            ->orderBy('m2.meaning_n')
            ->orderBy('m2.id')
            ->skip(($input['page'] - 1) * $page_size)
            ->take($page_size + 1)
            ->get();

        return response()->json([
            'results' => $this->partnerSearchResults($rows, $page_size),
            'pagination' => [
                'more' => count($rows) > $page_size,
            ],
        ]);
    }

    /**
     * Формирует подпись значения из предварительно загруженных связей.
     */
    protected function partnerOptionText(Meaning $meaning)
    {
        $locale = LaravelLocalization::getCurrentLocale();

        $text = $meaning->lemma->lemma
            . ' [' . $meaning->meaning_n . ']';

        if ($meaning->lemma->pos) {
            $text .= ' (' . $meaning->lemma->pos->name . ')';
        }

        $meaning_texts = $meaning->meaningTexts;

        $preferred_texts = $meaning_texts->filter(
            function ($meaning_text) use ($locale) {
                return $meaning_text->lang
                    && $meaning_text->lang->code === $locale;
            }
        );

        if ($preferred_texts->count()) {
            $meaning_texts = $preferred_texts;
        }

        $glosses = [];

        foreach ($meaning_texts as $meaning_text) {
            if (!$meaning_text->meaning_text) {
                continue;
            }

            $gloss = $meaning_text->meaning_text;

            if (
                $meaning_text->lang
                && $meaning_text->lang->code !== $locale
            ) {
                $gloss = $meaning_text->lang->code . ': ' . $gloss;
            }

            $glosses[] = $gloss;
        }

        if ($glosses) {
            $text .= ' — ' . implode(', ', $glosses);
        }

        return $text;
    }

    /**
     * Возвращает адрес добавления отсутствующей пары с проверенным партнёром.
     *
     * @return string|null
     */
    protected function collocationEditorUrl(Meaning $source, Word $word, ?Meaning $partner, array $interval, $examples_return = '')
    {
        if (!User::checkAccess('dict.edit') || !$partner || (int) $partner->relevance <= 1) {
            return null;
        }

        if ((int) $partner->lemma->lang_id !== (int) $source->lemma->lang_id) {
            return null;
        }

        $source_count = DB::table('meaning_text as mt')
            ->join('words as sw', function ($join) {
                $join->on('sw.id', '=', 'mt.word_id')
                    ->on('sw.text_id', '=', 'mt.text_id')
                    ->on('sw.s_id', '=', 'mt.s_id')
                    ->on('sw.w_id', '=', 'mt.w_id');
            })
            ->where('mt.meaning_id', $source->id)
            ->where('mt.relevance', '>', 0)
            ->where('sw.text_id', $word->text_id)
            ->where('sw.s_id', $word->s_id)
            ->where('sw.id', '<>', $word->id)
            ->count();

        if (!$source_count) {
            return null;
        }

        $existing_count = $source->collocations()
            ->where('collocate_meaning_id', $partner->id)
            ->count();

        if ($existing_count) {
            return null;
        }

        $query = ['partner_id' => $partner->id];

        if ($this->synset_id !== null) {
            $query['synset_id'] = $this->synset_id;
        }

        if ($examples_return !== '') {
            $query['examples_return'] = $examples_return;
        }

        return LaravelLocalization::localizeURL('/dict/meaning/' . $source->id . '/collocations') . '?' . http_build_query($query);
    }

    /**
     * Проверяет исходное употребление и ограничения показанного фрагмента.
     */
    protected function reviewContext(Meaning $source, Word $word, Request $request)
    {
        $this->validate($request, [
            'fragment_id' => 'required|integer|min:0',
            'original' => 'boolean',
            'source_w_id' => 'required|integer|min:1',
        ]);

        $original = (bool) (int) $request->input('original', 0);

        if ($original && !User::checkAccess('dict.edit') && !User::checkAccess('corpus.edit')) {
            abort(403);
        }
        $source_w_id = (int) $request->input('source_w_id');

        $source_word = Word::where('text_id', $word->text_id)
            ->where('s_id', $word->s_id)
            ->where('w_id', $source_w_id)
            ->firstOrFail();

        $source_link = DB::table('meaning_text')
            ->where('meaning_id', $source->id)
            ->where('word_id', $source_word->id)
            ->where('text_id', $source_word->text_id)
            ->where('s_id', $source_word->s_id)
            ->where('w_id', $source_word->w_id)
            ->first();

        if (!$source_link) {
            abort(404);
        }

        $fragment = SentenceFragment::where('sentence_id', $source_word->sentence_id)
            ->where('w_id', $source_w_id)
            ->orderBy('id')
            ->first();

        $is_fragment = false;

        if ($fragment && trim((string) $fragment->text_xml) !== '') {
            $original_xml = DB::table('sentences')
                ->where('id', $source_word->sentence_id)
                ->value('text_xml');

            if ($original_xml === null) {
                abort(404);
            }

            $is_fragment = SentenceFragment::differsFromOriginal(
                $fragment->text_xml,
                $original_xml
            );
        }

        $fragment_id = $is_fragment && !$original ? (int) $fragment->id : 0;

        if ($fragment_id !== (int) $request->input('fragment_id')) {
            abort(409);
        }

        return [
            'source_w_id' => $source_w_id,
            'fragment_id' => $fragment_id,
            'original' => $original,
            'read_only' => $is_fragment && !$original
                && (int) $word->w_id !== $source_w_id,
        ];
    }

    /**
     * Запрещает изменение остальных слов показанного фрагмента.
     */
    protected function assertReviewWritable()
    {
        if (!empty($this->review_context['read_only'])) {
            abort(403);
        }
    }

    /**
     * Записывает оценку исходного значения для конкретного употребления.
     */
    public function rateExample(
        $meaning_id,
        $text_id,
        $w_id,
        Request $request
    ) {
        if (!User::checkAccess('dict.edit')) {
            abort(403);
        }

        $this->validate($request, [
            'relevance' => 'required|integer|in:0,5,7,10',
        ]);

        list($source, $word, $interval) = $this->reviewWord(
            $meaning_id,
            $text_id,
            $w_id,
            $request
        );

        if ((int) $word->w_id !== $this->review_context['source_w_id']) {
            abort(403);
        }

        $relevance = (int) $request->input('relevance');

        DB::transaction(function () use ($source, $word, $relevance) {
            Word::where('id', $word->id)->lockForUpdate()->first();

            $link = DB::table('meaning_text')
                ->where('meaning_id', $source->id)
                ->where('text_id', $word->text_id)
                ->where('s_id', $word->s_id)
                ->where('w_id', $word->w_id)
                ->lockForUpdate()
                ->first();

            if (!$link) {
                abort(404);
            }

            MeaningTextRel::updateExample(
                $relevance,
                (int) $source->id,
                (int) $word->text_id,
                (int) $word->w_id
            );
        });

        return response()->json([
            'relevance' => $relevance,
        ]);
    }

    /**
     * Проверяет и нормализует параметры подбора соседних значений.
     */
    protected function partnerSearchInput(Request $request)
    {
        $input = [
            'distance_from' => $request->input('distance_from', -1),
            'distance_to' => $request->input('distance_to', 1),
            'only_checked' => $request->input('only_checked', 0),
            'page' => $request->input('page', 1),
            'q' => $request->input('q', ''),
            'same_gramset' => $request->input('same_gramset', 1),
            'whole_sentence' => $request->input('whole_sentence', 0),
        ];

        $validator = Validator::make(
            $input,
            [
                'distance_from' => 'required|integer',
                'distance_to' => 'required|integer',
                'only_checked' => 'required|boolean',
                'page' => 'required|integer|min:1|max:10000',
                'q' => 'string|max:100',
                'same_gramset' => 'required|boolean',
                'whole_sentence' => 'required|boolean',
            ],
            [],
            [
                'distance_from' => trans('meaning_examples.distance_from'),
                'distance_to' => trans('meaning_examples.distance_to'),
                'only_checked' => trans('meaning_examples.only_checked'),
                'page' => trans('meaning_examples.page'),
                'q' => trans('meaning_examples.partner'),
                'same_gramset' => trans('meaning_examples.same_gramset'),
            ]
        );

        if ($validator->fails()) {
            $this->throwValidationException($request, $validator);
        }

        foreach (
            [
                'distance_from',
                'distance_to',
                'only_checked',
                'page',
                'same_gramset',
                'whole_sentence',
            ] as $field
        ) {
            $input[$field] = (int) $input[$field];
        }

        $input['q'] = trim((string) $input['q']);

        if ($input['distance_from'] > $input['distance_to']) {
            $validator->errors()->add(
                'distance_from',
                trans('meaning_examples.invalid_distance')
            );

            $this->throwValidationException($request, $validator);
        }

        return $input;
    }

    /**
     * Загружает сведения только для возвращаемой страницы кандидатов.
     */
    protected function partnerSearchResults($rows, $limit)
    {
        $meaning_ids = [];

        foreach ($rows as $row) {
            if (count($meaning_ids) === $limit) {
                break;
            }

            $meaning_ids[] = (int) $row->meaning_id;
        }

        if (!$meaning_ids) {
            return [];
        }

        $meanings = Meaning::whereIn('id', $meaning_ids)
            ->with([
                'lemma.pos',
                'meaningTexts.lang',
            ])
            ->get()
            ->keyBy('id');

        $results = [];

        foreach ($meaning_ids as $meaning_id) {
            $meaning = $meanings->get($meaning_id);

            if (!$meaning) {
                continue;
            }

            $results[] = [
                'id' => $meaning->id,
                'text' => $this->partnerOptionText($meaning),
            ];
        }

        return $results;
    }

    /**
     * Готовит HTML разборов страницы, загружая данные группами по текстам.
     */
    protected function preloadWordBlocks(
        Meaning $source,
        array $items,
        array $interval
    ) {
        $requested_words = [];
        $sentence_ids = [];

        foreach ($items as $item) {
            $text_id = (int) $item['text_id'];

            $sentence_ids[$text_id][(int) $item['sentence_id']] = true;

            foreach ($item['linked_w_ids'] ?? [] as $w_id) {
                $requested_words[$text_id][(int) $w_id] = true;
            }
        }

        $words_by_text = [];
        $data_by_text = [];

        $loader = new MeaningExampleWordData();

        foreach ($requested_words as $text_id => $w_ids) {
            $words = Word::where('text_id', $text_id)
                ->whereIn('sentence_id', array_keys($sentence_ids[$text_id]))
                ->whereIn('w_id', array_keys($w_ids))
                ->get();

            $loaded_w_ids = [];

            foreach ($words as $word) {
                $w_id = (int) $word->w_id;

                $words_by_text[$text_id][$w_id] = $word;
                $loaded_w_ids[] = $w_id;
            }

            if ($loaded_w_ids) {
                $data_by_text[$text_id] = $loader->load(
                    $text_id,
                    $loaded_w_ids
                );
            }
        }

        $blocks = [];

        foreach ($items as $item) {
            $text_id = (int) $item['text_id'];

            if (!isset($data_by_text[$text_id])) {
                continue;
            }

            foreach ($item['source_examples'] as $example) {
                $example_id = $example['example_id'];
                $source_w_id = (int) $example['source_w_id'];

                foreach ($example['linked_w_ids'] ?? [] as $w_id) {
                    $w_id = (int) $w_id;

                    $word = $words_by_text[$text_id][$w_id] ?? null;

                    if (
                        !$word
                        || (int) $word->sentence_id !== (int) $example['sentence_id']
                    ) {
                        continue;
                    }

                    $context = [
                        'source_w_id' => $source_w_id,
                        'fragment_id' => (int) $example['fragment_id'],
                        'read_only' => !empty($example['is_fragment'])
                            && $w_id !== $source_w_id,
                    ];

                    $blocks[$example_id][$w_id] = $this->renderWordBlock(
                        $source,
                        $word,
                        $interval,
                        [
                            'context' => $context,
                            'data' => $data_by_text[$text_id],
                            'source_relevance' => (int) $example['source_relevance'],
                        ]
                    )->render();
                }
            }
        }

        return $blocks;
    }

    /**
     * Проверяет, что исходное значение входит в указанный синсет.
     */
    protected function exampleSynsetId(Request $request, Meaning $source)
    {
        $this->validate($request, [
            'synset_id' => 'integer|min:1|exists:synsets,id',
        ]);

        $synset_id = $request->input('synset_id');

        if ($synset_id === null || $synset_id === '') {
            return null;
        }

        $synset_id = (int) $synset_id;

        $membership_count = DB::table('meaning_synset')
            ->where('synset_id', $synset_id)
            ->where('meaning_id', $source->id)
            ->count();

        if (!$membership_count) {
            abort(409, trans('meaning_examples.synset_meaning_mismatch'));
        }

        return $synset_id;
    }

    /**
     * Загружает исходное предложение с готовыми разборами для редактора.
     */
    public function originalSentence($meaning_id, $text_id, $w_id, Request $request)
    {
        if (!User::checkAccess('dict.edit') && !User::checkAccess('corpus.edit')) {
            abort(403);
        }

        $request->merge([
            'fragment_id' => 0,
            'original' => 1,
            'source_w_id' => $w_id,
        ]);

        list($source, $source_word, $interval) = $this->reviewWord($meaning_id, $text_id, $w_id, $request);

        $sentence = DB::table('sentences')
            ->where('id', $source_word->sentence_id)
            ->first();

        if (!$sentence) {
            abort(404);
        }

        $words = Word::where('text_id', $source_word->text_id)
            ->where('sentence_id', $source_word->sentence_id)
            ->orderBy('word_number')
            ->get();

        $w_ids = [];
        $word_positions = [];

        foreach ($words as $word) {
            $word_w_id = (int) $word->w_id;
            $w_ids[] = $word_w_id;

            if ($word->word_number !== null) {
                $word_positions[$word_w_id] = (int) $word->word_number;
            }
        }

        $data = (new MeaningExampleWordData())->load((int) $source_word->text_id, $w_ids);

        $source_relevance = (int) DB::table('meaning_text')
            ->where('meaning_id', $source->id)
            ->where('word_id', $source_word->id)
            ->where('text_id', $source_word->text_id)
            ->where('s_id', $source_word->s_id)
            ->where('w_id', $source_word->w_id)
            ->value('relevance');

        $blocks = [];
        $linked_w_ids = [];

        foreach ($words as $word) {
            $word_w_id = (int) $word->w_id;

            if (empty($data['meanings'][$word_w_id])) {
                continue;
            }

            $linked_w_ids[] = $word_w_id;

            $blocks[$word_w_id] = $this->renderWordBlock(
                $source,
                $word,
                $interval,
                [
                    'context' => $this->review_context,
                    'data' => $data,
                    'source_relevance' => $source_relevance,
                ]
            )->render();
        }

        $item = [
            'example_id' => $source->id
                . '_' . $source_word->text_id
                . '_' . $source_word->s_id
                . '_' . $source_word->w_id,
            'fragment_id' => 0,
            'is_original' => true,
            'linked_w_ids' => $linked_w_ids,
            'matches' => [
                [
                    'source_w_id' => (int) $source_word->w_id,
                ],
            ],
            'sentence' => strip_tags((string) $sentence->text_xml),
            'source_w_id' => (int) $source_word->w_id,
            'text_id' => (int) $source_word->text_id,
            'text_xml' => (string) $sentence->text_xml,
            'word_positions' => $word_positions,
        ];

        return view(
            'dict.meaning.examples._original',
            [
                'original_html' => MeaningExampleFormatter::highlight($item, $blocks, $interval),
            ]
        );
    }

    /**
     * Возвращает соответствие ID партнёра и ID записи сочетаемости.
     */
    protected function recordedPartnerIds(Meaning $source)
    {
        $source_id = (int) $source->id;

        if (!array_key_exists($source_id, $this->recorded_partner_ids)) {
            $ids = [];

            $rows = $source->collocations()->get(['id', 'collocate_meaning_id']);

            foreach ($rows as $row) {
                $ids[(int) $row->collocate_meaning_id] = (int) $row->id;
            }

            $this->recorded_partner_ids[$source_id] = $ids;
        }

        return $this->recorded_partner_ids[$source_id];
    }

    /**
     * Возвращает актуальные ID записей сочетаемости по ID партнёров.
     */
    public function recordedPartners($meaning_id)
    {
        $source = Meaning::findOrFail($meaning_id);

        return response()->json([
            'partners' => (object) $this->recordedPartnerIds($source),
        ])->header('Cache-Control', 'no-store');
    }
}
