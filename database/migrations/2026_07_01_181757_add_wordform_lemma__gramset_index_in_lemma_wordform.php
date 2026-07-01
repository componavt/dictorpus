<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddWordformLemmaGramsetIndexInLemmaWordform extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lemma_wordform', function (Blueprint $table) {
            $table->index(
                ['wordform_for_search', 'lemma_id', 'gramset_id'],
                'idx_lemma_wordform_search_lemma_gramset'
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
        Schema::table('lemma_wordform', function (Blueprint $table) {
            $table->dropIndex('idx_lemma_wordform_search_lemma_gramset');
        });
    }
}
