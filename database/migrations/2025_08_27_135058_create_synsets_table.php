<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSynsetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('synsets', function (Blueprint $table) {
            $table->increments('id');
            
            // lang 
            $table->smallInteger('lang_id')->unsigned();
            $table->     foreign('lang_id')->references('id')->on('langs');
            
            // pos PartOfSpeech
            $table->tinyInteger('pos_id')->unsigned()->nullable();
            $table->    foreign('pos_id')->references('id')->on('parts_of_speech');
            
            $table->text('comment');
            $table->tinyInteger('status')->unsigned()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('synsets');
    }
}
