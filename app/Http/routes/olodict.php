<?php
        Route::get('olodict/abbr', 'Library\OlodictController@abbr');
        Route::get('olodict/help', 'Library\OlodictController@help');
        Route::get('olodict/gram_links/{letter}', 'Library\OlodictController@gramLinks');
        Route::get('olodict/lemma_list', 'Library\OlodictController@lemmaList');
        Route::get('olodict/lemmas', 'Library\OlodictController@lemmas');
        Route::get('olodict', 'Library\OlodictController@index');
        