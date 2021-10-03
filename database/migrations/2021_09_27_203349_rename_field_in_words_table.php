<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameFieldInWordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('words', function (Blueprint $table) {
            $table->renameColumn('sentence_id', 's_id');
/*            $table->integer('sentence_id')->unsigned();*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('words', function (Blueprint $table) {
/*            $table->dropColumn('sentence_id'); */
            $table->renameColumn('s_id', 'sentence_id');
        });
    }
}
