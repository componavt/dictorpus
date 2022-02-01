<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePlots extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plots', function (Blueprint $table) {
            //$table->increments('id');
            $table->unsignedSmallInteger('id')->autoIncrement(); // MySQL smallint(6)
            
            $table->unsignedSmallInteger('genre_id');
            $table->foreign('genre_id')->references('id')->on('genres');
            
            $table->string('name_en', 150);
            $table->string('name_ru', 150);

            $table->smallInteger('sequence_number')->unsigned();

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
        Schema::drop('plots');
    }
}
