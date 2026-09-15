<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePubpartTextTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pubpart_text', function ($table) {
            $table->unsignedInteger('text_id');
            $table->unsignedInteger('pubpart_id');

            $table->string('pages', 20)->nullable();

            $table->primary(
                ['text_id', 'pubpart_id'],
                'pubpart_text_text_id_pubpart_id_unique'
            );

            $table->foreign('text_id')
                ->references('id')
                ->on('texts')
                ->onDelete('cascade');

            $table->foreign('pubpart_id')
                ->references('id')
                ->on('pubparts')
                ->onDelete('cascade');
        });

        /*
         * Копируем существующие связи:
         *
         * pubpart_source:
         *     source_id, pubpart_id, pages
         *
         * texts:
         *     id, source_id
         *
         * pubpart_text:
         *     text_id, pubpart_id, pages
         *
         * Для source, используемого одним текстом, перенос однозначен.
         * Для shared source связь намеренно копируется всем текстам:
         * такие немногочисленные случаи будут проверены вручную позже.
         */
        DB::statement("
            INSERT INTO pubpart_text (
                text_id,
                pubpart_id,
                pages
            )
            SELECT DISTINCT
                t.id AS text_id,
                ps.pubpart_id,
                ps.pages
            FROM pubpart_source ps
            INNER JOIN texts t
                ON t.source_id = ps.source_id
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('pubpart_text');
    }
}
