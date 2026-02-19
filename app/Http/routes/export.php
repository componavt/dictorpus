<?php
Route::get('service/export', 'Library\ExportController@index');
Route::group(
    [
        'prefix' => 'service/export/',
    ],
    function () {
        Route::get('bible', 'Library\ExportController@bible');
        Route::get('compounds_for_unimorph', 'Library\ExportController@compoundsToUniMorph');
        Route::get('concepts', 'Library\ExportController@concepts');
        Route::get('concepts_without_images', 'Library\ExportController@conceptsWithoutImages');
        Route::get('conll', 'Library\ExportController@textsToCONLL');
        Route::get('conll/annotation', 'Library\ExportController@annotationConll');
        Route::get('for_mobile', 'Library\ExportController@forMobile');
        Route::get('for_speech', 'Library\ExportController@forSpeech');
        Route::get('lemma_with_pos', 'Library\ExportController@lemmasWithPOS');
        Route::get('concordance', 'Library\ExportController@concordance')->name('text.concordance.export');
        Route::get('for_yandex', 'Library\ExportController@forYandex');
        Route::get('text/{text}/annotated/{type}', 'Library\ExportController@annotatedText')->name('text.annotated.export');
        Route::get('multidict', 'Library\ExportController@multidict');
        Route::get('multidict_without_concepts', 'Library\ExportController@multidictWithoutConcepts');
        Route::get('olo_dict', 'Library\ExportController@oloDict');
        Route::get('runes', 'Library\ExportController@runes');
        Route::get('sentences', 'Library\ExportController@sentencesToLines');
        Route::get('unimorph', 'Library\ExportController@lemmasToUniMorph');
    }
);
