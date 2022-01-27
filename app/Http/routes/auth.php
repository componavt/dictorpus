<?php
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

        Route::resource('role', 'RoleController',
                       ['names' => ['update' => 'role.update',
                                    'store' => 'role.store',
                                    'destroy' => 'role.destroy']]);
        
        Route::resource('user', 'UserController',
                       ['names' => ['update' => 'user.update',
                                    'destroy' => 'user.destroy']]);
        