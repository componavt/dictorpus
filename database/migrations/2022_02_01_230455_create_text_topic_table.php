<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTextTopicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('text_topic', function (Blueprint $table) {
            $table->unsignedInteger('text_id');
            $table->foreign('text_id')->references('id')->on('texts');
            
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
        Schema::drop('text_topic');
    }
}
