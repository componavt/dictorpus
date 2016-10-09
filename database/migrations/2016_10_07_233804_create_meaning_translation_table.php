<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeaningTranslationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meaning_translation', function (Blueprint $table) {
            $table->integer('meaning1_id')->unsigned();
            $table->foreign('meaning1_id')->references('id')->on('meanings');
            
            $table->smallInteger('lang_id')->unsigned();
            $table->foreign('lang_id')->references('id')->on('langs');
            
            $table->integer('meaning2_id')->unsigned();
            $table->foreign('meaning2_id')->references('id')->on('meanings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('meaning_translation');
    }
}
