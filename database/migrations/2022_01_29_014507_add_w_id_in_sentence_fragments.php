<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWIdInSentenceFragments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sentence_fragments', function (Blueprint $table) {
            $table->increments('id')->first();
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
        Schema::table('sentence_fragments', function (Blueprint $table) {
            $table->dropColumn('w_id'); 
            $table->dropColumn('id'); 
        });
    }
}
