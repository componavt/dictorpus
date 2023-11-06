<?php
        Route::get('ldl/concept/{concept_id}', 'Library\LdlController@concept');
        Route::get('ldl', 'Library\LdlController@index');
        