<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use DB;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Устанавливаем временную директорию только для этого проекта
        if (env('TMP_DIR')) {
            putenv('TMPDIR=' . env('TMP_DIR'));
        }
/*        DB::listen(function ($query) {
            $location = collect(debug_backtrace())->filter(function ($trace) {
                return isset($trace['file']) && !str_contains($trace['file'], 'vendor/');
            })->first(); // берем первый элемент не из каталога вендора
            $bindings = implode(", ", $query->bindings); // форматируем привязку как строку
            Log::info("
                   ------------
                   Sql: $query->sql
                   Bindings: $bindings
                   Time: $query->time
                   File: ${location['file']}
                   Line: ${location['line']}
                   ------------
            ");
        });*/
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
