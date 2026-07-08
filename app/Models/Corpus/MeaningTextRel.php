<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

use App\Models\Corpus\TextWordform;
use App\Models\Corpus\Text;

use App\Models\Dict\Lang;
use App\Models\Dict\Meaning;
use App\Models\Dict\MeaningText;

class MeaningTextRel extends Model
{
    protected $table = 'meaning_text';

    public $timestamps = false;


    public static function updateExamples(array $relevances)
    {
        foreach ($relevances as $key => $value) {
            if (preg_match("/^(\d+)\_(\d+)_(\d+)_(\d+)$/", $key, $regs)) {
                self::updateExample((int)$value, (int)$regs[1], (int)$regs[2], (int)$regs[4]);
            }
        }
    }

    public static function updateExample(int $relevance, int $meaning_id, int $text_id, int $w_id)
    {
        DB::transaction(function () use ($relevance, $meaning_id, $text_id, $w_id) {
            if ($relevance == 1) { // не выставлена оценка  — проверяем конфликт с другим значением
                if (self::existsPositiveRelevance($text_id, $w_id, $meaning_id)) { // этот пример привязан к другому значению
                    $relevance = 0;
                }
            } elseif ($relevance != 0) { // положительная оценка — гасим все прочие значения слова
                self::setNegativeToUndefOthers($text_id, $w_id, $meaning_id);
            }

            DB::statement(
                'UPDATE meaning_text
                SET relevance = 0
                WHERE meaning_id <> ?
                AND text_id = ?
                AND w_id = ?',
                [$meaning_id, $text_id, $w_id]
            );

            if ($relevance > 1) {
                TextWordform::updateWordformLinksAfterCheckExample($text_id, $w_id, $meaning_id);
            }
        });
    }

    /** ищем другие значения лемм с положительной оценкой
     *   
     * Проверяет, привязан ли этот пример (text_id, w_id) уже к ДРУГОМУ значению
     * с положительной релевантностью. Вызывается ТОЛЬКО внутри транзакции
     * updateExample() и обязательно с блокировкой строк (lockForUpdate),
     * иначе гонка между двумя одновременными кликами возможна.
     */
    public static function existsPositiveRelevance(int $text_id, int $w_id, int $meaning_id)
    {
        return DB::table('meaning_text')
            ->where('text_id', $text_id)
            ->where('w_id', $w_id)
            ->where('meaning_id', '<>', $meaning_id)
            ->where('relevance', '>', 1)
            ->lockForUpdate()
            ->exists();
    }

    // всем значениям с неопределенными оценками проставим отрицательные
    public static function setNegativeToUndefOthers(int $text_id, int $w_id, int $meaning_id)
    {
        DB::statement('UPDATE meaning_text SET relevance=0' .
            ' WHERE meaning_id <> ' . $meaning_id .
            ' AND text_id=' . $text_id .
            ' AND w_id=' . $w_id);
    }

    public static function preparationForExampleEdit($example_id)
    {
        if (!preg_match("/^(\d+)_(\d+)_(\d+)$/", $example_id, $regs)) {
            return [NULL, NULL, NULL, NULL];
        }
        $text_id = (int)$regs[1];
        $s_id = (int)$regs[2];
        $w_id = (int)$regs[3];

        $sentence = Text::extractSentence($text_id, $s_id, $w_id);

        $meanings = Meaning::join('meaning_text', 'meanings.id', '=', 'meaning_text.meaning_id')
            ->where('text_id', $text_id)
            ->where('s_id', $s_id)
            ->where('w_id', $w_id)
            ->get();
        $meaning_texts = [];

        foreach ($meanings as $meaning) {
            $langs_for_meaning = Lang::getListWithPriority($meaning->lemma->lang_id);
            foreach ($langs_for_meaning as $lang_id => $lang_text) {
                $meaning_text_obj = MeaningText::where('lang_id', $lang_id)->where('meaning_id', $meaning->id)->first();
                if ($meaning_text_obj) {
                    $meaning_texts[$meaning->id][$lang_text] = $meaning_text_obj->meaning_text;
                }
            }
        }

        return [$sentence, $meanings, $meaning_texts, $w_id];
    }

    /**
     * Update meaning-text links after choosing gramset.
     * убираем те значения, у которых нет выбранного грамсета.
     * 
     * @param int $text_id
     * @param int $w_id
     * @param int $gramset_id
     */
    public static function updateMeaningLinksAfterCheckExample($text_id, $w_id, $gramset_id)
    {
        DB::statement(
            'UPDATE meaning_text mt
             JOIN meanings m ON m.id = mt.meaning_id
             JOIN lemmas l ON l.id = m.lemma_id
             SET mt.relevance = 0
             WHERE mt.text_id = ?
               AND mt.w_id = ?
               AND l.pos_id NOT IN (
                   SELECT pos_id FROM gramset_pos WHERE gramset_id = ?
               )',
            [$text_id, $w_id, $gramset_id]
        );
    }
}
