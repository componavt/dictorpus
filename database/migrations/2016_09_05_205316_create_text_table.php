<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTextTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('texts', function (Blueprint $table) {
            $table->increments('id');
            
            // corpus 
            $table->smallInteger('corpus_id')->unsigned();
            $table->     foreign('corpus_id')->references('id')->on('corpuses');

            // lang 
            $table->smallInteger('lang_id')->unsigned();
            $table->     foreign('lang_id')->references('id')->on('langs');
            
            // transtext 
            $table->integer('transtext_id')->unsigned()->nullable();
            $table->foreign('transtext_id')->references('id')->on('transtexts');

            // source 
            $table->integer('source_id')->unsigned()->nullable();
            $table->foreign('source_id')->references('id')->on('sources');

            // event 
            $table->smallInteger('event_id')->unsigned()->nullable();
            $table->     foreign('event_id')->references('id')->on('events');
            
            $table->string('title', 255);
            $table->text('text');

            //$table->timestamps();
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
        });

        DB::statement("alter table texts change text_xml text_xml mediumblob");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('texts');
    }
}
