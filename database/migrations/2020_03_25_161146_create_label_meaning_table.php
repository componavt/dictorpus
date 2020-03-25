<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLabelMeaningTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('label_meaning', function (Blueprint $table) {
            $table->smallInteger('label_id')->unsigned();
            $table->foreign('label_id')->references('id')->on('labels');
            
            $table->integer('meaning_id')->unsigned();
            $table->foreign('meaning_id')->references('id')->on('meanings');
            
            $table->primary(['label_id', 'meaning_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('label_meaning');
    }
}
