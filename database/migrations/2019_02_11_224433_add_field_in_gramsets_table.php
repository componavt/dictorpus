<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldInGramsetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gramsets', function (Blueprint $table) {
            $table->tinyInteger('gramset_category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gramsets', function (Blueprint $table) {
            $table->dropColumn('gramset_category_id');
        });
    }
}
