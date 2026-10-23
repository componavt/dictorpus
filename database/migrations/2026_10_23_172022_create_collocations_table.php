<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollocationsTable extends Migration
{
    public function up()
    {
        Schema::create('collocations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';

            $table->increments('id');

            $table->integer('meaning_id')->unsigned();
            $table->integer('collocate_meaning_id')->unsigned();

            $table->string('concept_category_id', 4)->nullable();

            $table->text('display_gloss');

            $table->timestamps();

            $table->unique(
                ['meaning_id', 'collocate_meaning_id'],
                'collocations_meaning_collocate_unique'
            );

            $table->index('collocate_meaning_id');
            $table->index('concept_category_id');

            $table->foreign('meaning_id')
                ->references('id')
                ->on('meanings')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->foreign('collocate_meaning_id')
                ->references('id')
                ->on('meanings')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->foreign('concept_category_id')
                ->references('id')
                ->on('concept_categories')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
    }

    public function down()
    {
        Schema::drop('collocations');
    }
}
