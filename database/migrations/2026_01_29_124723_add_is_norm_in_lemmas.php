<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsNormInLemmas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lemmas', function (Blueprint $table) {
            $table->tinyinteger('is_norm')->default(1)->after('pos_id');
            $table->index(['is_norm'], 'idx_lemmas_is_norm');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lemmas', function (Blueprint $table) {
            $table->dropIndex('idx_lemmas_is_norm');
            $table->dropColumn('is_norm');
        });
    }
}
