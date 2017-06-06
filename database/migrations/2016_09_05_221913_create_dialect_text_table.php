<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDialectTextTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dialect_text', function (Blueprint $table) {
            $table->smallInteger('dialect_id')->unsigned();
            $table->foreign('dialect_id')->references('id')->on('dialects');
            
            $table->integer('text_id')->unsigned();
            $table->foreign('text_id')->references('id')->on('texts');

            $table->tinyInteger('sequence_number')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('dialect_text');
    }
}
