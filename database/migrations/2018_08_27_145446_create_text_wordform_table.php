<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTextWordformTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('text_wordform', function (Blueprint $table) {
            $table->integer('text_id')->unsigned();
            $table->foreign('text_id')->references('id')->on('texts');
            
            $table->integer('w_id')->unsigned();
            
            $table->integer('wordform_id')->unsigned();
            $table->foreign('wordform_id')->references('id')->on('wordforms');
            
            $table->smallInteger('gramset_id')->unsigned()->nullable();
                       
            $table->primary(['text_id', 'w_id', 'wordform_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('text_wordform');
    }
}
