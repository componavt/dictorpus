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
        return "^([^\s\(]+\s*\([^\,\;]+\,?\s*[^\,\;]*[\;\,]?\s*[^\,\;]*\))";
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
        $stems = $base = $base_suff = null;
        if (in_array($pos_id, PartOfSpeech::getNameIDs())) { 
//dd($template,preg_match("/^([^\s\(\|]+)\|?([^\s\(\|]*)\s*\(-([^\,\;\)]+)\)/", $template));            
            if (preg_match("/^vep-decl-stems\|n=pl\|([^\|]*)\|([^\|]*)\|([^\|]*)\|?([^\|]*)$/u",$template, $regs) ||
                    ($name_num == 'pl' && preg_match("/^([^\s\(\|]+)\|?([^\s\(\|]*)\s*\(-([^\,\;\)]+)\)/", $template, $regs))) {
//dd(1, $regs);     
                $name_num = 'pl';
                list($stems, $base, $base_suff) =  VepsName::stemsPlFromTemplate($regs);
            } elseif (preg_match("/^vep-decl-stems\|n=sg\|([^\|]*)\|([^\|]*)\|([^\|]*)\|([^\|]*)$/u",$template, $regs) ||
                    ($name_num == 'sg' && preg_match("/^([^\s\(\|]+)\|?([^\s\(\|]*)\s*\(-([^\,\;\)]+)\,?\s*-?([^\,\;]*)\)/", $template, $regs)) ||
                    (preg_match("/^([^\s\(\|]+)\|?([^\s\(\|]*)\s*\(-([^\,\;\)]+)\)/", $template, $regs))) {
//dd(2, $regs);     
                $name_num = 'sg';
                list($stems, $base, $base_suff) =  VepsName::stemsSgFromTemplate($regs);
            } elseif (preg_match("/^vep-decl-stems\|([^\|]*)\|([^\|]*)\|([^\|]*)\|([^\|]*)\|?([^\|]*)$/u",$template, $regs) ||
                    preg_match("/^([^\s\(\|]+)\|?([^\s\(\|]*)\s*\(-([^\,\;]+)\,\s*-?([^\,\;]*)[\;\,]?\s*-([^\,\;]+)\)/", $template, $regs)) {
//dd(3, $regs);     
                list($stems, $base, $base_suff) = VepsName::stemsFromTemplate($regs, $pos_id, $name_num);
            }
//dd(4);
        } elseif ($pos_id == PartOfSpeech::getVerbID() && 
            (preg_match('/^vep-conj-stems\|([^\|]*)\|([^\|]*)\|([^\|]*)\|?([^\|]*)$/u',$template, $regs) ||
            preg_match("/^([^\s\(\|]+)\|?([^\s\(\|]*)\s*\(-([^\,\;]+)\,\s*-([^\,\;]+)\)/", $template, $regs))) {      
//dd($regs);     
            $base = $regs[1];
            $base_suff = $regs[2];
            $stems = VepsVerb::stemsFromTemplate($regs, $pos_id);
        }
//dd('stems:',$stems);                
        return [$stems, $name_num, $base, $base_suff];
    }
    
    public static function stemsFromDB($lemma, $dialect_id) {
        if (in_array($pos_id, PartOfSpeech::getNameIDs())) { 
            return VepsName::stemsFromDB($lemma, $dialect_id);
        } elseif (in_array($pos_id, PartOfSpeech::getNameIDs())) { 
            return VepsVerb::stemsFromDB($lemma, $dialect_id);
        }       
    }
}