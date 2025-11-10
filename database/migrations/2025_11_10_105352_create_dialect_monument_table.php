<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDialectMonumentTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        // 1. Создаём таблицу
        Schema::create('dialect_monument', function (Blueprint $table) {
            // dialect 
            $table->unsignedSmallInteger('dialect_id');
            $table->foreign('dialect_id')->references('id')->on('dialects');
            
            $table->unsignedInteger('monument_id');
            $table->foreign('monument_id')->references('id')->on('monuments')->onDelete('cascade');

            $table->primary(['dialect_id', 'monument_id']);
        });

        // 2. Переносим существующие данные
        DB::table('monuments')
                ->whereNotNull('dialect_id')
                ->chunk(100, function ($monuments) {
                    foreach ($monuments as $m) {
                        DB::table('dialect_monument')->insert([
                            'dialect_id' => $m->dialect_id,
                            'monument_id' => $m->id,
                        ]);
                    }
                });

        // 3. Удаляем старое поле
        Schema::table('monuments', function (Blueprint $table) {
            $table->dropForeign(['dialect_id']);
            $table->dropColumn('dialect_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        // Восстанавливаем lang_id
        Schema::table('monuments', function (Blueprint $table) {
            $table->unsignedSmallInteger('dialect_id')->nullable()->after('bibl_descr');
            $table->foreign('dialect_id')->references('id')->on('dialects');
        });

        // Заполняем из pivot
        DB::table('dialect_monument')->chunk(100, function ($rows) {
            foreach ($rows as $r) {
                DB::table('monuments')
                        ->where('id', $r->monument_id)
                        ->update(['dialect_id' => $r->dialect_id]);
            }
        });

        Schema::dropIfExists('dialect_monument');
    }

}
