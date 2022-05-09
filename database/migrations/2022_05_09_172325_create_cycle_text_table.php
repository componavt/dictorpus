<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCycleTextTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cycle_text', function (Blueprint $table) {
            $table->unsignedSmallInteger('cycle_id');
            $table->foreign('cycle_id')->references('id')->on('cycles');
            
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
        Schema::drop('cycle_text');
    }
}
