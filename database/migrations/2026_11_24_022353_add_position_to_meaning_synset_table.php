<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPositionToMeaningSynsetTable extends Migration
{
    public function up()
    {
        Schema::table('meaning_synset', function (Blueprint $table) {
            $table->unsignedInteger('position')->nullable();
        });
    }

    public function down()
    {
        Schema::table('meaning_synset', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
}
