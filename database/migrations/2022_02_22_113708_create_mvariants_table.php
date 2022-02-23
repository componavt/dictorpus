<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMvariantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mvariants', function (Blueprint $table) {
//            $table->increments('id');
            $table->unsignedSmallInteger('id')->autoIncrement(); // MySQL smallint(6)
            $table->smallInteger('dmarker_id')->unsigned();
            $table->     foreign('dmarker_id')->references('id')->on('dmarkers');
            $table->string('name', 150);
            $table->string('template', 150);
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
        Schema::drop('mvariants');
    }
}
