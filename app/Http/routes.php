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
        Route::get('/stats','HomeController@stats');

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
        Route::get('/grants', function () {
            return view('page.grants');
        });
        Route::get('/permission', function () {
            return view('page.permission');
        });
        
        Route::get('/help/text/show', function () {
            return view('help.text.show');
        });

        Route::get('/home', 'HomeController@index');
        Route::get('/dumps','DumpDownloadController@index');
        
        Route::get('corpus/text/add/example/{example_id}', 'Corpus\TextController@addExample');
/*        Route::get('corpus/corpus/list', 'Corpus\CorpusController@corpusList');*/
        Route::get('corpus/text/{id}/edit/example/{example_id}', 'Corpus\TextController@editExample');
        Route::post('corpus/text/{id}/update/examples', 'Corpus\TextController@updateExamples')
                        ->name('text.update.examples');

        Route::get('corpus/text/dialect_list', 'Corpus\TextController@dialectList');
        Route::get('corpus/text/{id}/history', 'Corpus\TextController@history');
        Route::get('corpus/text/sentence', 'Corpus\TextController@showWordInSentence');

        Route::get('corpus/text/full_new_list', 'Corpus\TextController@fullNewList');
        Route::get('corpus/text/limited_new_list', 'Corpus\TextController@limitedNewList');
        Route::get('corpus/text/full_updated_list', 'Corpus\TextController@fullUpdatedList');
        Route::get('corpus/text/limited_updated_list', 'Corpus\TextController@limitedUpdatedList');
        Route::get('corpus/text/word/create_checked_block', 'Corpus\TextController@getWordCheckedBlock');
        Route::get('corpus/text/export/conll', 'Corpus\TextController@exportToCONLL');

        Route::get('dict/dialect/list', 'Dict\DialectController@dialectList');

        Route::get('dict/lemma/add/example/{example_id}', 'Dict\LemmaController@addExample');
        Route::get('dict/lemma/{id}/edit/examples', 'Dict\LemmaController@editExamples');
        Route::get('dict/lemma/{id}/edit/example/{example_id}', 'Dict\LemmaController@editExample');
        Route::get('dict/lemma/{id}/edit/wordforms', 'Dict\LemmaController@editWordforms');
        Route::get('dict/lemma/{id}/history', 'Dict\LemmaController@history');
        Route::post('dict/lemma/{id}/update/examples', 'Dict\LemmaController@updateExamples')
                        ->name('lemma.update.examples');
        Route::post('dict/lemma/{id}/update/wordforms', 'Dict\LemmaController@updateWordforms')
                        ->name('lemma.update.wordforms');
        Route::get('dict/lemma/list', 'Dict\LemmaController@lemmaLangList');
        Route::get('dict/lemma/meaning/create', 'Dict\LemmaController@createMeaning');
        Route::get('dict/lemma/meanings_list', 'Dict\LemmaController@meaningsList');
        Route::get('dict/lemma/phrase_list', 'Dict\LemmaController@phraseList');
        Route::get('dict/lemma/relation', 'Dict\LemmaController@relation');
        Route::get('dict/lemma/remove/example/{example_id}', 'Dict\LemmaController@removeExample');
        Route::get('dict/lemma/omonyms', 'Dict\LemmaController@omonyms');
        Route::get('dict/lemma/phrases', 'Dict\LemmaController@phrases');
        Route::get('dict/lemma/sorted_by_length', 'Dict\LemmaController@sortedByLength');
        Route::get('dict/lemma/full_new_list', 'Dict\LemmaController@fullNewList');
        Route::get('dict/lemma/limited_new_list', 'Dict\LemmaController@limitedNewList');
        Route::get('dict/lemma/full_updated_list', 'Dict\LemmaController@fullUpdatedList');
        Route::get('dict/lemma/limited_updated_list', 'Dict\LemmaController@limitedUpdatedList');
        Route::get('dict/lemma/wordform/create', 'Dict\LemmaController@createWordform'); 
        Route::get('dict/lemma/wordform/update', 'Dict\LemmaController@updateWordformFromText'); 
//        Route::get('dict/lemma/tmpSplitWordforms', 'Dict\LemmaController@tmpSplitWordforms');
        Route::get('dict/lemma/tmpMoveReflexive', 'Dict\LemmaController@tmpMoveReflexive'); 
        
        Route::get('dict/pos', 'Dict\PartOfSpeechController@index');

        Route::get('dict/reverse_lemma/', 'Dict\ReverseLemmaController@index');
        Route::get('dict/reverse_lemma/tmpCreateAllReverse', 'Dict\ReverseLemmaController@tmpCreateAllReverse');

        Route::get('dict/wordform/with_multiple_lemmas', 'Dict\WordformController@withMultipleLemmas');
        Route::get('dict/wordform/tempCheckWordformsWithSpaces', 'Dict\WordformController@tempCheckWordformsWithSpaces');
      
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
//        Route::get('corpus/text/markup_all_texts', 'Corpus\TextController@markupAllTexts');
//        Route::get('corpus/text/markup_all_empty_text_xml', 'Corpus\TextController@markupAllEmptyTextXML');
        Route::get('corpus/text/{id}/markup', 'Corpus\TextController@markupText');
//        Route::get('corpus/text/tempStripSlashes', 'Corpus\TextController@tempStripSlashes');
//        Route::get('corpus/text/tmpProcessOldLetters', 'Corpus\TextController@tmpProcessOldLetters');
        Route::get('corpus/video', 'Corpus\VideoController@index');
        
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

        Route::resource('dict/lang', 'Dict\LangController',
                       ['names' => ['update' => 'lang.update',
                                    'store' => 'lang.store',
                                    'destroy' => 'lang.destroy']]);
        
        Route::resource('dict/lemma', 'Dict\LemmaController',
                       ['names' => ['update' => 'lemma.update',
                                    'store' => 'lemma.store',
                                    'destroy' => 'lemma.destroy']]);

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
