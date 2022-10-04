<?php
Route::group(
    [
        'prefix' => 'dict/',
    ],
    function()
    {
        Route::post('audio/upload', 'Dict\AudioController@upload');
        Route::get('concept/list', 'Dict\ConceptController@conceptList');
        Route::get('dialect/list', 'Dict\DialectController@dialectList');
        Route::get('example/create/{meaning_id}', 'Dict\ExampleController@create');        
        Route::get('example/store/{meaning_id}', 'Dict\ExampleController@store');        
        Route::get('example/{example_id}/edit', 'Dict\ExampleController@edit');        
        Route::get('example/{example_id}/update', 'Dict\ExampleController@update');        
        Route::get('gramset/list', 'Dict\GramsetController@gramsetList');

        Route::get('lemma/{id}/edit/examples', 'Dict\LemmaController@editExamples');
        Route::get('lemma/{id}/edit/example/{example_id}', 'Dict\LemmaController@editExample');
        Route::get('lemma/{id}/history', 'Dict\LemmaController@history');
        Route::get('lemma/{id}/reload_stem_affix_by_wordforms', 'Dict\LemmaController@reloadStemAffixByWordforms');
        Route::get('lemma/{id}/{label_id}/add_label', 'Dict\LemmaController@addLabel');
        Route::get('lemma/{id}/{label_id}/remove_label', 'Dict\LemmaController@removeLabel');
        Route::get('lemma/{id}/{label_id}/set_status/{status}', 'Dict\LemmaController@setStatus');
        Route::get('lemma/{id}/wordform_total', 'Dict\LemmaController@getWordformTotal'); 
        Route::post('lemma/{id}/update/examples', 'Dict\LemmaController@updateExamples')
                        ->name('lemma.update.examples');
        
        Route::get('lemma/by_wordforms', 'Dict\LemmaController@byWordforms')->name('lemma.by_wordforms');
        Route::get('lemma/store_simple', 'Dict\LemmaController@storeSimple');
        Route::get('lemma/list', 'Dict\LemmaController@lemmaLangList');
        Route::get('lemma/meanings_list', 'Dict\LemmaController@meaningsList');
        Route::get('lemma/list_with_pos_meaning', 'Dict\LemmaController@listWithPosMeaning');
        Route::get('lemma/relation', 'Dict\LemmaController@relation');
        Route::get('lemma/remove/example/{example_id}', 'Dict\LemmaController@removeExample');
        Route::get('lemma/omonyms', 'Dict\LemmaController@omonyms');
        Route::get('lemma/phrases', 'Dict\LemmaController@phrases');
        Route::get('lemma/sorted_by_length', 'Dict\LemmaController@sortedByLength')->name('lemma.sorted_by_length');
        Route::get('lemma/full_new_list', 'Dict\LemmaController@fullNewList');
        Route::get('lemma/limited_new_list', 'Dict\LemmaController@limitedNewList');
        Route::get('lemma/full_updated_list', 'Dict\LemmaController@fullUpdatedList');
        Route::get('lemma/limited_updated_list', 'Dict\LemmaController@limitedUpdatedList');
        Route::get('lemma/wordform_gram_form', 'Dict\LemmaController@wordformGramForm');
        
        Route::put('lemma_wordform/{id}', 'Dict\LemmaWordformController@update')
                        ->name('lemma_wordform.update');
        Route::get('lemma_wordform/{id}/edit/', 'Dict\LemmaWordformController@edit');
        Route::get('lemma_wordform/store', 'Dict\LemmaWordformController@store'); 
        Route::put('lemma_wordform/{id}/destroy', 'Dict\LemmaWordformController@update')
                        ->name('lemma_wordform.destroy');
        Route::get('lemma_wordform/{id}/get_bases', 'Dict\LemmaWordformController@getBases');
        Route::get('lemma_wordform/{id}/load/', 'Dict\LemmaWordformController@load');
        Route::get('lemma_wordform/{id}_{dialect_id}/delete_wordforms', 'Dict\LemmaWordformController@deleteWordforms');
        Route::get('lemma_wordform/{id}_{dialect_id}/get_wordforms', 'Dict\LemmaWordformController@getWordforms');
        Route::get('lemma_wordform/{id}_{dialect_id}/reload/', 'Dict\LemmaWordformController@reload');

        Route::get('lemma_wordform/affix_freq', 'Dict\LemmaWordformController@affixFrequency');
        Route::get('lemma_wordform/pos_common_wordforms', 'Dict\LemmaWordformController@posCommonWordforms');
        
        Route::get('meaning/create', 'Dict\MeaningController@create');
        Route::get('meaning/example/add/{example_id}/{relevance}', 'Dict\MeaningController@addExample');
//        Route::get('meaning/examples/reload/{id}', 'Dict\MeaningController@reloadExamples');
        Route::get('meaning/examples/load/{id}', 'Dict\MeaningController@loadExamples');
        Route::get('meaning/examples/load_more/{id}', 'Dict\MeaningController@loadMoreExamples');
        Route::get('meaning/{meaning_id}/remove_label/{label_id}', 'Dict\MeaningController@removeLabel');
        
        Route::get('pos', 'Dict\PartOfSpeechController@index');

        Route::get('reverse_lemma/', 'Dict\ReverseLemmaController@index');
        Route::get('reverse_lemma/inflexion_groups', 'Dict\ReverseLemmaController@inflexionGroups');
        Route::get('reverse_lemma/tmpCreateAllReverse', 'Dict\ReverseLemmaController@tmpCreateAllReverse');

        Route::get('concept/sosd/', 'Dict\ConceptController@SOSD');
        
        Route::get('wordform/create', 'Dict\WordformController@create'); 
        Route::get('wordform/with_multiple_lemmas', 'Dict\WordformController@withMultipleLemmas');
        
        Route::resource('audio', 'Dict\AudioController',
                       ['names' => [/*'update' => 'audio.update',
                                    'store' => 'audio.store',*/
                                    'destroy' => 'audio.destroy']]);
        
        Route::resource('concept', 'Dict\ConceptController',
                       ['names' => ['update' => 'concept.update',
                                    'store' => 'concept.store',
                                    'destroy' => 'concept.destroy']]);
        
        Route::resource('concept_category', 'Dict\ConceptCategoryController',
                       ['names' => ['update' => 'concept_category.update',
                                    'store' => 'concept_category.store',
                                    'destroy' => 'concept_category.destroy']]);
        
        Route::resource('dialect', 'Dict\DialectController',
                       ['names' => ['update' => 'dialect.update',
                                    'store' => 'dialect.store',
                                    'destroy' => 'dialect.destroy']]);

        Route::resource('gram', 'Dict\GramController',
                       ['names' => ['update' => 'gram.update',
                                    'store' => 'gram.store',
                                    'destroy' => 'gram.destroy']]);

        Route::resource('gramset', 'Dict\GramsetController',
                       ['names' => ['update' => 'gramset.update',
                                    'store' => 'gramset.store',
                                    'destroy' => 'gramset.destroy']]);

        Route::resource('gramset_category', 'Dict\GramsetCategoryController',
                       ['names' => ['update' => 'gramset_category.update',
                                    'store' => 'gramset_category.store',
                                    'destroy' => 'gramset_category.destroy']]);

        Route::resource('lang', 'Dict\LangController',
                       ['names' => ['update' => 'lang.update',
                                    'store' => 'lang.store',
                                    'destroy' => 'lang.destroy']]);
        
        Route::resource('lemma', 'Dict\LemmaController',
                       ['names' => ['update' => 'lemma.update',
                                    'store' => 'lemma.store',
                                    'show' => 'lemma.show',
                                    'create' => 'lemma.create',
                                    'destroy' => 'lemma.destroy']]);

/*        Route::resource('lemma_wordform', 'Dict\LemmaWordformController',
                       ['names' => ['update' => 'lemma_wordform.update',
                                    'store' => 'lemma_wordform.store',
                                    'destroy' => 'lemma_wordform.destroy']]); */

        Route::resource('pos', 'Dict\PartOfSpeechController',
                       ['names' => ['update' => 'pos.update',
                                    'store' => 'pos.store',
                                    'destroy' => 'pos.destroy']]);

        Route::resource('relation', 'Dict\RelationController',
                       ['names' => ['update' => 'relation.update',
                                    'store' => 'relation.store',
                                    'destroy' => 'relation.destroy']]);
        
        Route::resource('wordform', 'Dict\WordformController',
                       ['names' => ['update' => 'wordform.update']]);
    });        
