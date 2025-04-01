<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLgrInPartsOfSpeech extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parts_of_speech', function (Blueprint $table) {
            $table->string('lgr', 5)->nullable();
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
            $table->dropColumn('lgr');
        });
    }
}
