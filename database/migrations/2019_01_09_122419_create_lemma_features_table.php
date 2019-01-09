<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLemmaFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lemma_features', function (Blueprint $table) {
            $table->integer('lemma_id')->unsigned();
            $table->foreign('lemma_id')->references('id')->on('lemmas');
            
            $table->tinyInteger('animacy')->unsigned();
            $table->unsignedTinyInteger('abbr');
            $table->unsignedTinyInteger('plur_tan');
            $table->unsignedTinyInteger('transitivity');
            $table->unsignedTinyInteger('reflexive');
            $table->unsignedTinyInteger('prontype_id');
            $table->unsignedTinyInteger('numtype_id');
            $table->unsignedTinyInteger('degree_id');
            $table->unsignedTinyInteger('advtype_id');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('lemma_features');
    }
}
