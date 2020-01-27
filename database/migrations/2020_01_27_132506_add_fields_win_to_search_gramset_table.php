<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsWinToSearchGramsetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_gramset', function (Blueprint $table) {
            $table->smallInteger('win_end')->unsigned();
            $table->smallInteger('win_aff')->unsigned();
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
            $table->dropColumn('win_end');
            $table->dropColumn('win_aff');
        });
    }
}
