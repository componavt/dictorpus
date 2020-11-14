<?php
namespace App\Library;

class Predictor
{
    /**
     * Prediction a lemma and a gramset for a word
     * 
     * 1) Search unchangable lemma in other languages
     * 2) Search wordforms in other languages
     * 3) Search unchangable lemma in the word language by 
     * 
     * @param string $uword - unknown word form for prediction
     * @param int $lang_id - language ID of given word form
     */
    public static function predictLemmaGramset(string $uword, int $lang_id) {
        
    }
}