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
    
    public static function placeIds() {
        return [
            "01" => 145, 
            "02" => 233,
            "03" => 175,
            "04" => 232,
            "05" => 234,
            "06" => 140,
            "07" => 235,
            "08" => 236,
            "09" => 237,
            10   => 169,
            11   => 197,
            12   => 238,
            13   => 179,
            14   => 239,
            15   => 240,
            16   => 241,
            17   => 242,
            18   => 243,
            19   => 96,
            20   => 244,
            21   => 245,
            22   => 246,
            23   => 247,
            24   => 248,
            25   => 53,
            26   => 78,
            27   => 71,
            28   => 5,
            29   => 26,
            30   => 38];
    }
    /**
     * 
     * @return Array [<place_n> => ['id'=><place_id>, ['dialects'=>[<dialect1> => <lang1>, ...]]]
     */
    public static function placeDialects() {
        $places =[];
        foreach (self::placeIds() as $place_num => $place_id) {
            $places[$place_num] = ['id'=>$place_id]; 
        }
        
        foreach ($places as $place_n => $place_info) {
            $place_obj = Place::find($place_info['id']);
            if (!$place_obj) {
dd("Населенный пункт $place_n = ".$place_info['id']. " отсутствует в БД!");               
            }
            $places[$place_n]['dialects'] = $place_obj->getDialectLangs();
            $places[$place_n]['langs'] = array_unique(array_values($places[$place_n]['dialects']));
        }
        
        return $places;
    }

    public static function processBlocks($blocks) {
//dd($blocks);        
        $place_dialects = self::placeDialects();
        foreach ($blocks as $category_id => $concept_blocks) {
            foreach ($concept_blocks as $concept_block) {
//dd($concept_block);            
                $concept_obj = Concept::firstOrCreate(['text_ru'=>$concept_block['meaning'], 'concept_category_id'=>$category_id]);
                $lemma_dialects = self::chooseDialectsForLemmas($concept_block['place_lemmas'], $place_dialects);
//dd($lemma_dialects);      
                list($lang_lemmas, $lang_meanings) = 
                        self::addLemmas($concept_block['pos_id'], $concept_block['lemmas'], 
                                $lemma_dialects, $concept_obj);
//dd($concept_block['meaning'], $lang_lemmas, $lang_meanings);                
                self::addSynonims($concept_block['place_lemmas'], $lang_meanings, $place_dialects);
            }
        }
    }
    
    /**
     * 
     * @param Array $places [<place1_num>=>[<lemma1_num>,...], ...]
     * @return Array [<lemma1_num>=>[<lang1_id>=>[<dialect1_id>=>[<place1_id>, ...], ...], ...], ...]
     */
    public static function chooseDialectsForLemmas($places, $place_dialects) {
        $out = [];
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
     * Search objects of lemmas by lemma, pos_id and meaning text. 
     * Create new lemma if lemma is not found
     * Stop script if lemmas are found, but the meaning is not found.
     * 
     * @param INT pos_id - ID of part of speech
     * @param Array $lemmas [<lemma1_num>=><lemma1_text>, ...]
     * @param Array $lemma_places [<lemma1_num>=>[<lang1_id>=>[<dialect1_id>=>[<place1_id>, ...], ...], ...], ...]
     * 
     * @return Array [0=>[<lang1_id>=>[<lemma1_num>=><lemma1_obj>, ...],...], 1=>[<lang1_id>=>[<lemma1_num>=><meaning1_obj>, ...], ...]]
     */
    public static function addLemmas($pos_id, $lemmas, $lemma_places, $concept) {
        $lang_lemmas = $lang_meanings = [];
        $meaning_lang = Lang::getIDByCode('ru');
        
        foreach ($lemma_places as $lemma_num=> $lemma_langs) {
            if (Grammatic::hasPhonetics($lemmas[$lemma_num])) {
                $phonetics = Grammatic::toRightForm($lemmas[$lemma_num],false);
                $lemmas[$lemma_num] = Grammatic::toRightForm($lemmas[$lemma_num]);
            } 
            
            foreach ($lemma_langs as $lang_id=>$dialects) {
//dd($pos_id, $lemmas[$lemma_num], $lang_id, $dialects);   
                $lemma_coll = Lemma::wherePosId($pos_id)
                                  ->where('lemma', 'like', $lemmas[$lemma_num])
                                  ->whereLangId($lang_id)->get();
//dd($lemma_coll);       
                list($lemma_obj, $meaning_obj) = 
                        self::searchLemmaByMeaningText($lemma_coll, $concept->text, $meaning_lang, $lang_id);
                if (!isset($lemma_obj) || !$lemma_obj) {
                    $lemma_obj = Lemma::store($lemmas[$lemma_num], $pos_id, $lang_id);
                    $meaning_obj = Meaning::storeLemmaMeaning($lemma_obj->id, 1, [$meaning_lang=>$concept->text]);
                }
                $lemma_obj->addDialectLinks($dialects);
                if (isset($phonetics)) {
                    LemmaFeature::store($lemma_obj->id, ['phonetics'=>$phonetics]);
                }
                if (!$meaning_obj->concepts()->where('concept_id', $concept->id)->first()) {                           
                    $meaning_obj->concepts()->attach($concept->id);
                }
                $lang_lemmas[$lang_id][$lemma_num] = $lemma_obj;
                $lang_meanings[$lang_id][$lemma_num] = $meaning_obj;
            }
        }
        return [$lang_lemmas, $lang_meanings];
    }
    
    public static function searchLemmaByMeaningText($lemma_coll, $meaning_text, $meaning_lang, $search_lang) {
        if (!sizeof($lemma_coll)) {
            return [null,null];
        }
        foreach ($lemma_coll as $lemma) {
            $meaning_obj = self::meaningFound($lemma, $meaning_text, $meaning_lang);
            if ($meaning_obj) {
                return [$lemma, $meaning_obj];
            }
        }
        print "<p>Нашлись леммы, но нет подходящего значения: <a href=/ru/dict/lemma?search_lang=$search_lang&search_lemma=".$lemma_coll[0]->lemma.
                "&search_pos_id=".$lemma_coll[0]->pos_id.">проверить</a></p>";
        exit(1);
    }
    
    public static function meaningFound($lemma, $meaning_text, $meaning_lang) {
        foreach ($lemma->meanings as $meaning) {
            $meaning_text_obj = $meaning->meaningTexts()->where('lang_id',$meaning_lang)->first();
            if ($meaning_text_obj->meaning_text == $meaning_text) {
                return $meaning;
            }
        }            
        return false;
    }
    
    /**
     * 
     * @param Array $lemma_places [<lemma1_num>=>[<lang1_id>=>[<dialect1_id>=>[<place1_id>, ...], ...], ...], ...]
     * @param Array $lang_meanings [<lang1_id>=>[<lemma1_num>=><meaning1_obj>, ...], ...]
     */
    public static function addSynonims($place_lemmas, $lang_meanings, $place_dialects) {
        $synonyms_id = 2;
//dd($place_lemmas, $lang_lemmas, $place_dialects);      
        foreach ($place_lemmas as $place_num => $lemma_nums) {
            if (sizeof($lemma_nums)<2) {
                continue;
            }
print "<p>$place_num: ".join(', ', $lemma_nums)."; ".join(', ', $place_dialects[$place_num]['langs'])."</p>";  
            foreach ($place_dialects[$place_num]['langs'] as $lang_id) {
                for ($i=0; $i<sizeof($lemma_nums)-1; $i++) {
                    for ($j=$i+1; $j<sizeof($lemma_nums); $j++) {
                        if ($lemma_nums[$i][0] != $lemma_nums[$j][0]) {
print "<p>".$lemma_nums[$i]."=".$lemma_nums[$j]."</p>";                
                            $lang_meanings[$lang_id][$lemma_nums[$i]]->meaningRelations()
                                ->attach($synonyms_id,['meaning2_id'=>$lang_id][$lemma_nums[$j]]);
                
                            // reverse relation
                            $lang_meanings[$lang_id][$lemma_nums[$j]]->meaningRelations()
                                ->attach($synonyms_id,['meaning2_id'=>$lang_id][$lemma_nums[$i]]);
                        }
                    }
                }
            }
        }
exit(1);        
    }
}
