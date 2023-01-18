<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeBirthDateInInformantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('informants', function (Blueprint $table) {
            $table->string('birth_date',20)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('informants', function (Blueprint $table) {
            $table->unsignedSmallInteger('birth_date')->nullable();
        });
    }
}
