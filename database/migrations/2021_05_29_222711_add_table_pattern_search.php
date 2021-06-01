<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTablePatternSearch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pattern_search', function (Blueprint $table) {
            $table->smallInteger('dialect_id')->unsigned();
            
            $table->string('parent_end', 4)->collation('utf8_bin');
            $table->string('ending', 5)->collation('utf8_bin');
            $table->string('end_reverse', 5)->collation('utf8_bin');
            
            $table->tinyInteger('pos_id')->unsigned()->nullable();
            $table->smallInteger('gramset_id')->unsigned()->nullable();
            
            $table->smallInteger('count')->unsigned();
            
            $table->unique(['ending', 'pos_id', 'gramset_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('pattern_search');
    }
}
