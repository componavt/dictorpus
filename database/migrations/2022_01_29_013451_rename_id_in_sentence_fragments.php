<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameIdInSentenceFragments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sentence_fragments', function (Blueprint $table) {
            $table->renameColumn('id', 'sentence_id');
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
            $table->renameColumn('sentence_id', 'id');
        });
    }
}
