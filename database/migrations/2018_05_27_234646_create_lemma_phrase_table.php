<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLemmaPhraseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lemma_phrase', function (Blueprint $table) {
            $table->integer('lemma_id')->unsigned();
            $table->integer('phrase_id')->unsigned();
            
            $table->primary(['lemma_id', 'phrase_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('lemma_phrase');
    }
}
