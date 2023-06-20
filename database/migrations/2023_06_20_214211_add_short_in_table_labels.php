<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShortInTableLabels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('labels', function (Blueprint $table) {
            $table->string('short_en', 15);
            $table->string('short_ru', 15);
            $table->unsignedTinyInteger('visible')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('labels', function (Blueprint $table) {
            $table->dropColumn('short_en');
            $table->dropColumn('short_ru');
            $table->dropColumn('visible');
        });
    }
}

