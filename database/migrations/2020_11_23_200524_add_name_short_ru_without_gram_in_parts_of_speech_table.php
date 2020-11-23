<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNameShortRuWithoutGramInPartsOfSpeechTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parts_of_speech', function (Blueprint $table) {
            $table->string('name_short_ru', 15)->nullable();
            $table->unsignedTinyInteger('without_gram')->nullable();            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parts_of_speech', function (Blueprint $table) {
            $table->dropColumn('without_gram');
            $table->dropColumn('name_short_ru');
        });
    }
}
