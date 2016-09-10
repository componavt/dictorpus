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
        Route::get('/', function () {
            return view('welcome');
        });

        // Вызов страницы регистрации пользователя
        Route::get('register', 'AuthController@register');   
        // Пользователь заполнил форму регистрации и отправил
        Route::post('register', 'AuthController@registerProcess'); 
        // Пользователь получил письмо для активации аккаунта со ссылкой сюда
        Route::get('activate/{id}/{code}', 'AuthController@activate');
        // Вызов страницы авторизации
        Route::get('login', 'AuthController@login');
        // Пользователь заполнил форму авторизации и отправил
        Route::post('login', 'AuthController@loginProcess');
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


        Route::get('/home', 'HomeController@index');
        
        Route::get('dict/dialect', 'Dict\DialectController@index');
        Route::get('dict/gram', 'Dict\GramController@index');
        Route::get('dict/gramset', 'Dict\GramsetController@index');
        Route::get('dict/lang', 'Dict\LangController@index');
        Route::get('dict/pos', 'Dict\PartOfSpeechController@index');

        Route::get('dict/wordform/with_multiple_lemmas', 'Dict\WordformController@withMultipleLemmas');
        Route::get('dict/wordform', 'Dict\WordformController@index');

        Route::get('dict/lemma/sorted_by_length', 'Dict\LemmaController@sortedByLength');
      
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

        Route::resource('dict/lemma', 'Dict\LemmaController',
                       ['names' => ['update' => 'lemma.update',
                                    'store' => 'lemma.store',
                                    'destroy' => 'lemma.destroy']]);

        Route::resource('corpus/text', 'Corpus\TextController',
                       ['names' => ['update' => 'text.update',
                                    'store' => 'text.store',
                                    'destroy' => 'text.destroy']]);
        
        Route::resource('corpus/informant', 'Corpus\InformantController',
                       ['names' => ['update' => 'informant.update',
                                    'store' => 'informant.store',
                                    'destroy' => 'informant.destroy']]);
        
        Route::resource('corpus/place', 'Corpus\PlaceController',
                       ['names' => ['update' => 'place.update',
                                    'store' => 'place.store',
                                    'destroy' => 'place.destroy']]);
    }
);