<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlotTopicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plot_topic', function (Blueprint $table) {
            $table->unsignedSmallInteger('plot_id');
            $table->foreign('plot_id')->references('id')->on('plots');
            
            $table->unsignedSmallInteger('topic_id');
            $table->foreign('topic_id')->references('id')->on('topics');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('plot_topic');
    }
}
