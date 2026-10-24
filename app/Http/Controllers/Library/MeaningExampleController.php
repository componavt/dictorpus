<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use App\Models\User;
use App\Models\Corpus\MeaningTextRel;
use App\Models\Corpus\TextWordform;
use App\Models\Corpus\Word;
use App\Models\Dict\Gramset;
use App\Models\Dict\Meaning;
use App\Models\Dict\Wordform;

use App\Library\MeaningExampleSearch;

class MeaningExampleController extends Controller
{
    /**
     * Показывает найденные предложения без изменения корпусных связей.
     */
    public function index($meaning_id, Request $request)
    {
        $source = Meaning::with('lemma')->findOrFail($meaning_id);

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
            $partner = Meaning::with(['lemma.pos', 'meaningTexts'])->find((int) $input['partner_meaning_id']);

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

        return view(
            'dict.meaning.examples.search',
            compact(
                'distance_from',
                'distance_to',
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
    public function confirmMeaning(
        $meaning_id,
        $text_id,
        $w_id,
        Request $request
    ) {
        if (!User::checkAccess('corpus.edit')) {
            abort(403);
        }

        $this->validate($request, [
            'meaning_id' => 'required|integer|min:1',
        ]);

        list($source, $word, $interval) = $this->reviewWord($meaning_id, $text_id, $w_id, $request);

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

            $relevance = $selected_id === (int) $source->id ? 7 : 5;

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

        $fully_checked = $meaning_checked && $gramset_checked;
        $can_edit = User::checkAccess('corpus.edit');

        $can_reset = $can_edit && (!$meaning_checked || (int) $word_meanings['checked']->relevance !== 10);

        $add_collocation_url = $this->collocationEditorUrl(
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
                'source',
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
     * Ищет значения партнёров того же языка для Select2.
     */
    public function partners($meaning_id, Request $request)
    {
        $this->validate($request, [
            'page' => 'integer|min:1|max:10000',
            'q' => 'required|string|min:2|max:100',
        ]);

        $source = Meaning::with('lemma')->findOrFail($meaning_id);

        $search = trim($request->input('q'));
        $page = (int) $request->input('page', 1);
        $page_size = 25;

        if (mb_strlen($search) < 2) {
            return response()->json([
                'results' => [],
                'pagination' => ['more' => false],
            ]);
        }

        $meanings = Meaning::join(
            'lemmas',
            'lemmas.id',
            '=',
            'meanings.lemma_id'
        )
            ->select('meanings.*')
            ->where('lemmas.lang_id', $source->lemma->lang_id)
            ->where('lemmas.lemma', 'like', '%' . $search . '%')
            ->with([
                'lemma.pos',
                'meaningTexts',
            ])
            ->orderByRaw(
                'CASE
                    WHEN lemmas.lemma = ? THEN 0
                    WHEN lemmas.lemma LIKE ? THEN 1
                    ELSE 2
                END',
                [$search, $search . '%']
            )
            ->orderBy('lemmas.lemma')
            ->orderBy('lemmas.id')
            ->orderBy('meanings.meaning_n')
            ->orderBy('meanings.id')
            ->skip(($page - 1) * $page_size)
            ->take($page_size + 1)
            ->get();

        $has_more = $meanings->count() > $page_size;
        $results = [];

        foreach ($meanings->take($page_size) as $meaning) {
            $results[] = [
                'id' => $meaning->id,
                'text' => $this->partnerOptionText($meaning),
            ];
        }

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => $has_more,
            ],
        ]);
    }

    /**
     * Формирует текст значения для списка и выбранного партнёра.
     */
    protected function partnerOptionText(Meaning $meaning)
    {
        $text = $meaning->lemma->lemma . ' [' . $meaning->meaning_n . ']';

        if ($meaning->lemma->pos) {
            $text .= ' (' . $meaning->lemma->pos->name . ')';
        }

        $gloss = trim((string) $meaning->getMultilangMeaningTextsString(
            LaravelLocalization::getCurrentLocale()
        ));

        if ($gloss !== '') {
            $text .= ' — ' . $gloss;
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
}
