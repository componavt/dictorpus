<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSearchGramsetListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_gramset_list', function (Blueprint $table) {
            $table->integer('search_id');
            $table->integer('gramset_id');
            $table->smallinteger('count');
            $table->string('type', 5)->default('end');
            
            $table->unique([ 'search_id', 'gramset_id', 'type' ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('search_gramset_list');
    }
}
