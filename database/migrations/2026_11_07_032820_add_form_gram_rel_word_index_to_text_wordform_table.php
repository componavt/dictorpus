<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFormGramRelWordIndexToTextWordformTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection('mysql')->statement(
            'ALTER TABLE text_wordform
             ADD INDEX idx_tw_form_gram_rel_word
                 (wordform_id, gramset_id, relevance, word_id),
             ALGORITHM=INPLACE,
             LOCK=NONE'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection('mysql')->statement(
            'ALTER TABLE text_wordform
             DROP INDEX idx_tw_form_gram_rel_word,
             ALGORITHM=INPLACE,
             LOCK=NONE'
        );
    }
}
