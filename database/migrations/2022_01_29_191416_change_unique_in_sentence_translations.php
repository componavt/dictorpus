<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUniqueInSentenceTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sentence_translations', function (Blueprint $table) {
            $table->dropForeign(['lang_id']);
            $table->dropForeign(['sentence_id']);
            $table->dropUnique([ 'sentence_id', 'lang_id' ]);
            
            $table->unique([ 'sentence_id', 'w_id', 'lang_id' ]);
            $table->foreign('sentence_id')->references('id')->on('sentences');
            $table->foreign('lang_id')->references('id')->on('langs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sentence_translations', function (Blueprint $table) {
            $table->dropForeign(['lang_id']);
            $table->dropForeign(['sentence_id']);
            $table->dropUnique([ 'sentence_id', 'w_id', 'lang_id' ]);
            
            $table->unique([ 'sentence_id', 'lang_id' ]);
            $table->foreign('sentence_id')->references('id')->on('sentences');
            $table->foreign('lang_id')->references('id')->on('langs');
        });
    }
}
