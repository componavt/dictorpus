<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGramsetCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gramset_categories', function (Blueprint $table) {
            $table->tinyInteger('id')->unsigned()->autoIncrement();
            
            $table->tinyInteger('pos_category_id')->unsigned();
            $table->string('name_en', 45)->comment = "English name of category of grammatical set";
            $table->string('name_ru', 45)->comment = "Russian name of category of grammatical set";

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
        Schema::drop('gramset_categories');
    }
}
