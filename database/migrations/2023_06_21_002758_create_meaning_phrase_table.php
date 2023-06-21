<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeaningPhraseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meaning_phrase', function (Blueprint $table) {
            $table->unsignedInteger('meaning_id');
            $table->foreign('meaning_id')->references('id')->on('meanings');
            
            $table->unsignedInteger('lemma_id');
            $table->foreign('lemma_id')->references('id')->on('lemmas');
            
            $table->primary(['meaning_id', 'lemma_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('meaning_phrase');
    }
}
