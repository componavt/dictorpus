<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['auth.ristikanza', 'api.locale'],
    'prefix' => 'api/ristikanza/texts',
], function () {
    Route::get('dialects', 'Api\RistikanzaTextController@dialects');
    Route::get('districts', 'Api\RistikanzaTextController@districts');
    Route::get('ethnographic', 'Api\RistikanzaTextController@ethnographic');
    Route::get('folklore', 'Api\RistikanzaTextController@folklore');
    Route::get('form-values', 'Api\RistikanzaTextController@formValues');
    Route::get('genres', 'Api\RistikanzaTextController@genres');
    Route::get('places', 'Api\RistikanzaTextController@places');
    Route::get('topics', 'Api\RistikanzaTextController@topics');
    Route::get('{id}', 'Api\RistikanzaTextController@show');
});
