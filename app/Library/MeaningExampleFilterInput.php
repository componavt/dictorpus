<?php

namespace App\Library;

use Illuminate\Support\Facades\Validator;

use App\Models\Dict\Gramset;
use App\Models\Dict\Meaning;

class MeaningExampleFilterInput
{
    public function read(array $input, array $values)
    {
        $input = [
            'source_gramset_ids' => $input['source_gramset_ids'] ?? [],
            'corpus_ids' => $input['corpus_ids'] ?? [],
        ];

        $validator = Validator::make(
            $input,
            [
                'source_gramset_ids' => 'array|max:500',
                'source_gramset_ids.*' => 'integer|min:1',
                'corpus_ids' => 'array|max:500',
                'corpus_ids.*' => 'integer|min:1',
            ],
            [
                'source_gramset_ids.*.integer' => trans('meaning_examples.invalid_source_gramset_ids'),
                'source_gramset_ids.*.min' => trans('meaning_examples.invalid_source_gramset_ids'),
                'corpus_ids.*.integer' => trans('meaning_examples.invalid_corpus_ids'),
                'corpus_ids.*.min' => trans('meaning_examples.invalid_corpus_ids'),
            ],
            [
                'source_gramset_ids' => trans('meaning_examples.source_gramsets'),
                'corpus_ids' => trans('meaning_examples.corpuses'),
            ]
        );

        $options = [];

        if (!$validator->fails()) {
            $allowed = $this->allowedIds($values);

            foreach ($input as $field => $ids) {
                $options[$field] = array_values(array_unique(array_map('intval', $ids)));

                if (array_diff($options[$field], $allowed[$field])) {
                    $validator->errors()->add($field, trans('meaning_examples.invalid_' . $field));
                }
            }
        }

        return [
            'options' => $validator->errors()->count() ? null : $options,
            'errors' => $validator->errors(),
        ];
    }

    protected function allowedIds(array $values)
    {
        return [
            'source_gramset_ids' => array_map('intval', array_keys($values['source_gramsets'])),
            'corpus_ids' => array_map('intval', array_keys($values['corpuses'])),
        ];
    }

    public static function returnQuery(array $query)
    {
        $array_fields = ['source_gramset_ids', 'partner_gramset_ids', 'corpus_ids'];

        foreach ($query as $name => $value) {
            if (!in_array($name, $array_fields, true)) {
                if (!is_scalar($value)) {
                    return null;
                }
                continue;
            }

            if (!is_array($value) || count($value) > 500) {
                return null;
            }

            foreach ($value as $id) {
                if (!is_scalar($id) || !ctype_digit((string) $id) || (int) $id < 1) {
                    return null;
                }
            }

            $query[$name] = array_values(array_unique(array_map('intval', $value)));
        }

        return $query;
    }

    /**
     * Проверяет параметры страницы поиска и выбранного партнёра.
     */
    public function search(array $raw, Meaning $source, array $values)
    {
        $input = $this->searchInput($raw);
        $validator = $this->searchValidator($input);
        if (
            !$validator->fails() && (int) $input['partner_pos_id'] > 0
            && !\App\Models\Dict\PartOfSpeech::where('id', (int) $input['partner_pos_id'])->exists()
        ) {
            $validator->errors()->add('partner_pos_id', trans('collocation_candidates.invalid_pos'));
        }
        $partner = null;

        if (
            !$validator->fails() && $input['partner_meaning_id'] !== ''
            && $input['partner_meaning_id'] !== null
        ) {
            $partner = $this->searchPartner(
                $input['partner_meaning_id'],
                $source,
                $validator->errors()
            );
        }

        if ($partner && (int) $input['partner_pos_id'] > 0 && (int) $partner->lemma->pos_id !== (int) $input['partner_pos_id']) {
            $validator->errors()->add('partner_meaning_id', trans('meaning_examples.invalid_partner'));
        }

        if (!$validator->errors()->count() && (int) $input['distance_from'] > (int) $input['distance_to']) {
            $validator->errors()->add('distance_from', trans('meaning_examples.invalid_distance'));
        }

        $extra = $this->read($raw, $values);
        $partner_gramsets = $this->partnerGramsets($raw, $source);
        $errors = $validator->errors();

        foreach ([$extra['errors'], $partner_gramsets['errors']] as $error_bag) {
            foreach ($error_bag->getMessages() as $field => $messages) {
                foreach ($messages as $message) {
                    $errors->add($field, $message);
                }
            }
        }

        $selected = $extra['options'] ?? ['source_gramset_ids' => [], 'corpus_ids' => []];
        $selected['partner_gramset_ids'] = $partner_gramsets['ids'];

        return [
            'input' => $input,
            'partner' => $partner,
            'errors' => $errors,
            'selected_filters' => $selected,
        ];
    }

    protected function searchInput(array $raw)
    {
        $defaults = [
            'distance_from' => -1,
            'distance_to' => 1,
            'partner_meaning_id' => '',
            'partner_pos_id' => 0,
            'only_checked' => 0,
            'same_gramset' => 1,
            'source_only_checked' => array_key_exists('only_checked', $raw)
                ? $raw['only_checked'] : 0,
            'per_page' => 20,
            'page' => 1,
            'whole_sentence' => 0,
        ];

        return array_intersect_key($raw + $defaults, $defaults);
    }

    protected function searchValidator(array $input)
    {
        return Validator::make($input, [
            'distance_from' => 'required|integer',
            'distance_to' => 'required|integer',
            'partner_meaning_id' => 'integer|min:1',
            'partner_pos_id' => 'required|integer|min:0',
            'only_checked' => 'required|boolean',
            'same_gramset' => 'required|boolean',
            'source_only_checked' => 'required|boolean',
            'per_page' => 'required|integer|min:1|max:100',
            'page' => 'required|integer|min:1|max:1000000',
            'whole_sentence' => 'required|boolean',
        ], [], [
            'distance_from' => trans('meaning_examples.distance_from'),
            'distance_to' => trans('meaning_examples.distance_to'),
            'partner_meaning_id' => trans('meaning_examples.partner'),
            'only_checked' => trans('meaning_examples.only_checked'),
            'same_gramset' => trans('meaning_examples.same_gramset'),
            'per_page' => trans('meaning_examples.per_page'),
            'page' => trans('meaning_examples.page'),
        ]);
    }

    protected function searchPartner($id, Meaning $source, $errors)
    {
        $partner = Meaning::with(['lemma.pos', 'meaningTexts.lang'])->find((int) $id);

        if (!$partner) {
            $errors->add('partner_meaning_id', trans('meaning_examples.invalid_partner'));
        } elseif ((int) $partner->lemma->lang_id !== (int) $source->lemma->lang_id) {
            $errors->add('partner_meaning_id', trans('meaning_examples.different_language'));
        }

        return $partner;
    }

    /**
     * Проверяет грамсеты партнёра для выбранной части речи и языка источника.
     */
    public function partnerGramsets(array $raw, Meaning $source)
    {
        $input = [
            'partner_pos_id' => $raw['partner_pos_id'] ?? 0,
            'partner_gramset_ids' => $raw['partner_gramset_ids'] ?? [],
        ];

        $validator = Validator::make($input, [
            'partner_pos_id' => 'required|integer|min:0',
            'partner_gramset_ids' => 'array|max:500',
            'partner_gramset_ids.*' => 'integer|min:1',
        ], [], [
            'partner_gramset_ids' => trans('meaning_examples.partner_gramsets'),
        ]);

        $ids = [];

        if (!$validator->fails()) {
            $ids = array_values(array_unique(array_map('intval', $input['partner_gramset_ids'])));

            if ($ids) {
                $pos_id = (int) $input['partner_pos_id'];
                $allowed = $pos_id > 0 ? array_map('intval', array_keys(Gramset::getList($pos_id, (int) $source->lemma->lang_id))) : [];

                if (array_diff($ids, $allowed)) {
                    $validator->errors()->add('partner_gramset_ids', trans('meaning_examples.invalid_partner_gramset_ids'));
                }
            }
        }

        return [
            'ids' => $validator->errors()->count() ? [] : $ids,
            'errors' => $validator->errors(),
        ];
    }
}
