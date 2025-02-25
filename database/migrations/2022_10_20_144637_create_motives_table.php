<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMotivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('motives', function (Blueprint $table) {
            //$table->increments('id');
            $table->unsignedSmallInteger('id')->autoIncrement(); // MySQL smallint(6)
            $table->string('code', 2);
            
            $table->unsignedSmallInteger('motype_id');
            $table->foreign('motype_id')->references('id')->on('motypes');
            
            $table->unsignedSmallInteger('parent_id')->nullable();
            
            $table->string('name_en', 150);
            $table->string('name_ru', 150);

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
        Schema::drop('motives');
    }
}
