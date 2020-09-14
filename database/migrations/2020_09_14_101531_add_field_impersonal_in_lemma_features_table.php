<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldImpersonalInLemmaFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lemma_features', function (Blueprint $table) {
            $table->unsignedTinyInteger('impersonal')->nullable();
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
            $table->dropColumn('impersonal');
        });
    }
}
