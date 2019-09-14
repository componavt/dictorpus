<?php

namespace App\Library\Grammatic;

use App\Library\Grammatic\VepsName;
use App\Library\Grammatic\VepsVerb;

use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;

class VepsGram
{
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
    
    public static function getListForAutoComplete($pos_id) {
        $gramsets = [];
        if ($pos_id == PartOfSpeech::getVerbID()) {
            $gramsets = VepsVerb::getListForAutoComplete();
        } elseif (in_array($pos_id, PartOfSpeech::getNameIDs())) {
            $gramsets = VepsName::getListForAutoComplete();
        }
        return $gramsets;
    }
    
    public static function stemsFromTemplate($template, $pos_id, $name_num = null) {
        $template = trim($template);
        $stems[0] = $base = $template;
        $base_suff = null;
        $arg = "([^\|]*)";
        $div_arg = "\|".$arg;
        $base_shab = "([^\s\(\|]+)";
        $base_suff_shab = "([^\s\(\|]*)";
        $okon1_shab = "-([^\,\;\)]+)";
      
        // nominals
        if (in_array($pos_id, PartOfSpeech::getNameIDs())) { 
            list($stems, $name_num, $base, $base_suff) = VepsName::stemsFromTemplate($template, $name_num);
        // verbs
        } elseif ($pos_id == PartOfSpeech::getVerbID() && 
            (preg_match('/^{{vep-conj-stems'.$div_arg.$div_arg.$div_arg.'\|?'.$arg.'}}$/u',$template, $regs) ||
            preg_match("/^".$base_shab."\|?".$base_suff_shab."\s*\(".$okon1_shab."\,\s*-([^\,\;]+)\,?\s*-([^\,\;]*)\)/", $template, $regs))) {      
            $base = $regs[1];
            $base_suff = $regs[2];
            $stems = VepsVerb::stemsFromTemplate($regs, $pos_id);
        }
//dd('stems:',$stems);                
        return [$stems, $name_num, $base, $base_suff];
    }
    
    public static function stemsFromDB($lemma, $pos_id, $dialect_id) {
        if (in_array($pos_id, PartOfSpeech::getNameIDs())) { 
            return VepsName::stemsFromDB($lemma, $dialect_id);
        } elseif (in_array($pos_id, PartOfSpeech::getNameIDs())) { 
            return VepsVerb::stemsFromDB($lemma, $dialect_id);
        }       
    }
    
    public static function getStemFromWordform($lemma, $stem_n, $pos_id, $dialect_id) {
        if (in_array($pos_id, PartOfSpeech::getNameIDs())) { 
            return VepsName::getStemFromWordform($lemma, $stem_n, $dialect_id);
        } elseif (in_array($pos_id, PartOfSpeech::getNameIDs())) { 
            return VepsVerb::getStemFromWordform($lemma, $stem_n, $dialect_id);
        }
    }
    
    public static function templateForImport() {
        return "([^\.]+)\.\s*([^\.]*)\.?\s*\-\s*(.+)";
//        return "([^\.]+)\.\s*([^\.]*)\.?\s*\-\s*(.+)";
    }
}