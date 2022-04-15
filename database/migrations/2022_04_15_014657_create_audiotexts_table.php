<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAudiotextsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audiotexts', function (Blueprint $table) {
            $table->increments('id');
            
            $table->unsignedInteger('text_id');
            $table->foreign('text_id')->references('id')->on('texts');

            $table->char('filename', 100);
            
            $table->timestamps();
            
            $table->unique('text_id', 'filename');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('audiotexts');
    }
}
