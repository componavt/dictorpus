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
            $table->integer('id')->unsigned()->nullable()->unique();
            $table->foreign('id')->references('id')->on('lemmas');
            
            $table->unsignedTinyInteger('animacy')->nullable();
            $table->unsignedTinyInteger('abbr')->nullable();
            $table->unsignedTinyInteger('plur_tan')->nullable();
            $table->unsignedTinyInteger('reflexive')->nullable();
            $table->unsignedTinyInteger('transitive')->nullable();
            $table->unsignedTinyInteger('prontype_id')->nullable();
            $table->unsignedTinyInteger('numtype_id')->nullable();
            $table->unsignedTinyInteger('degree_id')->nullable();
            $table->unsignedTinyInteger('advtype_id')->nullable();            
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
