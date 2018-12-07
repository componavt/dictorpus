<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldInReverseLemmaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reverse_lemmas', function (Blueprint $table) {
            $table->string('inflexion', 10)->nullable()->collate('utf8_bin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reverse_lemmas', function (Blueprint $table) {
            $table->dropColumn('inflexion');
        });
    }
}
