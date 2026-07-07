<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class DropRedundantMeaningIdIndexFromMeaningTextsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * Индекс meaning_texts_meaning_id_index избыточен: он состоит
     * только из meaning_id, а этот столбец является самым левым
     * префиксом уже существующего уникального индекса `meaning_lang`
     * (meaning_id, lang_id). MariaDB/MySQL может использовать
     * `meaning_lang` для запросов вида WHERE meaning_id = ? так же
     * эффективно, поэтому отдельный индекс только замедляет записи
     * и занимает лишнее место.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meaning_texts', function (Blueprint $table) {
            $table->dropIndex('meaning_texts_meaning_id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meaning_texts', function (Blueprint $table) {
            $table->index('meaning_id', 'meaning_texts_meaning_id_index');
        });
    }
}
