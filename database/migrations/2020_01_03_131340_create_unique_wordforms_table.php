<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUniqueWordformsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unique_wordforms', function (Blueprint $table) {
            $table->increments('id');
            $column = $table->string('wordform',50)->unique();
            $column->collation ='utf8_bin';
            $table->integer('pos_id');
            $table->string('gramsets',50);
            $table->float('pos_val');
            $table->float('gram_val');
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
        Schema::drop('unique_wordforms');
    }
}
