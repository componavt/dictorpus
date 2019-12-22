<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConceptCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('concept_categories', function (Blueprint $table) {
            //$table->increments('id');
//            $table->smallInteger('id')->unsigned()->autoIncrement(); // MySQL smallint(6)
            $table->string('id',4);
            $table->primary('id');
            
            
            $table->string('name_en', 45);
            $table->string('name_ru', 45);
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
        Schema::drop('concept_categories');
    }
}
