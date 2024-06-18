<?php
Route::group(
    [
        'prefix' => 'ldl/',
    ],
    function()
    {
        Route::get('concept/{concept_id}', 'Library\LdlController@concept');
        Route::get('meaning/examples/load/{id}', 'Library\LdlController@loadExamples');
        Route::get('meaning/examples/load_more/{id}', 'Library\LdlController@loadMoreExamples');
        Route::get('stats', 'Library\LdlController@stats');
    });        
        Route::get('ldl', 'Library\LdlController@index');
        