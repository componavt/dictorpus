<?php
Route::get('service', 'Library\ServiceController@index');
Route::group(
    [
        'prefix' => 'service/',
    ],
    function()
    {
        Route::get('addCompTypeToPhrases', 'Library\ServiceController@addCompTypeToPhrases');
        Route::get('check_author', 'Library\ServiceController@checkAuthors');
        Route::get('check_wordforms_by_rules', 'Library\ServiceController@checkWordformsByRules');
        Route::get('copy_lemmas', 'Library\ServiceController@copyLemmas');
        Route::get('illative_table', 'Library\ServiceController@illativeTable');       
        Route::get('reGenerateTverPartic2active', 'Library\ServiceController@reGenerateTverPartic2active');
        Route::get('regenerate_wrong_names', 'Library\ServiceController@reGenerateWrongNames');
        Route::get('regenerate_livvic_ill_pl', 'Library\ServiceController@reGenerateLivvicIllPl');
        Route::get('reload_stem_affixes', 'Library\ServiceController@reloadStemAffixes');
        Route::get('select_lemmas_for_multidict', 'Library\ServiceController@selectLemmasForMultidict');
        Route::get('wordforms', 'Library\ServiceController@checkWordforms');
        Route::get('wordforms_by_wordform_total', 'Library\ServiceController@wordformsByWordformTotal');
        
        Route::get('dict/multi', 'Library\DictController@multiView');       
        Route::get('dict/multi/select', 'Library\DictController@multiSelect');       
        Route::get('dict/school', 'Library\DictController@schoolView');       
        Route::get('dict/school/select', 'Library\DictController@schoolSelect');       
        Route::get('dict/zaikov', 'Library\DictController@zaikovView');       
        Route::get('dict/zaikov/select', 'Library\DictController@zaikovSelect');  
        Route::get('dict/label/{meaning_id}/store', 'Library\DictController@storeLabel');
        Route::get('dict/lemma/store', 'Library\DictController@storeLemma');
//        Route::get('dict/lemma/{id}/edit', 'Library\DictController@editLemma');
        Route::get('dict/lemma/{id}/update', 'Library\DictController@updateLemma');
        Route::get('dict/meaning/{lemma_id}/create', 'Library\DictController@createMeaning');
        Route::get('dict/meaning/{lemma_id}/store', 'Library\DictController@storeMeaning');
        Route::get('dict/{meaning_id}/label/{label_id}/remove', 'Library\DictController@removeVisibleLabel');
        Route::get('dict/wordforms/{lemma_id}', 'Library\DictController@wordforms');
        
        Route::get('audio/{list}', 'Dict\AudioController@recordGroup');
    });