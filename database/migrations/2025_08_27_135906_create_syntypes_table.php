<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSyntypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('syntypes', function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->autoIncrement(); // MySQL smallint(6)
            $table->string('name_en', 50);
            $table->string('name_ru', 50);
            $table->text('comment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('syntypes');
    }
}
