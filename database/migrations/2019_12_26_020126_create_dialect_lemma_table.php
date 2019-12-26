<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDialectLemmaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dialect_lemma', function (Blueprint $table) {
            $table->smallInteger('dialect_id')->unsigned();
            $table->foreign('dialect_id')->references('id')->on('dialects');
            
            $table->integer('lemma_id')->unsigned();
            $table->foreign('lemma_id')->references('id')->on('lemmas');
            
            $table->primary(['dialect_id', 'lemma_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('dialect_lemma');
    }
}
