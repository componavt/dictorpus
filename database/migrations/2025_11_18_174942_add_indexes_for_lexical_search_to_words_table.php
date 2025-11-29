<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddIndexesForLexicalSearchToWordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('words', function (Blueprint $table) {
            // Индекс на w_id — критично для поиска по слову
            $table->index(['w_id'], 'idx_words_w_id');

            // Индекс на text_id — для WHERE IN(text_id)
            $table->index(['text_id'], 'idx_words_text_id');
        });

        // text_wordform
        Schema::table('text_wordform', function (Blueprint $table) {
            $table->index(['word_id', 'relevance'], 'idx_text_wordform_word_relevance');
            $table->index(['w_id'], 'idx_text_wordform_w_id');
        });

        // lemma_wordform
        Schema::table('lemma_wordform', function (Blueprint $table) {
            $table->index(['lemma_id', 'wordform_id'], 'idx_lemma_wordform_lemma_word');
            $table->index(['wordform_id'], 'idx_lemma_wordform_wordform_id');
        });

        // lemmas
        Schema::table('lemmas', function (Blueprint $table) {
            $table->index(['lemma_for_search'], 'idx_lemmas_lemma_for_search');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('words', function (Blueprint $table) {
            $table->dropIndex('idx_words_w_id');
            $table->dropIndex('idx_words_text_id');
        });

        Schema::table('text_wordform', function (Blueprint $table) {
            $table->dropIndex('idx_text_wordform_word_relevance');
            $table->dropIndex('idx_text_wordform_w_id');
        });

        Schema::table('lemma_wordform', function (Blueprint $table) {
            $table->dropIndex('idx_lemma_wordform_lemma_word');
            $table->dropIndex('idx_lemma_wordform_wordform_id');
        });

        Schema::table('lemmas', function (Blueprint $table) {
            $table->dropIndex('idx_lemmas_lemma_for_search');
        });
    }
}
