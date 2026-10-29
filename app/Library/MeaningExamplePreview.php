<?php

namespace App\Library;

use App\Models\User;
use App\Models\Dict\Meaning;

class MeaningExamplePreview
{
    /**
     * Готовит данные блока примеров словарной статьи.
     */
    public function forMeaning(
        Meaning $meaning,
        $start = 0,
        $update_examples = false
    ) {
        $limit = 5;
        $start = max(0, (int) $start);

        if (User::checkAccess('dict.edit') && ($update_examples || !$meaning->texts()->count())) {
            $meaning->reloadExamples();
        }

        $sentence_count = $meaning->countSentences(false);
        $sentence_total = $meaning->countSentences(true);

        // Включаем непроверенные, но не исключённые примеры.
        $sentences = $meaning->sentences(true, $limit, $start);

        $count = 1 + $start;

        return compact(
            'meaning',
            'limit',
            'start',
            'count',
            'sentence_count',
            'sentence_total',
            'sentences'
        );
    }
}
