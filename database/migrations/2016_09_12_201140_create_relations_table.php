<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relations', function (Blueprint $table) {
            //$table->increments('id');
            $table->tinyInteger('id')->unsigned()->autoIncrement(); // MySQL smallint(6)
            
            $table->string('name_en', 150);
            $table->string('name_ru', 150);

            $table->tinyInteger('reverse_relation_id')->unsigned()->nullable(); 
            $table->foreign('reverse_relation_id')->references('id')->on('relations');

            $table->tinyInteger('sequence_number')->unsigned();
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
        Schema::drop('relations');
    }
}
