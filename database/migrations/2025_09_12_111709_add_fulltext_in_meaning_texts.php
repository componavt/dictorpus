<?php

use Illuminate\Database\Migrations\Migration;

class AddFulltextInMeaningTexts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Добавляем FULLTEXT-индекс на колонку meaning_text
        DB::statement('ALTER TABLE meaning_texts ADD FULLTEXT fulltext_meaning_text (meaning_text)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE meaning_texts DROP INDEX fulltext_meaning_text');
    }
}
