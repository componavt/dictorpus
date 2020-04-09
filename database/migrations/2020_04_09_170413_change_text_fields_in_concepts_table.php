<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTextFieldsInConceptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('concepts', function (Blueprint $table) {
            $table->string('text_en', 256)->change();
            $table->string('text_ru', 256)->change();
            $table->string('wiki_photo', 256)->change();
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
            $table->string('text_en', 60)->change();
            $table->string('text_ru', 60)->change();
            $table->string('wiki_photo', 50)->change();
        });
    }
}
