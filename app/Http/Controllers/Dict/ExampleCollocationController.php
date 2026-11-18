<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Corpus\Word;
use App\Models\Dict\Collocation;
use App\Models\Dict\ConceptCategory;
use App\Models\Dict\Meaning;

class ExampleCollocationController extends CollocationController
{
    /**
     * Показывает форму для фиксированной пары из примера.
     */
    public function create(Request $request, $meaning_id)
    {
        $this->setSynsetContext($request, $meaning_id);
        $meaning = Meaning::with('lemma')->findOrFail($meaning_id);
        $pair = $this->examplePair($request, $meaning);

        if ($meaning->collocations()
            ->where('collocate_meaning_id', $pair['partner']->id)->first()) {
            $this->invalidExample($request, trans('collocations.duplicate'));
        }

        $category_values = ConceptCategory::getList();
        $default_category_id = '';
        $default_display_gloss = (string) $pair['partner']
            ->getMeaningTextByLangCode('ru');
        $synset_id = $this->synset_id;

        return view('dict.collocation._example_form', array_merge($pair, compact(
            'category_values',
            'default_category_id',
            'default_display_gloss',
            'synset_id'
        )));
    }

    /**
     * Использует общую валидацию редактора и повторно проверяет пример.
     */
    public function store(Request $request, $meaning_id)
    {
        $this->setSynsetContext($request, $meaning_id);

        $result = DB::transaction(function () use ($request, $meaning_id) {
            $meaning = Meaning::with('lemma')->where('id', $meaning_id)
                ->lockForUpdate()->firstOrFail();

            $pair = $this->examplePair($request, $meaning, true);
            $data = $this->validatedData($request, $meaning);
            $collocation = new Collocation($data);
            $collocation->meaning_id = $meaning->id;

            if (!$collocation->save()) {
                throw new \RuntimeException(trans('collocations.save_failed'));
            }

            return [
                'id' => (int) $collocation->id,
                'message' => trans('example_collocations.saved', [
                    'source' => $meaning->lemma->lemma,
                    'source_number' => $meaning->meaning_n,
                    'partner' => $pair['partner']->lemma->lemma,
                    'partner_number' => $pair['partner']->meaning_n,
                    'source_word' => $pair['source_word']->word,
                    'source_position' => $pair['source_word']->word_number,
                    'partner_word' => $pair['partner_word']->word,
                    'partner_position' => $pair['partner_word']->word_number,
                    'text_id' => $pair['source_word']->text_id,
                    'sentence_number' => $pair['source_word']->s_id,
                ]),
            ];
        });

        return response()->json($result);
    }

    /**
     * Проверяет язык, разные употребления и общее предложение.
     */
    protected function examplePair(Request $request, Meaning $meaning, $lock = false)
    {
        $this->validate($request, [
            'text_id' => 'required|integer|min:1',
            'source_w_id' => 'required|integer|min:1',
            'partner_w_id' => 'required|integer|min:1',
            'collocate_meaning_id' => 'required|integer|min:1|exists:meanings,id',
        ]);

        $source_w_id = (int) $request->input('source_w_id');
        $partner_w_id = (int) $request->input('partner_w_id');
        $partner = $this->findPartnerMeaning(
            $request->input('collocate_meaning_id'),
            $meaning->lemma->lang_id
        );

        if (!$partner || $source_w_id === $partner_w_id) {
            $this->invalidExample($request, trans('example_collocations.invalid_pair'));
        }

        $query = Word::with('text')
            ->where('text_id', $request->input('text_id'))
            ->whereIn('w_id', [$source_w_id, $partner_w_id])
            ->orderBy('id');

        if ($lock) {
            $query->lockForUpdate();
        }

        $words = $query->get()->keyBy('w_id');
        $source_word = $words->get($source_w_id);
        $partner_word = $words->get($partner_w_id);

        if (
            !$source_word || !$partner_word || !$source_word->text
            || !$source_word->sentence_id
            || (int) $source_word->sentence_id !== (int) $partner_word->sentence_id
            || (int) $source_word->s_id !== (int) $partner_word->s_id
            || (int) $source_word->text->lang_id !== (int) $meaning->lemma->lang_id
        ) {
            $this->invalidExample($request, trans('example_collocations.invalid_pair'));
        }

        $this->assertExampleMeaning($request, $source_word, $meaning->id, $lock);
        $this->assertExampleMeaning($request, $partner_word, $partner->id, $lock);

        return compact('meaning', 'partner', 'source_word', 'partner_word');
    }

    /**
     * Требует подтверждения именно нужного значения данного употребления.
     */
    protected function assertExampleMeaning(Request $request, Word $word, $meaning_id, $lock)
    {
        $query = DB::table('meaning_text')
            ->where('word_id', $word->id)
            ->where('text_id', $word->text_id)
            ->where('s_id', $word->s_id)
            ->where('w_id', $word->w_id)
            ->where('meaning_id', $meaning_id)
            ->where('relevance', '>', 1);

        if ($lock) {
            $query->lockForUpdate();
        }

        if (!$query->first()) {
            $this->invalidExample($request, trans('example_collocations.not_confirmed'));
        }
    }

    protected function invalidExample(Request $request, $message)
    {
        $validator = Validator::make([], []);
        $validator->errors()->add('example', $message);
        $this->throwValidationException($request, $validator);
    }
}
