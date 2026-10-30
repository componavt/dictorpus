<?php

namespace App\Library;

use Illuminate\Support\Facades\DB;

class SynsetCollocationCandidateData
{
    /**
     * Собирает общую матрицу корпусных кандидатов.
     *
     * Строки — значения партнёров.
     * Ячейки — статистика для конкретного участника синсета.
     */
    public function prepare($members, array $options = [], $partner_pos_id = 0)
    {
        $rows = [];

        foreach ($members as $member) {
            $source_id = (int) $member->id;

            $search = new MeaningExampleSearch($member, null, $options);

            $candidates = $search
                ->collocationCandidatesQuery($partner_pos_id)
                ->get();

            foreach ($candidates as $candidate) {
                $partner_id = (int) $candidate->meaning_id;

                if (!isset($rows[$partner_id])) {
                    $rows[$partner_id] = [
                        'meaning_id' => $partner_id,
                        'lemma_id' => (int) $candidate->lemma_id,
                        'lemma' => $candidate->lemma,
                        'meaning_n' => (int) $candidate->meaning_n,
                        'cells' => [],
                    ];
                }

                $rows[$partner_id]['cells'][$source_id] = [
                    'sentences' => (int) $candidate->sentences,
                    'texts' => (int) $candidate->texts,
                    'checked_sentences' => (int) $candidate->checked_sentences,
                    'unchecked_sentences' => (int) $candidate->unchecked_sentences,
                    'recorded' => false,
                    'collocation_id' => null,
                ];
            }
        }

        $rows = $this->markRecorded($rows);
        $rows = array_values($rows);

        usort($rows, function ($left, $right) {
            $comparison = strcmp($left['lemma'], $right['lemma']);

            if ($comparison !== 0) {
                return $comparison;
            }

            if ($left['meaning_n'] !== $right['meaning_n']) {
                return $left['meaning_n'] <=> $right['meaning_n'];
            }

            return $left['meaning_id'] <=> $right['meaning_id'];
        });

        return $rows;
    }

    /**
     * Отмечает уже внесённые пары среди корпусных находок.
     */
    protected function markRecorded(array $rows)
    {
        if (!$rows) {
            return $rows;
        }

        $source_ids = [];

        foreach ($rows as $row) {
            foreach (array_keys($row['cells']) as $source_id) {
                $source_ids[$source_id] = $source_id;
            }
        }

        $collocations = DB::table('collocations')
            ->whereIn('meaning_id', array_values($source_ids))
            ->whereIn('collocate_meaning_id', array_keys($rows))
            ->select([
                'id',
                'meaning_id',
                'collocate_meaning_id',
            ])
            ->orderBy('id')
            ->get();

        foreach ($collocations as $collocation) {
            $source_id = (int) $collocation->meaning_id;
            $partner_id = (int) $collocation->collocate_meaning_id;

            if (!isset($rows[$partner_id]['cells'][$source_id])) {
                continue;
            }

            $cell = &$rows[$partner_id]['cells'][$source_id];

            if (!$cell['recorded']) {
                $cell['recorded'] = true;
                $cell['collocation_id'] = (int) $collocation->id;
            }

            unset($cell);
        }

        return $rows;
    }
}
