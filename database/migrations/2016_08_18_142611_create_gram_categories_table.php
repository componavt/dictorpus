<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGramCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gram_categories', function (Blueprint $table) {            
            $table->tinyInteger('id')->unsigned()->autoIncrement();
            
            $table->string('name_en', 45)->comment = "English name of category of grammatical attribute";
            $table->string('name_ru', 45)->comment = "Russian name of category of grammatical attribute";

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
        Schema::drop('gram_categories');
    }
}
