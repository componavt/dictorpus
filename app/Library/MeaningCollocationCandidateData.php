<?php

namespace App\Library;

use App\Models\Dict\Meaning;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class MeaningCollocationCandidateData
{
    public function prepare(array $items)
    {
        if (!$items) {
            return [];
        }

        $meanings = Meaning::with(['lemma.pos', 'meaningTexts.lang'])
            ->whereIn('id', array_column($items, 'meaning_id'))
            ->get()->keyBy('id');

        $groups = [];

        foreach ($items as $item) {
            $meaning = $meanings->get($item['meaning_id']);

            if (!$meaning) {
                continue;
            }

            $lemma_id = (int) $meaning->lemma_id;

            if (!isset($groups[$lemma_id])) {
                $groups[$lemma_id] = [
                    'lemma_id' => $lemma_id,
                    'lemma' => $meaning->lemma->lemma,
                    'pos' => $meaning->lemma->pos ? $meaning->lemma->pos->name : '',
                    'items' => [],
                ];
            }

            $item['gloss'] = $this->gloss($meaning);
            $groups[$lemma_id]['items'][] = $item;
        }

        return array_values($groups);
    }

    protected function gloss(Meaning $meaning)
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $texts = $meaning->meaningTexts->filter(function ($text) {
            return trim((string) $text->meaning_text) !== '';
        });

        $preferred = $texts->filter(function ($text) use ($locale) {
            return $text->lang && $text->lang->code === $locale;
        });

        if ($preferred->count()) {
            $texts = $preferred;
        }

        $glosses = [];

        foreach ($texts as $text) {
            $prefix = $text->lang && $text->lang->code !== $locale
                ? $text->lang->code . ': ' : '';
            $glosses[] = $prefix . $text->meaning_text;
        }

        return implode('; ', $glosses);
    }
}
