<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldLangToSearchPosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_pos', function (Blueprint $table) {
            $table->smallInteger('lang_id')->unsigned();
            $table->dropUnique('search_pos_wordform_pos_id_unique');
            $table->unique([ 'wordform', 'pos_id', 'lang_id' ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('search_pos', function (Blueprint $table) {
            $table->dropUnique('search_pos_wordform_pos_id_lang_id_unique');
            $table->dropColumn('lang_id');
            $table->unique([ 'wordform', 'pos_id' ]);
        });
    }
}
