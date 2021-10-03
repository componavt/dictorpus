<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWordIdInTextWordformTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('text_wordform', function (Blueprint $table) {
            $table->integer('word_id')->unsigned();
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
            $table->dropColumn('word_id'); 
        });
    }
}
