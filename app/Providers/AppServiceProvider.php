<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
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

        // Помесячное логирование
        $monthlyFile = storage_path('logs/laravel-' . date('Y-m') . '.log');
        $handler = new \Monolog\Handler\StreamHandler($monthlyFile);
        $this->app['log']->getMonolog()->pushHandler($handler);


        if (env('LOG_SLOW_QUERIES', false)) {
            DB::listen(function ($query) {
                    if ($query->time >= 100) {
                        \Log::info(sprintf(
                            "[SLOW %sms] %s | bindings: %s",
                            $query->time,
                            $query->sql,
                            json_encode($query->bindings)
                        ));

 /*                       // либо через file_put_contents в отдельный файл:
                        file_put_contents(
                            storage_path('logs/slow_queries.log'),
                            date('Y-m-d H:i:s') . " [{$query->time}ms] {$query->sql} | " . json_encode($query->bindings) . PHP_EOL,
                            FILE_APPEND
                        );*/
                    }
                });
        }
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
