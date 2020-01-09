<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSearchGramsetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_gramset', function (Blueprint $table) {
            $table->increments('id');
            $column = $table->string('wordform',50);
            $column->collation ='utf8_bin';
            $table->integer('gramset_id');
            $table->string('ending',50)->nullable();
            $table->float('eval_end')->nullable();
            $table->float('eval_end_gen')->nullable();
            $table->string('affix',50)->nullable();
            $table->float('eval_aff')->nullable();
            $table->float('eval_aff_gen')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            $table->unique([ 'wordform', 'gramset_id' ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('search_gramset');
    }
}
