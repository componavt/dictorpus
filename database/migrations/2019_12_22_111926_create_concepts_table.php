<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConceptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('concepts', function (Blueprint $table) {
            $table->increments('id');
            
//            $table->smallInteger('concept_category_id')->unsigned();
            $table->string('concept_category_id',4);
            $table->     foreign('concept_category_id')->references('id')->on('concept_categories');
            
            $table->string('text_en', 45);
            $table->string('text_ru', 45);
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
        Schema::drop('concepts');
    }
}
