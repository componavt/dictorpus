<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShortNamesToCorpusesTable extends Migration
{
    public function up()
    {
        Schema::table('corpuses', function (Blueprint $table) {
            $table->string('short_ru', 20)->nullable()->after('name_ru');
            $table->string('short_en', 20)->nullable()->after('name_en');
        });
    }

    public function down()
    {
        Schema::table('corpuses', function (Blueprint $table) {
            $table->dropColumn(['short_ru', 'short_en']);
        });
    }
}
