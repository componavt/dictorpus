<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFieldGramsetIdInSearchGramsetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_gramset', function (Blueprint $table) {
            $table->integer('gramset_id')->nullable()->change();
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
            $table->integer('gramset_id')->change();
        });
    }
}
