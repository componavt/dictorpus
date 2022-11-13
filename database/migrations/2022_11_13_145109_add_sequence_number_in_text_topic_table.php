<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSequenceNumberInTextTopicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('text_topic', function (Blueprint $table) {
            $table->unsignedTinyInteger('sequence_number')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('text_topic', function (Blueprint $table) {
            $table->dropColumn('sequence_number'); 
        });
    }
}
