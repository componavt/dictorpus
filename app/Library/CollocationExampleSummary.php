<?php

namespace App\Library;

use Illuminate\Support\Facades\DB;
use App\Models\Corpus\Text;

class CollocationExampleSummary
{
    public function forMatrix(array $matrix)
    {
        $ids = [];

        foreach ($matrix['groups'] as $group) {
            foreach ($group['rows'] as $row) {
                foreach ($row['cells'] as $collocation) {
                    if ($collocation) {
                        $ids[(int)$collocation->id] = (int)$collocation->id;
                    }
                }
            }
        }

        if (!$ids) {
            return [];
        }

        $rows = DB::table('collocation_examples')
            ->whereIn('collocation_id', array_values($ids))
            ->select('collocation_id', 'text_id', DB::raw('COUNT(*) as total'))
            ->groupBy('collocation_id', 'text_id')
            ->orderBy('collocation_id')
            ->orderBy('text_id')
            ->get();

        $text_ids = [];
        foreach ($rows as $row) {
            $text_ids[(int)$row->text_id] = (int)$row->text_id;
        }

        if (!$text_ids) {
            return [];
        }

        $texts = Text::with(['corpuses', 'dialects'])
            ->whereIn('id', array_values($text_ids))
            ->get(['id'])
            ->keyBy('id');

        $result = [];

        foreach ($rows as $row) {
            $id = (int)$row->collocation_id;
            $total = (int)$row->total;

            if (!isset($result[$id])) {
                $result[$id] = ['total' => 0, 'groups' => []];
            }

            $result[$id]['total'] += $total;
            $text = $texts->get((int)$row->text_id);

            if (!$text || $text->corpuses->isEmpty()) {
                $this->addGroup(
                    $result[$id]['groups'],
                    'unknown',
                    trans('collocations.matrix_corpus_unknown'),
                    trans('collocations.matrix_corpus_unknown'),
                    $total
                );
                continue;
            }

            foreach ($text->corpuses->sortBy('id') as $corpus) {
                $key = 'corpus-' . $corpus->id;
                $label = $corpus->short;
                $title = $corpus->name;

                if ((int)$corpus->id === 1) {
                    $dialect_ids = [];
                    $dialect_shorts = [];
                    $dialect_names = [];

                    foreach ($text->dialects->sortBy('id') as $dialect) {
                        $dialect_ids[] = (int)$dialect->id;
                        $dialect_shorts[] = $dialect->short;
                        $dialect_names[] = $dialect->name;
                    }

                    $key .= ':' . implode(',', $dialect_ids);

                    if ($dialect_ids) {
                        $label .= ' (' . implode(', ', $dialect_shorts) . ')';
                        $title .= ' (' . implode(', ', $dialect_names) . ')';
                    } else {
                        $unknown = trans('collocations.matrix_dialect_unknown');
                        $label .= ' (' . $unknown . ')';
                        $title .= ' (' . $unknown . ')';
                    }
                }

                $this->addGroup($result[$id]['groups'], $key, $label, $title, $total);
            }
        }

        foreach ($result as &$summary) {
            ksort($summary['groups']);
            $summary['groups'] = array_values($summary['groups']);
        }
        unset($summary);

        return $result;
    }

    protected function addGroup(array &$groups, $key, $label, $title, $total)
    {
        if (!isset($groups[$key])) {
            $groups[$key] = [
                'label' => $label,
                'title' => $title,
                'total' => 0,
            ];
        }

        $groups[$key]['total'] += $total;
    }
}
