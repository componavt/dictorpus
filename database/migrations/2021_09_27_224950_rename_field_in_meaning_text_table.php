<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameFieldInMeaningTextTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meaning_text', function (Blueprint $table) {
            $table->renameColumn('sentence_id', 's_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meaning_text', function (Blueprint $table) {
            $table->renameColumn('s_id', 'sentence_id');
        });
    }
}
