<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNamesInConceptCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('concept_categories', function (Blueprint $table) {
            $table->string('name_en', 75)->change();
            $table->string('name_ru', 75)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('concept_categories', function (Blueprint $table) {
            $table->string('name_en', 45)->change();
            $table->string('name_ru', 45)->change();
        });
    }
}
