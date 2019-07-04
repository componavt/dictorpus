<?php

namespace App\Library\Import;

use App\Models\Dict\PartOfSpeech;
use App\Library\Grammatic;
/** 
 */
class DictParser
{
    public static function parseEntry($line, $dialect_id) {
        $line = preg_replace("~\x{00a0}~siu", " ", $line); // non-break space
        $line = preg_replace("~\x{01c0}~siu", "|", $line); // dental click ǀ

        // split by '. - ' into lemma and meanings parts
        if (!preg_match("/^([^\.]+)\.\s+([^\.]*)\.?\s*\–\s+(.+)$/", $line, $regs)) {
            return false;
        }
        $num = trim($regs[2]);
        $lemma_part = self::parseLemmaPart(trim($regs[1]), $num, $dialect_id);
        $meaning_part = self::parseMeaningPart(trim($regs[3]));

        return array_merge($lemma_part, ['num'=>$num], $meaning_part);
        
        
    }

    public static function parseLemmaPart($lemma_pos, $num, $dialect_id) {
        if (!preg_match("/^(.+)\s+([^\s]+)$/", $lemma_pos, $regs)) {
            return ['lemmas' => false];
        }
        $lemma_part['pos_id'] = self::getPOSID(trim($regs[2]));            
        $lemma_part['lemmas'] = self::parseLemmas(trim($regs[1]), $num, $dialect_id, $lemma_part['pos_id']);
        return $lemma_part;
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
    public static function parseLemmas($lemmas, $num, $dialect_id, $pos_id) {
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
            return self::nominalToRightTemplate($bases, $base_list, $lemma_str, $num);
        } elseif ($pos_id == PartOfSpeech::getVerbID()) {  
            return self::verbToRightTemplate($bases, $base_list, $lemma_str, $num);
        }
print "<p>Unknown pos</p>";        
        return $lemma_str;
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
     * @param type $lemma_str
     */
    public static function nominalToRightTemplate($bases, $base_list, $lemma_str, $num) {
        if (!(sizeof($base_list)==3 || sizeof($base_list)==1 && $num=='pl')) {
            return $lemma_str;
        }
        if (preg_match("/^([^\/\s]+)\s*\/\s*([^\s]+)$/", $base_list[0], $regs)) {
            $bases[1] = $regs[1];
            $bases[2] = $regs[2];
        } else {
            $bases[1] = $bases[2] = $base_list[0];
        }
        if ($num=='pl') {
            $bases[3] = '';
            $bases[4] = $bases[1];
            $bases[5] = $bases[2];
            $bases[1] = $bases[2] = '';
        } else {
            $bases[3] = $base_list[1];
            if (preg_match("/^([^\/\s]+)\s*\/\s*([^\s]+)$/", $base_list[2], $regs)) {
                $bases[4] = $regs[1];
                $bases[5] = $regs[2];
            } else {
                $bases[4] = $bases[5] = $base_list[2];
            }
        }
        return '{'.join(', ',$bases).'}';
    }
    
    /**
     * Only for dialect_id=47 (tver)
     * 
     * lemma_str examples:
     * 
     * ahavoit|tua {-a / -ta, -i / -ti, -ta, -eta, -ett}
     * avau|duo {-du, -du, -du, -vuta, -vutt} (pos=v, num=impers - without base 1 and base 3) - НЕ ПРЕДУСМОТРЕН ШАБЛОН без основ 1 и 3, исправить KarVerb!!!!
     * 
     * @param type $lemma_str
     */
    public static function verbToRightTemplate($bases, $base_list, $lemma_str, $num) {
        if (sizeof($base_list)!=5) {
            return $lemma_str;
        }

        if (preg_match("/^([^\/\s]+)\s*\/\s*([^\s]+)$/", $base_list[0], $regs)) {
            $bases[1] = $regs[1];
            $bases[2] = $regs[2];
        } else {
            if ($num=='impers') {
                $bases[1] = '';
            } else {
                $bases[1] = $base_list[0];
                
            }
            $bases[2] = $base_list[0];
        }
        
        if (preg_match("/^([^\/\s]+)\s*\/\s*([^\s]+)$/", $base_list[1], $regs)) {
            $bases[3] = $regs[1];
            $bases[4] = $regs[2];
        } else {
            if ($num=='impers') {
                $bases[3] = '';
            } else {
                $bases[3] = $base_list[1];
                
            }
            $bases[4] = $base_list[1];
        }
        $bases[5] = $base_list[2];
        $bases[6] = $base_list[3];
        $bases[7] = $base_list[4];
        
        return '{'.join(', ',$bases).'}';
    }
    
    /** Splits text line to meanings, e.g. "1. first meaning 2. second meaning" 
     * will return ["first meaning", "second meaning"]
     * 
     * @param type $meanings text line from the dictionary
     * @return type array of strings, that is array of meanings
     */
    public static function parseMeaningPart($meanings) {
        $count = 1;
        $meaning_part['meanings'][$count] = $meanings;
        // only one meaning
        if (!preg_match("/^".$count."\.\s*(.+)$/", $meaning_part['meanings'][$count], $regs)) {
            return $meaning_part;
        }
        $meaning_part['meanings'][$count++] = $regs[1];
        while (preg_match("/^(.+)\s*".$count."\.\s*(.+)$/", $meaning_part['meanings'][$count-1], $regs)) {
            $meaning_part['meanings'][$count-1] = trim($regs[1]);
            $meaning_part['meanings'][$count++] = trim($regs[2]);
        }
        return $meaning_part;
    }    
    /**
     * a. – имя прилагательное
     * adv. – наречие
     * conj. – союз
     * interj. – междометие
     * num. – имя числительное
     * partic. – частица
     * postp. – послелог
     * prep. – предлог
     * pron. – местоимение
     * s. – имя существительное
     * v. – глагол 
     * 
     * @param string $name - abbreviated part of speech, i.e. 'adv' 
     */
    public static function getPOSID($name) {
        $names_to_codes = [
            'a' => 'ADJ',
            'adv' => 'ADV',
            'conj' => 'CCONJ',
            'interj' => 'INTJ',
            'num' => 'NUM',
            'partic' => 'PART',
            'postp' => 'POSTP',
            'prep' => 'PREP',
            'pron' => 'PRON',
            's' => 'NOUN',
            'v' => 'VERB'
        ];
        if (!isset($names_to_codes[$name])) {
            print "<p><b>ERROR pos:</b> $name</p>\n";
        } else {
            $pos_code = $names_to_codes[$name];
            return PartOfSpeech::getIDByCode($pos_code);
            
        }
    }
    
    public static function checkEntry($entry, $line, $count) {
        if (!$entry) {
            print "<p><b>$count. ERROR line:</b> $line</p>\n"; 
            return;
        } 
        if (!isset($entry['lemmas'])) {
            print "<p><b>$count. ERROR lemma_pos:</b> $line</p>\n";                
            return;
        } 
        if (!$entry['lemmas'][0] || preg_match("/.+\{/",$entry['lemmas'][0]) || mb_strpos('|',$entry['lemmas'][0])) {
            print "<p><b>$count. ERROR lemma:</b> $line</p>\n";                
            return;
        } 
        if (!isset($entry['pos_id'])) {
            print "<p><b>$count. ERROR pos:</b> $line</p>\n";                
            return;
        } 
        if (!isset($entry['meanings']) || !is_array($entry['meanings'])) {
            print "<p><b>$count. ERROR meanings:</b> $line</p>\n";                
            return;
        }
        foreach ($entry['meanings'] as $meaning) {
            if (preg_match("/\d\./", $meaning)) {
                print "<p><b>$count. ERROR meaning:</b> $line</p>\n";                
                return;                
            }
        }
    }
}
