<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeaningRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meaning_relation', function (Blueprint $table) {
            $table->integer('meaning1_id')->unsigned();
            $table->foreign('meaning1_id')->references('id')->on('meanings');
            
            $table->tinyInteger('relation_id')->unsigned();
            $table->foreign('relation_id')->references('id')->on('relations');
            
            $table->integer('meaning2_id')->unsigned();
            $table->foreign('meaning2_id')->references('id')->on('meanings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('meaning_relation');
    }
}
