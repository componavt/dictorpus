<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLabelSyntypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('label_syntype', function (Blueprint $table) {
            $table->unsignedSmallInteger('label_id');
            $table->foreign('label_id')->references('id')->on('labels')->cascadeOnDelete();
            
            $table->unsignedSmallInteger('syntype_id');
            $table->foreign('syntype_id')->references('id')->on('syntypes')->cascadeOnDelete();
            
            $table->unique(['label_id','syntype_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('label_syntype');
    }
}
