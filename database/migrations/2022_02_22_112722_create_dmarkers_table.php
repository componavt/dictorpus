<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDmarkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmarkers', function (Blueprint $table) {
//            $table->increments('id');
            $table->unsignedSmallInteger('id')->autoIncrement(); // MySQL smallint(6)
            $table->string('name', 150);
            $table->boolean('absence'); 
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
        Schema::drop('dmarkers');
    }
}
