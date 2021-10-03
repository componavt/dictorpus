<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSentenceIdInTextWordformTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('text_wordform', function (Blueprint $table) {
            $table->integer('sentence_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('text_wordform', function (Blueprint $table) {
            $table->dropColumn('sentence_id'); 
        });
    }
}
