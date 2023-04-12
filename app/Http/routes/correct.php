<?php
Route::group(
    ['prefix' => 'service/correct'],
    function()
    {
        Route::get('/', 'Library\CorrectController@index');
        Route::get('add_accusatives', 'Library\CorrectController@addAccusatives');
        Route::get('add_approx_term', 'Library\CorrectController@addApproxTerm');
        Route::get('add_audiofiles_to_db', 'Library\CorrectController@addAudiofilesToDb');
        Route::get('add_meaning_text_links', 'Library\CorrectController@addMeaningTextLinks');
        Route::get('add_synonyms', 'Library\CorrectController@addSynonyms');
        Route::get('add_wordform_affixes', 'Library\CorrectController@addWordformAffixes');
        Route::get('add_text_wordform_links', 'Library\CorrectController@addTextWordformLinks');
        Route::get('calculate_lemma_wordforms', 'Library\CorrectController@calculateLemmaWordforms');
        Route::get('check_meaning_text', 'Library\CorrectController@checkMeaningText');
        Route::get('check_parallel_texts', 'Library\CorrectController@checkParallelTexts');
        Route::get('create_initial_wordforms', 'Library\CorrectController@createInitialWordforms');
        Route::get('generate_wordforms', 'Library\CorrectController@generateWordforms');        
        Route::get('move_char_out_word', 'Library\CorrectController@moveCharOutWord');       
/*
        Route::get('tmp_fill_sentence_id_in_text_wordform', 'Library\CorrectTmpController@tmpFillSentenceIdInTextWordform');
        Route::get('tmp_fill_sentence_id_in_words', 'Library\CorrectTmpController@tmpFillSentenceIdInWords');
        Route::get('tmp_fill_word_id_in_text_wordform', 'Library\CorrectTmpController@tmpFillWordIdInTextWordform');
        Route::get('tmp_fill_wordform_for_search', 'Library\CorrectTmpController@tmpFillWordformForSearch');
        Route::get('tmp_fill_genres', 'Library\CorrectTmpController@tmpFillGenres');
        Route::get('tmp_split_into_sentences', 'Library\CorrectTmpController@tmpSplitTextsIntoSentences');
        Route::get('tmp_word_numbers_for_words', 'Library\CorrectTmpController@tmpWordNumbersForWords');
        Route::get('tmp_move_br_from_sentences', 'Library\CorrectTmpController@tmpMoveBrFromSentences');
*/ 
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
        
//        Route::get('dict/wordform/tempCheckWordformsWithSpaces', 'Dict\WordformController@tempCheckWordformsWithSpaces');
//        Route::get('dict/wordform/tmpFixNegativeVepsVerbForms', 'Dict\WordformController@tmpFixNegativeVepsVerbForms');
//        Route::get('dict/lemma/tmpUpdateStemAffix', 'Dict\LemmaController@tmpUpdateStemAffix');
//        Route::get('dict/lemma/tmpSplitWordforms', 'Dict\LemmaController@tmpSplitWordforms');
//        Route::get('dict/lemma/tmpMoveReflexive', 'Dict\LemmaController@tmpMoveReflexive'); 
//        Route::get('dict/gramset/tempInsertGramsetsForReflexive', 'Dict\GramsetController@tempInsertGramsetsForReflexive');       
//        Route::get('dict/gramset/tempInsertGramsetPosLang', 'Dict\GramsetController@tempInsertGramsetPosLang');       
//        Route::get('dict/lemma/tempInsertVepsianLemmas', 'Dict\LemmaController@tempInsertVepsianLemmas');       
//        Route::get('dict/lemma/meaning/tempInsertVepsianMeanings', 'Dict\MeaningController@tempInsertVepsianMeanings');
//        Route::get('dict/lemma/meaning/meaning_text/tempJoinMeaningText', 'Dict\MeaningTextController@tempJoinMeaningText');        
//        Route::get('dict/lemma/wordform/tempInsertVepsianWordform', 'Dict\WordformController@tempInsertVepsianWordform');
        
    }
);
