<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartsOfSpeechTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parts_of_speech', function (Blueprint $table) {
            //$table->increments('id');
            $table->tinyInteger('id')->unsigned()->autoIncrement(); // MySQL tinyint(4)
            
            $table->string('name_en', 255)->comment = "English name of POS";
            $table->string('name_ru', 255)->comment = "Russian name of POS";
            
            /* short abbreviation of POS  */
            $table->string('code', 20)->unique()->comment = "short abbreviation of POS";
            
            // $table->timestamps(); // disable
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('parts_of_speech');
    }
}
