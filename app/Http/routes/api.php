Route::group(['prefix' => 'api/ristikanza/texts'], function () {
    Route::get('ethnographic', 'Api\RistikanzaTextController@ethnographic');
    Route::get('{id}', 'Api\RistikanzaTextController@show');
});