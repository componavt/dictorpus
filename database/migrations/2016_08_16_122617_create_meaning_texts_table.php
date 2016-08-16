<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeaningTextsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meaning_texts', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('meaning_id')->unsigned();
            $table->foreign('meaning_id')->references('id')->on('meanings');
            
            // lang 
            $table->smallInteger('lang_id')->unsigned();
            $table->     foreign('lang_id')->references('id')->on('langs');
            
            $table->string('meaning_text', 2047)->comment = "text describing this meaning";
            
            //$table->timestamps();
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
            
            // Index -------------------
            $table->index('meaning_text');
            $table->index('lang_id');
            $table->index('meaning_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('meaning_texts');
    }
}
