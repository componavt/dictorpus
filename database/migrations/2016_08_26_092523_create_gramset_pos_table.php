<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGramsetPosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gramset_pos', function (Blueprint $table) {
            $table->smallInteger('gramset_id')->unsigned()->nullable();
            $table->foreign('gramset_id')->references('id')->on('gramsets');

            $table->tinyInteger('pos_id')->unsigned();
            $table->foreign('pos_id')->references('id')->on('parts_of_speech');
        });
        //
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('gramset_pos');
    }
}
