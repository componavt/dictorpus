<?php

namespace App\Library;

use Illuminate\Support\Facades\DB;

class MeaningExampleAvailability
{
    /**
     * Проверяет пары участников и партнёров, представленных в матрице.
     */
    public function forMatrix(array $matrix)
    {
        $source_ids = [];
        $partner_ids = [];

        foreach ($matrix['columns'] as $member) {
            $source_ids[] = (int) $member->id;
        }

        foreach ($matrix['groups'] as $group) {
            foreach ($group['rows'] as $row) {
                $partner_ids[] = (int) $row['meaning']->id;
            }
        }

        return $this->pairs($source_ids, $partner_ids);
    }

    /**
     * Возвращает множество пар с примерами во всём предложении.
     *
     * Результат: [source_meaning_id => [partner_meaning_id => true]].
     */
    public function pairs(array $source_ids, array $partner_ids)
    {
        $source_ids = array_values(array_unique(array_map(
            'intval',
            $source_ids
        )));

        $partner_ids = array_values(array_unique(array_map(
            'intval',
            $partner_ids
        )));

        if (!$source_ids || !$partner_ids) {
            return [];
        }

        $rows = DB::table('meaning_text as mt1')
            ->join('words as w1', function ($join) {
                $join->on('w1.id', '=', 'mt1.word_id')
                    ->on('w1.text_id', '=', 'mt1.text_id')
                    ->on('w1.s_id', '=', 'mt1.s_id')
                    ->on('w1.w_id', '=', 'mt1.w_id');
            })
            ->join('sentences as s', 's.id', '=', 'w1.sentence_id')
            ->join('words as w2', function ($join) {
                $join->on('w2.sentence_id', '=', 'w1.sentence_id')
                    ->on('w2.text_id', '=', 'w1.text_id')
                    ->on('w2.s_id', '=', 'w1.s_id')
                    ->on('w2.id', '<>', 'w1.id');
            })
            ->join('meaning_text as mt2', function ($join) {
                $join->on('mt2.word_id', '=', 'w2.id')
                    ->on('mt2.text_id', '=', 'w2.text_id')
                    ->on('mt2.s_id', '=', 'w2.s_id')
                    ->on('mt2.w_id', '=', 'w2.w_id');
            })
            ->join('meanings as m1', 'm1.id', '=', 'mt1.meaning_id')
            ->join('lemmas as l1', 'l1.id', '=', 'm1.lemma_id')
            ->join('meanings as m2', 'm2.id', '=', 'mt2.meaning_id')
            ->join('lemmas as l2', function ($join) {
                $join->on('l2.id', '=', 'm2.lemma_id')
                    ->on('l2.lang_id', '=', 'l1.lang_id');
            })
            ->whereIn('mt1.meaning_id', $source_ids)
            ->whereIn('mt2.meaning_id', $partner_ids)
            ->where('mt1.relevance', '>', 0)
            ->where('mt2.relevance', '>', 0)
            ->select([
                'mt1.meaning_id as source_meaning_id',
                'mt2.meaning_id as partner_meaning_id',
            ])
            ->distinct()
            ->get();

        $result = [];

        foreach ($rows as $row) {
            $source_id = (int) $row->source_meaning_id;
            $partner_id = (int) $row->partner_meaning_id;

            $result[$source_id][$partner_id] = true;
        }

        return $result;
    }
}
