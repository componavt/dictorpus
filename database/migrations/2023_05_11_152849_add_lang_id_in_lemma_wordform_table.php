<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLangIdInLemmaWordformTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lemma_wordform', function (Blueprint $table) {
            $table->tinyInteger('lang_id')->unsigned();
            $table->index(['lang_id', 'wordform_for_search']);
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
            $table->dropIndex(['lang_id', 'wordform_for_search']); 
            $table->dropColumn('lang_id'); 
        });
    }
}
/*
UPDATE lemma_wordform SET lemma_wordform.lang_id=(SELECT lang_id FROM lemmas WHERE lemmas.id = lemma_wordform.lemma_id);
 */