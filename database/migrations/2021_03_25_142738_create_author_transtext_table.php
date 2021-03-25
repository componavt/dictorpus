<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthorTranstextTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('author_transtext', function (Blueprint $table) {
            $table->integer('author_id')->unsigned();
            $table->foreign('author_id')->references('id')->on('authors');
            
            $table->integer('transtext_id')->unsigned();
            $table->foreign('transtext_id')->references('id')->on('transtexts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('author_transtext');
    }
}
