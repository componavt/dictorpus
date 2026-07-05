<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddIndexToPunctsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('puncts', function (Blueprint $table) {
            $table->index(['text_id', 's_id', 'left_w_id'], 'idx_puncts_textid_sid_leftwid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('puncts', function (Blueprint $table) {
            $table->dropIndex('idx_puncts_textid_sid_leftwid');
        });
    }
}
