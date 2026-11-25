<?php

namespace App\Library;

use Illuminate\Support\Facades\DB;

use App\Models\Corpus\Word;
use App\Models\Dict\Collocation;
use App\Models\Dict\CollocationExample;

class CollocationExampleLink
{
    /**
     * Прикрепляет проверенную пару употреблений.
     */
    public function attach($collocation_id, $text_id, $source_w_id, $partner_w_id)
    {
        return $this->saveDecision(
            $collocation_id,
            $text_id,
            $source_w_id,
            $partner_w_id,
            CollocationExample::STATUS_ATTACHED
        );
    }

    /**
     * Исключает проверенную пару употреблений из рассмотрения.
     */
    public function reject($collocation_id, $text_id, $source_w_id, $partner_w_id)
    {
        return $this->saveDecision(
            $collocation_id,
            $text_id,
            $source_w_id,
            $partner_w_id,
            CollocationExample::STATUS_REJECTED
        );
    }

    /**
     * Создаёт решение или меняет статус существующей записи.
     */
    protected function saveDecision($collocation_id, $text_id, $source_w_id, $partner_w_id, $status)
    {
        if (!in_array($status, [
            CollocationExample::STATUS_ATTACHED,
            CollocationExample::STATUS_REJECTED,
        ], true)) {
            abort(422, 'Некорректный статус примера.');
        }

        $text_id = (int)$text_id;
        $source_w_id = (int)$source_w_id;
        $partner_w_id = (int)$partner_w_id;

        if (
            $text_id < 1 || $source_w_id < 1 || $partner_w_id < 1
            || $source_w_id === $partner_w_id
        ) {
            abort(422, 'Некорректная пара употреблений.');
        }

        return DB::transaction(function () use (
            $collocation_id,
            $text_id,
            $source_w_id,
            $partner_w_id,
            $status
        ) {
            $collocation = Collocation::where('id', $collocation_id)
                ->lockForUpdate()->firstOrFail();

            $words = Word::where('text_id', $text_id)
                ->whereIn('w_id', [$source_w_id, $partner_w_id])
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('w_id');

            $source = $words->get($source_w_id);
            $partner = $words->get($partner_w_id);

            $sentence = $this->sentence($source, $partner);

            $this->checkMeaning($source, $collocation->meaning_id);
            $this->checkMeaning($partner, $collocation->collocate_meaning_id);

            $attributes = [
                'collocation_id' => (int)$collocation->id,
                'text_id' => $text_id,
                'source_w_id' => $source_w_id,
                'partner_w_id' => $partner_w_id,
            ];

            $existing = CollocationExample::where($attributes)->lockForUpdate()->first();

            if ($existing) {
                if ($existing->status !== $status) {
                    $existing->status = $status;
                    $existing->sentence_xml = $sentence->text_xml;
                    $existing->save();
                }

                return $existing;
            }

            return CollocationExample::create(array_merge($attributes, [
                'status' => $status,
                'sentence_xml' => $sentence->text_xml,
            ]));
        });
    }

    /**
     * Проверяет принадлежность обоих слов одному предложению.
     */
    protected function sentence($source, $partner)
    {
        if (
            !$source || !$partner || !$source->sentence_id
            || (int) $source->sentence_id !== (int) $partner->sentence_id
            || (int) $source->s_id !== (int) $partner->s_id
        ) {
            abort(422, 'Оба употребления должны находиться в одном предложении.');
        }

        $sentence = DB::table('sentences')
            ->where('id', $source->sentence_id)
            ->where('text_id', $source->text_id)
            ->where('s_id', $source->s_id)
            ->lockForUpdate()
            ->first();

        if (!$sentence || trim((string) $sentence->text_xml) === '') {
            abort(422, 'Предложение не найдено или не содержит текста.');
        }

        return $sentence;
    }

    /**
     * Проверяет подтверждённую привязку употребления к нужному значению.
     */
    protected function checkMeaning(Word $word, $meaning_id)
    {
        $link = DB::table('meaning_text')
            ->where('word_id', $word->id)
            ->where('text_id', $word->text_id)
            ->where('s_id', $word->s_id)
            ->where('w_id', $word->w_id)
            ->where('meaning_id', $meaning_id)
            ->where('relevance', '>', 1)
            ->lockForUpdate()
            ->first();

        if (!$link) {
            abort(422, 'Сначала подтвердите нужное значение каждого слова.');
        }
    }
}
