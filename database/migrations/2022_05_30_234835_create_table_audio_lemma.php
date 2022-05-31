<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAudioLemma extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audio_lemma', function (Blueprint $table) {            
            $table->unsignedInteger('audio_id');
            $table->foreign('audio_id')->references('id')->on('audios');

            $table->unsignedInteger('lemma_id');
            $table->foreign('lemma_id')->references('id')->on('lemmas');
            
            $table->unique(['audio_id', 'lemma_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('audio_lemma');
    }
}
