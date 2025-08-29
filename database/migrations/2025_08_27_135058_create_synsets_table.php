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
            
            $table->text('comment');
            $table->enum('status', ['draft','approved','archived'])->default('draft');            
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
