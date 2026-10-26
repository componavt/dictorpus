<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

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

        $input = [
            'distance_from' => $request->input('distance_from', -1),
            'distance_to' => $request->input('distance_to', 1),
            'partner_meaning_id' => $request->input('partner_meaning_id', ''),
            'only_checked' => $request->input('only_checked', 0),
            'same_gramset' => $request->input('same_gramset', 1),
            'per_page' => $request->input('per_page', 20),
            'page' => $request->input('page', 1),
        ];

        $validator = Validator::make(
            $input,
            [
                'distance_from' => 'required|integer',
                'distance_to' => 'required|integer',
                'partner_meaning_id' => 'integer|min:1',
                'only_checked' => 'required|boolean',
                'same_gramset' => 'required|boolean',
                'per_page' => 'required|integer|min:1|max:100',
                'page' => 'required|integer|min:1|max:1000000',
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

        if ($search_errors->count() === 0) {
            $threshold = (int) $input['only_checked'];

            $search = new MeaningExampleSearch(
                $source,
                $partner,
                [
                    'distance_from' => (int) $input['distance_from'],
                    'distance_to' => (int) $input['distance_to'],
                    'partner_threshold' => $threshold,
                    'same_gramset' => (bool) (int) $input['same_gramset'],
                    'source_threshold' => $threshold,
                ]
            );

            $sentences = $search->paginateSentences((int) $input['per_page'], (int) $input['page']);

            $sentences->appends([
                'distance_from' => (int) $input['distance_from'],
                'distance_to' => (int) $input['distance_to'],
                'partner_meaning_id' => $partner ? $partner->id : '',
                'only_checked' => $threshold,
                'same_gramset' => (int) $input['same_gramset'],
                'per_page' => (int) $input['per_page'],
            ]);
        }

        $only_checked = !empty($input['only_checked']);
        $same_gramset = !empty($input['same_gramset']);

        $per_page = $sentences ? $sentences->perPage() : 20;

        $distance_from = is_scalar($input['distance_from']) ? (string) $input['distance_from'] : '';
        $distance_to = is_scalar($input['distance_to']) ? (string) $input['distance_to'] : '';

        $partner_text = $partner ? $this->partnerOptionText($partner) : '';

        $lemma = $source->lemma;
        $meaning_labels = $source->showShortLabels();
        $back_to_url = LaravelLocalization::localizeURL('/dict/lemma/' . $lemma->id) . '#meaning-' . $source->id;

        $examples_return = '';

        if ($sentences) {
            $examples_return = '/dict/meaning/' . $source->id
                . '/examples/search?'
                . http_build_query([
                    'distance_from' => (int) $input['distance_from'],
                    'distance_to' => (int) $input['distance_to'],
                    'only_checked' => (int) $input['only_checked'],
                    'page' => $sentences->currentPage(),
                    'partner_meaning_id' => $partner ? $partner->id : '',
                    'per_page' => $sentences->perPage(),
                    'same_gramset' => (int) $input['same_gramset'],
                ]);
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
        $interval = [
            'distance_from' => $request->input('distance_from', -1),
            'distance_to' => $request->input('distance_to', 1),
        ];

        $validator = Validator::make(
            $interval,
            [
                'distance_from' => 'required|integer',
                'distance_to' => 'required|integer',
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
    protected function renderWordBlock(Meaning $source, Word $word, array $interval)
    {
        list($meanings, $gramsets, $wordforms) = $word->text
            ->meaningsGramsetsByWid([(int) $word->w_id]);

        $word_meanings = $meanings[$word->w_id] ?? [];
        $word_gramsets = $gramsets[$word->w_id] ?? [];

        if (!empty($word_meanings['checked'])) {
            $lemma_id = (int) $word_meanings['checked']->lemma_id;

            $gramset_rows = Gramset::join(
                'text_wordform as tw',
                'tw.gramset_id',
                '=',
                'gramsets.id'
            )
                ->where('tw.text_id', $word->text_id)
                ->where('tw.w_id', $word->w_id)
                ->where('tw.relevance', '>', 0)
                ->whereRaw(
                    'EXISTS (
                        SELECT 1
                        FROM lemma_wordform lw
                        WHERE lw.lemma_id = ?
                            AND lw.wordform_id = tw.wordform_id
                            AND lw.gramset_id = tw.gramset_id
                    )',
                    [$lemma_id]
                )
                ->select([
                    'gramsets.*',
                    'tw.wordform_id',
                    'tw.relevance',
                ])
                ->orderBy('gramsets.sequence_number')
                ->orderBy('gramsets.id')
                ->orderBy('tw.wordform_id')
                ->get();

            $word_gramsets = [
                'checked' => null,
                'unchecked' => [],
            ];

            foreach ($gramset_rows as $gramset) {
                if ((int) $gramset->relevance > 1) {
                    $word_gramsets['checked'] = $gramset;
                } else {
                    $word_gramsets['unchecked'][] = $gramset;
                }
            }
        }

        $meaning_checked = !empty($word_meanings['checked']);
        $gramset_checked = !empty($word_gramsets['checked']);

        $meaning_values = $meaning_checked
            ? [$word_meanings['checked']]
            : ($word_meanings['unchecked'] ?? []);

        $gramset_values = $gramset_checked
            ? [$word_gramsets['checked']]
            : ($word_gramsets['unchecked'] ?? []);

        $has_gramsets = count($gramset_values) > 0;

        $fully_checked = $meaning_checked
            && ($gramset_checked || !$has_gramsets);

        $read_only = !empty($this->review_context['read_only']);
        $can_edit = User::checkAccess('corpus.edit') && !$read_only;

        $can_reset = $can_edit && (!$meaning_checked || (int) $word_meanings['checked']->relevance !== 10);

        $source_relevance = (int) DB::table('meaning_text')
            ->where('meaning_id', $source->id)
            ->where('text_id', $word->text_id)
            ->where('w_id', $this->review_context['source_w_id'])
            ->value('relevance');

        $add_collocation_url = $read_only ? null
            : $this->collocationEditorUrl(
                $source,
                $word,
                $word_meanings['checked'] ?? null,
                $interval
            );

        return view(
            'dict.meaning.examples._word_block',
            compact(
                'add_collocation_url',
                'can_edit',
                'can_reset',
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

        $source = Meaning::with('lemma')->findOrFail($meaning_id);

        $search = new MeaningExampleSearch(
            $source,
            null,
            [
                'distance_from' => $input['distance_from'],
                'distance_to' => $input['distance_to'],
                'partner_threshold' => $input['only_checked'],
                'same_gramset' => (bool) $input['same_gramset'],
                'source_threshold' => $input['only_checked'],
            ]
        );

        $query = $search->partnerMeaningsQuery();

        if ($input['q'] !== '') {
            $query->where('l2.lemma', 'like', '%' . $input['q'] . '%')
                ->orderByRaw(
                    'CASE
                        WHEN l2.lemma = ? THEN 0
                        WHEN l2.lemma LIKE ? THEN 1
                        ELSE 2
                    END',
                    [$input['q'], $input['q'] . '%']
                );
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
    protected function collocationEditorUrl(
        Meaning $source,
        Word $word,
        ?Meaning $partner,
        array $interval
    ) {
        if (
            !User::checkAccess('dict.edit')
            || !$partner
            || (int) $partner->relevance <= 1
        ) {
            return null;
        }

        if (
            (int) $partner->lemma->lang_id
            !== (int) $source->lemma->lang_id
        ) {
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
            ->whereRaw(
                '? - sw.word_number BETWEEN ? AND ?',
                [
                    (int) $word->word_number,
                    $interval['distance_from'],
                    $interval['distance_to'],
                ]
            )
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

        return LaravelLocalization::localizeURL(
            '/dict/meaning/' . $source->id . '/collocations'
        ) . '?' . http_build_query([
            'partner_id' => $partner->id,
        ]);
    }

    /**
     * Проверяет исходное употребление и ограничения показанного фрагмента.
     */
    protected function reviewContext(
        Meaning $source,
        Word $word,
        Request $request
    ) {
        $this->validate($request, [
            'fragment_id' => 'required|integer|min:0',
            'source_w_id' => 'required|integer|min:1',
        ]);

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

        $fragment = SentenceFragment::where(
            'sentence_id',
            $source_word->sentence_id
        )
            ->where('w_id', $source_w_id)
            ->orderBy('id')
            ->first();

        $is_fragment = $fragment
            && trim((string) $fragment->text_xml) !== '';

        $fragment_id = $is_fragment ? (int) $fragment->id : 0;

        if ($fragment_id !== (int) $request->input('fragment_id')) {
            abort(409);
        }

        return [
            'source_w_id' => $source_w_id,
            'fragment_id' => $fragment_id,
            'read_only' => $is_fragment
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
}
