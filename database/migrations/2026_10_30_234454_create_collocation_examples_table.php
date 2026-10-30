<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollocationExamplesTable extends Migration
{
    public function up()
    {
        Schema::create('collocation_examples', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';

            $table->increments('id');

            $table->integer('collocation_id')->unsigned();
            $table->integer('text_id')->unsigned();
            $table->integer('source_w_id')->unsigned();
            $table->integer('partner_w_id')->unsigned();

            $table->mediumText('sentence_xml');

            $table->timestamps();

            $table->unique(['collocation_id', 'text_id', 'source_w_id', 'partner_w_id'], 'collocation_examples_pair_unique');

            $table->index('text_id');

            $table->foreign('collocation_id')
                ->references('id')
                ->on('collocations')
                ->onDelete('cascade')
                ->onUpdate('restrict');
        });
    }

    public function down()
    {
        Schema::drop('collocation_examples');
    }
}
