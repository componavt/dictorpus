<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMotiveTextTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('motive_text', function (Blueprint $table) {
            $table->unsignedSmallInteger('motive_id');
            $table->foreign('motive_id')->references('id')->on('motives');
            
            $table->unsignedInteger('text_id');
            $table->foreign('text_id')->references('id')->on('texts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('motive_text');
    }
}
