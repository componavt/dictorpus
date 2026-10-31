<?php

namespace App\Library;

use App\Models\Dict\Collocation;
use App\Models\Dict\CollocationExample;

class CollocationExampleData
{
    /**
     * Готовит действия для конкретных совпадений на странице.
     */
    public function forPage(Collocation $collocation, array $items)
    {
        if (!$items) {
            return [];
        }

        $attached = [];

        $examples = CollocationExample::where('collocation_id', $collocation->id)
            ->whereIn('text_id', array_column($items, 'text_id'))
            ->get();

        foreach ($examples as $example) {
            $key = $this->key(
                $example->text_id,
                $example->source_w_id,
                $example->partner_w_id
            );

            $attached[$key] = (int) $example->id;
        }

        $actions = [];

        foreach ($items as $item) {
            foreach ($item['source_examples'] as $example) {
                foreach ($example['matches'] as $match) {
                    if (
                        (int) $match['source_relevance'] <= 1
                        || (int) $match['partner_relevance'] <= 1
                        || !$this->visible($example, $match['source_w_id'])
                        || !$this->visible($example, $match['partner_w_id'])
                    ) {
                        continue;
                    }

                    $key = $this->key(
                        $example['text_id'],
                        $match['source_w_id'],
                        $match['partner_w_id']
                    );

                    $actions[$example['example_id']][$match['partner_w_id']] = [
                        'text_id' => (int) $example['text_id'],
                        'source_w_id' => (int) $match['source_w_id'],
                        'partner_w_id' => (int) $match['partner_w_id'],
                        'attached' => isset($attached[$key]),
                        'attachment_id' => $attached[$key] ?? null,
                        'label' => trans('collocation_examples.pair', [
                            'source' => $match['source_word'],
                            'partner' => $match['partner_word'],
                            'from' => $match['source_position'],
                            'to' => $match['partner_position'],
                        ]),
                    ];
                }
            }
        }

        return $actions;
    }

    protected function key($text_id, $source_w_id, $partner_w_id)
    {
        return $text_id . ':' . $source_w_id . ':' . $partner_w_id;
    }

    /**
     * Не предлагает прикрепить употребление, скрытое за пределами фрагмента.
     */
    protected function visible(array $example, $w_id)
    {
        if (!$example['is_fragment']) {
            return true;
        }

        return (bool) preg_match(
            "~<w\\b[^>]*\\bid=[\"']" . (int) $w_id . "[\"']~u",
            $example['text_xml']
        );
    }
}
