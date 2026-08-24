<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBibleTextTable extends Migration
{
    public function up()
    {
        Schema::create('bible_text', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('bible_id');
            $table->unsignedInteger('text_id');
            $table->unsignedSmallInteger('chapter');

            /*
             * Оба NULL означают ссылку на главу целиком.
             */
            $table->unsignedSmallInteger('verse_from')->nullable();

            $table->unsignedSmallInteger('verse_to')->nullable();

            /*
             * Nullable, потому что в публикации-источнике
             * может не быть выделенной части или заголовка.
             */
            $table->unsignedInteger('pubpart_id')
                ->nullable();

            $table->string('comment', 1024)
                ->nullable();

            /*
             * Основной поиск по канонической адресации:
             *
             * WHERE bible_id = ?
             *   AND chapter = ?
             *   AND verse_from <= ?
             *   AND verse_to >= ?
             */
            $table->index(
                array(
                    'bible_id',
                    'chapter',
                    'verse_from',
                    'verse_to',
                ),
                'bible_text_bible_chapter_verses_index'
            );

            $table->foreign(
                'text_id',
                'bible_text_text_id_foreign'
            )
                ->references('id')
                ->on('texts')
                ->onDelete('restrict');

            $table->foreign(
                'bible_id',
                'bible_text_bible_id_foreign'
            )
                ->references('id')
                ->on('bibles')
                ->onDelete('restrict');

            $table->foreign(
                'pubpart_id',
                'bible_text_pubpart_id_foreign'
            )
                ->references('id')
                ->on('pubparts')
                ->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bible_text');
    }
}