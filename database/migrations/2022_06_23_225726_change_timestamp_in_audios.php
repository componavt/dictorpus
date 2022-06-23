<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTimestampInAudios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audios', function (Blueprint $table) {
            $table->datetime('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'))->change();
            $table->datetime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audios', function (Blueprint $table) {
            $table->datetime('updated_at')->change();
            $table->datetime('created_at')->change();
        });
    }
}
