<?php
Route::get('service/export', 'Library\ExportController@index');
Route::group(
    [
        'prefix' => 'service/export/',
    ],
    function()
    {
        Route::get('bible', 'Library\ExportController@exportBible');        
        Route::get('compounds_for_unimorph', 'Library\ExportController@exportCompoundsToUniMorph'); 
        Route::get('conceps_without_images', 'Library\ExportController@concepsWithoutImages'); 
        Route::get('conll', 'Library\ExportController@exportTextsToCONLL');        
        Route::get('conll/annotation', 'Library\ExportController@exportAnnotationConll'); 
        Route::get('for_mobile', 'Library\ExportController@forMobile');        
        Route::get('for_speech', 'Library\ExportController@forSpeech');        
        Route::get('lemma_with_pos', 'Library\ExportController@exportLemmasWithPOS');
        Route::get('multidict', 'Library\ExportController@multidict');                
        Route::get('multidict_without_concepts', 'Library\ExportController@multidictWithoutConcepts');                
        Route::get('sentences', 'Library\ExportController@exportSentencesToLines');                
        Route::get('unimorph', 'Library\ExportController@exportLemmasToUniMorph'); 
    });