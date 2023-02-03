<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInformantLemmaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('informant_lemma', function (Blueprint $table) {
            $table->unsignedSmallInteger('informant_id');
            $table->foreign('informant_id')->references('id')->on('informants');
            
            $table->unsignedInteger('lemma_id');
            $table->foreign('lemma_id')->references('id')->on('lemmas');
            
            $table->primary(['informant_id', 'lemma_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('informant_lemma');
    }
}
