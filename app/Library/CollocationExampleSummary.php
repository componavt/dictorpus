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

            foreach ($this->corpusGroups($text) as $group) {
                $this->addGroup($result[$id]['groups'], $group['key'], $group['label'], $group['title'], $total);
            }
        }

        foreach ($result as &$summary) {
            ksort($summary['groups']);
            $summary['groups'] = array_values($summary['groups']);
        }
        unset($summary);

        return $result;
    }

    public function corpusGroups($text)
    {
        $unknown = trans('collocations.matrix_corpus_unknown');

        if (!$text || $text->corpuses->isEmpty()) {
            return [['key' => 'unknown', 'label' => $unknown, 'title' => $unknown]];
        }

        $groups = [];

        foreach ($text->corpuses->sortBy('id') as $corpus) {
            $key = 'corpus-' . $corpus->id;
            $label = $corpus->short;
            $title = $corpus->name;

            if ((int)$corpus->id === 1) {
                $ids = $shorts = $names = [];

                foreach ($text->dialects->sortBy('id') as $dialect) {
                    $ids[] = (int)$dialect->id;
                    $shorts[] = $dialect->short;
                    $names[] = $dialect->name;
                }

                $unknown_dialect = trans('collocations.matrix_dialect_unknown');
                $key .= ':' . implode(',', $ids);
                $label .= ' (' . ($ids ? implode(', ', $shorts) : $unknown_dialect) . ')';
                $title .= ' (' . ($ids ? implode(', ', $names) : $unknown_dialect) . ')';
            }

            $groups[] = ['key' => $key, 'label' => $label, 'title' => $title];
        }

        return $groups;
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
