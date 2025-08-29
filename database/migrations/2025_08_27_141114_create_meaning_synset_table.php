<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeaningSynsetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meaning_synset', function (Blueprint $table) {
            $table->unsignedInteger('meaning_id');
            $table->foreign('meaning_id')->references('id')->on('meanings')->cascadeOnDelete();
            
            $table->unsignedInteger('synset_id');
            $table->foreign('synset_id')->references('id')->on('synsets')->cascadeOnDelete();
            
            $table->smallInteger('syntype_id')->unsigned()->default(1);
            $table->     foreign('syntype_id')->references('id')->on('syntypes');
            
            $table->unique(['meaning_id','synset_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('meaning_synset');
    }
}
