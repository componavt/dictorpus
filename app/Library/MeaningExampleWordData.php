<?php

namespace App\Library;

use App\Models\Dict\Gramset;
use App\Models\Dict\Meaning;
use App\Models\Dict\Wordform;

class MeaningExampleWordData
{
    /**
     * Загружает значения, грамсеты и словоформы выбранных слов одного текста.
     */
    public function load($text_id, array $w_ids)
    {
        $text_id = (int) $text_id;
        $w_ids = array_values(array_unique(array_map('intval', $w_ids)));

        if (!$w_ids) {
            return [
                'meanings' => [],
                'gramsets' => [],
                'wordforms' => [],
            ];
        }

        $meaning_rows = Meaning::join(
            'meaning_text as mt',
            'mt.meaning_id',
            '=',
            'meanings.id'
        )
            ->where('mt.text_id', $text_id)
            ->whereIn('mt.w_id', $w_ids)
            ->where('mt.relevance', '>', 0)
            ->select([
                'meanings.*',
                'mt.w_id',
                'mt.relevance',
            ])
            ->with([
                'lemma.pos',
                'meaningTexts.lang',
            ])
            ->get();

        $meanings = $this->groupByWord($meaning_rows);

        $checked_lemma_words = [];
        $unchecked_w_ids = [];

        foreach ($w_ids as $w_id) {
            if (!empty($meanings[$w_id]['checked'])) {
                $lemma_id = (int) $meanings[$w_id]['checked']->lemma_id;
                $checked_lemma_words[$lemma_id][] = $w_id;
            } else {
                $unchecked_w_ids[] = $w_id;
            }
        }

        $gramset_query = Gramset::join(
            'text_wordform as tw',
            'tw.gramset_id',
            '=',
            'gramsets.id'
        )
            ->where('tw.text_id', $text_id)
            ->whereIn('tw.w_id', $w_ids)
            ->where('tw.relevance', '>', 0);

        if ($checked_lemma_words) {
            $gramset_query->where(function ($query) use (
                $unchecked_w_ids,
                $checked_lemma_words
            ) {
                if ($unchecked_w_ids) {
                    $query->whereIn('tw.w_id', $unchecked_w_ids);
                }

                foreach ($checked_lemma_words as $lemma_id => $lemma_w_ids) {
                    $query->orWhere(function ($lemma_query) use (
                        $lemma_id,
                        $lemma_w_ids
                    ) {
                        $lemma_query
                            ->whereIn('tw.w_id', $lemma_w_ids)
                            ->whereRaw(
                                'EXISTS (
                                    SELECT 1
                                    FROM lemma_wordform lw
                                    WHERE lw.lemma_id = ?
                                        AND lw.wordform_id = tw.wordform_id
                                        AND lw.gramset_id = tw.gramset_id
                                )',
                                [(int) $lemma_id]
                            );
                    });
                }
            });
        }

        $gramset_rows = $gramset_query
            ->select([
                'gramsets.*',
                'tw.w_id',
                'tw.wordform_id',
                'tw.relevance',
            ])
            ->orderBy('gramsets.sequence_number')
            ->orderBy('gramsets.id')
            ->orderBy('tw.wordform_id')
            ->get();

        $wordforms = Wordform::whereIn('id', function ($query) use (
            $text_id,
            $w_ids
        ) {
            $query->select('wordform_id')
                ->from('text_wordform')
                ->where('text_id', $text_id)
                ->whereIn('w_id', $w_ids);
        })
            ->pluck('wordform', 'id')
            ->toArray();

        return [
            'meanings' => $meanings,
            'gramsets' => $this->groupByWord($gramset_rows),
            'wordforms' => $wordforms,
        ];
    }

    /**
     * Разделяет привязки каждого слова на проверенную и неподтверждённые.
     */
    protected function groupByWord($rows)
    {
        $result = [];

        foreach ($rows as $row) {
            $w_id = (int) $row->w_id;

            if (!isset($result[$w_id])) {
                $result[$w_id] = [
                    'checked' => null,
                    'unchecked' => [],
                ];
            }

            if ((int) $row->relevance > 1) {
                $result[$w_id]['checked'] = $row;
            } elseif ((int) $row->relevance === 1) {
                $result[$w_id]['unchecked'][] = $row;
            }
        }

        return $result;
    }
}
