<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSsindexInDialectDmarker extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dialect_dmarker', function (Blueprint $table) {
            $table->double('SSindex', 7, 5)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dialect_dmarker', function (Blueprint $table) {
            $table->dropColumn('SSindex'); 
        });
    }
}
