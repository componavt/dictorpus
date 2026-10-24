<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use App\Http\Controllers\Controller;
use App\Models\Dict\Collocation;
use App\Models\Dict\ConceptCategory;
use App\Models\Dict\Lang;
use App\Models\Dict\Meaning;

class CollocationController extends Controller
{
    /**
     * Синсет, из которого открыт редактор в текущем запросе.
     * @var int|null
     */
    protected $synset_id = null;

    /**
     * Ограничивает доступ к редактору правом редактирования словаря.
     */
    public function __construct()
    {
        $this->middleware('auth:dict.edit,/dict/lemma/');
    }

    /**
     * Показывает редактор и предзаполняет добавление при переходе из матрицы.
     */
    public function index(Request $request, $meaning_id)
    {
        $this->setSynsetContext($request, $meaning_id);

        $this->validate($request, [
            'category_id' => 'string|max:4|exists:concept_categories,id',
            'edit' => 'integer|min:1',
            'partner_id' => 'integer|min:1|exists:meanings,id',
        ]);

        $meaning = Meaning::with('lemma')->findOrFail($meaning_id);

        $editing = null;
        $prefill_meaning = null;

        if ($request->input('edit')) {
            $editing = $meaning->collocations()
                ->where('id', (int) $request->input('edit'))
                ->firstOrFail();
        }

        if (!$editing && $request->input('partner_id')) {
            $prefill_meaning = $this->findPartnerMeaning(
                $request->input('partner_id'),
                $meaning->lemma->lang_id
            );

            if (!$prefill_meaning) {
                abort(422, trans('collocations.wrong_language'));
            }

            $existing = $meaning->collocations()
                ->where('collocate_meaning_id', $prefill_meaning->id)
                ->first();

            if ($existing) {
                $url = $this->withSynsetContext(
                    $this->editorUrl($meaning->id),
                    ['edit' => $existing->id]
                );

                return redirect($url)->with(
                    'matrix_notice',
                    trans('collocations.matrix_pair_exists')
                );
            }
        }

        $default_category_id = $editing
            ? $editing->concept_category_id
            : $request->input('category_id', '');

        $collocations = $meaning->collocations()
            ->with(['collocateMeaning.lemma', 'conceptCategory'])
            ->orderBy('concept_category_id')
            ->orderBy('id')
            ->get();

        $category_values = ConceptCategory::getList();

        $default_partner_id = $editing
            ? $editing->collocate_meaning_id
            : ($prefill_meaning ? $prefill_meaning->id : null);

        $selected_id = $request->old(
            'collocate_meaning_id',
            $default_partner_id
        );

        $selected_meaning = null;
        $selected_gloss = '';

        if (is_scalar($selected_id) && ctype_digit((string) $selected_id)) {
            $selected_meaning = $this->findPartnerMeaning($selected_id, $meaning->lemma->lang_id);

            if ($selected_meaning) {
                $selected_gloss = (string) $selected_meaning->getMeaningTextByLangCode('ru');
            }
        }

        $default_display_gloss = $editing ? $editing->display_gloss : $selected_gloss;

        $base_url = $this->editorUrl($meaning->id);

        return view('dict.collocation.index', compact(
            'base_url',
            'category_values',
            'collocations',
            'default_category_id',
            'default_display_gloss',
            'editing',
            'meaning',
            'selected_gloss',
            'selected_meaning'
        ))->with($this->navigationData($meaning->id));
    }

    /**
     * Ищет значения слов того же языка с постраничной выдачей.
     *
     * Точные совпадения и совпадения с началом леммы выводятся первыми.
     */
    public function search(Request $request, $meaning_id)
    {
        $this->validate($request, [
            'q' => 'required|string|min:2|max:100',
            'page' => 'integer|min:1|max:10000',
        ]);

        $meaning = Meaning::with('lemma')->findOrFail($meaning_id);

        $search = trim($request->input('q'));
        $page = (int) $request->input('page', 1);
        $page_size = 25;

        if (mb_strlen($search) < 2) {
            return response()->json([
                'results' => [],
                'pagination' => ['more' => false],
            ]);
        }

        $ru_lang_id = Lang::getIDByCode('ru');

        $meanings = Meaning::join(
            'lemmas',
            'lemmas.id',
            '=',
            'meanings.lemma_id'
        )
            ->select('meanings.*')
            ->where('lemmas.lang_id', $meaning->lemma->lang_id)
            ->where('lemmas.lemma', 'like', '%' . $search . '%')
            ->with([
                'lemma',
                'meaningTexts' => function ($query) use ($ru_lang_id) {
                    $query->where('lang_id', $ru_lang_id);
                },
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
            ->orderBy('meanings.meaning_n')
            ->orderBy('meanings.id')
            ->skip(($page - 1) * $page_size)
            ->take($page_size + 1)
            ->get();

        $has_more = $meanings->count() > $page_size;

        $results = [];

        foreach ($meanings->take($page_size) as $partner) {
            $meaning_text = $partner->meaningTexts->first();

            $gloss = $meaning_text
                ? $meaning_text->meaning_text
                : '';

            $results[] = [
                'id' => $partner->id,
                'text' => $partner->lemma->lemma
                    . ' (' . $partner->meaning_n . '. ' . $gloss . ')',
                'display_gloss' => $gloss,
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
     * Создаёт сочетаемость и сохраняет категорию для следующего добавления.
     */
    public function store(Request $request, $meaning_id)
    {
        $this->setSynsetContext($request, $meaning_id);
        $category_id = DB::transaction(function () use ($request, $meaning_id) {
            $meaning = Meaning::where('id', $meaning_id)
                ->lockForUpdate()
                ->firstOrFail();

            $data = $this->validatedData($request, $meaning);

            $collocation = new Collocation($data);
            $collocation->meaning_id = $meaning->id;

            if (!$collocation->save()) {
                throw new \RuntimeException(trans('collocations.save_failed'));
            }

            return $collocation->concept_category_id;
        });

        $query = $category_id === null ? [] : ['category_id' => $category_id];

        $url = $this->withSynsetContext($this->editorUrl($meaning_id), $query);

        return redirect($url)
            ->withSuccess(trans('collocations.saved'));
    }

    /**
     * Изменяет только сочетаемость, принадлежащую указанному значению.
     */
    public function update(Request $request, $meaning_id, $collocation_id)
    {
        $this->setSynsetContext($request, $meaning_id);
        DB::transaction(function () use ($request, $meaning_id, $collocation_id) {
            $meaning = Meaning::where('id', $meaning_id)
                ->lockForUpdate()
                ->firstOrFail();

            $collocation = $meaning->collocations()
                ->where('id', $collocation_id)
                ->lockForUpdate()
                ->firstOrFail();

            $data = $this->validatedData($request, $meaning, $collocation->id);

            if (!$collocation->fill($data)->save()) {
                throw new \RuntimeException(trans('collocations.save_failed'));
            }
        });

        return redirect(
            $this->withSynsetContext($this->editorUrl($meaning_id))
        )->withSuccess(trans('collocations.saved'));
    }

    /**
     * Удаляет сочетаемость с сохранением события в истории статьи.
     */
    public function destroy(Request $request, $meaning_id, $collocation_id)
    {
        $this->setSynsetContext($request, $meaning_id);
        DB::transaction(function () use ($meaning_id, $collocation_id) {
            $meaning = Meaning::where('id', $meaning_id)
                ->lockForUpdate()
                ->firstOrFail();

            $collocation = $meaning->collocations()
                ->where('id', $collocation_id)
                ->firstOrFail();

            $collocation->removeWithHistory();
        });

        return redirect(
            $this->withSynsetContext($this->editorUrl($meaning_id))
        )->withSuccess(trans('collocations.deleted'));
    }

    /**
     * Проверяет поля, язык партнёра и уникальность пары значений.
     */
    protected function validatedData(
        Request $request,
        Meaning $meaning,
        $ignore_id = null
    ) {
        $this->validate($request, [
            'collocate_meaning_id' => 'required|integer|min:1|exists:meanings,id',
            'concept_category_id' => 'string|max:4|exists:concept_categories,id',
            'display_gloss' => 'present|string|max:10000',
        ], [], [
            'collocate_meaning_id' => trans('collocations.partner'),
            'concept_category_id' => trans('collocations.category'),
            'display_gloss' => trans('collocations.display_gloss'),
        ]);

        $partner = Meaning::with('lemma')
            ->findOrFail($request->input('collocate_meaning_id'));

        $validator = Validator::make([], []);

        if ((int) $partner->lemma->lang_id !== (int) $meaning->lemma->lang_id) {
            $validator->errors()->add('collocate_meaning_id', trans('collocations.wrong_language'));
        }

        $duplicates = $meaning->collocations()
            ->where('collocate_meaning_id', $partner->id);

        if ($ignore_id !== null) {
            $duplicates->where('id', '<>', $ignore_id);
        }

        if ($duplicates->exists()) {
            $validator->errors()->add('collocate_meaning_id', trans('collocations.duplicate'));
        }

        if ($validator->errors()->count()) {
            $this->throwValidationException($request, $validator);
        }

        $category_id = $request->input('concept_category_id');

        return [
            'collocate_meaning_id' => $partner->id,
            'concept_category_id' => $category_id === '' || $category_id === null
                ? null : $category_id,
            'display_gloss' => (string) $request->input('display_gloss'),
        ];
    }

    /**
     * Формирует локализованный адрес редактора значения.
     */
    protected function editorUrl($meaning_id)
    {
        return LaravelLocalization::localizeURL(
            '/dict/meaning/' . (int) $meaning_id . '/collocations'
        );
    }

    /**
     * Показывает выбор источника и предварительный список для копирования.
     */
    public function copyForm(Request $request, $meaning_id)
    {
        $this->setSynsetContext($request, $meaning_id);
        $this->validate($request, [
            'source_meaning_id' => 'integer|min:1|exists:meanings,id',
        ], [], [
            'source_meaning_id' => trans('collocations.copy_source'),
        ]);

        $meaning = Meaning::with('lemma')->findOrFail($meaning_id);

        $source = null;
        $copy_rows = [];
        $copyable_count = 0;

        if ($request->input('source_meaning_id')) {
            $source = Meaning::with('lemma')->findOrFail($request->input('source_meaning_id'));

            $this->validateCopySource($request, $meaning, $source);

            $existing = $meaning->collocations()->get()->keyBy('collocate_meaning_id');

            $source_collocations = $source->collocations()
                ->with(['collocateMeaning.lemma', 'conceptCategory'])
                ->orderBy('concept_category_id')
                ->orderBy('id')
                ->get();

            foreach ($source_collocations as $collocation) {
                $existing_collocation = $existing->get($collocation->collocate_meaning_id);

                $conflict = $existing_collocation
                    && !$this->sameCollocationData($collocation, $existing_collocation);

                $copy_rows[] = [
                    'collocation' => $collocation,
                    'exists' => $existing_collocation !== null,
                    'conflict' => $conflict,
                ];

                if ($existing_collocation === null) {
                    $copyable_count++;
                }
            }
        }

        $base_url = $this->editorUrl($meaning->id);

        return view('dict.collocation.copy', compact(
            'base_url',
            'copy_rows',
            'copyable_count',
            'meaning',
            'source'
        ))->with($this->navigationData($meaning->id));
    }

    /**
     * Копирует выбранные записи, не изменяя существующие сочетаемости.
     */
    public function copySelected(Request $request, $meaning_id)
    {
        $this->setSynsetContext($request, $meaning_id);
        $this->validate($request, [
            'source_meaning_id' => 'required|integer|min:1|exists:meanings,id',
            'collocation_ids' => 'required|array',
            'collocation_ids.*' => 'required|integer|min:1',
        ], [], [
            'source_meaning_id' => trans('collocations.copy_source'),
            'collocation_ids' => trans('collocations.list'),
        ]);

        $source_id = (int) $request->input('source_meaning_id');

        $selected_ids = array_values(array_unique(array_map('intval', $request->input('collocation_ids'))));

        $result = DB::transaction(function () use ($request, $meaning_id, $source_id, $selected_ids) {
            $locked_meanings = Meaning::whereIn('id', [(int) $meaning_id, $source_id])
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $meaning = $locked_meanings->get((int) $meaning_id);
            $source = $locked_meanings->get($source_id);

            if (!$meaning || !$source) {
                abort(404);
            }

            $this->validateCopySource($request, $meaning, $source);

            $source_collocations = $source->collocations()
                ->whereIn('id', $selected_ids)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($source_collocations->count() !== count($selected_ids)) {
                $validator = Validator::make([], []);
                $validator->errors()->add('collocation_ids', trans('collocations.copy_invalid_selection'));
                $this->throwValidationException($request, $validator);
            }

            $existing = $meaning->collocations()->get()->keyBy('collocate_meaning_id');

            $result = [
                'added' => 0,
                'skipped' => 0,
                'conflicts' => 0,
            ];

            foreach ($source_collocations as $source_collocation) {
                $existing_collocation = $existing->get($source_collocation->collocate_meaning_id);

                if ($existing_collocation) {
                    $result['skipped']++;

                    if (!$this->sameCollocationData($source_collocation, $existing_collocation)) {
                        $result['conflicts']++;
                    }

                    continue;
                }

                $collocation = new Collocation([
                    'meaning_id' => $meaning->id,
                    'collocate_meaning_id' => $source_collocation->collocate_meaning_id,
                    'concept_category_id' => $source_collocation->concept_category_id,
                    'display_gloss' => $source_collocation->display_gloss,
                ]);

                if (!$collocation->save()) {
                    throw new \RuntimeException(trans('collocations.save_failed'));
                }

                $existing->put($collocation->collocate_meaning_id, $collocation);

                $result['added']++;
            }

            return $result;
        });

        return redirect(
            $this->withSynsetContext($this->editorUrl($meaning_id))
        )->withSuccess(trans('collocations.copy_result', $result));
    }

    /**
     * Проверяет различие источника и получателя и совпадение их языков.
     */
    protected function validateCopySource(Request $request, Meaning $meaning, Meaning $source)
    {
        $validator = Validator::make([], []);

        if ((int) $meaning->id === (int) $source->id) {
            $validator->errors()->add('source_meaning_id', trans('collocations.copy_same_meaning'));
        }

        if ((int) $meaning->lemma->lang_id !== (int) $source->lemma->lang_id) {
            $validator->errors()->add('source_meaning_id', trans('collocations.copy_wrong_language'));
        }

        if ($validator->errors()->count()) {
            $this->throwValidationException($request, $validator);
        }
    }

    /**
     * Сравнивает категорию и сохранённое пояснение одной пары значений.
     *
     * @return bool
     */
    protected function sameCollocationData(Collocation $left, Collocation $right)
    {
        return (string) $left->concept_category_id
            === (string) $right->concept_category_id
            && (string) $left->display_gloss
            === (string) $right->display_gloss;
    }

    /**
     * Проверяет контекст синсета до выполнения операций редактора.
     */
    protected function setSynsetContext(Request $request, $meaning_id)
    {
        $this->synset_id = null;

        $this->validate($request, [
            'synset_id' => 'integer|min:1|exists:synsets,id',
        ]);

        $synset_id = $request->input('synset_id');

        if ($synset_id === null || $synset_id === '') {
            return;
        }

        $synset_id = (int) $synset_id;

        $belongs_to_synset = DB::table('meaning_synset')
            ->where('synset_id', $synset_id)
            ->where('meaning_id', (int) $meaning_id)
            ->exists();

        if (!$belongs_to_synset) {
            abort(
                409,
                trans('collocations.synset_meaning_mismatch')
            );
        }

        $this->synset_id = $synset_id;
    }

    /**
     * Добавляет проверенный контекст синсета к адресу без query-параметров.
     */
    protected function withSynsetContext($url, array $query = [])
    {
        if ($this->synset_id !== null) {
            $query['synset_id'] = $this->synset_id;
        }

        return $url . ($query ? '?' . http_build_query($query) : '');
    }

    /**
     * Формирует ссылки редактора, копирования и возврата к синсету.
     *
     * @return array
     */
    protected function navigationData($meaning_id)
    {
        $base_url = $this->editorUrl($meaning_id);

        $navigation_args = $this->synset_id === null
            ? []
            : ['synset_id' => $this->synset_id];

        $synset_url = $this->synset_id === null
            ? null
            : LaravelLocalization::localizeURL(
                '/dict/synset/' . $this->synset_id . '/edit'
            ) . '#synset-collocations';

        return [
            'copy_url' => $this->withSynsetContext($base_url . '/copy'),
            'editor_url' => $this->withSynsetContext($base_url),
            'navigation_args' => $navigation_args,
            'synset_id' => $this->synset_id,
            'synset_url' => $synset_url,
        ];
    }

    /**
     * Находит значение партнёра с проверкой языка исходной леммы.
     *
     * @return \App\Models\Dict\Meaning|null
     */
    protected function findPartnerMeaning($meaning_id, $lang_id)
    {
        return Meaning::join('lemmas', 'lemmas.id', '=', 'meanings.lemma_id')
            ->select('meanings.*')
            ->with('lemma')
            ->where('meanings.id', (int) $meaning_id)
            ->where('lemmas.lang_id', $lang_id)
            ->first();
    }
}
