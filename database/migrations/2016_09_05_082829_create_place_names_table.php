<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaceNamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('place_names', function (Blueprint $table) {
            //$table->increments('id');
            $table->smallInteger('id')->unsigned()->autoIncrement(); // MySQL smallint(6)
            
            // place 
            $table->smallInteger('place_id')->unsigned();
            $table->     foreign('place_id')->references('id')->on('places');

            // lang 
            $table->smallInteger('lang_id')->unsigned();
            $table->     foreign('lang_id')->references('id')->on('langs');

            $table->string('name', 150);
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
        Schema::drop('place_names');
    }
}
