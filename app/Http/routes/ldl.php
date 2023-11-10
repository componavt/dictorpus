<?php
Route::group(
    [
        'prefix' => 'ldl/',
    ],
    function()
    {
        Route::get('concept/{concept_id}', 'Library\LdlController@concept');
        Route::get('meaning/examples/load/{id}', 'Dict\LdlController@loadExamples');
        Route::get('meaning/examples/load_more/{id}', 'Dict\LdlController@loadMoreExamples');
    });        
        Route::get('ldl', 'Library\LdlController@index');
        