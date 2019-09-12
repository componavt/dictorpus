<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLemmaVariantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lemma_variants', function (Blueprint $table) {
            $table->integer('lemma1_id')->unsigned();
            $table->foreign('lemma1_id')->references('id')->on('lemmas');
            
            $table->integer('lemma2_id')->unsigned();
            $table->foreign('lemma2_id')->references('id')->on('lemmas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('lemma_variants');
    }
}
