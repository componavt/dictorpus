<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShortRuToDialectsTable extends Migration
{
    public function up()
    {
        Schema::table('dialects', function (Blueprint $table) {
            $table->string('short_ru', 20)->nullable()->after('name_ru');
        });
    }

    public function down()
    {
        Schema::table('dialects', function (Blueprint $table) {
            $table->dropColumn('short_ru');
        });
    }
}