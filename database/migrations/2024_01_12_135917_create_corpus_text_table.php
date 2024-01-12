<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

use App\Models\Corpus\Text;

class CreateCorpusTextTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corpus_text', function (Blueprint $table) {
            $table->smallInteger('corpus_id')->unsigned();
            $table->     foreign('corpus_id')->references('id')->on('corpuses');
            
            $table->integer('text_id')->unsigned();
            $table->foreign('text_id')->references('id')->on('texts');
            
            $table->primary(['corpus_id', 'text_id']);
        });
        
        $texts = Text::all();
        foreach($texts as $text) {
            DB::table('corpus_text')->insert([
                    'corpus_id'=>$text->corpus_id, 
                    'text_id'  =>$text->id
                ]);
        }        
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('corpus_text');
    }
}
