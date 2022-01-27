<?php
        Route::get('service/export', 'Library\ExportController@index');
        Route::get('export/bible', 'Library\ExportController@exportBible');        
        Route::get('export/compounds_for_unimorph', 'Library\ExportController@exportCompoundsToUniMorph'); 
        Route::get('export/conll', 'Library\ExportController@exportTextsToCONLL');        
        Route::get('export/conll/annotation', 'Library\ExportController@exportAnnotationConll'); 
        Route::get('export/for_mobile', 'Library\ExportController@forMobile');        
        Route::get('export/lemma_with_pos', 'Library\ExportController@exportLemmasWithPOS');
        Route::get('export/sentences', 'Library\ExportController@exportSentencesToLines');                
        Route::get('export/unimorph', 'Library\ExportController@exportLemmasToUniMorph'); 
        Route::get('export/for_speech', 'Library\ExportController@forSpeech');        
