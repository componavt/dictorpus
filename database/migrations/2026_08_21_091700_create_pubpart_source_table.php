<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePubpartSourceTable extends Migration
{
    public function up()
    {
        Schema::create('pubpart_source', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('source_id');
            $table->unsignedInteger('pubpart_id');

            $table->unique(
                array('source_id', 'pubpart_id'),
                'pubpart_source_source_pubpart_unique'
            );

            $table->string('pages', 20)->nullable();

            $table->foreign(
                'source_id',
                'pubpart_source_source_id_foreign'
            )
                ->references('id')
                ->on('sources')
                ->onDelete('cascade');

            $table->foreign(
                'pubpart_id',
                'pubpart_source_pubpart_id_foreign'
            )
                ->references('id')
                ->on('pubparts')
                ->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pubpart_source');
    }
}