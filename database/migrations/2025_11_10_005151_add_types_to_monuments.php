<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypesToMonuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Добавляем JSON-поле (в Laravel 5.2 используем text)
        Schema::table('monuments', function (Blueprint $table) {
            $table->text('types')->nullable()->after('volume');
        });

        // 2. Переносим type_id → [type_id]
        DB::table('monuments')
            ->whereNotNull('type_id')
            ->chunk(100, function ($monuments) {
                foreach ($monuments as $m) {
                    $json = json_encode([(int)$m->type_id]);
                    DB::table('monuments')
                        ->where('id', $m->id)
                        ->update(['types' => $json]);
                }
            });

        // 3. Удаляем старое поле
        Schema::table('monuments', function (Blueprint $table) {
            $table->dropColumn('type_id');
        });
    }

    public function down()
    {
        // Восстанавливаем type_id (берём первый элемент из types или null)
        Schema::table('monuments', function (Blueprint $table) {
            $table->unsignedTinyInteger('type_id')->nullable()->after('volume');
        });

        DB::table('monuments')
            ->whereNotNull('types')
            ->chunk(100, function ($monuments) {
                foreach ($monuments as $m) {
                    $types = json_decode($m->types, true);
                    $typeId = is_array($types) && !empty($types) ? (int)$types[0] : null;
                    DB::table('monuments')
                        ->where('id', $m->id)
                        ->update(['type_id' => $typeId]);
                }
            });

        Schema::table('monuments', function (Blueprint $table) {
            $table->dropColumn('types');
        });
    }
}
