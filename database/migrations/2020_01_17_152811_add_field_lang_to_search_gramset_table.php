<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldLangToSearchGramsetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_gramset', function (Blueprint $table) {
            $table->smallInteger('lang_id')->unsigned();
            $table->dropUnique('search_gramset_wordform_gramset_id_unique');
            $table->unique([ 'wordform', 'gramset_id', 'lang_id' ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('search_gramset', function (Blueprint $table) {
            $table->dropUnique('search_gramset_wordform_gramset_id_lang_id_unique');
            $table->dropColumn('lang_id');
            $table->unique([ 'wordform', 'gramset_id' ]);
        });
    }
}
