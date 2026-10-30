<?php

namespace App\Library;

use Illuminate\Database\Query\Builder;

class MeaningExampleFilters
{
    public function apply(Builder $query, $lemma_id, array $gramset_ids, array $corpus_ids)
    {
        if ($gramset_ids) {
            $this->sourceGramsets($query, $lemma_id, $gramset_ids);
        }

        if ($corpus_ids) {
            $placeholders = implode(', ', array_fill(0, count($corpus_ids), '?'));

            $query->whereRaw(
                'EXISTS (
                    SELECT 1 FROM corpus_text AS filter_ct
                    WHERE filter_ct.text_id = s.text_id
                        AND filter_ct.corpus_id IN (' . $placeholders . '))',
                $corpus_ids
            );
        }

        return $query;
    }

    protected function sourceGramsets(Builder $query, $lemma_id, array $gramset_ids)
    {
        $placeholders = implode(', ', array_fill(0, count($gramset_ids), '?'));
        $bindings = array_merge([(int) $lemma_id], $gramset_ids);

        $query->whereRaw(
            'EXISTS (
                SELECT 1
                FROM text_wordform AS filter_tw
                JOIN lemma_wordform AS filter_lw
                    ON filter_lw.wordform_id = filter_tw.wordform_id
                    AND filter_lw.gramset_id = filter_tw.gramset_id
                WHERE filter_tw.word_id = w1.id
                    AND filter_tw.text_id = w1.text_id
                    AND filter_tw.w_id = w1.w_id
                    AND filter_tw.relevance > 0
                    AND filter_lw.lemma_id = ?
                    AND filter_tw.gramset_id IN (' . $placeholders . ')
            )',
            $bindings
        );
    }

    public static function ids($value, $field)
    {
        if (!is_array($value)) {
            throw new \InvalidArgumentException($field . ' must be an array.');
        }

        foreach ($value as $id) {
            if (!is_int($id) || $id < 1) {
                throw new \InvalidArgumentException($field . ' must contain positive integer IDs.');
            }
        }

        return array_values(array_unique($value));
    }
}
