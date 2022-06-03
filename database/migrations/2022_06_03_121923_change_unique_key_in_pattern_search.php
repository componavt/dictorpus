<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUniqueKeyInPatternSearch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pattern_search', function (Blueprint $table) {
            $table->dropUnique('pattern_search_ending_pos_id_gramset_id_unique');
            $table->unique(['dialect_id', 'ending', 'pos_id', 'gramset_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pattern_search', function (Blueprint $table) {
            $table->dropUnique('pattern_search_dialect_id_ending_pos_id_gramset_id_unique');
            $table->unique(['ending', 'pos_id', 'gramset_id']);
        });
    }
}
