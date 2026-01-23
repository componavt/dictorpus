<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCelebrationTypeTextTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('celebration_type_text', function (Blueprint $table) {
            $table->unsignedTinyInteger('type_id');

            $table->unsignedInteger('text_id');
            $table->foreign('text_id')->references('id')->on('texts');
            
            $table->primary(['type_id', 'text_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('celebration_type_text');
    }
}
