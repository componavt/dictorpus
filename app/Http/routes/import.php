<?php
        Route::get('import/concept_parser', 'Library\ImportController@conceptParser');
        Route::get('import/concept_parser_check', 'Library\ImportController@conceptParserCheck');
        Route::get('import/dict_parser', 'Library\ImportController@dictParser');
        Route::get('import/dict_zaikov_verb_parser', 'Library\ImportController@dictZaikovVerbParser');
        Route::get('import/extract_livvic_verbs', 'Library\ImportController@extractVerbs');
        Route::get('import/extract_livvic_compound_words', 'Library\ImportController@extractCompoundWords');
        Route::get('import/change_stem_for_compound_words', 'Library\ImportController@changeStemForCompoundWords');
        Route::get('import/phonetics_to_lemmas', 'Library\ImportController@phoneticsToLemmas');
        Route::get('import/wiki_photo', 'Library\ImportController@wikiPhoto');
