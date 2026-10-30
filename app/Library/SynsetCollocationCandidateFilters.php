<?php

namespace App\Library;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Dict\PartOfSpeech;

class SynsetCollocationCandidateFilters
{
    /**
     * Собирает доступные грамсеты и корпуса без потери числовых ключей.
     */
    public function values($members)
    {
        $values = ['source_gramsets' => [], 'corpuses' => []];
        $loaded = [];

        foreach ($members as $member) {
            $key = $member->lemma->lang_id . ':' . $member->lemma->pos_id;

            if (isset($loaded[$key])) {
                continue;
            }

            $member_values = (new MeaningExampleFilterValues())->load($member);

            foreach ($values as $field => $items) {
                $values[$field] += $member_values[$field];
            }

            $loaded[$key] = true;
        }

        return $values;
    }

    /**
     * Проверяет и нормализует GET-параметры.
     */
    public function read(Request $request, array $values)
    {
        $defaults = [
            'distance_from' => -1,
            'distance_to' => 1,
            'source_only_checked' => 0,
            'only_checked' => 0,
            'same_gramset' => 1,
            'whole_sentence' => 0,
            'partner_pos_id' => 0,
            'row_mode' => 0,
            'unrecorded_only' => 0,
        ];

        $input = [];

        foreach ($defaults as $field => $default) {
            $input[$field] = $request->input($field, $default);
        }

        $validator = Validator::make($input, [
            'distance_from' => 'required|integer',
            'distance_to' => 'required|integer',
            'source_only_checked' => 'required|boolean',
            'only_checked' => 'required|boolean',
            'same_gramset' => 'required|boolean',
            'whole_sentence' => 'required|boolean',
            'partner_pos_id' => 'required|integer|min:0',
            'row_mode' => 'required|integer|in:0,1,2',
            'unrecorded_only' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            abort(422, $validator->errors()->first());
        }

        $filters = array_map('intval', $input);

        if ($filters['distance_from'] > $filters['distance_to']) {
            abort(422, trans('meaning_examples.invalid_distance'));
        }

        if ($filters['partner_pos_id'] && !PartOfSpeech::where('id', $filters['partner_pos_id'])->exists()) {
            abort(422, trans('collocation_candidates.invalid_pos'));
        }

        $extra = (new MeaningExampleFilterInput())->read($request->all(), $values);

        if ($extra['errors']->count()) {
            abort(422, $extra['errors']->first());
        }

        return array_merge($filters, $extra['options']);
    }

    /**
     * Переводит параметры формы в настройки корпусного поиска.
     */
    public function options(array $filters)
    {
        return [
            'distance_from' => $filters['distance_from'],
            'distance_to' => $filters['distance_to'],
            'source_threshold' => $filters['source_only_checked'],
            'partner_threshold' => $filters['only_checked'],
            'same_gramset' => (bool) $filters['same_gramset'],
            'whole_sentence' => (bool) $filters['whole_sentence'],
            'source_gramset_ids' => $filters['source_gramset_ids'],
            'corpus_ids' => $filters['corpus_ids'],
        ];
    }

    /**
     * Читает выбранные значения и проверяет их принадлежность синсету.
     */
    public function members(Request $request, $members)
    {
        $allowed_ids = array_map('intval', $members->pluck('id')->all());

        $input = [
            'member_ids' => $request->input('member_ids', $allowed_ids),
        ];

        if (is_array($input['member_ids'])) {
            $input['member_ids'] = array_values(array_filter(
                $input['member_ids'],
                function ($id) {
                    return $id !== '';
                }
            ));
        }

        $validator = Validator::make($input, [
            'member_ids' => 'present|array|max:500',
            'member_ids.*' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            abort(422, trans('collocation_candidates.invalid_members'));
        }

        $selected_ids = array_values(array_unique(
            array_map('intval', $input['member_ids'])
        ));

        if (array_diff($selected_ids, $allowed_ids)) {
            abort(422, trans('collocation_candidates.invalid_members'));
        }

        return $selected_ids;
    }
}
