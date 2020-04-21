<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDialectMeaningTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dialect_meaning', function (Blueprint $table) {
            $table->smallInteger('dialect_id')->unsigned();
            $table->foreign('dialect_id')->references('id')->on('dialects');
            
            $table->integer('meaning_id')->unsigned();
            $table->foreign('meaning_id')->references('id')->on('meanings');
            
            $table->primary(['dialect_id', 'meaning_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('dialect_meaning');
    }
}
