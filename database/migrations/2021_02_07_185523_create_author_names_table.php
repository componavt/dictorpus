<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthorNamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('author_names', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('author_id')->unsigned();
            $table->foreign('author_id')->references('id')->on('authors');
            
            // lang 
            $table->smallInteger('lang_id')->unsigned();
            $table->     foreign('lang_id')->references('id')->on('langs');
            
            $table->string('name', 150);
            
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
        Schema::drop('author_names');
    }
}
