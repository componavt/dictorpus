<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaceTextTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('place_text', function (Blueprint $table) {
            $table->smallInteger('place_id')->unsigned();
            $table->     foreign('place_id')->references('id')->on('places');
            
            $table->unsignedInteger('text_id');
            $table->foreign('text_id')->references('id')->on('texts');
            
            $table->primary(['place_id', 'text_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('place_text');
    }
}
