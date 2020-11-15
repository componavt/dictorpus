<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreatedUpdatedWithoutGramInLemmaFeatureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lemma_features', function (Blueprint $table) {
            $table->unsignedTinyInteger('without_gram')->nullable();            
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
        Schema::table('lemma_features', function (Blueprint $table) {
            $table->dropColumn('updated_at');
            $table->dropColumn('created_at');
            $table->dropColumn('without_gram');
        });
    }
}
