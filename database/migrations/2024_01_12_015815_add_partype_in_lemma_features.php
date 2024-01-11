<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPartypeInLemmaFeatures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lemma_features', function (Blueprint $table) {
            $table->string('partype_id', 2)->after('impersonal')->nullable();
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
            $table->dropColumn('partype_id');
        });
    }
}
