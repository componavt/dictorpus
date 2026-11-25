<?php

namespace App\Library;

use Illuminate\Support\Facades\DB;

use App\Models\Dict\Collocation;
use App\Models\Dict\CollocationExample;
use App\Models\Dict\Meaning;

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

            $attached[$key] = $example;
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
                        'collocation_id' => (int) $collocation->id,
                        'text_id' => (int) $example['text_id'],
                        'source_w_id' => (int) $match['source_w_id'],
                        'partner_w_id' => (int) $match['partner_w_id'],
                        'status' => isset($attached[$key]) ? $attached[$key]->status : null,
                        'decision_id' => isset($attached[$key]) ? (int)$attached[$key]->id : null,
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

    /**
     * Готовит действия без выбранного партнёра в фильтре поиска.
     */
    public function forSource(Meaning $source, array $items)
    {
        if (!$items) {
            return [];
        }

        $partners = collect($this->confirmedPartners($source, $items));

        if (!$partners->count()) {
            return [];
        }

        $attached = $this->sourceAttachments($partners, $items);
        $partners_by_sentence = $partners->groupBy('sentence_id');
        $actions = [];

        foreach ($items as $item) {
            foreach ($item['source_examples'] as $example) {
                if (
                    (int) $example['source_relevance'] <= 1
                    || !$this->visible($example, $example['source_w_id'])
                ) {
                    continue;
                }

                foreach ($partners_by_sentence->get($item['sentence_id'], []) as $partner) {
                    if (
                        (int) $partner->partner_w_id === (int) $example['source_w_id']
                        || !$this->visible($example, $partner->partner_w_id)
                    ) {
                        continue;
                    }

                    $key = $partner->collocation_id . ':' . $this->key(
                        $example['text_id'],
                        $example['source_w_id'],
                        $partner->partner_w_id
                    );

                    $actions[$example['example_id']][$key] =
                        $this->sourceAction($example, $partner, $attached[$key] ?? null);
                }
            }
        }

        return $actions;
    }

    /**
     * Загружает подтверждённых партнёров уже внесённых сочетаний
     * только для предложений текущей страницы.
     */
    protected function confirmedPartners(Meaning $source, array $items)
    {
        return DB::table('words as w')
            ->join('meaning_text as mt', function ($join) {
                $join->on('mt.word_id', '=', 'w.id')
                    ->on('mt.text_id', '=', 'w.text_id')
                    ->on('mt.s_id', '=', 'w.s_id')
                    ->on('mt.w_id', '=', 'w.w_id');
            })
            ->join('collocations as c', 'c.collocate_meaning_id', '=', 'mt.meaning_id')
            ->where('c.meaning_id', $source->id)
            ->where('mt.relevance', '>', 1)
            ->whereIn('w.sentence_id', array_column($items, 'sentence_id'))
            ->select([
                'c.id as collocation_id',
                'w.sentence_id',
                'w.w_id as partner_w_id',
                'w.word as partner_word',
                'w.word_number as partner_position',
            ])
            ->distinct()
            ->orderBy('w.sentence_id')
            ->orderBy('w.word_number')
            ->orderBy('c.id')
            ->get();
    }

    /**
     * Индексирует существующие прикрепления по сочетанию и паре употреблений.
     */
    protected function sourceAttachments($partners, array $items)
    {
        $examples = CollocationExample::whereIn('collocation_id', $partners->pluck('collocation_id')->unique()->all())
            ->whereIn('text_id', array_column($items, 'text_id'))
            ->get();

        $attached = [];

        foreach ($examples as $example) {
            $key = $example->collocation_id . ':' . $this->key(
                $example->text_id,
                $example->source_w_id,
                $example->partner_w_id
            );

            $attached[$key] = $example;
        }

        return $attached;
    }

    /**
     * Описывает действие для одной конкретной пары употреблений.
     */
    protected function sourceAction(array $example, $partner, $decision)
    {
        return [
            'collocation_id' => (int) $partner->collocation_id,
            'text_id' => (int) $example['text_id'],
            'source_w_id' => (int) $example['source_w_id'],
            'partner_w_id' => (int) $partner->partner_w_id,
            'status' => $decision ? $decision->status : null,
            'decision_id' => $decision ? (int)$decision->id : null,
            'label' => trans('collocation_examples.pair', [
                'source' => $example['source_word'],
                'partner' => $partner->partner_word,
                'from' => $example['source_position'],
                'to' => $partner->partner_position,
            ]),
        ];
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
