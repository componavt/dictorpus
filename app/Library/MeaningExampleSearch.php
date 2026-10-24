<?php

namespace App\Library;

use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

use App\Models\Dict\Meaning;

class MeaningExampleSearch
{
    protected $source;
    protected $partner;
    protected $options;

    /**
     * Задаёт исходное значение, необязательного партнёра и условия поиска.
     */
    public function __construct(Meaning $source, ?Meaning $partner = null, array $options = [])
    {
        $this->source = $source;
        $this->partner = $partner;

        $this->options = array_merge([
            'distance_from' => -1,
            'distance_to' => 1,
            'partner_threshold' => 0,
            'same_gramset' => true,
            'source_threshold' => 0,
        ], $options);

        $this->validateOptions();

        if ($partner && (int) $source->lemma->lang_id !== (int) $partner->lemma->lang_id) {
            throw new \InvalidArgumentException('Source and partner meanings must have the same language.');
        }
    }

    /**
     * Проверяет пороги relevance, расстояние и режим согласования.
     */
    protected function validateOptions()
    {
        foreach (['source_threshold', 'partner_threshold'] as $field) {
            if (!in_array($this->options[$field], [0, 1], true)) {
                throw new \InvalidArgumentException($field . ' must be integer 0 or 1.');
            }
        }

        foreach (['distance_from', 'distance_to'] as $field) {
            if (!is_int($this->options[$field])) {
                throw new \InvalidArgumentException($field . ' must be an integer.');
            }
        }

        if ($this->options['distance_from'] > $this->options['distance_to']) {
            throw new \InvalidArgumentException('distance_from must not exceed distance_to.');
        }

        if (!is_bool($this->options['same_gramset'])) {
            throw new \InvalidArgumentException('same_gramset must be a boolean.');
        }
    }

    /**
     * Строит запрос употреблений одного значения или пар употреблений.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function query()
    {
        $query = DB::table('meaning_text as mt1')
            ->join('words as w1', function ($join) {
                $join->on('w1.id', '=', 'mt1.word_id')
                    ->on('w1.text_id', '=', 'mt1.text_id')
                    ->on('w1.s_id', '=', 'mt1.s_id')
                    ->on('w1.w_id', '=', 'mt1.w_id');
            })
            ->join('sentences as s', 's.id', '=', 'w1.sentence_id')
            ->where('mt1.meaning_id', $this->source->id)
            ->where(
                'mt1.relevance',
                '>',
                $this->options['source_threshold']
            )
            ->select([
                's.id as sentence_id',
                's.text_id',
                's.s_id',
                'w1.id as source_word_id',
                'w1.w_id as source_w_id',
                'w1.word as source_word',
                'w1.word_number as source_position',
                'mt1.relevance as source_relevance',
            ]);

        if ($this->partner) {
            $query = $this->addPartner($query);

            if ($this->options['same_gramset']) {
                $query = $this->applyCommonGramset($query);
            }
        }

        return $query;
    }

    /**
     * Ограничивает поиск наличием партнёра в заданном диапазоне позиций.
     */
    protected function addPartner(Builder $query)
    {
        return $query
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
            ->where('mt2.meaning_id', $this->partner->id)
            ->where(
                'mt2.relevance',
                '>',
                $this->options['partner_threshold']
            )
            ->whereRaw(
                'w2.word_number - w1.word_number BETWEEN ? AND ?',
                [
                    $this->options['distance_from'],
                    $this->options['distance_to'],
                ]
            )
            ->addSelect([
                'w2.id as partner_word_id',
                'w2.w_id as partner_w_id',
                'w2.word as partner_word',
                'w2.word_number as partner_position',
                'mt2.relevance as partner_relevance',
            ])
            ->selectRaw(
                'w2.word_number - w1.word_number as distance'
            );
    }

    /**
     * Требует общий неисключённый грамсет, допустимый для обеих лемм.
     */
    protected function applyCommonGramset(Builder $query)
    {
        return $query->whereRaw(
            'EXISTS (
                SELECT 1
                FROM text_wordform AS tw1
                JOIN lemma_wordform AS lw1
                    ON lw1.wordform_id = tw1.wordform_id
                    AND lw1.gramset_id = tw1.gramset_id
                JOIN text_wordform AS tw2
                    ON tw2.gramset_id = tw1.gramset_id
                JOIN lemma_wordform AS lw2
                    ON lw2.wordform_id = tw2.wordform_id
                    AND lw2.gramset_id = tw2.gramset_id
                WHERE tw1.word_id = w1.id
                    AND tw2.word_id = w2.id
                    AND tw1.relevance > 0
                    AND tw2.relevance > 0
                    AND lw1.lemma_id = ?
                    AND lw2.lemma_id = ?
            )',
            [
                $this->source->lemma_id,
                $this->partner->lemma_id,
            ]
        );
    }

    /**
     * Возвращает число совпадений и разных предложений.
     *
     * Совпадение — употребление source либо пара source + partner.
     *
     * @return array
     */
    public function statistics()
    {
        $query = $this->query();

        return [
            'matches' => (clone $query)->count(),
            'sentences' => (clone $query)
                ->distinct()
                ->count('s.id'),
        ];
    }

    /**
     * Возвращает ограниченную диагностическую выборку по оценке source.
     *
     * @return \Illuminate\Support\Collection
     */
    public function samples($limit = 5)
    {
        $limit = max(1, min(100, (int) $limit));

        $query = $this->query()
            ->addSelect('s.text_xml')
            ->orderBy('mt1.relevance', 'desc')
            ->orderBy('s.id')
            ->orderBy('w1.word_number')
            ->orderBy('w1.id');

        if ($this->partner) {
            $query->orderBy('w2.word_number')
                ->orderBy('w2.id');
        }

        $samples = [];

        foreach ($query->take($limit)->get() as $row) {
            $sample = (array) $row;

            $sample['sentence'] = trim(preg_replace(
                '/\s+/u',
                ' ',
                strip_tags((string) $sample['text_xml'])
            ));

            unset($sample['text_xml']);

            $samples[] = $sample;
        }

        return collect($samples);
    }

    /**
     * Возвращает страницу уникальных предложений.
     *
     * Порядок: максимальная оценка подходящего source, затем ID предложения.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateSentences(
        $per_page = 20,
        $page = null,
        $page_name = 'page'
    ) {
        $per_page = max(1, min(100, (int) $per_page));

        if ($page === null) {
            $page = Paginator::resolveCurrentPage($page_name);
        }

        $page = max(1, (int) $page);

        $total = (int) $this->query()
            ->distinct()
            ->count('s.id');

        $rows = $this->query()
            ->select([
                's.id as sentence_id',
                's.text_id',
                's.s_id',
            ])
            ->selectRaw('MAX(mt1.relevance) as source_relevance')
            ->selectRaw('COUNT(*) as match_count')
            ->groupBy('s.id')
            ->groupBy('s.text_id')
            ->groupBy('s.s_id')
            ->orderBy('source_relevance', 'desc')
            ->orderBy('s.id')
            ->skip(($page - 1) * $per_page)
            ->take($per_page)
            ->get();

        return new LengthAwarePaginator(
            $this->loadSentencePage($rows),
            $total,
            $per_page,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => $page_name,
            ]
        );
    }

    /**
     * Загружает тексты и все совпадения для предложений выбранной страницы.
     *
     * @return array
     */
    protected function loadSentencePage($rows)
    {
        $items = [];
        $sentence_ids = [];

        foreach ($rows as $row) {
            $sentence_id = (int) $row->sentence_id;

            $sentence_ids[] = $sentence_id;

            $items[$sentence_id] = [
                'sentence_id' => $sentence_id,
                'text_id' => (int) $row->text_id,
                's_id' => (int) $row->s_id,
                'source_relevance' => (int) $row->source_relevance,
                'match_count' => (int) $row->match_count,
                'matches' => [],
                'linked_w_ids' => [],
                'text_xml' => '',
                'sentence' => '',
            ];
        }

        if (!$sentence_ids) {
            return [];
        }

        $linked_words = DB::table('meaning_text as mt')
            ->join('words as w', function ($join) {
                $join->on('w.id', '=', 'mt.word_id')
                    ->on('w.text_id', '=', 'mt.text_id')
                    ->on('w.s_id', '=', 'mt.s_id')
                    ->on('w.w_id', '=', 'mt.w_id');
            })
            ->whereIn('w.sentence_id', $sentence_ids)
            ->where('mt.relevance', '>', 0)
            ->select([
                'w.sentence_id',
                'w.w_id',
            ])
            ->distinct()
            ->get();

        foreach ($linked_words as $word) {
            $sentence_id = (int) $word->sentence_id;
            $items[$sentence_id]['linked_w_ids'][] = (int) $word->w_id;
        }

        $sentences = DB::table('sentences')
            ->whereIn('id', $sentence_ids)
            ->select(['id', 'text_xml'])
            ->get();

        foreach ($sentences as $sentence) {
            $sentence_id = (int) $sentence->id;
            $text_xml = (string) $sentence->text_xml;

            $items[$sentence_id]['text_xml'] = $text_xml;
            $items[$sentence_id]['sentence'] = trim(preg_replace('/\s+/u', ' ', strip_tags($text_xml)));
        }

        $matches_query = $this->query()
            ->whereIn('s.id', $sentence_ids)
            ->orderBy('s.id')
            ->orderBy('mt1.relevance', 'desc')
            ->orderBy('w1.word_number')
            ->orderBy('w1.id');

        if ($this->partner) {
            $matches_query->orderBy('w2.word_number')
                ->orderBy('w2.id');
        }

        foreach ($matches_query->get() as $match) {
            $sentence_id = (int) $match->sentence_id;

            $items[$sentence_id]['matches'][] = (array) $match;
        }

        return array_values($items);
    }
}
