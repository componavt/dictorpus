<?php
        Route::get('karelian_runes', 'Corpus\CollectionController@karelianRunes');
        Route::get('karelian_legends', 'Corpus\CollectionController@karelianLegends');
        
Route::group(
    [
        'prefix' => 'corpus/',
    ],
    function()
    {
        Route::get('audiotext/map', 'Corpus\AudiotextController@onMap');
        Route::get('audiotext/add_files/{text_id}', 'Corpus\AudiotextController@addFiles');
        Route::get('audiotext/remove_file/{text_id}_{audiotext_id}', 'Corpus\AudiotextController@removeFile');
        Route::get('audiotext/choose_files/{text_id}', 'Corpus\AudiotextController@chooseFiles');
        Route::get('audiotext/show_files/{text_id}', 'Corpus\AudiotextController@showFiles');
//        Route::post('audiotext/upload', 'Corpus\AudiotextController@upload')->name('audiotext.upload');
        Route::get('audiotext/{id}', 'Corpus\AudiotextController@show')->name('audiotext.show');
        
        Route::get('author/store', 'Corpus\AuthorController@simpleStore');

        Route::get('district/birth_list', 'Corpus\DistrictController@birthDistrictList');
        Route::get('district/list', 'Corpus\DistrictController@districtList');
        Route::get('district/{id}/text_count', 'Corpus\DistrictController@textCount');
        
        Route::get('collection/2/topics/{topic_id}', 'Corpus\CollectionController@runesForTopic');
        Route::get('collection/2/topics', 'Corpus\CollectionController@runeTopics');
        Route::get('collection/2/{plot_id}', 'Corpus\CollectionController@runesForPlot');
        Route::get('collection/3/motives/{motive_id}', 'Corpus\CollectionController@predictionTextsForMotive');
        Route::get('collection/3/motives', 'Corpus\CollectionController@predictionMotives');
        Route::get('collection/3/{cycle_id}', 'Corpus\CollectionController@predictionTextsForCycle');
        Route::get('collection/{id}', 'Corpus\CollectionController@show');
        Route::get('collection', 'Corpus\CollectionController@index');
        Route::get('cycle/list', 'Corpus\CycleController@cycleList');
//        Route::get('gram_search', 'Corpus\GramSearchController@index');
        
        Route::get('informant/{id}/audio', 'Corpus\InformantController@audio')->name('informant.audio');
        Route::get('informant/{id}/get_lang', 'Corpus\InformantController@getLang');
        
        Route::get('informant/store', 'Corpus\InformantController@simpleStore');
        Route::get('genre/list', 'Corpus\GenreController@genreList');
        Route::get('motive/list', 'Corpus\MotiveController@motiveList');
        Route::get('place/birth_list', 'Corpus\PlaceController@birthPlaceList');
        Route::get('place/list', 'Corpus\PlaceController@placeList');
        Route::get('place/store', 'Corpus\PlaceController@simpleStore');
        Route::get('plot/list', 'Corpus\PlotController@plotList');
        
        Route::get('recorder/store', 'Corpus\RecorderController@simpleStore');
        Route::get('region/{id}/text_count', 'Corpus\RegionController@textCount');
        
        Route::get('sentence/{id}/edit', 'Corpus\SentenceController@edit');
        Route::get('sentence/{id}/markup', 'Corpus\SentenceController@markup');
        Route::get('sentence/results', 'Corpus\SentenceController@results');
        Route::get('sentence/word_gram_form', 'Corpus\SentenceController@wordGramForm');

        Route::get('sentence/{sentence_id}/fragment/{w_id}/edit', 'Corpus\SentenceFragmentController@edit');
        Route::get('sentence/{sentence_id}/fragment/{w_id}/update', 'Corpus\SentenceFragmentController@update');
        
        Route::get('sentence/{sentence_id}/translation/{w_id}_{lang_id}/create', 'Corpus\SentenceTranslationController@create');
        Route::get('sentence/{sentence_id}/translation/{w_id}_{lang_id}/store', 'Corpus\SentenceTranslationController@store');
        Route::get('sentence/{sentence_id}/translation/{w_id}_{lang_id}/edit', 'Corpus\SentenceTranslationController@edit');
        Route::get('sentence/{sentence_id}/translation/{w_id}_{lang_id}/update', 'Corpus\SentenceTranslationController@update');
        
        Route::get('speech_corpus', 'Corpus\TextController@speechCorpus');
        Route::get('spellchecking', 'Corpus\TextController@spellchecking');
        Route::post('spellchecking', 'Corpus\TextController@analysSpellchecking');
        
        Route::get('text/{id}/check_sentence', 'Corpus\TextController@checkSentences')->name('text.check_sentences');
        Route::get('text/{id}/edit/example/{example_id}', 'Corpus\TextController@editExample');
        Route::get('text/{id}/history', 'Corpus\TextController@history');
        Route::get('text/{id}/markup', 'Corpus\TextController@markupText');
        Route::get('text/{id}/sentences', 'Corpus\TextController@editSentences');
        Route::get('text/{id}/photos', 'Corpus\TextController@photos');
        Route::post('text/{id}/photos', 'Corpus\TextController@updatePhotos')->name('text.update.photos');
        Route::delete('text/{id}/photos/{photo_id}', 'Corpus\TextController@deletePhoto')->name('text.photos.destroy');
        Route::get('text/{id}/stats', 'Corpus\TextController@stats');
        Route::post('text/{id}/update/examples', 'Corpus\TextController@updateExamples')
                        ->name('text.update.examples');
        Route::get('text/{text}/concordance', 'Corpus\TextController@concordance')->name('text.concordance');
        Route::get('text/add_example/{example_id}', 'Corpus\TextController@addExample');
        Route::get('text/frequency/lemmas', 'Dict\LemmaController@frequencyInTexts');
        Route::get('text/frequency/symbols', 'Corpus\TextController@frequencySymbols');
        Route::get('text/full_new_list', 'Corpus\TextController@fullNewList');
        Route::get('text/full_updated_list', 'Corpus\TextController@fullUpdatedList');
        Route::get('text/limited_new_list', 'Corpus\TextController@limitedNewList');
        Route::get('text/limited_updated_list', 'Corpus\TextController@limitedUpdatedList');
        Route::get('text/markup_all_texts', 'Corpus\TextController@markupAllTexts');
        Route::get('text/sentence', 'Corpus\TextController@showWordInSentence');
        Route::get('text/simple_search', 'Corpus\TextController@simpleSearch')->name('text.simple_search');

        Route::get('topic/list', 'Corpus\TopicController@topicList');
        Route::get('topic/store', 'Corpus\TopicController@simpleStore');
        
        Route::get('word/add_gramset/{id}', 'Corpus\WordController@addGramset');        
        Route::get('word/create_checked_block', 'Corpus\WordController@getWordCheckedBlock');        
        Route::get('word/edit/{text_id}_{w_id}', 'Corpus\WordController@edit');       
        Route::get('word/freq_dict', 'Corpus\WordController@frequencyDict');
        Route::get('word/load_lemma_block/{text_id}_{w_id}', 'Corpus\WordController@loadLemmaBlock');       
        Route::get('word/load_word_block/{text_id}_{w_id}', 'Corpus\WordController@loadWordBlock');       
        Route::get('word/load_unlinked_lemma_block/', 'Corpus\WordController@loadUnlinkedLemmaBlock');       
        Route::get('word/prediction', 'Corpus\WordController@lemmaGramsetPrediction');
        Route::get('word/update_meaning_links', 'Corpus\WordController@updateMeaningLinks');
        Route::get('word/update_word_block/{text_id}_{w_id}', 'Corpus\WordController@updateWordBlock');       

        Route::get('video', 'Corpus\VideoController@index');

        Route::resource('author', 'Corpus\AuthorController',
                       ['names' => ['update' => 'author.update',
                                    'store' => 'author.store',
                                    'destroy' => 'author.destroy']]);
        
        Route::resource('corpus', 'Corpus\CorpusController',
                       ['names' => ['update' => 'corpus.update',
                                    'store' => 'corpus.store',
                                    'destroy' => 'corpus.destroy']]);
        
        Route::resource('cycle', 'Corpus\CycleController',
                       ['names' => ['update' => 'cycle.update',
                                    'store' => 'cycle.store',
                                    'destroy' => 'cycle.destroy']]);
        
        Route::resource('genre', 'Corpus\GenreController',
                       ['names' => ['update' => 'genre.update',
                                    'store' => 'genre.store',
                                    'destroy' => 'genre.destroy']]);
        
        Route::resource('text', 'Corpus\TextController',
                       ['names' => ['update' => 'text.update',
                                    'store' => 'text.store',
                                    'destroy' => 'text.destroy']]);
        
        Route::resource('district', 'Corpus\DistrictController',
                       ['names' => ['update' => 'district.update',
                                    'store' => 'district.store',
                                    'destroy' => 'district.destroy']]);

        Route::resource('informant', 'Corpus\InformantController',
                       ['names' => ['update' => 'informant.update',
                                    'store' => 'informant.store',
                                    'destroy' => 'informant.destroy']]);
        
        Route::resource('monument', 'Corpus\MonumentController',
                       ['names' => ['update' => 'monument.update',
                                    'store' => 'monument.store',
                                    'show' => 'monument.show',
                                    'destroy' => 'monument.destroy']]);
        
        Route::resource('motive', 'Corpus\MotiveController',
                       ['names' => ['update' => 'motive.update',
                                    'store' => 'motive.store',
                                    'destroy' => 'motive.destroy']]);
        
        Route::resource('motype', 'Corpus\MotypeController',
                       ['names' => ['update' => 'motype.update',
                                    'store' => 'motype.store',
                                    'destroy' => 'motype.destroy']]);
        
        Route::resource('place', 'Corpus\PlaceController',
                       ['names' => ['update' => 'place.update',
                                    'store' => 'place.store',
                                    'destroy' => 'place.destroy']]);
        
        Route::resource('plot', 'Corpus\PlotController',
                       ['names' => ['update' => 'plot.update',
                                    'store' => 'plot.store',
                                    'destroy' => 'plot.destroy']]);
        
        Route::resource('recorder', 'Corpus\RecorderController',
                       ['names' => ['update' => 'recorder.update',
                                    'store' => 'recorder.store',
                                    'destroy' => 'recorder.destroy']]);
        
        Route::resource('region', 'Corpus\RegionController',
                       ['names' => ['update' => 'region.update',
                                    'store' => 'region.store',
                                    'destroy' => 'region.destroy']]);
        
        Route::resource('sentence', 'Corpus\SentenceController',
                       ['names' => ['update' => 'sentence.update',
                                    'store' => 'sentence.store',
                                    'destroy' => 'sentence.destroy']]);
        
        Route::resource('source', 'Corpus\SourceController',
                       ['names' => ['update' => 'source.update',
                                    'store' => 'source.store',
                                    'destroy' => 'source.destroy']]);

        Route::resource('topic', 'Corpus\TopicController',
                       ['names' => ['update' => 'topic.update',
                                    'store' => 'topic.store',
                                    'destroy' => 'topic.destroy']]);
    });                
        