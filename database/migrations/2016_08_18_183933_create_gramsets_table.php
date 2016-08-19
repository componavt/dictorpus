<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGramsetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gramsets', function (Blueprint $table) {
            $table->smallInteger('id')->unsigned()->autoIncrement();
            
            // pos PartOfSpeech
            $table->tinyInteger('pos_id')->unsigned()->nullable();
            $table->    foreign('pos_id')->references('id')->on('parts_of_speech');
            
            // id of grammatical number attribute
            $table->smallInteger('gram_id_number')->unsigned()->nullable();
            $table->     foreign('gram_id_number')->references('id')->on('grams');

            // id of grammatical case attribute
            $table->smallInteger('gram_id_case')->unsigned()->nullable();
            $table->     foreign('gram_id_case')->references('id')->on('grams');

            // id of grammatical tense attribute
            $table->smallInteger('gram_id_tense')->unsigned()->nullable();
            $table->     foreign('gram_id_tense')->references('id')->on('grams');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('gramsets');
    }
}
