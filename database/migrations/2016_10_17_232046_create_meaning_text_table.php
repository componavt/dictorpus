<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeaningTextTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meaning_text', function (Blueprint $table) {
            $table->integer('meaning_id')->unsigned();
            $table->foreign('meaning_id')->references('id')->on('meanings');
            
            $table->integer('text_id')->unsigned();
            $table->foreign('text_id')->references('id')->on('texts');
            
            $table->integer('sentence_id')->unsigned();
            
            $table->integer('word_id')->unsigned();
            
            $table->tinyInteger('relevance')->unsigned()->default(1);
            
            $table->primary(array('meaning_id', 'text_id', 'sentence_id', 'word_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('meaning_text');
    }
}
