<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDialectPhoneticTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dialect_phonetic', function (Blueprint $table) {
            $table->smallInteger('dialect_id')->unsigned();
            $table->foreign('dialect_id')->references('id')->on('dialects');
            
            $table->integer('phonetic_id')->unsigned();
            $table->foreign('phonetic_id')->references('id')->on('phonetics');
            
            $table->primary(['dialect_id', 'phonetic_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('dialect_phonetic');
    }
}
