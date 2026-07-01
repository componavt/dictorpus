<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class LangIdLemmaForSearchIndexInLemmas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lemmas', function (Blueprint $table) {
            $table->index(
                ['lang_id', 'lemma_for_search'],
                'idx_lemmas_lang_id_lemma_for_search'
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lemmas', function (Blueprint $table) {
            $table->dropIndex('idx_lemmas_lang_id_lemma_for_search');
        });
    }
}
