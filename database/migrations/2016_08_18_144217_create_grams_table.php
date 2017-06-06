<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grams', function (Blueprint $table) {
            $table->smallInteger('id')->unsigned()->autoIncrement();
            
            // gram_category 
            $table->tinyInteger('gram_category_id')->unsigned();
            $table->    foreign('gram_category_id')->references('id')->on('gram_categories');
            
            $table->string('name_short_en', 15)->comment = "English short name of grammatical attribute";
            $table->string('name_en', 255)->comment = "English name of grammatical attribute";
            $table->string('name_short_ru', 15)->comment = "Russian short name of grammatical attribute";
            $table->string('name_ru', 255)->comment = "Russian name of grammatical attribute";
           
            $table->tinyInteger('sequence_number')->unsigned()->nullable();
            
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('grams');
    }
}
