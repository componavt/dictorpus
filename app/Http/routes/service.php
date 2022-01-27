<?php
        Route::get('service', 'Library\ServiceController@index');
        Route::get('service/addCompTypeToPhrases', 'Library\ServiceController@addCompTypeToPhrases');
        Route::get('service/check_author', 'Library\ServiceController@checkAuthors');
        Route::get('service/check_wordforms_by_rules', 'Library\ServiceController@checkWordformsByRules');
        Route::get('service/copy_lemmas', 'Library\ServiceController@copyLemmas');
        Route::get('service/illative_table', 'Library\ServiceController@illativeTable');       
        Route::get('service/multidict', 'Library\ServiceController@multidictView');       
        Route::get('service/multidict/select', 'Library\ServiceController@multidictSelect');       
        Route::get('service/reGenerateTverPartic2active', 'Library\ServiceController@reGenerateTverPartic2active');
        Route::get('service/regenerate_wrong_names', 'Library\ServiceController@reGenerateWrongNames');
        Route::get('service/regenerate_livvic_ill_pl', 'Library\ServiceController@reGenerateLivvicIllPl');
        Route::get('service/reload_stem_affixes', 'Library\ServiceController@reloadStemAffixes');
        Route::get('service/select_lemmas_for_multidict', 'Library\ServiceController@selectLemmasForMultidict');
        Route::get('service/wordforms', 'Library\ServiceController@checkWordforms');
        Route::get('service/wordforms_by_wordform_total', 'Library\ServiceController@wordformsByWordformTotal');
