<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSearchPosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_pos', function (Blueprint $table) {
            $table->increments('id');
            $column = $table->string('wordform',50);
            $column->collation ='utf8_bin';
            $table->integer('pos_id');
            $table->string('ending',50)->nullable();
            $table->float('eval_end')->nullable();
            $table->float('eval_end_gen')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            $table->unique([ 'wordform', 'pos_id' ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('search_pos');
    }
}
