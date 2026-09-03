<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class AddReferenceTypeToBibleTextTable extends Migration
{
    /**
     * Добавляет тип связи текста с библейским фрагментом.
     *
     * 1 — основной библейский текст;
     * 2 — параллельное место.
     */
    public function up()
    {
        Schema::table('bible_text', function (Blueprint $table) {
            $table->tinyInteger('reference_type')
                ->unsigned()
                ->default(1)
                ->after('comment');
        });
    }


    /**
     * Удаляет поле типа библейской ссылки.
     */
    public function down()
    {
        Schema::table('bible_text', function (Blueprint $table) {
            $table->dropColumn('reference_type');
        });
    }
}
