<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWIdInSentenceTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sentence_translations', function (Blueprint $table) {
            $table->integer('w_id')->after('sentence_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sentence_translations', function (Blueprint $table) {
            $table->dropColumn('w_id'); 
        });
    }
}
