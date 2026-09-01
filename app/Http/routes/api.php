<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['auth.ristikanza', 'api.locale'],
    'prefix' => 'api/ristikanza/texts',
], function () {
    Route::get('bible_books', 'Api\RistikanzaTextController@bibleBooks');
    Route::get('bible', 'Api\RistikanzaTextController@bible');
    Route::get('dialects', 'Api\RistikanzaTextController@dialects');
    Route::get('districts', 'Api\RistikanzaTextController@districts');
    Route::get('ethnographic', 'Api\RistikanzaTextController@ethnographic');
    Route::get('folklore', 'Api\RistikanzaTextController@folklore');
    Route::get('folklore_genres', 'Api\RistikanzaTextController@folkloreGenres');
    Route::get('for_map', 'Api\RistikanzaTextController@forMap');
    Route::get('form-values', 'Api\RistikanzaTextController@formValues');
    Route::get('genres', 'Api\RistikanzaTextController@genres');
    Route::get('monument_books', 'Api\RistikanzaTextController@monumentBooks');
    Route::get('monuments', 'Api\RistikanzaTextController@monuments');
    Route::get('places', 'Api\RistikanzaTextController@places');
    Route::get('plots', 'Api\RistikanzaTextController@plots');
    Route::get('topics', 'Api\RistikanzaTextController@topics');
    Route::get('{id}', 'Api\RistikanzaTextController@show');
});
