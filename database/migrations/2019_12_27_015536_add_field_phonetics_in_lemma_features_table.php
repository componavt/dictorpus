<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldPhoneticsInLemmaFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lemma_features', function (Blueprint $table) {
            $table->string('phonetics', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lemma_features', function (Blueprint $table) {
            $table->dropColumn('phonetics');
        });
    }
}
