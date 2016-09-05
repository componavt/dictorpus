<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventRecorderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_recorder', function (Blueprint $table) {
            $table->smallInteger('event_id')->unsigned();
            $table->foreign('event_id')->references('id')->on('events');
            
            $table->smallInteger('recorder_id')->unsigned();
            $table->foreign('recorder_id')->references('id')->on('recorders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('event_recorder');
    }
}
