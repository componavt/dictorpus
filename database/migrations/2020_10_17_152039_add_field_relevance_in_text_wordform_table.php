<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldRelevanceInTextWordformTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('text_wordform', function (Blueprint $table) {
            $table->unsignedTinyInteger('relevance')->default(1);
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
            $table->dropColumn('relevance');
        });
    }
}
