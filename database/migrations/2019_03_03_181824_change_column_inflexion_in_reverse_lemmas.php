<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnInflexionInReverseLemmas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reverse_lemmas', function (Blueprint $table) {
            $table->renameColumn('inflexion', 'affix');
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
            $table->renameColumn('affix', 'inflexion');
        });
    }
}
