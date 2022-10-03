<?php
        Route::get('karelian_runes', 'Corpus\CollectionController@karelianRunes');
        Route::get('karelian_legends', 'Corpus\CollectionController@karelianLegends');
        
        Route::get('corpus/audiotext/map', 'Corpus\AudiotextController@onMap');
        Route::get('corpus/audiotext/add_files/{text_id}', 'Corpus\AudiotextController@addFiles');
        Route::get('corpus/audiotext/remove_file/{text_id}_{audiotext_id}', 'Corpus\AudiotextController@removeFile');
        Route::get('corpus/audiotext/choose_files/{text_id}', 'Corpus\AudiotextController@chooseFiles');
        Route::get('corpus/audiotext/show_files/{text_id}', 'Corpus\AudiotextController@showFiles');
//        Route::post('corpus/audiotext/upload', 'Corpus\AudiotextController@upload')->name('audiotext.upload');
        Route::get('corpus/audiotext/{id}', 'Corpus\AudiotextController@show')->name('audiotext.show');
        
        Route::get('corpus/author/store', 'Corpus\AuthorController@simpleStore');

        Route::get('corpus/district/birth_list', 'Corpus\DistrictController@birthDistrictList');
        Route::get('corpus/district/list', 'Corpus\DistrictController@districtList');
        
        Route::get('corpus/collection', 'Corpus\CollectionController@index');
        Route::get('corpus/collection/{id}', 'Corpus\CollectionController@show');
        Route::get('corpus/cycle/list', 'Corpus\CycleController@cycleList');
//        Route::get('corpus/gram_search', 'Corpus\GramSearchController@index');
        
        Route::get('corpus/informant/store', 'Corpus\InformantController@simpleStore');
        Route::get('corpus/genre/list', 'Corpus\GenreController@genreList');
        Route::get('corpus/place/birth_list', 'Corpus\PlaceController@birthPlaceList');
        Route::get('corpus/place/list', 'Corpus\PlaceController@placeList');
        Route::get('corpus/place/store', 'Corpus\PlaceController@simpleStore');
        Route::get('corpus/plot/list', 'Corpus\PlotController@plotList');
        Route::get('corpus/recorder/store', 'Corpus\RecorderController@simpleStore');
        
        Route::get('corpus/sentence/{id}/edit', 'Corpus\SentenceController@edit');
        Route::get('corpus/sentence/{id}/markup', 'Corpus\SentenceController@markup');
        Route::get('corpus/sentence/results', 'Corpus\SentenceController@results');
        Route::get('corpus/sentence/word_gram_form', 'Corpus\SentenceController@wordGramForm');

        Route::get('corpus/sentence/{sentence_id}/fragment/{w_id}/edit', 'Corpus\SentenceFragmentController@edit');
        Route::get('corpus/sentence/{sentence_id}/fragment/{w_id}/update', 'Corpus\SentenceFragmentController@update');
        
        Route::get('corpus/sentence/{sentence_id}/translation/{w_id}_{lang_id}/create', 'Corpus\SentenceTranslationController@create');
        Route::get('corpus/sentence/{sentence_id}/translation/{w_id}_{lang_id}/store', 'Corpus\SentenceTranslationController@store');
        Route::get('corpus/sentence/{sentence_id}/translation/{w_id}_{lang_id}/edit', 'Corpus\SentenceTranslationController@edit');
        Route::get('corpus/sentence/{sentence_id}/translation/{w_id}_{lang_id}/update', 'Corpus\SentenceTranslationController@update');

        Route::get('corpus/speech_corpus', 'Corpus\TextController@speechCorpus');
        
        Route::get('corpus/text/{id}/history', 'Corpus\TextController@history');
        Route::get('corpus/text/{id}/markup', 'Corpus\TextController@markupText');
        Route::get('corpus/text/{id}/sentences', 'Corpus\TextController@editSentences');
        Route::get('corpus/text/{id}/edit/example/{example_id}', 'Corpus\TextController@editExample');
        Route::post('corpus/text/{id}/update/examples', 'Corpus\TextController@updateExamples')
                        ->name('text.update.examples');
        Route::get('corpus/text/add_example/{example_id}', 'Corpus\TextController@addExample');
        Route::get('corpus/text/frequency/lemmas', 'Dict\LemmaController@frequencyInTexts');
        Route::get('corpus/text/frequency/symbols', 'Corpus\TextController@frequencySymbols');
        Route::get('corpus/text/full_new_list', 'Corpus\TextController@fullNewList');
        Route::get('corpus/text/full_updated_list', 'Corpus\TextController@fullUpdatedList');
        Route::get('corpus/text/limited_new_list', 'Corpus\TextController@limitedNewList');
        Route::get('corpus/text/limited_updated_list', 'Corpus\TextController@limitedUpdatedList');
        Route::get('corpus/text/markup_all_texts', 'Corpus\TextController@markupAllTexts');
        Route::get('corpus/text/sentence', 'Corpus\TextController@showWordInSentence');

        Route::get('corpus/topic/list', 'Corpus\TopicController@topicList');
        Route::get('corpus/topic/store', 'Corpus\TopicController@simpleStore');
        
        Route::get('corpus/word/add_gramset/{id}', 'Corpus\WordController@addGramset');        
        Route::get('corpus/word/create_checked_block', 'Corpus\WordController@getWordCheckedBlock');        
        Route::get('corpus/word/edit/{text_id}_{w_id}', 'Corpus\WordController@edit');       
        Route::get('corpus/word/freq_dict', 'Corpus\WordController@frequencyDict');
        Route::get('corpus/word/load_lemma_block/{text_id}_{w_id}', 'Corpus\WordController@loadLemmaBlock');       
        Route::get('corpus/word/load_word_block/{text_id}_{w_id}', 'Corpus\WordController@loadWordBlock');       
        Route::get('corpus/word/prediction', 'Corpus\WordController@lemmaGramsetPrediction');
        Route::get('corpus/word/update_meaning_links', 'Corpus\WordController@updateMeaningLinks');
        Route::get('corpus/word/update_word_block/{text_id}_{w_id}', 'Corpus\WordController@updateWordBlock');       

        Route::get('corpus/video', 'Corpus\VideoController@index');

        Route::resource('corpus/author', 'Corpus\AuthorController',
                       ['names' => ['update' => 'author.update',
                                    'store' => 'author.store',
                                    'destroy' => 'author.destroy']]);
        
        Route::resource('corpus/corpus', 'Corpus\CorpusController',
                       ['names' => ['update' => 'corpus.update',
                                    'store' => 'corpus.store',
                                    'destroy' => 'corpus.destroy']]);
        
        Route::resource('corpus/cycle', 'Corpus\CycleController',
                       ['names' => ['update' => 'cycle.update',
                                    'store' => 'cycle.store',
                                    'destroy' => 'cycle.destroy']]);
        
        Route::resource('corpus/genre', 'Corpus\GenreController',
                       ['names' => ['update' => 'genre.update',
                                    'store' => 'genre.store',
                                    'destroy' => 'genre.destroy']]);
        
        Route::resource('corpus/text', 'Corpus\TextController',
                       ['names' => ['update' => 'text.update',
                                    'store' => 'text.store',
                                    'destroy' => 'text.destroy']]);
        
        Route::resource('corpus/district', 'Corpus\DistrictController',
                       ['names' => ['update' => 'district.update',
                                    'store' => 'district.store',
                                    'destroy' => 'district.destroy']]);

        Route::resource('corpus/informant', 'Corpus\InformantController',
                       ['names' => ['update' => 'informant.update',
                                    'store' => 'informant.store',
                                    'destroy' => 'informant.destroy']]);
        
        Route::resource('corpus/place', 'Corpus\PlaceController',
                       ['names' => ['update' => 'place.update',
                                    'store' => 'place.store',
                                    'destroy' => 'place.destroy']]);
        
        Route::resource('corpus/plot', 'Corpus\PlotController',
                       ['names' => ['update' => 'plot.update',
                                    'store' => 'plot.store',
                                    'destroy' => 'plot.destroy']]);
        
        Route::resource('corpus/recorder', 'Corpus\RecorderController',
                       ['names' => ['update' => 'recorder.update',
                                    'store' => 'recorder.store',
                                    'destroy' => 'recorder.destroy']]);
        
        Route::resource('corpus/region', 'Corpus\RegionController',
                       ['names' => ['update' => 'region.update',
                                    'store' => 'region.store',
                                    'destroy' => 'region.destroy']]);
        
        Route::resource('corpus/sentence', 'Corpus\SentenceController',
                       ['names' => ['update' => 'sentence.update',
                                    'store' => 'sentence.store',
                                    'destroy' => 'sentence.destroy']]);
        
        Route::resource('corpus/source', 'Corpus\SourceController',
                       ['names' => ['update' => 'source.update',
                                    'store' => 'source.store',
                                    'destroy' => 'source.destroy']]);

        Route::resource('corpus/topic', 'Corpus\TopicController',
                       ['names' => ['update' => 'topic.update',
                                    'store' => 'topic.store',
                                    'destroy' => 'topic.destroy']]);
        
        