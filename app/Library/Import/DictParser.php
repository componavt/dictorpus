<?php

namespace App\Library\Import;

use App\Models\Dict\PartOfSpeech;

/** 
 */
class DictParser
{
    public static function parseEntry($line) {
        $line = preg_replace("~\x{00a0}~siu", " ", $line);
        // split by '. - ' into lemma and meanings parts
        if (!preg_match("/^([^\.]+)\.\s+\–\s+(.+)$/", $line, $regs)) {
            print "<p><b>ERROR line:</b> $line</p>\n";
        } else {
            $lemma_part = self::parseLemmaPart(trim($regs[1]));
            $meaning_part = self::parseMeaningPart(trim($regs[2]));
            return array_merge($lemma_part, $meaning_part);
        }
        
    }

    public static function parseLemmaPart($lemma_pos) {
        if (!preg_match("/^(.+)\s+([^\s]+)$/", $lemma_pos, $regs)) {
            print "<p><b>ERROR lemma:</b> $lemma_pos</p>\n";
        } else {
//print '|'.$lemma_pos.'|<br>';
            $lemma_part['lemma'] = trim($regs[1]);
print $lemma_part['lemma'].'<br>';
            $lemma_part['pos_id'] = self::getPOSID(trim($regs[2]));            
print $lemma_part['pos_id'].'<br>';
        }
        return $lemma_part;
    }    
    
    public static function parseMeaningPart($meanings) {
        $meaning_part['meanings'][0] = $meanings;
print $meanings.'<br><br>';
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
}
