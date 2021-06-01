<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReverseLemmasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reverse_lemmas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reverse_lemma', 100)->collation('utf8_bin');
            
            // lemma
//            $table->integer('lemma_id')->unsigned();
  //          $table->foreign('lemma_id')->references('id')->on('lemmas');

            // lang 
            $table->smallInteger('lang_id')->unsigned();
            $table->     foreign('lang_id')->references('id')->on('langs');
            
            $table->string('stem', 100)->collation('utf8_bin');

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
        Schema::drop('reverse_lemmas');
    }
}
