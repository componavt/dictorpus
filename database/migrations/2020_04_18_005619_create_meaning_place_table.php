<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeaningPlaceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meaning_place', function (Blueprint $table) {
            $table->integer('meaning_id')->unsigned();
            $table->foreign('meaning_id')->references('id')->on('meanings');
            
            
            $table->smallInteger('place_id')->unsigned();
            $table->foreign('place_id')->references('id')->on('places');
            
            $table->primary(['meaning_id', 'place_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('meaning_place');
    }
}
