<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInformantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('informants', function (Blueprint $table) {
            //$table->increments('id');
            $table->smallInteger('id')->unsigned()->autoIncrement(); // MySQL smallint(6)
            
            // place 
            $table->smallInteger('birth_place_id')->unsigned()->nullable();
            $table->     foreign('birth_place_id')->references('id')->on('places');

            // year 
            $table->smallInteger('birth_date')->unsigned()->nullable();

            $table->string('name_en', 150)->nullable()->nullable();
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
        Schema::drop('informants');
    }
}
