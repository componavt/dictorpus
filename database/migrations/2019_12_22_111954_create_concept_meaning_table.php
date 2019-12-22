<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConceptMeaningTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('concept_meaning', function (Blueprint $table) {
            $table->integer('concept_id')->unsigned();
            $table->foreign('concept_id')->references('id')->on('concepts');
            
            $table->integer('meaning_id')->unsigned();
            $table->foreign('meaning_id')->references('id')->on('meanings');
            
            $table->primary(['concept_id', 'meaning_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('concept_meaning');
    }
}
