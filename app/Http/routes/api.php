<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['auth.ristikanza', 'api.locale'],
    'prefix' => 'api/ristikanza/texts',
], function () {
    Route::get('ethnographic', 'Api\RistikanzaTextController@ethnographic');
    Route::get('form-values', 'Api\RistikanzaTextController@formValues');
    Route::get('{id}', 'RistikanzaTextController@show');
});
