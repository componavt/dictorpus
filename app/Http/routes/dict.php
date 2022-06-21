<?php
        Route::get('dict/concept/list', 'Dict\ConceptController@conceptList');
        Route::get('dict/dialect/list', 'Dict\DialectController@dialectList');
        Route::get('dict/example/create/{meaning_id}', 'Dict\ExampleController@create');        
        Route::get('dict/example/store/{meaning_id}', 'Dict\ExampleController@store');        
        Route::get('dict/example/{example_id}/edit', 'Dict\ExampleController@edit');        
        Route::get('dict/example/{example_id}/update', 'Dict\ExampleController@update');        
        Route::get('dict/gramset/list', 'Dict\GramsetController@gramsetList');

        Route::get('dict/lemma/{id}/edit/examples', 'Dict\LemmaController@editExamples');
        Route::get('dict/lemma/{id}/edit/example/{example_id}', 'Dict\LemmaController@editExample');
        Route::get('dict/lemma/{id}/history', 'Dict\LemmaController@history');
        Route::get('dict/lemma/{id}/reload_stem_affix_by_wordforms', 'Dict\LemmaController@reloadStemAffixByWordforms');
        Route::get('dict/lemma/{id}/{label_id}/set_status/{status}', 'Dict\LemmaController@setStatus');
        Route::get('dict/lemma/{id}/wordform_total', 'Dict\LemmaController@getWordformTotal'); 
        Route::post('dict/lemma/{id}/update/examples', 'Dict\LemmaController@updateExamples')
                        ->name('lemma.update.examples');
        
        Route::get('dict/lemma/by_wordforms', 'Dict\LemmaController@byWordforms')->name('lemma.by_wordforms');
        Route::get('dict/lemma/store_simple', 'Dict\LemmaController@storeSimple');
        Route::get('dict/lemma/list', 'Dict\LemmaController@lemmaLangList');
        Route::get('dict/lemma/meanings_list', 'Dict\LemmaController@meaningsList');
        Route::get('dict/lemma/list_with_pos_meaning', 'Dict\LemmaController@listWithPosMeaning');
        Route::get('dict/lemma/relation', 'Dict\LemmaController@relation');
        Route::get('dict/lemma/remove/example/{example_id}', 'Dict\LemmaController@removeExample');
        Route::get('dict/lemma/omonyms', 'Dict\LemmaController@omonyms');
        Route::get('dict/lemma/phrases', 'Dict\LemmaController@phrases');
        Route::get('dict/lemma/sorted_by_length', 'Dict\LemmaController@sortedByLength')->name('lemma.sorted_by_length');
        Route::get('dict/lemma/full_new_list', 'Dict\LemmaController@fullNewList');
        Route::get('dict/lemma/limited_new_list', 'Dict\LemmaController@limitedNewList');
        Route::get('dict/lemma/full_updated_list', 'Dict\LemmaController@fullUpdatedList');
        Route::get('dict/lemma/limited_updated_list', 'Dict\LemmaController@limitedUpdatedList');
        Route::get('dict/lemma/wordform_gram_form', 'Dict\LemmaController@wordformGramForm');
        
        Route::put('dict/lemma_wordform/{id}', 'Dict\LemmaWordformController@update')
                        ->name('lemma_wordform.update');
        Route::get('dict/lemma_wordform/{id}/edit/', 'Dict\LemmaWordformController@edit');
        Route::get('dict/lemma_wordform/store', 'Dict\LemmaWordformController@store'); 
        Route::put('dict/lemma_wordform/{id}/destroy', 'Dict\LemmaWordformController@update')
                        ->name('lemma_wordform.destroy');
        Route::get('dict/lemma_wordform/{id}/get_bases', 'Dict\LemmaWordformController@getBases');
        Route::get('dict/lemma_wordform/{id}/load/', 'Dict\LemmaWordformController@load');
        Route::get('dict/lemma_wordform/{id}_{dialect_id}/delete_wordforms', 'Dict\LemmaWordformController@deleteWordforms');
        Route::get('dict/lemma_wordform/{id}_{dialect_id}/get_wordforms', 'Dict\LemmaWordformController@getWordforms');
        Route::get('dict/lemma_wordform/{id}_{dialect_id}/reload/', 'Dict\LemmaWordformController@reload');

        Route::get('dict/lemma_wordform/affix_freq', 'Dict\LemmaWordformController@affixFrequency');
        Route::get('dict/lemma_wordform/pos_common_wordforms', 'Dict\LemmaWordformController@posCommonWordforms');
        
        Route::get('dict/meaning/create', 'Dict\MeaningController@create');
        Route::get('dict/meaning/example/add/{example_id}/{relevance}', 'Dict\MeaningController@addExample');
//        Route::get('dict/meaning/examples/reload/{id}', 'Dict\MeaningController@reloadExamples');
        Route::get('dict/meaning/examples/load/{id}', 'Dict\MeaningController@loadExamples');
        Route::get('dict/meaning/examples/load_more/{id}', 'Dict\MeaningController@loadMoreExamples');
        Route::get('dict/meaning/{meaning_id}/remove_label/{label_id}', 'Dict\MeaningController@removeLabel');
        
        Route::get('dict/pos', 'Dict\PartOfSpeechController@index');

        Route::get('dict/reverse_lemma/', 'Dict\ReverseLemmaController@index');
        Route::get('dict/reverse_lemma/inflexion_groups', 'Dict\ReverseLemmaController@inflexionGroups');
        Route::get('dict/reverse_lemma/tmpCreateAllReverse', 'Dict\ReverseLemmaController@tmpCreateAllReverse');

        Route::get('dict/concept/sosd/', 'Dict\ConceptController@SOSD');
        
        Route::get('dict/wordform/create', 'Dict\WordformController@create'); 
        Route::get('dict/wordform/with_multiple_lemmas', 'Dict\WordformController@withMultipleLemmas');
        
        Route::resource('dict/concept', 'Dict\ConceptController',
                       ['names' => ['update' => 'concept.update',
                                    'store' => 'concept.store',
                                    'destroy' => 'concept.destroy']]);
        
        Route::resource('dict/concept_category', 'Dict\ConceptCategoryController',
                       ['names' => ['update' => 'concept_category.update',
                                    'store' => 'concept_category.store',
                                    'destroy' => 'concept_category.destroy']]);
        
        Route::resource('dict/dialect', 'Dict\DialectController',
                       ['names' => ['update' => 'dialect.update',
                                    'store' => 'dialect.store',
                                    'destroy' => 'dialect.destroy']]);

        Route::resource('dict/gram', 'Dict\GramController',
                       ['names' => ['update' => 'gram.update',
                                    'store' => 'gram.store',
                                    'destroy' => 'gram.destroy']]);

        Route::resource('dict/gramset', 'Dict\GramsetController',
                       ['names' => ['update' => 'gramset.update',
                                    'store' => 'gramset.store',
                                    'destroy' => 'gramset.destroy']]);

        Route::resource('dict/gramset_category', 'Dict\GramsetCategoryController',
                       ['names' => ['update' => 'gramset_category.update',
                                    'store' => 'gramset_category.store',
                                    'destroy' => 'gramset_category.destroy']]);

        Route::resource('dict/lang', 'Dict\LangController',
                       ['names' => ['update' => 'lang.update',
                                    'store' => 'lang.store',
                                    'destroy' => 'lang.destroy']]);
        
        Route::resource('dict/lemma', 'Dict\LemmaController',
                       ['names' => ['update' => 'lemma.update',
                                    'store' => 'lemma.store',
                                    'show' => 'lemma.show',
                                    'create' => 'lemma.create',
                                    'destroy' => 'lemma.destroy']]);

/*        Route::resource('dict/lemma_wordform', 'Dict\LemmaWordformController',
                       ['names' => ['update' => 'lemma_wordform.update',
                                    'store' => 'lemma_wordform.store',
                                    'destroy' => 'lemma_wordform.destroy']]); */

        Route::resource('dict/pos', 'Dict\PartOfSpeechController',
                       ['names' => ['update' => 'pos.update',
                                    'store' => 'pos.store',
                                    'destroy' => 'pos.destroy']]);

        Route::resource('dict/relation', 'Dict\RelationController',
                       ['names' => ['update' => 'relation.update',
                                    'store' => 'relation.store',
                                    'destroy' => 'relation.destroy']]);
        
        Route::resource('dict/wordform', 'Dict\WordformController',
                       ['names' => ['update' => 'wordform.update']]);
        
