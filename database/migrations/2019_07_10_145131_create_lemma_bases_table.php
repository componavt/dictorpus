<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLemmaBasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lemma_bases', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('lemma_id')->unsigned();
            $table->foreign('lemma_id')->references('id')->on('lemmas');
            
            $table->unsignedTinyInteger('base_n');
            $table->string('base', 45)->collate('utf8_bin');  
            
            $table->unique(['lemma_id', 'base_n']);
            
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('lemma_bases');
    }
}
