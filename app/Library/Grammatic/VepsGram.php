<?php

namespace App\Library\Grammatic;

use App\Library\Grammatic\VepsName;
use App\Library\Grammatic\VepsVerb;

use App\Library\Grammatic;

use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;

class VepsGram
{
    public static function consSet() {
        return "bcčdfghjklmnprsšzžtv";
    }
    
    public static function vowelSet() {
        return "aeiouüäö";
    }
    
    public static function consGeminantSet() {
        return "kgtdpbz";
    }
        
    public static function dictTemplate() {
        return "([^\s\(]+\s*\([^\,\;]+\,?\s*[^\,\;]*[\;\,]?\s*[^\,\;]*\))";
//        return "([^\s\(]+)";
    }
    
    public static function rightConsonant($d, $l) {
        $consonants = ["d" => ["b"=>"b", "d"=>"d", "g"=>"g"],
                       "t" => ["b"=>"p", "d"=>"t", "g"=>"k"]];
        if (isset($consonants[$d][$l])) {
            return $consonants[$d][$l];
        }
    }
    
    public static function ringConsonant($l) {
        $consonants = ["k"=>"g", "p"=>"b", "s"=>"z", "š"=>"ž", "t"=>"d"];
        if (isset($consonants[$l])) {
            return $consonants[$l];
        }
    }
    
    public static function sonantSet() {
        return "bdgvzžjmnlr";
    }
    
    public static function getListForAutoComplete($pos_id) {
        $gramsets = [];
        if ($pos_id == PartOfSpeech::getVerbID()) {
            $gramsets = VepsVerb::getListForAutoComplete();
        } elseif (in_array($pos_id, PartOfSpeech::getNameIDs())) {
            $gramsets = VepsName::getListForAutoComplete();
        }
        return $gramsets;
    }
    
    public static function stemsFromTemplate($template, $pos_id, $name_num = null, $is_reflexive = null) {
//dd($template);        
        if (!preg_match("/\{\{/", $template)) {
            $template = preg_replace('/\|\|/','',$template);
        }
        
        $arg = "([^\|]*)";
        $div_arg = "\|".$arg;
        $base_shab = "([^\s\(\|]+)";
        $base_suff_shab = "([^\s\(\|]*)";
        $okon1_shab = "-([^\,\;\)]+)";
      
        // nominals
        if (in_array($pos_id, PartOfSpeech::getNameIDs())) { 
            return VepsName::stemsFromTemplate($template, $name_num);
        // verbs
        } elseif ($pos_id == PartOfSpeech::getVerbID() && 
            (preg_match('/^{{vep-conj-stems'.$div_arg.$div_arg.$div_arg.'\|?'.$arg.'}}$/u',$template, $regs) ||
            preg_match("/^".$base_shab."\|?".$base_suff_shab."\s*\(".$okon1_shab."\,?\s*-?([^\,\;]*)\,?\s*-?([^\,\;]*)\)/", $template, $regs))) {  
//dd('regs:',$regs);            
            $base = $regs[1];
            $base_suff = $regs[2];
            $stems = VepsVerb::stemsFromTemplate($regs, $is_reflexive);
        } else {
            return Grammatic::getAffixFromtemplate($template, $name_num);
        }
//dd('stems out:',$stems);                
        return [$stems, $name_num, $base, $base_suff];
    }
    
    public static function stemsFromDB($lemma, $dialect_id) {
        if (in_array($lemma->pos_id, PartOfSpeech::getNameIDs())) { 
            return VepsName::stemsFromDB($lemma, $dialect_id);
        } elseif ($lemma->pos_id == PartOfSpeech::getVerbID()) { 
            return VepsVerb::stemsFromDB($lemma, $dialect_id);
        }       
    }
    
    public static function getStemFromWordform($lemma, $stem_n, $pos_id, $dialect_id, $is_reflexive) {
        if (in_array($pos_id, PartOfSpeech::getNameIDs())) { 
            return VepsName::getStemFromWordform($lemma, $stem_n, $dialect_id);
        } elseif ($pos_id == PartOfSpeech::getVerbID()) { 
            return VepsVerb::getStemFromWordform($lemma, $stem_n, $dialect_id, $is_reflexive);
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
    public static function getStemFromStems($stems, $stem_n, $pos_id, $dialect_id, $lemma) {
        if (in_array($pos_id, PartOfSpeech::getNameIDs())) { 
            return VepsName::getStemFromStems($stems, $stem_n, $dialect_id);
        } elseif ($pos_id == PartOfSpeech::getVerbID()) { 
            return VepsVerb::getStemFromStems($stems, $stem_n, $dialect_id, $lemma);
        }
    }
    
    public static function templateForImport() {
        return "([^\.]+)\.\s*([^\.]*)\.?\s*\-\s*(.+)";
//        return "([^\.]+)\.\s*([^\.]*)\.?\s*\-\s*(.+)";
    }
    
    /**
     * Сколько слогов в основе?
     * 
     * @param String $stem
     * @return INT 1 - односложное, 2 - двусложное, 3 - трехсложное, 4 - многосложное
     */
    public static function countSyllable($stem) {
        $consonant = "[".vepsGram::consSet()."]";
        $syllable = "(".$consonant."’?)*[".vepsGram::vowelSet()."][iu]?(".$consonant."’?)*";
        if (preg_match("/^".$syllable."$/u",$stem)) {
            return 1;
        } elseif (preg_match("/^".$syllable.$syllable."$/u",$stem)) {
            return 2;
        } elseif (preg_match("/^".$syllable.$syllable.$syllable."$/u",$stem)) {
            return 3;
        }
//dd($stem, $syllable.$syllable.$syllable);        
        return 4;
    }
    
    public static function getAffixesForGramset($gramset_id) {
        if (in_array($gramset_id, VepsName::getListForAutoComplete())) {
            return VepsName::getAffixesForGramset($gramset_id);
        } elseif (in_array($gramset_id, VepsVerb::getListForAutoComplete())) {
            return VepsVerb::getAffixesForGramset($gramset_id);
        }
        return [];
    }
}