<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreatePutypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('putypes', function (Blueprint $table) {
            $table->unsignedTinyInteger('id', true);
            $table->string('slug', 50)->unique();
            $table->string('name_en', 100);
            $table->string('name_ru', 100);
            $table->json('symbols')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('putypes');
    }
}
