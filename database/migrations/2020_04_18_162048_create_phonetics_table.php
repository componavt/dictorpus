<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhoneticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phonetics', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('lemma_id')->unsigned();
            $table->foreign('lemma_id')->references('id')->on('lemmas');
            
            $column1 = $table->string('phonetic',100);
            $column1->collation ='utf8_bin';
            
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
        Schema::drop('phonetics');
    }
}
