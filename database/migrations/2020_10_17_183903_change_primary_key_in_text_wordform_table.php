<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePrimaryKeyInTextWordformTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('text_wordform', function (Blueprint $table) {
            $table->unsignedSmallInteger('gramset_id')->change();
            
            $table->dropPrimary(['text_id', 'w_id', 'wordform_id']);
            $table->primary(['text_id', 'w_id', 'wordform_id', 'gramset_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('text_wordform', function (Blueprint $table) {
            $table->dropPrimary(['text_id', 'w_id', 'wordform_id', 'gramset_id']);
            $table->primary(['text_id', 'w_id', 'wordform_id']);
        });
    }
}
