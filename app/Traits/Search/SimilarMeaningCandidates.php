<?php

namespace App\Traits\Search;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Trait SimilarMeaningCandidates
 *
 * Поведение для экспертного подбора кандидатов:
 * - есть donor meaning с уже существующим EN;
 * - есть target meaning без EN;
 * - при совпадении review-key donor EN предлагается для target.
 *
 * Это НЕ fully automatic safe-backfill, а логика для review-интерфейса.
 */
trait SimilarMeaningCandidates
{
    /**
     * ID языков в таблице langs.
     *
     * По текущим договорённостям проекта:
     * - RU = 2
     * - EN = 3
     */
    public static $reviewLangRu = 2;
    public static $reviewLangEn = 3;

    /**
     * Загружает meanings для review-задачи.
     *
     * Возвращаем плоский массив строк:
     * - meaning_id
     * - lemma_id
     * - meaning_n
     * - lemma
     * - lemma_lang_id
     * - pos_id
     * - meaning_ru
     * - meaning_en
     *
     * Важно:
     * - работаем по meaning_texts;
     * - is_norm не учитываем;
     * - для Laravel 5.2 используем foreach, а не collection->map().
     *
     * @return array
     */
    public static function loadMeaningRowsForReview()
    {
        $rows = DB::table('meanings as m')
            ->join('lemmas as l', 'l.id', '=', 'm.lemma_id')
            ->join('meaning_texts as ru', function ($join) {
                $join->on('ru.meaning_id', '=', 'm.id')
                    ->where('ru.lang_id', '=', static::$reviewLangRu);
            })
            ->leftJoin('meaning_texts as en', function ($join) {
                $join->on('en.meaning_id', '=', 'm.id')
                    ->where('en.lang_id', '=', static::$reviewLangEn);
            })
            ->select([
                'm.id as meaning_id',
                'm.lemma_id',
                'm.meaning_n',
                'l.lemma',
                'l.lang_id as lemma_lang_id',
                'l.pos_id',
                'ru.meaning_text as meaning_ru',
                'en.meaning_text as meaning_en',
            ])
            ->orderBy('m.id')
            ->get();

        $result = [];

        foreach ($rows as $row) {
            $result[] = [
                'meaning_id' => (int) $row->meaning_id,
                'lemma_id' => (int) $row->lemma_id,
                'meaning_n' => isset($row->meaning_n) ? (int) $row->meaning_n : 0,
                'lemma' => (string) $row->lemma,
                'lemma_lang_id' => isset($row->lemma_lang_id) ? (int) $row->lemma_lang_id : 0,
                'pos_id' => $row->pos_id !== null ? (int) $row->pos_id : null,
                'meaning_ru' => (string) $row->meaning_ru,
                'meaning_en' => $row->meaning_en !== null ? (string) $row->meaning_en : null,
            ];
        }

        return $result;
    }

    /**
     * Канонизирует английский перевод.
     *
     * Пустая строка трактуется как отсутствие перевода.
     *
     * @param string|null $text
     * @return string
     */
    public static function canonicalEnglish($text)
    {
        return trim((string) ($text ?: ''));
    }

    /**
     * Нормализация русского meaning_text для review-задачи.
     *
     * ВАЖНО:
     * Это более "мягкая" логика, чем в строго безопасном backfill.
     * Сейчас версия всё ещё достаточно консервативна:
     * - trim
     * - схлопывание пробелов
     *
     * Потом сюда можно будет добавить:
     * - удаление внешних скобок;
     * - усечение хвоста после ';';
     * - другие review-friendly эвристики.
     *
     * @param string|null $text
     * @return string
     */
    public static function normalizeReviewMeaningRu($text)
    {
        $text = trim((string) ($text ?: ''));
        if ($text === '') {
            return '';
        }

        $text = preg_replace('/\s+/u', ' ', $text);

        return trim((string) $text);
    }

    /**
     * Строит review-ключ.
     *
     * В первой версии:
     *   pos_id + normalized meaning_ru
     *
     * @param int|null $posId
     * @param string|null $meaningRu
     * @return string
     */
    public static function buildReviewKey($posId, $meaningRu)
    {
        $normalized = static::normalizeReviewMeaningRu($meaningRu);
        if ($normalized === '') {
            return '';
        }

        $posPart = $posId !== null ? (string) $posId : 'UNKNOWN';

        return $posPart . "\t" . $normalized;
    }

    /**
     * Собирает donor/target-кандидатов для web review.
     *
     * donor:
     * - уже имеет EN
     *
     * target:
     * - не имеет EN
     *
     * Условия первой версии:
     * - совпадает pos_id
     * - совпадает review_key
     *
     * @param int $limit
     * @return array
     */
    public static function buildSimilarMeaningCandidates($limit = 50)
    {
        $rows = static::loadMeaningRowsForReview();

        $donorsByKey = [];
        $targets = [];

        /**
         * Крупный блок:
         * один проход по всем rows.
         * donors группируем по review_key сразу в hash-map,
         * targets просто собираем в список.
         * Это заменяет O(targets * donors) на O(n).
         */
        foreach ($rows as $row) {
            $canonicalEn = static::canonicalEnglish($row['meaning_en']);

            if ($canonicalEn !== '') {
                $key = static::buildReviewKey($row['pos_id'], $row['meaning_ru']);
                if ($key === '') {
                    continue;
                }

                if (!isset($donorsByKey[$key])) {
                    $donorsByKey[$key] = [];
                }

                $donorsByKey[$key][] = $row;
            } else {
                $targets[] = $row;
            }
        }

        $candidates = [];

        /**
         * Крупный блок:
         * для каждого target делаем O(1) lookup по review_key
         * вместо перебора всех donors.
         */
        foreach ($targets as $target) {
            $targetKey = static::buildReviewKey($target['pos_id'], $target['meaning_ru']);
            if ($targetKey === '') {
                continue;
            }

            if (!isset($donorsByKey[$targetKey])) {
                continue;
            }

            foreach ($donorsByKey[$targetKey] as $donor) {
                if ((int) $donor['meaning_id'] === (int) $target['meaning_id']) {
                    continue;
                }

                $candidates[] = [
                    'source_meaning_id' => $donor['meaning_id'],
                    'source_lemma_id' => $donor['lemma_id'],
                    'source_lemma' => $donor['lemma'],
                    'source_meaning_n' => $donor['meaning_n'],
                    'source_meaning_ru' => $donor['meaning_ru'],
                    'source_meaning_en' => $donor['meaning_en'],

                    'target_meaning_id' => $target['meaning_id'],
                    'target_lemma_id' => $target['lemma_id'],
                    'target_lemma' => $target['lemma'],
                    'target_meaning_n' => $target['meaning_n'],
                    'target_meaning_ru' => $target['meaning_ru'],
                    'proposed_meaning_en' => $donor['meaning_en'],

                    'pos_id' => $target['pos_id'],
                    'review_key' => $targetKey,
                ];

                if ($limit > 0 && count($candidates) >= (int) $limit) {
                    return $candidates;
                }
            }
        }

        return $candidates;
    }

    /**
     * Сохраняет подтверждённые экспертом EN meaning_texts.
     *
     * Формат входного массива:
     * [
     *   [
     *     'target_meaning_id' => ...,
     *     'approved' => 1,
     *     'meaning_en' => '...'
     *   ],
     *   ...
     * ]
     *
     * Правила:
     * - сохраняем только approved = 1;
     * - если EN уже существует, пропускаем;
     * - ничего не перезаписываем;
     * - создаём новую строку в meaning_texts.
     *
     * @param array $rows
     * @return array
     */
    public static function saveApprovedMeaningTexts(array $rows)
    {
        $inserted = 0;
        $skipped = 0;
        $timestamp = Carbon::now();

        DB::transaction(function () use ($rows, $timestamp, &$inserted, &$skipped) {
            /**
             * Крупный блок:
             * сохраняем только подтверждённые строки.
             */
            foreach ($rows as $row) {
                $approved = !empty($row['approved']);
                $meaningId = isset($row['target_meaning_id']) ? (int) $row['target_meaning_id'] : 0;
                $meaningEn = isset($row['meaning_en']) ? trim((string) $row['meaning_en']) : '';

                if (!$approved || $meaningId <= 0 || $meaningEn === '') {
                    $skipped++;
                    continue;
                }

                $exists = DB::table('meaning_texts')
                    ->where('meaning_id', $meaningId)
                    ->where('lang_id', static::$reviewLangEn)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                DB::table('meaning_texts')->insert([
                    'meaning_id' => $meaningId,
                    'lang_id' => static::$reviewLangEn,
                    'meaning_text' => $meaningEn,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);

                $inserted++;
            }
        });

        return [
            'inserted' => $inserted,
            'skipped' => $skipped,
        ];
    }

    /**
     * Собирает кандидатов, сгруппированных по review_key (нормализованный
     * русский перевод + pos_id), а внутри — по целевому meaning.
     *
     * Структура результата:
     * [
     *   'review_key' => [
     *      'review_key' => '...',
     *      'meaning_ru' => '...',          // представительный текст группы
     *      'targets' => [
     *          target_meaning_id => [
     *              'target_meaning_id' => ...,
     *              'target_lemma_id' => ...,
     *              'target_lemma' => ...,
     *              'target_meaning_n' => ...,
     *              'target_meaning_ru' => ...,
     *              'proposed_meaning_en' => ...,   // из первого source
     *              'sources' => [
     *                  [
     *                      'source_meaning_id' => ...,
     *                      'source_lemma_id' => ...,
     *                      'source_lemma' => ...,
     *                      'source_meaning_n' => ...,
     *                      'source_meaning_ru' => ...,
     *                      'source_meaning_en' => ...,
     *                  ],
     *                  ...
     *              ],
     *          ],
     *          ...
     *      ],
     *   ],
     *   ...
     * ]
     *
     * @param int $limitGroups   сколько review-групп вернуть
     * @return array
     */
    public static function buildSimilarMeaningGroups($limitGroups = 50)
    {
        $flat = static::buildSimilarMeaningCandidates(5000);

        $groups = [];

        /**
         * Крупный блок:
         * раскладываем плоский список кандидатов в дерево
         * review_key -> target_meaning_id -> sources[].
         */
        foreach ($flat as $row) {
            $key = $row['review_key'];

            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'review_key' => $key,
                    'meaning_ru' => $row['target_meaning_ru'],
                    'targets' => [],
                ];
            }

            $targetId = $row['target_meaning_id'];

            if (!isset($groups[$key]['targets'][$targetId])) {
                $groups[$key]['targets'][$targetId] = [
                    'target_meaning_id' => $row['target_meaning_id'],
                    'target_lemma_id' => $row['target_lemma_id'],
                    'target_lemma' => $row['target_lemma'],
                    'target_meaning_n' => $row['target_meaning_n'],
                    'target_meaning_ru' => $row['target_meaning_ru'],
                    'proposed_meaning_en' => $row['proposed_meaning_en'],
                    'sources' => [],
                ];
            }

            $groups[$key]['targets'][$targetId]['sources'][] = [
                'source_meaning_id' => $row['source_meaning_id'],
                'source_lemma_id' => $row['source_lemma_id'],
                'source_lemma' => $row['source_lemma'],
                'source_meaning_n' => $row['source_meaning_n'],
                'source_meaning_ru' => $row['source_meaning_ru'],
                'source_meaning_en' => $row['source_meaning_en'],
            ];
        }

        /**
         * Сортировка групп по русскому толкованию,
         * чтобы близкие значения шли подряд.
         */
        uasort($groups, function ($a, $b) {
            return strnatcasecmp($a['meaning_ru'], $b['meaning_ru']);
        });

        if ($limitGroups > 0 && count($groups) > $limitGroups) {
            $groups = array_slice($groups, 0, $limitGroups, true);
        }

        return $groups;
    }
}
