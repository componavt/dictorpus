<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLemmaWordformTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lemma_wordform', function (Blueprint $table) {
            $table->integer('lemma_id')->unsigned();
            $table->foreign('lemma_id')->references('id')->on('lemmas');
            
            $table->integer('wordform_id')->unsigned();
            $table->foreign('wordform_id')->references('id')->on('wordforms');
            
            $table->smallInteger('gramset_id')->unsigned()->nullable();
            //$table->foreign('gramset_id')->references('id')->on('gramsets');
            
            // dialect 
            $table->smallInteger('dialect_id')->unsigned()->nullable();
            //$table->     foreign('dialect_id')->references('id')->on('dialects');
            
            // $table->timestamp('updated_at')->useCurrent();
            // $table->timestamp('created_at')->useCurrent();

            // Index -------------------
            $table->unique([ 'lemma_id', 'wordform_id', 'gramset_id', 'dialect_id' ]);
            // $table->index('lemma_id');
            // $table->index('wordform_id');
            $table->index('gramset_id');
            $table->index('dialect_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('lemma_wordform');
    }
}
