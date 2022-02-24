<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDialectDmarkerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dialect_dmarker', function (Blueprint $table) {
//            $table->increments('id');
            $table->smallInteger('dialect_id')->unsigned();
            $table->     foreign('dialect_id')->references('id')->on('dialects');
            
            $table->smallInteger('dmarker_id')->unsigned();
            $table->smallInteger('mvariant_id')->unsigned();
            $table->integer('frequency')->unsigned()->nullable();
            $table->double('fraction', 7, 5)->nullable();
//            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('dialect_dmarker');
    }
}
