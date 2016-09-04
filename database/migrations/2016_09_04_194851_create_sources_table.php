<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sources', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('title', 150)->nullable();
            $table->string('author', 150)->nullable();
            $table->smallInteger('year')->unsigned()->nullable();
            $table->smallInteger('ieeh_archive_number1')->unsigned()->nullable();
            $table->smallInteger('ieeh_archive_number2')->unsigned()->nullable();
            $table->string('pages', 15)->nullable();
            $table->string('comment', 255)->nullable();            
            
//            $table->timestamps();
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sources');
    }
}
