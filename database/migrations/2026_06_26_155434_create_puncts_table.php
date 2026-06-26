<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePunctsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('puncts', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('text_id');
            $table->unsignedInteger('sentence_id');

            $table->unsignedInteger('p_id');
            $table->unsignedInteger('p_number');

            $table->string('punct', 10);
            $table->unsignedTinyInteger('putype_id')->nullable();

            $table->unsignedInteger('left_word_number')->nullable();
            $table->unsignedInteger('right_word_number')->nullable();

            $table->unique(['text_id', 'p_id'], 'puncts_text_pid_unique');
            $table->unique(['sentence_id', 'p_number'], 'puncts_sentence_pnumber_unique');

            $table->index(['sentence_id', 'left_word_number'], 'puncts_sentence_left_index');
            $table->index(['sentence_id', 'right_word_number'], 'puncts_sentence_right_index');

            $table->foreign('text_id', 'puncts_text_id_foreign')
                ->references('id')
                ->on('texts')
                ->onDelete('cascade');

            $table->foreign('sentence_id', 'puncts_sentence_id_foreign')
                ->references('id')
                ->on('sentences')
                ->onDelete('cascade');

            $table->foreign('putype_id', 'puncts_putype_id_foreign')
                ->references('id')
                ->on('putypes');
        });    }

    public function down(): void
    {
        Schema::dropIfExists('puncts');
    }
}
