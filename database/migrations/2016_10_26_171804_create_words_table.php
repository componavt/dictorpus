<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('words', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('text_id')->unsigned();
            $table->foreign('text_id')->references('id')->on('texts');

            $table->integer('sentence_id')->unsigned();
            $table->integer('w_id')->unsigned();
            $table->string('word', 255)->collate('utf8_bin');
            
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('words');
    }
}
