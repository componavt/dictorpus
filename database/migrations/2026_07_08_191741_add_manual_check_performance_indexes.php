<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddManualCheckPerformanceIndexes extends Migration
{
    public function up()
    {
        // 1. meaning_text: закрывает checkedMeaning(), uncheckedMeanings(),
        //    hasImportantExamples(), existsPositiveRelevance(), setNegativeToUndefOthers(),
        //    updateMeaningLinksAfterCheckExample()
        DB::statement(
            'ALTER TABLE meaning_text ADD INDEX idx_meaning_text_text_wid_relevance
             (text_id, w_id, relevance), ALGORITHM=INPLACE, LOCK=NONE'
        );
        DB::statement('ALTER TABLE meaning_text ADD INDEX idx_meaning_text_word_relevance (word_id, relevance)');

        // 2. words: закрывает Word::whereTextId()->whereWId()->first() в createWordBlock()
        DB::statement(
            'ALTER TABLE words ADD INDEX idx_words_text_w (text_id, w_id),
             ALGORITHM=INPLACE, LOCK=NONE'
        );


        // 3. чистка дублирующегося индекса (words_text_id_foreign дублирует idx_words_text_id)
        // Раскомментировать после проверки, что words_text_id_foreign используется
        // только как хранитель FK-ограничения words_text_id_foreign, а не отдельно в запросах:
        DB::statement('ALTER TABLE words DROP INDEX idx_words_text_id');
    }

    public function down()
    {
        DB::statement('ALTER TABLE meaning_text DROP INDEX idx_meaning_text_text_wid_relevance');
        DB::statement('ALTER TABLE meaning_text DROP INDEX idx_meaning_text_word_relevance');
        DB::statement('ALTER TABLE words DROP INDEX idx_words_text_w');
    }
}
