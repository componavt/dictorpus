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
            // it is not used, because there is pivot table gramset_pos, but this helps to debug code
            $table->tinyInteger('pos_id_debug')->unsigned()->nullable();
            
            // id of grammatical number attribute
            $table->smallInteger('gram_id_number')->unsigned()->nullable();
            $table->     foreign('gram_id_number')->references('id')->on('grams');

            // id of grammatical case attribute
            $table->smallInteger('gram_id_case')->unsigned()->nullable();
            $table->     foreign('gram_id_case')->references('id')->on('grams');

            // id of grammatical tense attribute
            $table->smallInteger('gram_id_tense')->unsigned()->nullable();
            $table->     foreign('gram_id_tense')->references('id')->on('grams');
            
            // id of grammatical person attribute
            $table->smallInteger('gram_id_person')->unsigned()->nullable();
            $table->     foreign('gram_id_person')->references('id')->on('grams');
            
            $table->tinyInteger('sequence_number')->unsigned();
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
