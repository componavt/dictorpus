<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlotTextTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plot_text', function (Blueprint $table) {
            $table->unsignedSmallInteger('plot_id');
            $table->foreign('plot_id')->references('id')->on('plots');
            
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
        Schema::drop('plot_text');
    }
}
