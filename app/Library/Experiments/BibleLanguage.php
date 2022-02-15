<?php

namespace App\Library\Experiments;

use App\Models\Corpus\Text;
use App\Models\Corpus\Word;

use App\Models\Dict\Gram;

class BibleLanguage
{
    public static function getTextSelection($corpus_id=null) {
        switch ($corpus_id) {
            case 2:
                return [3601, 3635, 3629, 3636, 3634, 3643, 3591];
            case 3:
                return [1753, 2906, 2307, 2571, 1686, 1419];
            case 8:
                return [3213, 3324, 3326, 2470, 2444];
            case 'all':
                return [3601, 3635, 3629, 3636, 3634, 3643, 3591,
                        1753, 2906, 2307, 2571, 1686, 1419,
                        3213, 3324, 3326, 2470, 2444];
        }        
    }

    public static function inf3Gram() {
        return 42;
    }
    public static function potGram() {
        return 48;
    }
    public static function condGram() {
        return 28;
    }
    public static function taId($lang_id) {
        if ($lang_id == 4) {
            return 15795;
        }
    }
    public static function aId($lang_id) {
        if ($lang_id == 4) {
            return 15796;
        }
    }
    public static function niId($lang_id) {
        if ($lang_id == 4) {
            return 25012;
        }
    }
    public static function noId($lang_id) {
        if ($lang_id == 4) {
            return 25033;
        }
    }
    public static function voiId($lang_id) {
        if ($lang_id == 4) {
            return 26408;
        }
    }      
    
    public static function gramField($gram_id) {
        if ($gram_id == self::inf3Gram()) {
            return 'gram_id_infinitive';
        } elseif($gram_id == self::potGram() || $gram_id == self::condGram()) {
            return 'gram_id_mood';
        }
    }
    
    public static function selectTexts($corpus_id, $lang_id, $text_ids=null) {
        return Text::select('id')
                   ->when($corpus_id, function ($q) use ($corpus_id) {
                        return $q->whereCorpusId($corpus_id);
                    })->when($lang_id, function ($q) use ($lang_id) {
                        return $q->whereLangId($lang_id);
                    })->when($text_ids, function ($q) use ($text_ids) {
                        return $q->whereIn('id', $text_ids);
                    })->get();
    }
    
    public static function textTotal($corpus_id, $lang_id, $text_ids=null) {
        return Text::whereCorpusId($corpus_id)
                   ->when($lang_id, function ($q) use ($lang_id) {
                        return $q->whereLangId($lang_id);
                    })->when($text_ids, function ($q) use ($text_ids) {
                        return $q->whereIn('id', $text_ids);
                    })->count();
    }
    
    public static function linkedWordTotal($corpus_id, $lang_id, $text_ids) {
        return Word::whereIn('text_id', self::selectTexts($corpus_id, $lang_id, $text_ids))
                   ->whereIn('id', function ($q) {
                            $q->select('word_id')->from('meaning_text');
//                              ->where('relevance', '>', 0)
                        })->count();        
    }

    public static function wordTotal($corpus_id, $lang_id, $text_ids) {
        return Word::whereIn('text_id', self::selectTexts($corpus_id, $lang_id, $text_ids))
                     ->count();        
    }

    public static function inf3Total($corpus_id, $lang_id, $text_ids) {
        return self::wordsForGram(self::inf3Gram(), $corpus_id, $lang_id, $text_ids)
                     ->count();        
    }

    public static function potTotal($corpus_id, $lang_id, $text_ids) {
        return self::wordsForGram(self::potGram(), $corpus_id, $lang_id, $text_ids)
                     ->count();        
    }

    public static function condTotal($corpus_id, $lang_id, $text_ids) {
        return self::wordsForGram(self::condGram(), $corpus_id, $lang_id, $text_ids)
                     ->count();        
    }

    public static function taTotal($corpus_id, $lang_id, $text_ids) {
        return self::countWordsForLemma(self::taId($lang_id), $corpus_id, $lang_id, $text_ids);        
    }

    public static function aTotal($corpus_id, $lang_id, $text_ids) {
        return self::countWordsForLemma(self::aId($lang_id), $corpus_id, $lang_id, $text_ids);        
    }

    public static function niTotal($corpus_id, $lang_id, $text_ids) {
        return self::countWordsForLemma(self::niId($lang_id), $corpus_id, $lang_id, $text_ids);        
    }

    public static function noTotal($corpus_id, $lang_id, $text_ids) {
        return self::countWordsForLemma(self::noId($lang_id), $corpus_id, $lang_id, $text_ids);        
    }

    public static function voiTotal($corpus_id, $lang_id, $text_ids) {
        return self::countWordsForLemma(self::voiId($lang_id), $corpus_id, $lang_id, $text_ids);        
    }
    
    public static function wordsForGram($gram_id, $corpus_id, $lang_id, $text_ids) {
        $gram_field = self::gramField($gram_id);
        return Word::whereIn('text_id', self::selectTexts($corpus_id, $lang_id, $text_ids))
                              ->whereIn('id', function ($q) use ($gram_id, $gram_field){
                                  $q->select('word_id')->from('text_wordform')
                                    ->where('relevance', '<>', 0)
                                    ->whereIn('gramset_id', function ($q2) use ($gram_id, $gram_field){
                                        $q2->select('id')->from('gramsets')
                                           ->where($gram_field, $gram_id);
                                    });
                              });        
    }
    
    public static function researchFormsforTexts($text_ids, $corpus_id=null, $lang_id=null) {
        $gram_ids = [self::inf3Gram(), 
                     self::potGram(), 
                     self::condGram()];
        $grams = Gram::whereIn('id', $gram_ids)->get();
//dd($grams);        
        foreach ($gram_ids as $gram_id) {
//        $inf3_ids = Gramset::where('gram_id_infinitive',42)->pluck('id')->toArray();
            $words[$gram_id] = self::wordsForGram($gram_id, $corpus_id, $lang_id, $text_ids)
                                   ->orderBy('text_id', 'w_id')->get();
        }       
        
        return [$grams, $words];
    }    
    
    public static function getWordsForLemma($text_ids, $lemma_id) {
        return self::when(sizeof($text_ids), function($q) use ($text_ids) { 
                        return $q->whereIn('text_id', $text_ids);
                    })->whereIn('id', function ($q) use ($lemma_id){
                        $q->select('word_id')->from('meaning_text')
                          ->where('relevance', '<>', 0)
                          ->whereIn('meaning_id', function ($q2) use ($lemma_id){
                                   $q2->select('id')->from('meanings')
                                      ->where('lemma_id', $lemma_id);
                               });
                    })
                    ->orderBy('text_id', 'w_id')->get();
    }
    
    public static function countWordsForLemma($lemma_id, $corpus_id, $lang_id, $text_ids) {
        if (!$lemma_id) {
            return null;
        }
        return Word::when($corpus_id || sizeof($text_ids), function($q) use ($corpus_id, $lang_id, $text_ids) { 
                        return $q->whereIn('text_id', self::selectTexts($corpus_id, $lang_id, $text_ids));
                    })->whereIn('id', function ($q) use ($lemma_id){
                        $q->select('word_id')->from('meaning_text')
                          ->where('relevance', '<>', 0)
                          ->whereIn('meaning_id', function ($q2) use ($lemma_id){
                                   $q2->select('id')->from('meanings')
                                      ->where('lemma_id', $lemma_id);
                               });
                    })
                    ->count();
    }
    
    public static function researchServiceWordsforTexts($text_ids, $lang_id=4) {
        $ta_positions = [
            1=>'В начале предложения',
            2=>'После запятой', 
//            3=>'После союза а', 
//            4=>'После частицы ni',
            5=>'Другие'];
        $ta_words = self::getWordsForLemma($text_ids, self::taId($lang_id));
        $tap_words = [];
        foreach ($ta_words as $word) {
            if ($word->word_number == 1) {
                $tap_words[1][]=$word;
            } elseif ($word->getPrevSign() == ',') {
                $tap_words[2][]=$word;
            } else {
//                $sentence = $word->getClearSentence();
                $tap_words[5][]=$word;
            }
        }
        
        $a_words = self::getWordsForLemma($text_ids, self::aId($lang_id));        
        $ni_words = self::getWordsForLemma($text_ids, self::niId($lang_id));        
        $no_words = self::getWordsForLemma($text_ids, self::noId($lang_id));        
        $voi_words = self::getWordsForLemma($text_ids, self::voiId($lang_id));
        
        return [$a_words, $ni_words, $no_words, $tap_words, $ta_positions, $voi_words];
    }    
}
