<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sentences', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('text_id')->unsigned();
            $table->foreign('text_id')->references('id')->on('texts');

            $table->integer('s_id')->unsigned();
            
            $table->binary('text_xml')->collate('utf8_bin');
            
            //$table->timestamps();
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sentences');
    }
}
