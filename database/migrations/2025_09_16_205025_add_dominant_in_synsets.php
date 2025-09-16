<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDominantInSynsets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('synsets', function (Blueprint $table) {
            $table->unsignedInteger('dominant_id')->after('pos_id')->nullable();
            $table->foreign('dominant_id')->references('id')->on('meanings')->cascadeOnDelete();
            $table->text('descr')->after('dominant_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('synsets', function (Blueprint $table) {
            $table->dropForeign(['dominant_id']);
            $table->dropColumn('dominant_id');
            $table->dropColumn('descr');
        });
    }
}
