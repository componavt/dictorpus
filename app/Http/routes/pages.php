<?php
        Route::get('/','HomeController@index');
        Route::get('/help/{section}/{page}', 'HomeController@help');
        Route::get('/home', 'HomeController@index');
        Route::get('/page/{page}', 'HomeController@page');        
        Route::get('/dumps','DumpDownloadController@index');
        
        Route::get('/experiments', function () {
            return view('experiments.index');
        });

        Route::get('/service/import', function () {
            return view('service.import');
        });
         
        