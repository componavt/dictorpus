<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

use App\Models\Corpus\Text;
use App\Models\Dict\Meaning;

class TextWordform extends Model
{
    protected $table = 'text_wordform';

    public $timestamps = false;

    /**
     * Update text-wordform links after choosing meaning.
     * Исключает разборы, чья пара словоформа–грамсет отсутствует у выбранной леммы.
     * 
     * @param int $text_id
     * @param int $w_id
     * @param int $meaning_id
     */
    public static function updateWordformLinksAfterCheckExample(
        $text_id,
        $w_id,
        $meaning_id
    ) {
        $meaning = Meaning::findOrFail($meaning_id);

        DB::statement(
            'UPDATE text_wordform tw
             LEFT JOIN lemma_wordform lw
                 ON lw.wordform_id = tw.wordform_id
                 AND lw.gramset_id = tw.gramset_id
                 AND lw.lemma_id = ?
             SET tw.relevance = 0
             WHERE tw.text_id = ?
                 AND tw.w_id = ?
                 AND lw.lemma_id IS NULL',
            [
                $meaning->lemma_id,
                $text_id,
                $w_id,
            ]
        );
    }
}
