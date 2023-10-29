<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoalitionDialectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coalition_dialect', function (Blueprint $table) {
            $table->string('coalition', 255);
            
            $table->smallInteger('dialect_id')->unsigned();
            $table->     foreign('dialect_id')->references('id')->on('dialects');
            
            $table->integer('frequency')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('coalition_dialect');
    }
}
