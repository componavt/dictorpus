<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            //$table->increments('id');
            $table->smallInteger('id')->unsigned()->autoIncrement(); // MySQL smallint(6)
            
            // informant 
            $table->smallInteger('informant_id')->unsigned()->nullable();
            $table->     foreign('informant_id')->references('id')->on('informants');

            // place 
            $table->smallInteger('place_id')->unsigned()->nullable();
            $table->     foreign('place_id')->references('id')->on('places');

            $table->smallInteger('date')->unsigned()->nullable();

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
        Schema::drop('events');
    }
}
//alter table sources change `comment` `comment` varchar(1024) default null;