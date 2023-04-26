<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDialectDmarkerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dialect_dmarker', function (Blueprint $table) {
            $table->dropColumn('frequency'); 
            $table->dropColumn('fraction'); 
            $table->integer('t_frequency')->unsigned()->nullable();
            $table->double('t_fraction', 7, 5)->nullable();
            $table->integer('w_frequency')->unsigned()->nullable();
            $table->double('w_fraction', 7, 5)->nullable();
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
            $table->integer('frequency')->unsigned()->nullable();
            $table->double('fraction', 7, 5)->nullable();
            $table->dropColumn('t_frequency'); 
            $table->dropColumn('t_fraction'); 
            $table->dropColumn('w_frequency'); 
            $table->dropColumn('w_fraction'); 
        });
    }
}
