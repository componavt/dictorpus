<?php

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
        'middleware' => [ 'localeSessionRedirect', 'localizationRedirect' ]

    ],
    function()
    {
        Route::get('/', function () {
            return view('welcome');
        });

        Route::auth();

        Route::get('/home', 'HomeController@index');

        Route::get('dict/lang', 'Dict\LangController@index');

        Route::get('dict/lemma/sorted_by_length', 'Dict\LemmaController@sortedByLength');
        
//        Route::get('dict/lemma/tempInsertVepsianLemmas', 'Dict\LemmaController@tempInsertVepsianLemmas');
        
//        Route::get('dict/lemma/meaning/tempInsertVepsianMeanings', 'Dict\MeaningController@tempInsertVepsianMeanings');

        Route::resource('dict/lemma', 'Dict\LemmaController');

    }
);