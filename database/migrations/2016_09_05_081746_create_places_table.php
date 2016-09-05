<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('places', function (Blueprint $table) {
            //$table->increments('id');
            $table->smallInteger('id')->unsigned()->autoIncrement(); // MySQL smallint(6)
            
            // district 
            $table->smallInteger('district_id')->unsigned()->nullable();
            $table->     foreign('district_id')->references('id')->on('districts');

            // region 
            $table->smallInteger('region_id')->unsigned();
            $table->     foreign('region_id')->references('id')->on('regions');

            $table->string('name_en', 150)->nullable();
            $table->string('name_ru', 150)->nullable();
            
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
        Schema::drop('places');
    }
}
