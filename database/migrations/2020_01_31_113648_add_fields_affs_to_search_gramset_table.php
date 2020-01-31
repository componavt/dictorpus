<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsAffsToSearchGramsetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_gramset', function (Blueprint $table) {
            $table->float('eval_affs')->nullable();
            $table->float('eval_affs_gen')->nullable();
            $table->smallInteger('win_affs')->unsigned()->nullable();
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
            $table->dropColumn('eval_affs');
            $table->dropColumn('eval_affs_gen');
            $table->dropColumn('win_affs');
        });
    }
}
