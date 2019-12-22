<?php

namespace App\Library\Import;

use App\Library\Grammatic;

use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\LemmaBase;
use App\Models\Dict\LemmaFeature;
use App\Models\Dict\Meaning;
use App\Models\Dict\MeaningText;
use App\Models\Dict\PartOfSpeech;
/** 
 */
class ConceptParser
{
    public static function readBlocks($lines) {
        $count = $block_line_num = 0;
        $categories = $blocks = [];
        $category_code = $category_name = '';
        foreach ($lines as $line) {
            $count++;
            $line = trim($line);
//print "<p>$count) $line\n</p>";            
            if (!$line) {
                $block_line_num = 0;
                continue;
            }
            
            self::checkWrongSymbols($line);
            if ($block_line_num == 0) { // category
                $block_line_num++;
                if (preg_match("/^([ABC]\d{1,3})\s*-\s*(.+)$/",$line, $regs)) {
                    $category_code = $regs[1];
                    $category_name = $regs[2];
                    $categories[$category_code] = $category_name;
                    continue;
                }
            }
            if ($block_line_num == 1) { // concept ID
                if (preg_match("/^(\d{4})$/",$line, $regs)) {
                    $concept_id = $regs[1];
                    $block_line_num++;
                    continue;
                }
                print "<p>ОШИБОЧНЫЙ КОД ПОНЯТИЯ в $count строке</p>\n";
                return [$categories, $blocks];                    

            } elseif ($block_line_num == 2) { // POS
                $pos_id = self::getPOSID($line);
                if ($pos_id) {
                    $block_line_num++;
                    continue;
                }
                print "<p>ОШИБОЧНАЯ ЧАСТЬ РЕЧИ в $count строке</p>\n";
                return [$categories, $blocks];                    
                
            } elseif ($block_line_num == 3) { // Concept = Meaning
                $concept_text = $line;
                $block_line_num++;
                continue;                
                
            } elseif ($block_line_num == 4) { // Lemmas
                $lemmas = self::parseLemmas($line);
                if (!sizeof($lemmas)){
                    print "<p>ОШИБКА РАЗБОРА ЛЕММ в $count строке</p>\n";
                    return [$categories, $blocks];                    
                }
                $block_line_num++;
                
            } elseif ($block_line_num > 4) { // Places
                $place_num=str_pad($block_line_num-4, 2, "0", STR_PAD_LEFT);
                $place_lemmas[$place_num] = self::parsePlace($line, $place_num);
                if ($place_lemmas[$place_num] === false) {
                    print "<p>ОШИБКА РАЗБОРА НАСЕЛЕННОГО ПУНКТА в $count строке</p>\n";
                    return [$categories, $blocks];                    
                }
                if ($block_line_num < 34) {
                    $block_line_num ++;
                } else {
                    $blocks[$category_code][$concept_id] = ['meaning'=>$concept_text, 'lemmas'=>$lemmas, 'place_lemmas'=>$place_lemmas]; 
                    $block_line_num = 0;
                }
            }             
        }        
        return [$categories, $blocks];
    }
    
    public static function checkWrongSymbols($line) {
        if (preg_match("/[^a-zäöüčšž’0-9а-яё\|\-\?\s\,\;\(\)\}\{\:\.\/i̮i̬ń΄u̯ŕĺśź~ηć]/iu", $line)) { //sulaimi(~e)
            print "<p>В строке $count недопустимый символ<br>$line</p>";
        }        
    }
    
    /**
     * @param string $name - abbreviated part of speech, i.e. 'adv' 
     * @return INT ID of part of speech
     */
    public static function getPOSID($pos_code) {
        $names_to_codes = ['NOUN', 'VERB', 'ADJ'];
        if (!in_array($pos_code, $names_to_codes)) {
            return false;
        } 
        return PartOfSpeech::getIDByCode($pos_code);
    }
    
    public static function parseLemmas($line) {
        $out = [];
        $letters = range('a', 'z');
        $synonims = preg_split("/\|\|/", $line);
        for ($i=0; $i<sizeof($synonims); $i++) {
           $lemmas = preg_split("/\//", $synonims[$i]);
           for ($j=0; $j<sizeof($lemmas); $j++) {
               $out[$letters[$i].(string)($j+1)] = $lemmas[$j];
           }
        }
        return $out;
    }
    
    public static function parsePlace($line, $place_num) {
        if (!preg_match("/^".$place_num."\:(.*)$/",$line, $regs)) {
            return false;
        }
        if ($regs[1] == '' || $regs[1] == '-') {
            return [];
        }
        return preg_split("/,/", $regs[1]);
    }
}
