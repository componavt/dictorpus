<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublicationsTable extends Migration
{
    public function up()
    {
        Schema::create('publications', function (Blueprint $table) {
            $table->increments('id');

            $table->string('authors', 255)->nullable();
            $table->string('title', 255);
            $table->string('addition_info', 255)->nullable();
            $table->smallInteger('year')->unsigned()->nullable();

            $table->index(array('title', 'year'));
        });
    }

    public function down()
    {
        Schema::dropIfExists('publications');
    }
}