<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParentCorpusInGenres extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('genres', function (Blueprint $table) {
            $table->unsignedSmallInteger('corpus_id')->nullable()->after('id');
            $table->foreign('corpus_id')->references('id')->on('corpuses');
            
            $table->unsignedSmallInteger('parent_id')->nullable()->after('corpus_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('genres', function (Blueprint $table) {
            $table->dropForeign('genres_corpus_id_foreign');
            $table->dropColumn('corpus_id');
            $table->dropColumn('parent_id');
        });
    }
}
