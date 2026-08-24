<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBiblesTable extends Migration
{
    public function up()
    {
        Schema::create('bibles', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name_ru', 150);
            $table->string('name_en', 150);

            $table->unsignedSmallInteger('sequence_number');

            $table->unique(
                'name_ru',
                'bibles_name_ru_unique'
            );

            $table->unique(
                'sequence_number',
                'bibles_sequence_number_unique'
            );
        });
    }

    public function down()
    {
        Schema::dropIfExists('bibles');
    }
}