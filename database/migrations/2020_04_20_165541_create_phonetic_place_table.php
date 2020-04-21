<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhoneticPlaceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phonetic_place', function (Blueprint $table) {
            $table->integer('phonetic_id')->unsigned();
            $table->foreign('phonetic_id')->references('id')->on('phonetics');
            
            
            $table->smallInteger('place_id')->unsigned();
            $table->foreign('place_id')->references('id')->on('places');
            
            $table->primary(['phonetic_id', 'place_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('phonetic_place');
    }
}
