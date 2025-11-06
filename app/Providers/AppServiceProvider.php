<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
//use Illuminate\Support\Facades\Auth;

//use DB;
//use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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

        View::composer('layouts.master', function ($view) {
            $view->with('scriptTime', microtime(true) - LARAVEL_START);
        });
        
        // Устанавливаем локаль для Carbon
        Carbon::setLocale(app()->getLocale());

        // Устанавливаем системную локаль (для formatLocalized)
        $locale = app()->getLocale();
        if ($locale === 'ru') {
            if (PHP_OS_FAMILY === 'Windows') {
                setlocale(LC_TIME, 'Russian_Russia.UTF-8');
            } else {
                setlocale(LC_TIME, 'ru_RU.UTF-8');
            }
        } elseif ($locale === 'en') {
            if (PHP_OS_FAMILY === 'Windows') {
                setlocale(LC_TIME, 'English_United States.1252');
            } else {
                setlocale(LC_TIME, 'en_US.UTF-8');
            }
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
