<?php

namespace App\Library\Grammatic;

use App\Library\Grammatic;
use App\Library\Grammatic\KarName;
use App\Library\Grammatic\KarVerb;

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
    
    public static function getStemFromWordform($lemma, $stem_n, $pos_id, $dialect_id) {
        if (in_array($pos_id, PartOfSpeech::getNameIDs())) { 
            return KarName::getStemFromWordform($lemma, $stem_n, $dialect_id);
        } elseif (in_array($pos_id, PartOfSpeech::getNameIDs())) { 
            return KarVerb::getStemFromWordform($lemma, $stem_n, $dialect_id);
        }
    }
    
    public static function stemsFromFullList($template) {
        if (!preg_match('/^\s*\{+([^\}]+)\}+\s*$/', $template, $template_in_brackets)) {
            return NULL;
        }
        
        $stems = preg_split('/,/',$template_in_brackets[1]);
        for ($i=0; $i<sizeof($stems); $i++) {
            $stems[$i] = trim($stems[$i]);
        }

        return $stems;
    }
    
    public static function stemsFromTemplate($template, $pos_id, $name_num) {
        $template = self::toRightTemplate($template, $name_num, $pos_id);
        
        $stems = self::stemsFromFullList($template);        
//var_dump($stems);        
        if (!$stems || ($pos_id == PartOfSpeech::getVerbID() && sizeof($stems)!=8) // constraints for tver dialects
                || (in_array($pos_id, PartOfSpeech::getNameIDs()) && sizeof($stems)!=6)) {
            return [NULL, $name_num, $template, NULL];
        } 
        
        list($max_stem, $affix) = Grammatic::maxStem($stems);
        
        return [$stems, $name_num, $max_stem, $affix];
    }
    
    public static function templateForImport() {
        return "([^\.]+)\.\s*([^\.]*)\.?\s*\–\s*(.+)\s+\–\s*(.*)";
    }
    
    /**
     * Lemmas examples:
     * a
     * abie {-, -da, -loi}
     * a|bu {-vu / -bu, -buo, -buloi} 
     * ahavoit|tua {-a / -ta, -i / -ti, -ta, -eta, -ett}, ahavoi|ja {-če / -ččo, -či / -čči, -, -ja, -d}
     * aijalleh, aijaldi
     * 
     * @param type $lemmas
     * @return type
     */
    public static function parseLemmasForImport($lemmas, $num, $dialect_id, $pos_id) {
        $lemmas = Grammatic::toRightForm($lemmas);
        if ($dialect_id!=47) { // not tver
            return [0=>$lemmas];
        }
        
        $lemma_arr=[];
        $count_brackets = mb_substr_count($lemmas, '}');

        if (!$count_brackets) { // not changeble pos
            $lemma_arr=preg_split("/\s*,\s*/", $lemmas);
        } else {
            $lemma_arr=preg_split("/\}\s*,\s*/", $lemmas);
            for ($i=0; $i<sizeof($lemma_arr); $i++) {
                $lemma_arr[$i] = self::toRightTemplate(trim($lemma_arr[$i]), $num, $pos_id);
            }
        }
        return $lemma_arr;
    }    
    
    /**
     * Only for dialect_id=47 (tver)
     * 
     * lemma_str examples:
     * 
     * abie {-, -da, -loi}
     * a|bu {-vu / -bu, -buo, -buloi} 
     * ai|ga {-ja / -ga, -gua, -joi / -goi}
     * aluššo|vat {-vi / -bi}   (pos=nominals, num=pl - only base 4/ base 5)
     * 
     * ahavoit|tua {-a / -ta, -i / -ti, -ta, -eta, -ett}
     * avau|duo {-du, -du, -du, -vuta, -vutt} (pos=v, num=impers - without base 1 and base 3) - НЕ ПРЕДУСМОТРЕН ШАБЛОН без основ 1 и 3, исправить KarVerb!!!!
     * 
     * @param type $lemma_str
     */
    public static function toRightTemplate($lemma_str, $num, $pos_id) {
        if (!preg_match("/^([^\s\{]+)\s*\{([^\}]+)\}?$/", $lemma_str, $regs)) {
            return $lemma_str;
        }
        $base = $bases[0] = $regs[1];
        $base_str = trim($regs[2]);
        
        if (preg_match("/^([^\|]+)\|(.+)$/", $bases[0], $regs)) {
            $base = $regs[1];
            $bases[0] = $base.$regs[2];
        }
        
        $base_str = str_replace('-', $base, $base_str);
//print "<p>$base_str</p>";        
        $base_list = preg_split("/\s*,\s*/",$base_str);

        if (in_array($pos_id, PartOfSpeech::getNameIDs())) {
            return KarName::toRightTemplate($bases, $base_list, $lemma_str, $num);
        } elseif ($pos_id == PartOfSpeech::getVerbID()) {  
            return KarVerb::toRightTemplate($bases, $base_list, $lemma_str, $num);
        }
//print "<p>Unknown pos</p>";        
        return $lemma_str;
    }
    
}
