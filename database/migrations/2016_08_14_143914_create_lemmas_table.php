<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLemmasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lemmas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('lemma', 255)->collation('utf8_bin');
            
            // lang 
            $table->smallInteger('lang_id')->unsigned();
            $table->     foreign('lang_id')->references('id')->on('langs');
            
            // pos PartOfSpeech
            $table->tinyInteger('pos_id')->unsigned()->nullable();
            $table->    foreign('pos_id')->references('id')->on('parts_of_speech');
            
            $table->boolean('reflexive')->unsigned();

            //$table->timestamps();
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
            
            //$table->integer('temp_translation_lemma_id');
            
            
            // Index -------------------
            $table->index([ 'lemma', 'lang_id', 'pos_id' ]);
            $table->index('lemma');
            $table->index('lang_id');
            $table->index('pos_id');
            
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('lemmas');
    }
}
