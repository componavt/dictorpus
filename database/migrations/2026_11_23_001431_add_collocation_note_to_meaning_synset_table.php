<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCollocationNoteToMeaningSynsetTable extends Migration
{
    public function up()
    {
        Schema::table('meaning_synset', function (Blueprint $table) {
            $table->text('collocation_note')->nullable();
        });
    }

    public function down()
    {
        Schema::table('meaning_synset', function (Blueprint $table) {
            $table->dropColumn('collocation_note');
        });
    }
}
