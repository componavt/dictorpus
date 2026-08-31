<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLangInPublications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('publications', function (Blueprint $table) {
            // lang 
            $table->smallInteger('lang_id')->unsigned()->nullable();
            $table->     foreign('lang_id')->references('id')->on('langs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->dropForeign('publications_lang_id_foreign');
            $table->dropColumn('lang_id');
        });
    }
}
