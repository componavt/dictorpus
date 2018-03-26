<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IncreaseNumberFieldsInSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sources', function (Blueprint $table) {
            $table->string('ieeh_archive_number1',10)->nullable()->change();
            $table->string('pages', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sources', function (Blueprint $table) {
            $table->smallInteger('ieeh_archive_number1')->unsigned()->nullable()->change();
            $table->string('pages', 15)->nullable()->change();
        });
    }
}
