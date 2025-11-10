<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLangMonumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Создаём таблицу
        Schema::create('lang_monument', function (Blueprint $table) {
            $table->smallInteger('lang_id')->unsigned();
            $table->     foreign('lang_id')->references('id')->on('langs');
            
            $table->unsignedInteger('monument_id');
            $table->        foreign('monument_id')->references('id')->on('monuments')->onDelete('cascade');
            
            $table->primary(['lang_id', 'monument_id']);
        });
        
        // 2. Переносим существующие данные
        DB::table('monuments')
            ->whereNotNull('lang_id')
            ->chunk(100, function ($monuments) {
                foreach ($monuments as $m) {
                    DB::table('lang_monument')->insert([
                        'lang_id'     => $m->lang_id,
                        'monument_id' => $m->id,
                    ]);
                }
            });
            
        // 3. Удаляем старое поле
        Schema::table('monuments', function (Blueprint $table) {
            $table->dropForeign(['lang_id']);
            $table->dropColumn('lang_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Восстанавливаем lang_id
        Schema::table('monuments', function (Blueprint $table) {
            $table->unsignedSmallInteger('lang_id')->nullable()->after('bibl_descr');
            $table->foreign('lang_id')->references('id')->on('langs');
        });

        // Заполняем из pivot
        DB::table('lang_monument')->chunk(100, function ($rows) {
            foreach ($rows as $r) {
                DB::table('monuments')
                    ->where('id', $r->monument_id)
                    ->update(['lang_id' => $r->lang_id]);
            }
        });
        
        Schema::dropIfExists('lang_monument');
    }
}
