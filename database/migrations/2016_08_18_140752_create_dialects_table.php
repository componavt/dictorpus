<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDialectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dialects', function (Blueprint $table) {
            $table->smallInteger('id')->unsigned()->autoIncrement();
            
            // lang 
            $table->smallInteger('lang_id')->unsigned();
            $table->     foreign('lang_id')->references('id')->on('langs');
            
            $table->string('name_en', 64)->comment = "English name of dialect";
            $table->string('name_ru', 64)->comment = "Russian name of dialect";
            
            /* short abbreviation of dialect  */
            $table->string('code', 20)->unique()->comment = "short abbreviation of dialect";
            
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
        Schema::drop('dialects');
    }
}
