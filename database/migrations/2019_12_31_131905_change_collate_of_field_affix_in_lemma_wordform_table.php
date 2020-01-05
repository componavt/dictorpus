<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeCollateOfFieldAffixInLemmaWordformTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lemma_wordform', function (Blueprint $table) {
            $column = $table->string('affix', 10)->nullable()->change();
            $column->collation ='utf8_bin';

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
            $column = $table->string('affix', 10)->nullable()->change();
            $column->collation ='utf8_unicode_ci';
        });
    }
}
