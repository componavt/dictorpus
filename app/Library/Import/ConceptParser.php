<?php

namespace App\Library\Import;

use App\Library\Grammatic;

use App\Models\Corpus\Place;

use App\Models\Dict\Concept;
use App\Models\Dict\ConceptCategory;
use App\Models\Dict\Dialect;
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
                    $blocks[$category_code][$concept_id] = ['meaning'=>$concept_text, 'pos_id'=>$pos_id, 'lemmas'=>$lemmas, 'place_lemmas'=>$place_lemmas]; 
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
        $names_to_codes = Concept::getPOSCodes();
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
    
    public static function saveCategories($categories) {
        foreach ($categories as $category_id => $category_name) {
            if (!ConceptCategory::whereId($category_id)->count()) {
                ConceptCategory::create(['id'=>$category_id, 'name_ru'=>$category_name]);
            }
        }
    }
    
    /**
     * 
     * @return Array [<place_n> => ['id'=><place_id>, ['dialects'=>[<dialect1> => <lang1>, ...]]]
     */
    public static function placeDialects() {
        $places =[
            "01" => ['id'=>145], 
            "02" => ['id'=>233],
            "03" => ['id'=>175],
            "04" => ['id'=>232],
            "05" => ['id'=>234],
            "06" => ['id'=>140],
            "07" => ['id'=>235],
            "08" => ['id'=>236],
            "09" => ['id'=>237],
            10   => ['id'=>169],
            11   => ['id'=>197],
            12   => ['id'=>238],
            13   => ['id'=>179],
            14   => ['id'=>239],
            15   => ['id'=>240],
            16   => ['id'=>241],
            17   => ['id'=>242],
            18   => ['id'=>243],
            19   => ['id'=>96],
            20   => ['id'=>244],
            21   => ['id'=>245],
            22   => ['id'=>246],
            23   => ['id'=>247],
            24   => ['id'=>248],
            25   => ['id'=>53],
            26   => ['id'=>78],
            27   => ['id'=>71],
            28   => ['id'=>5],
            29   => ['id'=>26],
            30   => ['id'=>38]];
        
        foreach ($places as $place_n => $place_info) {
            $place_obj = Place::find($place_info['id']);
            if (!$place_obj) {
dd("Населенный пункт $place_n = ".$place_id. " отсутствует в БД!");               
            }
            $places[$place_n]['dialects'] = $place_obj->getDialectLangs();
        }
        
        return $places;
    }

    public static function processBlocks($blocks) {
        foreach ($blocks as $category_id => $concept_blocks) {
            foreach ($concept_blocks as $concept_block) {
//dd($concept_block);                
                $lemma_dialects = self::chooseDialectsForLemmas($concept_block['place_lemmas']);
//dd($lemma_dialects);      
                list($lang_lemmas, $lang_meanings) = self::addLemmas($concept_block['pos_id'], $concept_block['lemmas'], $lemma_dialects);
            }
        }
    }
    
    /**
     * 
     * @param Array $places [<place1_num>=>[<lemma1_num>,...], ...]
     * @return Array [<lemma1_num>=>[<lang1_id>=>[<dialect1_id>=>[<place1_id>, ...], ...], ...], ...]
     */
    public static function chooseDialectsForLemmas($places) {
        $out = [];
        $place_dialects = self::placeDialects();
//dd($place_dialects);        
        foreach ($places as $place_n => $place_lemmas) {
            $place_id = $place_dialects[$place_n]['id'];
            $place_dials = $place_dialects[$place_n]['dialects'];
            foreach ($place_lemmas as $lemma) {
                foreach ($place_dials as $dialect_id => $lang_id) {
                    if (!isset($out[$lemma][$lang_id])) {
                        $out[$lemma][$lang_id] = [];
                    }
                    
                    if (!isset($out[$lemma][$lang_id][$dialect_id])) {
                        $out[$lemma][$lang_id][$dialect_id] = [];
                    }
                    
                    $out[$lemma][$lang_id][$dialect_id][] = $place_id;
                }
            }
        }
        return $out;
    }
    
    /**
     * @param INT pos_id - ID of part of speech
     * @param Array $lemmas [<lemma1_num>=><lemma1_text>, ...]
     * @param Array $lemmas [<lemma1_num>=>[<lang1_id>=>[<dialect1_id>=>[<place1_id>, ...], ...], ...], ...]
     * 
     * @return Array [0=>[<lang1_id>=><lemma1_obj>,...], 1=>[<lang1_id>=><meaning1_obj>,...]]
     */
    public static function addLemmas($pos_id, $lemmas, $lemma_places) {
        foreach ($lemma_places as $lemma_num=> $lemma_langs) {
            foreach ($lemma_langs as $lang_id=>$dialects) {
//dd($pos_id, $lemmas[$lemma_num], $lang_id, $dialects);   
                $lemma_obj = Lemma::wherePosId($pos_id)
                                  ->where('lemma', 'like', $lemmas[$lemma_num])
                                  ->whereLangId($lang_id)->get();
dd($lemma_obj);         
                if (!sizof($lemma_obj)) {
                    $lemma_obj = Lemma::store($lemmas[$lemma_num], $pos_id, $lang_id);
//найти формат массива $request->new_meanings в форме леммы
//                                        storeLemmaMeanings($meanings, $lemma_id);
                    // может быть выделить storeLemmaMeaning
                }
                $meaning_is_found = false;
                
            }
        }
    }
}
