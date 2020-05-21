<?php
//        Config::set('laravel-debugbar::config.enabled',false);
        

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => [ 'localeSessionRedirect', 'localizationRedirect'] // , 'web'

    ],
    function()
    {
        Route::get('/','HomeController@index');

        // Вызов страницы регистрации пользователя
        Route::get('register', 'AuthController@register');   
        // Пользователь заполнил форму регистрации и отправил
        Route::post('register', 'AuthController@registerProcess'); 
        // Пользователь получил письмо для активации аккаунта со ссылкой сюда
        Route::get('activate/{id}/{code}', 'AuthController@activate');
        // Вызов страницы авторизации
        Route::get('login', 'AuthController@login');
        // Пользователь заполнил форму авторизации и отправил
        Route::post('login', 'AuthController@loginProcess')->name('login');
        // Выход пользователя из системы
        Route::get('logout', 'AuthController@logoutuser');
        // Пользователь забыл пароль и запросил сброс пароля. Это начало процесса - 
        // Страница с запросом E-Mail пользователя
        Route::get('reset', 'AuthController@resetOrder');
        // Пользователь заполнил и отправил форму с E-Mail в запросе на сброс пароля
        Route::post('reset', 'AuthController@resetOrderProcess');
        // Пользователю пришло письмо со ссылкой на эту страницу для ввода нового пароля
        Route::get('reset/{id}/{code}', 'AuthController@resetComplete');
        // Пользователь ввел новый пароль и отправил.
        Route::post('reset/{id}/{code}', 'AuthController@resetCompleteProcess');
        // Сервисная страничка, показываем после заполнения рег формы, формы сброса и т.
        // о том, что письмо отправлено и надо заглянуть в почтовый ящик.
        Route::get('wait', 'AuthController@wait');


        Route::get('/about_veps', function () {
            return view('page.about_veps');
        });
        Route::get('/about_karelians', function () {
            return view('page.about_karelians');
        });
        Route::get('/corpus/frequency', function () {
            return view('page.corpus_freq');
        });
        Route::get('/dict/selections', function () {
            return view('page.dict_selections');
        });
        Route::get('/experiments', function () {
            return view('experiments.index');
        });
        Route::get('/grants', function () {
            return view('page.grants');
        });
        Route::get('/help/text/show', function () {
            return view('help.text.show');
        });
        Route::get('/participants', function () {
            return view('page.participants');
        });
        Route::get('/permission', function () {
            return view('page.permission');
        });
        Route::get('/publ', function () {
            return view('page.publ');
        });
        
        Route::get('/home', 'HomeController@index');
        Route::get('/dumps','DumpDownloadController@index');
        
        Route::get('corpus/text/add/example/{example_id}', 'Corpus\TextController@addExample');
/*        Route::get('corpus/corpus/list', 'Corpus\CorpusController@corpusList');*/
        Route::get('corpus/text/{id}/edit/example/{example_id}', 'Corpus\TextController@editExample');
        Route::post('corpus/text/{id}/update/examples', 'Corpus\TextController@updateExamples')
                        ->name('text.update.examples');

        Route::get('corpus/text/{id}/history', 'Corpus\TextController@history');
        Route::get('corpus/text/sentence', 'Corpus\TextController@showWordInSentence');

        Route::get('corpus/text/full_new_list', 'Corpus\TextController@fullNewList');
        Route::get('corpus/text/limited_new_list', 'Corpus\TextController@limitedNewList');
        Route::get('corpus/text/full_updated_list', 'Corpus\TextController@fullUpdatedList');
        Route::get('corpus/text/limited_updated_list', 'Corpus\TextController@limitedUpdatedList');
        Route::get('corpus/text/word/create_checked_block', 'Corpus\WordController@getWordCheckedBlock');
        Route::get('corpus/text/frequency/symbols', 'Corpus\TextController@frequencySymbols');
        Route::get('corpus/text/frequency/lemmas', 'Dict\LemmaController@frequencyInTexts');
        
        Route::get('corpus/word/freq_dict', 'Corpus\WordController@frequencyDict');
        Route::get('corpus/word/update_meaning_links', 'Corpus\WordController@updateMeaningLinks');

        Route::get('dict/concept/list', 'Dict\ConceptController@conceptList');
        Route::get('dict/dialect/list', 'Dict\DialectController@dialectList');
        Route::get('dict/gramset/list', 'Dict\GramsetController@gramsetList');

        Route::get('dict/lemma/{id}/edit/examples', 'Dict\LemmaController@editExamples');
        Route::get('dict/lemma/{id}/edit/example/{example_id}', 'Dict\LemmaController@editExample');
        Route::get('dict/lemma/{id}/history', 'Dict\LemmaController@history');
        Route::post('dict/lemma/{id}/update/examples', 'Dict\LemmaController@updateExamples')
                        ->name('lemma.update.examples');
        
        Route::get('dict/lemma/store_simple', 'Dict\LemmaController@storeSimple');
        Route::get('dict/lemma/list', 'Dict\LemmaController@lemmaLangList');
        Route::get('dict/lemma/meanings_list', 'Dict\LemmaController@meaningsList');
        Route::get('dict/lemma/list_with_pos_meaning', 'Dict\LemmaController@listWithPosMeaning');
        Route::get('dict/lemma/relation', 'Dict\LemmaController@relation');
        Route::get('dict/lemma/remove/example/{example_id}', 'Dict\LemmaController@removeExample');
        Route::get('dict/lemma/omonyms', 'Dict\LemmaController@omonyms');
        Route::get('dict/lemma/phrases', 'Dict\LemmaController@phrases');
        Route::get('dict/lemma/sorted_by_length', 'Dict\LemmaController@sortedByLength');
        Route::get('dict/lemma/full_new_list', 'Dict\LemmaController@fullNewList');
        Route::get('dict/lemma/limited_new_list', 'Dict\LemmaController@limitedNewList');
        Route::get('dict/lemma/full_updated_list', 'Dict\LemmaController@fullUpdatedList');
        Route::get('dict/lemma/limited_updated_list', 'Dict\LemmaController@limitedUpdatedList');
        Route::get('dict/lemma/{id}/reload_stem_affix_by_wordforms', 'Dict\LemmaController@reloadStemAffixByWordforms');
        
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
        Route::get('dict/meaning/example/add/{example_id}', 'Dict\MeaningController@addExample');
        Route::get('dict/meaning/examples/reload/{id}', 'Dict\MeaningController@reloadExamples');
        Route::get('dict/meaning/examples/load/{id}', 'Dict\MeaningController@loadExamples');
        
        Route::get('dict/pos', 'Dict\PartOfSpeechController@index');

        Route::get('dict/reverse_lemma/', 'Dict\ReverseLemmaController@index');
        Route::get('dict/reverse_lemma/inflexion_groups', 'Dict\ReverseLemmaController@inflexionGroups');
        Route::get('dict/reverse_lemma/tmpCreateAllReverse', 'Dict\ReverseLemmaController@tmpCreateAllReverse');

        Route::get('dict/concept/sosd/', 'Dict\ConceptController@SOSD');
        
        Route::get('dict/wordform/create', 'Dict\WordformController@create'); 
        Route::get('dict/wordform/with_multiple_lemmas', 'Dict\WordformController@withMultipleLemmas');
        Route::get('dict/wordform/tempCheckWordformsWithSpaces', 'Dict\WordformController@tempCheckWordformsWithSpaces');
        Route::get('dict/wordform/tmpFixNegativeVepsVerbForms', 'Dict\WordformController@tmpFixNegativeVepsVerbForms');
      
        Route::get('corpus/text/markup_all_texts', 'Corpus\TextController@markupAllTexts');
        Route::get('corpus/text/{id}/markup', 'Corpus\TextController@markupText');
        Route::get('corpus/video', 'Corpus\VideoController@index');
        
        Route::get('export/compounds_for_unimorph', 'Library\ExportController@exportCompoundsToUniMorph'); 
        Route::get('export/conll', 'Library\ExportController@exportTextsToCONLL');        
        Route::get('export/conll/annotation', 'Library\ExportController@exportAnnotationConll'); 
        Route::get('export/lemma_with_pos', 'Library\ExportController@exportLemmasWithPOS');
        Route::get('export/sentences', 'Library\ExportController@exportSentencesToLines');                
        Route::get('export/unimorph', 'Library\ExportController@exportLemmasToUniMorph'); 

        Route::get('import/concept_parser', 'Library\ImportController@conceptParser');
        Route::get('import/concept_parser_check', 'Library\ImportController@conceptParserCheck');
        Route::get('import/dict_parser', 'Library\ImportController@dictParser');
        Route::get('import/extract_livvic_verbs', 'Library\ImportController@extractVerbs');

        Route::get('import/phonetics_to_lemmas', 'Library\ImportController@phoneticsToLemmas');

        Route::get('experiments/search_by_analog/', 'Library\Experiments\SearchByAnalogController@index');
        Route::get('experiments/search_by_analog/check_word', 'Library\Experiments\SearchByAnalogController@checkWord');
        Route::get('experiments/search_by_analog/error_list', 'Library\Experiments\SearchByAnalogController@errorList');
        Route::get('experiments/search_by_analog/export_error_shift', 'Library\Experiments\SearchByAnalogController@exportErrorShift');
        Route::get('experiments/search_by_analog/export_error_shift_to_dot', 'Library\Experiments\SearchByAnalogController@exportErrorShiftToDot');
        Route::get('experiments/search_by_analog/fill_search_pos', 'Library\Experiments\SearchByAnalogController@fillSearchPos');
        Route::get('experiments/search_by_analog/fill_search_gramset', 'Library\Experiments\SearchByAnalogController@fillSearchGramset');
        Route::get('experiments/search_by_analog/evaluate_search_gramset_by_affix', 'Library\Experiments\SearchByAnalogController@evaluateSearchGramsetByAffix');
        Route::get('experiments/search_by_analog/evaluate_search_table', 'Library\Experiments\SearchByAnalogController@evaluateSearchPosGramset');
        Route::get('experiments/search_by_analog/results_search', 'Library\Experiments\SearchByAnalogController@resultsSearch');
        Route::get('experiments/search_by_analog/results_search_gramset', 'Library\Experiments\SearchByAnalogController@resultsSearchGramset');
        Route::get('experiments/search_by_analog/results_search_pos', 'Library\Experiments\SearchByAnalogController@resultsSearchPOS');
        Route::get('experiments/search_by_analog/write_winners', 'Library\Experiments\SearchByAnalogController@writeWinners');

        Route::get('experiments/vowel_gradation/', 'Library\Experiments\VowelGradationController@index');
        Route::get('experiments/vowel_gradation/nom_gen_part/{num}/{pos_code}/{sl}/{part_gr}', 'Library\Experiments\VowelGradationController@nomGenPart');
        
        Route::get('service', 'Library\ServiceController@index');
        Route::get('service/addCompTypeToPhrases', 'Library\ServiceController@addCompTypeToPhrases');
        Route::get('service/add_wordform_affixes', 'Library\ServiceController@addWordformAffixes');
        Route::get('service/add_unmarked_links', 'Library\ServiceController@addUnmarkedLinks');
        Route::get('service/calculate_lemma_wordforms', 'Library\ServiceController@calculateLemmaWordforms');
        Route::get('service/check_wordforms_by_rules', 'Library\ServiceController@checkWordformsByRules');
        Route::get('service/generate_wordforms', 'Library\ServiceController@generateWordforms');
        Route::get('service/illative_table', 'Library\ServiceController@illativeTable');       
        Route::get('service/reload_stem_affixes', 'Library\ServiceController@reloadStemAffixes');
        Route::get('service/wordforms', 'Library\ServiceController@checkWordforms');
        
        
//        Route::get('dict/lemma/tmpUpdateStemAffix', 'Dict\LemmaController@tmpUpdateStemAffix');
//        Route::get('dict/lemma/tmpSplitWordforms', 'Dict\LemmaController@tmpSplitWordforms');
//        Route::get('dict/lemma/tmpMoveReflexive', 'Dict\LemmaController@tmpMoveReflexive'); 
//        Route::get('dict/gramset/tempInsertGramsetsForReflexive', 'Dict\GramsetController@tempInsertGramsetsForReflexive');       
//        Route::get('dict/gramset/tempInsertGramsetPosLang', 'Dict\GramsetController@tempInsertGramsetPosLang');       
//        Route::get('dict/lemma/tempInsertVepsianLemmas', 'Dict\LemmaController@tempInsertVepsianLemmas');       
//        Route::get('dict/lemma/meaning/tempInsertVepsianMeanings', 'Dict\MeaningController@tempInsertVepsianMeanings');
//        Route::get('dict/lemma/meaning/meaning_text/tempJoinMeaningText', 'Dict\MeaningTextController@tempJoinMeaningText');        
//        Route::get('dict/lemma/wordform/tempInsertVepsianWordform', 'Dict\WordformController@tempInsertVepsianWordform');
//        Route::get('corpus/source/tempInsertVepsianSource', 'Corpus\SourceController@tempInsertVepsianSource');
//        Route::get('corpus/place/tempInsertVepsianPlace', 'Corpus\PlaceController@tempInsertVepsianPlace');
//        Route::get('corpus/informant/tempInsertVepsianInformant', 'Corpus\InformantController@tempInsertVepsianInformant');
//        Route::get('corpus/recorder/tempInsertVepsianRecorder', 'Corpus\RecorderController@tempInsertVepsianRecorder');
//        Route::get('corpus/text/tempInsertVepsianText', 'Corpus\TextController@tempInsertVepsianText');
//        Route::get('corpus/text/tempInsertVepsianDialectText', 'Corpus\TextController@tempInsertVepsianDialectText');
//        Route::get('corpus/text/tempInsertVepsianGenreText', 'Corpus\TextController@tempInsertVepsianGenreText');
//        Route::get('corpus/text/markup_all_empty_text_xml', 'Corpus\TextController@markupAllEmptyTextXML');
//        Route::get('corpus/text/tempStripSlashes', 'Corpus\TextController@tempStripSlashes');
//        Route::get('corpus/text/tmpProcessOldLetters', 'Corpus\TextController@tmpProcessOldLetters');
        
        Route::get('stats','Library\StatsController@index');
        Route::get('stats/by_dict','Library\StatsController@byDict');
        Route::get('stats/by_corp','Library\StatsController@byCorp');
        
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
                                    'destroy' => 'lemma.destroy']]);

/*        Route::resource('dict/lemma_wordform', 'Dict\LemmaWordformController',
                       ['names' => ['update' => 'lemma_wordform.update',
                                    'store' => 'lemma_wordform.store',
                                    'destroy' => 'lemma_wordform.destroy']]); */

        Route::resource('dict/relation', 'Dict\RelationController',
                       ['names' => ['update' => 'relation.update',
                                    'store' => 'relation.store',
                                    'destroy' => 'relation.destroy']]);
        
        Route::resource('dict/wordform', 'Dict\WordformController',
                       ['names' => ['update' => 'wordform.update']]);

        Route::resource('corpus/corpus', 'Corpus\CorpusController',
                       ['names' => ['update' => 'corpus.update',
                                    'store' => 'corpus.store',
                                    'destroy' => 'corpus.destroy']]);
        
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
        
        Route::resource('corpus/recorder', 'Corpus\RecorderController',
                       ['names' => ['update' => 'recorder.update',
                                    'store' => 'recorder.store',
                                    'destroy' => 'recorder.destroy']]);
        
        Route::resource('corpus/region', 'Corpus\RegionController',
                       ['names' => ['update' => 'region.update',
                                    'store' => 'region.store',
                                    'destroy' => 'region.destroy']]);
        
        Route::resource('role', 'RoleController',
                       ['names' => ['update' => 'role.update',
                                    'store' => 'role.store',
                                    'destroy' => 'role.destroy']]);
        
        Route::resource('user', 'UserController',
                       ['names' => ['update' => 'user.update',
                                    'destroy' => 'user.destroy']]);
    }
);
