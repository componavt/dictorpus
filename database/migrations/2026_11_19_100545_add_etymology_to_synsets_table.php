<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEtymologyToSynsetsTable extends Migration
{
    /**
     * Добавляет текстовые поля синсета.
     */
    public function up()
    {
        Schema::table('synsets', function (Blueprint $table) {
            $table->text('etymology')->nullable();
            $table->text('potential_candidates')->nullable();
        });
    }

    /**
     * Удаляет добавленные текстовые поля.
     */
    public function down()
    {
        Schema::table('synsets', function (Blueprint $table) {
            $table->dropColumn([
                'etymology',
                'potential_candidates',
            ]);
        });
    }
}
