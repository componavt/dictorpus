<?php

namespace App\Library\Grammatic;

use App\Library\Grammatic;
use App\Library\Grammatic\KarName;
use App\Library\Grammatic\KarVerb;

use App\Models\Dict\Dialect;
//use App\Models\Dict\Gramset;
//use App\Models\Dict\Lang;
//use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;

/**
 * Functions related to Karelian grammatic, 
 * these functions do not depend to part of speech.
 */
class KarGram
{
    public static function isConsonant($letter) {
        $consonants = ['p', 't', 'k', 's', 'h', 'j', 'v', 'l', 'r', 'm', 'n', 'č', 'd', 'g', 'z', 'ž', 'š','b'];
        if (in_array($letter, $consonants)) {
            return true;
        } 
        return false;
    }
    
    public static function isVowel($letter) {
        $vowels = ['a', 'i', 'y', 'u', 'e', 'ö', 'o', 'ä'];
        if (in_array($letter, $vowels)) {
            return true;
        } 
        return false;
    }
    
    public static function consSet() {
        return "bcčdfghjklmnprsšzžtv";
    }
    
    public static function vowelSet() {
        return "aeiouyäö";
    }
    
    public static function vowelSetWithoutI() {
        return "aeouyäö";
    }
    
    /**
     * Is exists back vowels in the word
     * @param String $word
     * @return Boolean
     */
    public static function isBackVowels($word) {
/*        if (preg_match("/[äöy]/u", $word)) { 
            return false;
        }
        return true;
*/        
        if (preg_match("/\|{2}(.+)$/", $word, $regs) || preg_match("/ǁ(.+)$/", $word, $regs)) {
            $word = $regs[1];
        }
        if (preg_match("/[aou]/u", $word)) { 
            return true;
        }
        return false;
    }
    
    /**
     * Сколько слогов в основе?
     * 
     * Количество слогов в форме равно количеству гласных или любых комбинаций гласных (VV или VVV), разделенных между собой согласными: 
     * paganoija – CVCVCVVCV (4 слога), puija – CVVCV (2 слога), andua – VCCVV (2 слога).
     * 
     * @param String $stem
     * @return INT 1 - односложное, 2 - двусложное, 3 - трехсложное, 4 - многосложное
     */
    public static function countSyllable($stem) {
        if (preg_match("/\s+([^\_]+)$/", $stem, $regs) 
                || preg_match("/\|\|([^\|]+)$/", $stem, $regs) 
                || preg_match("/ǁ([^ǁ]+)$/", $stem, $regs) ) {
            $stem = $regs[1];
        }
        $C = "[".KarGram::consSet()."]";
        $C_n= "(".$C."’?)*";
        $C_o= "(".$C."’?)+";
        $V = "[".KarGram::vowelSet()."]+";
//        $syllable = "(".$C."’?)*[".KarGram::vowelSet()."][iu]?(".$C."’?)*";
        $syllable = $C_n.$V."+(".$C."’?)*";
        $template = $C_n.$V;
        for ($i=1; $i<20; $i++) {
            if (preg_match("/^".$template.$C_n."$/u",$stem)) {
                return $i;
            }
            $template .= $C_o.$V;
        }
/*        
        if (preg_match("/^".$C_n.$V.$C_n."$/u",$stem)) {
            return 1;
        } elseif (preg_match("/^".$C_n.$V.$C_o.$V.$C_n."$/u",$stem)) {
            return 2;
        } elseif (preg_match("/^".$C_n.$V.$C_o.$V.$C_o.$V.$C_n."$/u",$stem)) {
            return 3;
        } */
//dd($stem, $syllable.$syllable.$syllable);        
        return 4;
    }        
    
    public static function getListForAutoComplete($pos_id, $lang_id) {
        $gramsets = [];
        if ($pos_id == PartOfSpeech::getVerbID()) {
            $gramsets = KarVerb::getListForAutoComplete($lang_id);
        } elseif (in_array($pos_id, PartOfSpeech::getNameIDs())) {
            $gramsets = KarName::getListForAutoComplete($lang_id);
        }
        return $gramsets;
    }
    
    public static function garmVowel($harmony, $vowel) {
        if (!$vowel || $harmony) {
            return $vowel;
        }
        
        $frontVowels = ['a'=>'ä', 'o'=>'ö', 'u'=>'y'];
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
     * дистрибуция свистящих и шипящих согласных: 
     * 1) если о.1 (stem1) заканчивается на i, используется свистящий вариант
     * 1) иначе шипящий вариант;
     * 
     * @param type $stem
     * @param type $whistl_str
     */
    public static function distrCons($stem1, $whistl_str) {
        if (!$whistl_str || preg_match("/i$/u", $stem1)) {
            return $whistl_str;
        }
        $hess_consonats = ['s'=>'š'];
        $letters = preg_split("//", $whistl_str);
        $new_str = '';
        foreach ($letters as $v) {
            if (isset($hess_consonats[$v])) {
                $new_str .= $hess_consonats[$v];
            } else {
                $new_str .= $v;
            }
        } 
        return $new_str;
    }
    
    /**
     * Если $stem заканчивается на одиночный $vowel 
     * т.е. любой согласный + $vowel, то $vowel переходит в $replacement
     * 
     * @param String $stem
     * @param String $vowel - group of chars
     * @param String $replacement - any string
     */
    public static function replaceCV($stem, $vowel, $replacement) {
        $C = "[".KarGram::consSet()."]’?";
        if (preg_match("/^(.+".$C.")[".$vowel."]$/u", $stem, $regs)) {
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

    public static function stemsFromDB($lemma, $dialect_id) {
//dd($lemma->pos_id);        
        if (in_array($lemma->pos_id, PartOfSpeech::getNameIDs())) { 
            return KarName::stemsFromDB($lemma, $dialect_id);
        } elseif ($lemma->pos_id == PartOfSpeech::getVerbID()) { 
            return KarVerb::stemsFromDB($lemma, $dialect_id);
        }       
    }
    
    public static function getStemFromWordform($lemma, $stem_n, $pos_id, $dialect_id) {
        if (in_array($pos_id, PartOfSpeech::getNameIDs())) { 
            return KarName::getStemFromWordform($lemma, $stem_n, $dialect_id);
        } elseif ($pos_id == PartOfSpeech::getVerbID()) { 
            return KarVerb::getStemFromWordform($lemma, $stem_n, $dialect_id);
        }
    }

    /**
     * 
     * @param Array $stems
     * @param Int $stem_n
     * @param Int $pos_id
     * @param Int $dialect_id
     * @param STRING $lemma
     * @return String
     */
    public static function getStemFromStems($stems, $stem_n, $pos_id, $lang_id, $dialect_id, $lemma) {
        if (in_array($pos_id, PartOfSpeech::getNameIDs())) { 
            return KarName::getStemFromStems($stems, $stem_n, $dialect_id);
        } elseif ($pos_id == PartOfSpeech::getVerbID()) { 
            return KarVerb::getStemFromStems($stems, $stem_n, $lang_id, $dialect_id, $lemma);
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
    
    /**
     * verbs:
     * puhk|eta (-ien/-enen, -ieu/-enou; -etah; -ei/-eni, -ettih)
     * pakastu|o (-u; -i)
     * 
     * @param type $template
     * @param type $pos_id
     * @param type $name_num
     * @param type $dialect_id
     * @return type
     */
    public static function stemsFromTemplate($template, $pos_id, $name_num, $dialect_id, $is_reflexive=null) {
        if (preg_match("/\{/", $template)) {//if ($dialect_id == 47) {
            return self::stemsFromTemplateWithBases($template, $pos_id, $name_num);
        }
        $lang_id = Dialect::getLangIDByID($dialect_id);
        
        if (in_array($pos_id, PartOfSpeech::getNameIDs())) { 
            return KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id);
        } elseif ($pos_id == PartOfSpeech::getVerbID()) {
            return KarVerb::stemsFromTemplate($template, $lang_id, $name_num, $is_reflexive);
        } else {
            return Grammatic::getAffixFromtemplate($template, $name_num);
        }
    }
    
    /**
     * Only for dialect_id=47 (tver)
     * 
     * @param type $template
     * @param type $pos_id
     * @param type $name_num
     * @return type
     */
    public static function stemsFromTemplateWithBases($template, $pos_id, $name_num) {
        $template = self::toRightTemplate($template, $name_num, $pos_id);
        
        $stems = self::stemsFromFullList($template);        
//var_dump($stems);        
        if (!$stems || ($pos_id == PartOfSpeech::getVerbID() && sizeof($stems)!=8) // constraints for tver dialects
                || (in_array($pos_id, PartOfSpeech::getNameIDs()) && sizeof($stems)!=6)) {
            return Grammatic::getAffixFromtemplate($template, $name_num);
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
    
    public static function getAffixesForGramset($gramset_id, $lang_id) {
        if (in_array($gramset_id, KarName::getListForAutoComplete($lang_id))) {
            if ($lang_id == 5) { 
                return KarNameOlo::getAffixesForGramset($gramset_id);
            } elseif ($lang_id == 6) { 
                return KarNameLud::getAffixesForGramset($gramset_id);
            } else {
                return KarName::getAffixesForGramset($gramset_id);
            }
        } elseif (in_array($gramset_id, KarVerb::getListForAutoComplete($lang_id))) {
            if ($lang_id == 5) { 
                return KarVerbOlo::getAffixesForGramset($gramset_id);
            } elseif ($lang_id == 6) { 
                return KarVerbLud::getAffixesForGramset($gramset_id);
            } else {
                return KarVerb::getAffixesForGramset($gramset_id);
            }
        }
        return [];
    }
    
    public static function templateFromWordforms($wordforms, $lang_id, $pos_id, $number) {
        if (in_array($pos_id, PartOfSpeech::getNameIDs())) {
            return KarName::templateFromWordforms($wordforms, $lang_id, $number);
        } elseif ($pos_id == PartOfSpeech::getVerbID()) { 
            return KarVerb::templateFromWordforms($wordforms, $lang_id);
        }
        return NULL;
    }
    
    public static function suggestTemplates($lang_id, $pos_id, $word) {
        if (in_array($pos_id, PartOfSpeech::getNameIDs())) {
            return KarName::suggestTemplates($lang_id, $word);
        } elseif ($pos_id == PartOfSpeech::getVerbID()) {
            return KarVerb::suggestTemplates($lang_id, $word);
        }
        return [];
    }    
}
