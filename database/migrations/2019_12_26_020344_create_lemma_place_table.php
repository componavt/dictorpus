<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLemmaPlaceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lemma_place', function (Blueprint $table) {
            $table->integer('lemma_id')->unsigned();
            $table->foreign('lemma_id')->references('id')->on('lemmas');
            
            
            $table->smallInteger('place_id')->unsigned();
            $table->foreign('place_id')->references('id')->on('places');
            
            $table->primary(['lemma_id', 'place_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('lemma_place');
    }
}
