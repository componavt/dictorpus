<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAnnotInPlotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plots', function (Blueprint $table) {
            $table->string('annot_ru', 2047)->nullable();
            $table->string('annot_en', 2047)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('label_lemma', function (Blueprint $table) {
            $table->dropColumn('annot_ru'); 
            $table->dropColumn('annot_en'); 
        });
    }
}
