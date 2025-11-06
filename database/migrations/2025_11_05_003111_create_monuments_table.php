<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monuments', function (Blueprint $table) {
            $table->increments('id');

            $table->string('author', 50)->nullable();
            $table->string('title', 100);
            $table->string('place', 50)->nullable();
            $table->date('publ_date_from')->nullable();
            $table->date('publ_date_to')->nullable();
            $table->string('pages', 20)->nullable();
            $table->text('bibl_descr')->nullable();
            
            // lang 
            $table->smallInteger('lang_id')->unsigned()->nullable();
            $table->     foreign('lang_id')->references('id')->on('langs');

            // dialect 
            $table->smallInteger('dialect_id')->unsigned()->nullable();
            $table->     foreign('dialect_id')->references('id')->on('dialects');

            $table->tinyInteger('graphic_id')->unsigned();
            $table->boolean('has_trans')->unsigned();
            $table->string('volume', 20)->nullable();
            $table->tinyInteger('type_id')->unsigned();
            $table->boolean('is_printed')->unsigned();
            $table->boolean('is_full')->unsigned();
            $table->text('dcopy_link')->nullable();
            $table->text('publ')->nullable();
            $table->text('study')->nullable();
            $table->text('archive')->nullable();
            $table->text('comment')->nullable();
            
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
        Schema::drop('monuments');
    }
}
