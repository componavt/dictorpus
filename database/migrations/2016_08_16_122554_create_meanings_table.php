<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeaningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meanings', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('lemma_id')->unsigned();
            $table->foreign('lemma_id')->references('id')->on('lemmas');
            
            $table->tinyInteger('meaning_n')->unsigned()->comment="number of the meaning for this lemma";
            
            //$table->timestamps();
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
            
            // Index -------------------
            $table->index('lemma_id');
            $table->unique([ 'lemma_id', 'meaning_n' ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('meanings');
    }
}
