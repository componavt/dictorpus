<?php
//        Config::set('laravel-debugbar::config.enabled',false);
        

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => [ 'localeSessionRedirect', 'localizationRedirect'] // , 'web'

    ],
    function()
    {
        Route::get('simple_search', 'HomeController@simpleSearch')->name('simple_search');   
        include_once 'routes/pages.php';
        include_once 'routes/auth.php';
        include_once 'routes/correct.php';
        include_once 'routes/corpus.php';
        include_once 'routes/dict.php';
        include_once 'routes/experiments.php';        
        include_once 'routes/export.php';
        include_once 'routes/import.php';
        include_once 'routes/ldl.php';
        include_once 'routes/service.php';
        include_once 'routes/olodict.php';
        include_once 'routes/stats.php';
        
    }
);
