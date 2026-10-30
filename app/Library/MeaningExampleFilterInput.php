<?php

namespace App\Library;

use Illuminate\Support\Facades\Validator;

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
        $array_fields = ['source_gramset_ids', 'corpus_ids'];

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
}
