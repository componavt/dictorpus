<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTextFieldsInConceptTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('concepts', function (Blueprint $table) {
            $table->string('text_en', 60)->change();
            $table->string('text_ru', 60)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('concepts', function (Blueprint $table) {
            $table->string('text_en', 45)->change();
            $table->string('text_ru', 45)->change();
        });
    }
}
