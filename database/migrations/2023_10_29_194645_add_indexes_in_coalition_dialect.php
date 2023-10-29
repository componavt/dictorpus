<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexesInCoalitionDialect extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coalition_dialect', function (Blueprint $table) {
            $table->unique(['coalition', 'dialect_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coalition_dialect', function (Blueprint $table) {
            $table->dropUnique(['coalition', 'dialect_id']);
        });
    }
}
