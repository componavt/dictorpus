<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSentenceTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sentence_translations', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('sentence_id')->unsigned();
            $table->foreign('sentence_id')->references('id')->on('sentences');
            
            $table->smallInteger('lang_id')->unsigned()->default(2);
            $table->foreign('lang_id')->references('id')->on('langs');
            
            $table->binary('text');
            
            $table->unique([ 'sentence_id', 'lang_id' ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sentence_translations');
    }
}
