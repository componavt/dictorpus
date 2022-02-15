<?php
        Route::get('experiments/bible_language/', 'Library\Experiments\BibleLanguageController@index');
        Route::get('experiments/bible_language/for_all', 'Library\Experiments\BibleLanguageController@forAll');
        Route::get('experiments/bible_language/for_selection/{corpus_id}', 'Library\Experiments\BibleLanguageController@forSelection');
        
        Route::get('experiments/pattern_search/', 'Library\Experiments\PatternSearchController@index');
        Route::get('experiments/pattern_search_in_wordforms/', 'Library\Experiments\PatternSearchController@inWordforms');
        Route::get('experiments/pattern_search_in_wordforms_results/', 'Library\Experiments\PatternSearchController@inWordformsResults');
        Route::get('experiments/prediction_by_analog/', 'Library\Experiments\SearchByAnalogController@lemmaGramsetPrediction');
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
        Route::get('experiments/vowel_gradation/verb_imp_3sg/', 'Library\Experiments\VowelGradationController@verbImp3Sg');
