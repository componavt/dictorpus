<?php

namespace App\Library\Grammatic;

use App\Library\Grammatic\KarNameGram;
use App\Library\Grammatic\KarVerbGram;

use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;

/**
 * Functions related to Karelian grammatic, 
 * these functions do not depend to part of speech.
 */
class KarGram
{
    public static function isConsonant($letter) {
        $consonants = ['p', 't', 'k', 's', 'h', 'j', 'v', 'l', 'r', 'm', 'n', 'č', 'd'];
        if (in_array($letter, $consonants)) {
            return true;
        } 
        return false;
    }
    
    public static function isVowel($letter) {
        $vowels = ['i', 'y', 'u', 'e', 'ö', 'o', 'ä', 'a'];
        if (in_array($letter, $vowels)) {
            return true;
        } 
        return false;
    }
    
    /**
     * Is exists back vowels in the word
     * @param String $word
     * @return Boolean
     */
    public static function isBackVowels($word) {
        if (preg_match("/[aou]/u", $word)) { 
            return true;
        }
        return false;
    }
    
    public static function getListForAutoComplete($pos_id) {
        $gramsets = [];
        if ($pos_id == PartOfSpeech::getVerbID()) {
            $gramsets = KarVerb::getListForAutoComplete();
        } elseif (in_array($pos_id, PartOfSpeech::getNameIDs())) {
            $gramsets = KarName::getListForAutoComplete();
        }
        return $gramsets;
    }
    
    public static function garmVowel($stem, $vowel) {
        if (!$vowel) {
            return '';
        }
        $frontVowels = ['a'=>'ä', 'o'=>'ö', 'u'=>'y'];
        if (self::isBackVowels($stem)) {
            return $vowel;
        }
        $vowels = preg_split("//", $vowel);
        $new_vowels = '';
        foreach ($vowels as $v) {
            if (isset($frontVowels[$v])) {
                $new_vowels .= $frontVowels[$v];
            } else {
                $new_vowels .= $v;
            }
        } 
        return $new_vowels;
    }
    
    /**
     * Если $stem заканчивается на одиночный $vowel 
     * т.е. любой согласный + $vowel, то $vowel переходит в $replacement
     * @param String $stem
     * @param String $vowel - one char
     * @param String $replacement - one char
     */
    public static function replaceSingVowel($stem, $vowel, $replacement) {
        $before_last_let = mb_substr($stem, -2, 1);
        if (self::isConsonant($before_last_let) && preg_match("/^(.+)".$vowel."$/u", $stem, $regs)) {
            return $regs[1].$replacement;
        }
        return $stem;
    }
    
    public static function changeLetters($word) {
        $word = str_replace('ü','y',$word);
        $word = str_replace('Ü','Y',$word);
        
        if (self::isBackVowels($word)) { 
            $word = str_replace('w','u',$word);
            $word = str_replace('W','U',$word);            
        } else {
            $word = str_replace('w','y',$word);
            $word = str_replace('W','Y',$word);            
        }
        return $word;
    }

    public static function stemsFromDB($lemma, $pos_id, $dialect_id) {
        if (in_array($pos_id, PartOfSpeech::getNameIDs())) { 
            return KarName::stemsFromDB($lemma, $dialect_id);
        } elseif (in_array($pos_id, PartOfSpeech::getNameIDs())) { 
            return KarVerb::stemsFromDB($lemma, $dialect_id);
        }       
    }
}