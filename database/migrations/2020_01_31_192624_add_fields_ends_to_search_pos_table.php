<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsEndsToSearchPosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_pos', function (Blueprint $table) {
            $table->float('eval_ends')->nullable();
            $table->float('eval_ends_gen')->nullable();
            $table->smallInteger('win_ends')->unsigned()->nullable();
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
            $table->dropColumn('eval_ends');
            $table->dropColumn('eval_ends_gen');
            $table->dropColumn('win_ends');
        });
    }
}
