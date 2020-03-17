<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeCollateOfFieldsInSearchGramsetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_gramset', function (Blueprint $table) {
            $column1 = $table->string('affix', 50)->nullable()->change();
            $column1->collation ='utf8_bin';
            $column2 = $table->string('ending', 50)->nullable()->change();
            $column1->collation ='utf8_bin';

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lemma_wordform', function (Blueprint $table) {
            $column1 = $table->string('affix', 50)->nullable()->change();
            $column1->collation ='utf8_unicode_ci';
            $column2 = $table->string('affix', 50)->nullable()->change();
            $column2->collation ='utf8_unicode_ci';
        });
    }
}
