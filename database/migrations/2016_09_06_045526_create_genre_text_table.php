<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGenreTextTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('genre_text', function (Blueprint $table) {
            $table->smallInteger('genre_id')->unsigned();
            $table->foreign('genre_id')->references('id')->on('genres');
            
            $table->integer('text_id')->unsigned();
            $table->foreign('text_id')->references('id')->on('texts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('genre_text');
    }
}
