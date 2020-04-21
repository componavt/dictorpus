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
class ConceptParser {
//    public $parserLabel = 2;    
    
    public static function getLabel() {
        return 2;
    }

    /**
     * parse import text and create two arrays:
     *  1) categories: [<category1_code> => <category1_text>, ...], 
     *          f.e. ["A11" => "Небо, небесные тела", ...]
     *  2) blocks: [<category1_code> => [
                        <concept1_id_from_text> => [
                          "meaning" => <concept1_meaning_text>,
                          "pos_id" => <part_of_speech_id>,
                          "lemmas" => [<lemma1_id_inside_concept> => <lemma1>, ...],
                          "place_lemmas" => [
                            <place1_id_from_text> => [<lemma1_id_inside_concept>, ...],
     *                      ...
                            ]
     *                  ]
     *              ]
     *          f.e. ["A11" => [
                        "0001" => [
                          "meaning" => "земля, суша",
                          "pos_id" => 5,
                          "lemmas" => ["a1" => "mua", ...],
                          "place_lemmas" => [
                            "01" => ["a1"],
     *                      ...
                            ]
     *                  ]
     *              ]
     * @param string $lines
     * @return array
     */
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

            self::checkWrongSymbols($line, $count);
            if ($block_line_num == 0) { // category
                $block_line_num++;
                if (preg_match("/^([ABC]\d{1,3})\s*-\s*(.+)$/", $line, $regs)) {
                    $category_code = $regs[1];
                    $category_name = $regs[2];
                    $categories[$category_code] = $category_name;
                    continue;
                }
            }
            if ($block_line_num == 1) { // concept ID
                if (preg_match("/^(\d{4})$/", $line, $regs)) {
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
                $lemmas = self::parseLemmas($line, $concept_id);
                if (!sizeof($lemmas)) {
                    print "<p>ОШИБКА РАЗБОРА ЛЕММ в $count строке</p>\n";
                    return [$categories, $blocks];
                }
                $block_line_num++;
            } elseif ($block_line_num > 4) { // Places
                $place_num = str_pad($block_line_num - 4, 2, "0", STR_PAD_LEFT);
                $place_lemmas[$place_num] = self::parsePlace($line, $place_num);
                if ($place_lemmas[$place_num] === false) {
                    print "<p>ОШИБКА РАЗБОРА НАСЕЛЕННОГО ПУНКТА в $count строке</p>\n";
                    return [$categories, $blocks];
                }
                if ($block_line_num < 34) {
                    $block_line_num ++;
                } else {
                    $blocks[$category_code][$concept_id] = ['meaning' => $concept_text, 'pos_id' => $pos_id, 'lemmas' => $lemmas, 'place_lemmas' => $place_lemmas];
                    $block_line_num = 0;
                }
            }
        }
        return [$categories, $blocks];
    }

    public static function readWords($lines) {
        $count = $block_line_num = 0;
        $blocks = [];
        $category_code = $category_name = '';
        foreach ($lines as $line) {
            $count++;
            $line = trim($line);
//print "<p>$count) $line\n</p>";            
            if (!$line) {
                $block_line_num = 0;
                continue;
            }

            self::checkWrongSymbols($line, $count);
            if ($block_line_num == 0) { // category
                $block_line_num++;
                if (preg_match("/^([ABC]\d{1,3})\s*-\s*(.+)$/", $line, $regs)) {
                    continue;
                }
            }
            if ($block_line_num == 1) { // concept ID
                if (preg_match("/^(\d{4})$/", $line, $regs)) {
                    $concept_id = $regs[1];
                    $block_line_num++;
                    continue;
                }
                print "<p>ОШИБОЧНЫЙ КОД ПОНЯТИЯ в $count строке</p>\n";
                return $blocks;
            } elseif ($block_line_num == 2) { // POS
                $pos_id = self::getPOSID($line);
                if ($pos_id) {
                    $block_line_num++;
                    continue;
                }
                print "<p>ОШИБОЧНАЯ ЧАСТЬ РЕЧИ в $count строке</p>\n";
                return $blocks;
            } elseif ($block_line_num == 3) { // Concept = Meaning
                $block_line_num++;
                continue;
            } elseif ($block_line_num == 4) { // Lemmas
                $lemmas = self::parseLemmas($line, $concept_id);
                if (!sizeof($lemmas)) {
                    print "<p>ОШИБКА РАЗБОРА ЛЕММ в $count строке</p>\n";
                    return $blocks;
                }
                $block_line_num++;
                $blocks = array_merge($blocks,array_values($lemmas));
            } elseif ($block_line_num > 4) { // Places
                $place_num = str_pad($block_line_num - 4, 2, "0", STR_PAD_LEFT);
                $place_lemmas[$place_num] = self::parsePlace($line, $place_num);
                if ($place_lemmas[$place_num] === false) {
                    print "<p>ОШИБКА РАЗБОРА НАСЕЛЕННОГО ПУНКТА в $count строке</p>\n";
                    return $blocks;
                }
                if ($block_line_num < 34) {
                    $block_line_num ++;
                } else {
                    $block_line_num = 0;
                }
            }
        }
        return $blocks;
    }
    
    public static function checkWrongSymbols($line, $count=1) {
        if (preg_match("/[^a-zäöüčšž’0-9а-яё\|\-\?\s\,\;\(\)\}\{\:\.\/i̮i̬ń΄ηu̯ŕĺśźćéá]/iu", $line)) { //sulaimi(~e)
            print "<p>В строке $count недопустимый символ<br>$line</p>";
            return false;
        }
        return true;
    }

    /**
     * @param string $name - abbreviated part of speech, i.e. 'adv' 
     * @return int ID of part of speech
     */
    public static function getPOSID($pos_code) {
        $names_to_codes = Concept::getPOSCodes();
        if (!in_array($pos_code, $names_to_codes)) {
            return false;
        }
        return PartOfSpeech::getIDByCode($pos_code);
    }

    public static function parseLemmas($line, $concept_id=NULL) {
        $out = [];
        $letters = range('a', 'z');
        $synonims = preg_split("/\|\|/", $line);
        for ($i = 0; $i < sizeof($synonims); $i++) {
            $phonetics = preg_split("/\//", $synonims[$i]);
            for ($j = 0; $j < sizeof($phonetics); $j++) {
                if (preg_match("/\(/", $phonetics[$j])) {
                    die("Ошибочная лемма <b>".$phonetics[$j]."</b>");
                }
                if (in_array($phonetics[$j], array_values($out))) {
                    die("ПОВТОРНОЕ СЛОВО В СПИСКЕ: <b>$concept_id</b>. ". $letters[$i]. (string)(1+$j). '. '. $phonetics[$j]);
                }
                $out[$letters[$i] . (string)(1+$j)] = $phonetics[$j];
            }
        }
        foreach ($out as $code => $phonetic) {
            $lemma = Grammatic::phoneticsToLemma($phonetic);
            $phonetic = Grammatic::removeSpaces($phonetic);
            $out[$code] = [$lemma, $phonetic];
            
        }
        return $out;
    }

    public static function parsePlace($line, $place_num) {
        if (!preg_match("/^" . $place_num . "\:(.*)$/", $line, $regs)) {
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
                ConceptCategory::create(['id' => $category_id, 'name_ru' => $category_name]);
            }
        }
    }

    /**
     * Get array of place ids: keys - id in import text, values - id in DB
     * 
     * @return array
     */
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
            10 => 169,
            11 => 197,
            12 => 238,
            13 => 179,
            14 => 239,
            15 => 240,
            16 => 241,
            17 => 242,
            18 => 243,
            19 => 96,
            20 => 244,
            21 => 245,
            22 => 246,
            23 => 247,
            24 => 248,
            25 => 53,
            26 => 78,
            27 => 71,
            28 => 5,
            29 => 26,
            30 => 38];
    }

    /**
     * Get array with information about place
     * @return array [<place_n> => ['id'=><place_id>, ['dialects'=>[<dialect1> => <lang1>, ...]]]
     *      <place_n> - place id in import text
     *      <place_id> - place id in DB
     *      dialects - list of pairs 'dialect - lang'
     */
    public static function placeDialects() {
        $places = [];
        foreach (self::placeIds() as $place_num => $place_id) {
            $places[$place_num] = ['id' => $place_id];
        }

        foreach ($places as $place_n => $place_info) {
            $place_obj = Place::find($place_info['id']);
            if (!$place_obj) {
                dd("Населенный пункт $place_n = " . $place_info['id'] . " отсутствует в БД!");
            }
            $places[$place_n]['dialects'] = $place_obj->getDialectLangs();
            $places[$place_n]['langs'] = array_unique(array_values($places[$place_n]['dialects']));
        }

        return $places;
    }

    public static function processBlocks($blocks) {
        $place_dialects = self::placeDialects();                    
//dd($blocks);        
        foreach ($blocks as $category_id => $concept_blocks) {
//dd($concept_blocks);           
            foreach ($concept_blocks as $concept_id => $concept_block) {
                $concept_id = (int)$concept_id;
//dd($category_id, $concept_id, $concept_block);            
                $concept_obj = Concept::firstOrCreate(
                        ['id'=>$concept_id, 
                         'text_ru' => $concept_block['meaning'], 
                         'pos_id' => $concept_block['pos_id'],
                         'concept_category_id' => $category_id]);
print "<p><b>Понятие ".$concept_obj->id.": ".$concept_obj->text_ru."</b></p>";                
                $lemma_dialects = self::chooseDialectsForLemmas($concept_block['place_lemmas'], $concept_block['lemmas'], $place_dialects);
//dd($lemma_dialects);                
                list($lang_lemmas, $lang_meanings) = self::addLemmas($concept_block['pos_id'], $concept_block['lemmas'], $lemma_dialects, $concept_obj);
                self::addPhoneticVariants($lang_lemmas);
                self::addTranslations($lang_meanings);
            }
        }
    }

    /**
     * 
     * @param array $places [<place1_num>=>[<word1_num>,...], ...]
     * @param array $words [<word1_num>=>[<lemma>, <phonetic>], ...]
     * @param array $place_dialects [<place1_num>=>["id"=><place_id>, "dialects"=>[<dialect1_id>=><lang1_id>, ...], "langs"=>[<lang1_id>, ...]], ...]
     * 
     * @return array ['a'=>[<lemma1>=>[<lang1_id>=>[<phonetic1>=>[<dialect1_id>=>[<place1_id>, ...], ...], ...], ...], ...], ...]
     */
    public static function chooseDialectsForLemmas($places, $words, $place_dialects=NULL) {
        if (!$place_dialects) {
            $place_dialects = self::placeDialects();            
        }
        $out = [];
//dd($place_dialects);        
        foreach ($places as $place_n => $place_words) {
            $place_id = $place_dialects[$place_n]['id'];
            $place_dials = $place_dialects[$place_n]['dialects'];
            foreach ($place_words as $word_num) {
                if (!isset($words[$word_num])) {
                    dd("НЕТ СЛОВА с КОДОМ <b>$word_num</b>");
                }
                $letter = substr($word_num, 0, 1);
                list($lemma,$phonetic) = $words[$word_num];
                foreach ($place_dials as $dialect_id => $lang_id) {
                    if (!isset($out[$letter][$lemma][$lang_id][$phonetic])) {
                        $out[$letter][$lemma][$lang_id][$phonetic] = [];
                    }

                    if (!isset($out[$letter][$lemma][$lang_id][$phonetic][$dialect_id])) {
                        $out[$letter][$lemma][$lang_id][$phonetic][$dialect_id] = [];
                    }

                    $out[$letter][$lemma][$lang_id][$phonetic][$dialect_id][] = $place_id;
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
     * @param int pos_id - ID of part of speech
     * @param array $lemmas [<lemma1_num>=><lemma1_text>, ...]
     * @param array $lemma_places ['a'=>[<lemma1>=>[<lang1_id>=>[<phonetic1>=>[<dialect1_id>=>[<place1_id>, ...], ...], ...], ...], ...], ...]
     * 
     * @return array [0=>[<lang1_id>=>['a'=>[<lemma1_obj>, ...],...],...], 1=>[<lang1_id>=>[<meaning1_obj>, ...], ...]]
     */
    public static function addLemmas($pos_id, $lemmas, $lemma_places, $concept) {
        $lang_lemmas = $lang_meanings = [];
        $meaning_lang_ru = Lang::getIDByCode('ru');
        $meaning_lang_en = Lang::getIDByCode('en');
//dd($lemma_places);        
        foreach ($lemma_places as $letter => $lemma_langs) {
            foreach ($lemma_langs as $lemma => $lang_phonetics) {
                if (preg_match("/\s/", $lemma)) {
                    $lpos_id = PartOfSpeech::getIDByCode('PHRASE');
                } else {
                    $lpos_id = $pos_id;
                }

                foreach ($lang_phonetics as $lang_id => $phonetic_dialects) {            
        //dd($pos_id, $lemma, $lang_id, $phonetic_dialects);   
                    $lemma_coll = Lemma::wherePosId($lpos_id)
                                    ->where('lemma', 'like', $lemma)
                                    ->whereLangId($lang_id)->get();
    //dd($lemma_coll);       
                    list($lemma_obj, $meaning_obj) = self::searchLemmaByMeaningText($lemma_coll, $concept->text_ru, $meaning_lang_ru, $lang_id, $concept->id);
                    if ($lemma_obj && !$meaning_obj) {
                        continue;
                    }
                    if (!isset($lemma_obj) || !$lemma_obj) {
                        $lemma_obj = Lemma::store($lemma, $lpos_id, $lang_id);
                        $meaning_texts = [$meaning_lang_ru => $concept->text_ru, $meaning_lang_en => $concept->text_en];
                        $meaning_obj = Meaning::storeLemmaMeaning($lemma_obj->id, 1, $meaning_texts);
                    } elseif ($concept->text_en && !$meaning_obj->meaningTexts()->where('lang_id',$meaning_lang_en)->first()) {
                        $meaning_text_obj = MeaningText::create(['meaning_id' => $meaning_obj->id, 'lang_id' => $meaning_lang_en, 'meaning_text' => $concept->text_en]);
                    }
                    $lemma_obj->updatePhonetics($phonetic_dialects);
                    
                    $meaning_obj->updateDialects($phonetic_dialects);                    
                    $meaning_obj->addConcept($concept->id);
                    $meaning_obj->addLabel(self::getLabel());                    
                    
                    $lang_lemmas[$lang_id][$letter][] = $lemma_obj;
                    $lang_meanings[$lang_id][] = $meaning_obj;
                    
print "<p><a href=\"/dict/lemma/".$lemma_obj->id."\">".$lemma_obj->lemma."</a> (".Lang::getNameByID($lang_id).")</p>";  
                }
            }
        }
        return [$lang_lemmas, $lang_meanings];
    }

    public static function searchLemmaByMeaningText($lemma_coll, $meaning_text, $meaning_lang, $search_lang, $concept_id) {
        if (!sizeof($lemma_coll)) {
            return [null, null];
        }
        foreach ($lemma_coll as $lemma) {
            $meaning_obj = self::meaningFound($lemma, $meaning_text, $meaning_lang, $concept_id);
            if ($meaning_obj) {
                return [$lemma, $meaning_obj];
            }
        }
        print "<p>Нашлись леммы <b>".$lemma_coll[0]->lemma."</b>, но нет подходящего значения <b>'".$meaning_text."'</b>: "
                . "<a href=/ru/dict/lemma?search_lang=$search_lang&search_lemma=" . $lemma_coll[0]->lemma
                . "&search_pos=" . $lemma_coll[0]->pos_id . ">проверить</a></p>";
//        exit(1);
        return [$lemma_coll[0], NULL];
    }

    public static function meaningFound($lemma, $meaning_text, $meaning_lang, $concept_id) {
        foreach ($lemma->meanings as $meaning) {
            if ($meaning->concepts()->where('concept_id', $concept_id)->first()) {
                return $meaning;
            }
            $meaning_text_obj = $meaning->meaningTexts()->where('lang_id', $meaning_lang)->first();
            if ($meaning_text_obj->meaning_text == $meaning_text) {
                return $meaning;
            }
        }
        return false;
    }

    /**
     * 
     * @param array $lemma_places [<lemma1_num>=>[<lang1_id>=>[<dialect1_id>=>[<place1_id>, ...], ...], ...], ...]
     * @param array $lang_meanings [<lang1_id>=>[<lemma1_num>=><meaning1_obj>, ...], ...]
     */
    public static function addSynonims($place_lemmas, $lang_meanings, $place_dialects) {
        $synonyms_id = 2;
//dd($place_lemmas, $lang_meanings, $place_dialects);      
        foreach ($place_lemmas as $place_num => $lemma_nums) {
            if (sizeof($lemma_nums) < 2) {
                continue;
            }
//print "<p>$place_num: " . join(', ', $lemma_nums) . "; " . join(', ', $place_dialects[$place_num]['langs']) . "</p>";
            foreach ($place_dialects[$place_num]['langs'] as $lang_id) {
                for ($i = 0; $i < sizeof($lemma_nums) - 1; $i++) {
                    for ($j = $i + 1; $j < sizeof($lemma_nums); $j++) {
                        if ($lemma_nums[$i][0] != $lemma_nums[$j][0]) {
                            print "<p>" . $lemma_nums[$i] . "=" . $lemma_nums[$j] . "</p>";
                            $lang_meanings[$lang_id][$lemma_nums[$i]]->meaningRelations()
                                    ->attach($synonyms_id, ['meaning2_id' => $lang_id][$lemma_nums[$j]]);

                            // reverse relation
                            $lang_meanings[$lang_id][$lemma_nums[$j]]->meaningRelations()
                                    ->attach($synonyms_id, ['meaning2_id' => $lang_id][$lemma_nums[$i]]);
                        }
                    }
                }
            }
        }
//        exit(1);
    }

    /**
     * 
     * @param array $lang_lemmas [<lang1_id>=>['a' => [<lemma1_num>=><lemma1_obj>, ...], ...], ...]
     */
    public static function addPhoneticVariants($lang_lemmas) {
        foreach ($lang_lemmas as $groups) {
            foreach ($groups as $lemmas) {
                if (sizeof($lemmas)<2) {
                    continue;
                }
                $lemma_nums = array_keys($lemmas);
                for ($i=0; $i<sizeof($lemma_nums)-1; $i++) {
                    for ($j=$i+1; $j<sizeof($lemma_nums); $j++) {
//print "<p>".$lemma_nums[$i]."-".$lemma_nums[$j]."</p>";             
                        $l1 = $lemmas[$lemma_nums[$i]];
                        $l2 = $lemmas[$lemma_nums[$j]];
                        if ($l1->id !=$l2->id) {
                            if (!$l1->variants()->where('lemma2_id',$l2->id)->first()) {                        
                                $l1->variants()->attach($l2->id);
                            }
                            if (!$l2->variants()->where('lemma2_id',$l1->id)->first()) {
                                $l2->variants()->attach($l1->id);
                            }
                        }
                    }                
                }
            }
//print "<br><br>";            
        }
    }

    /**
     * 
     * @param array $lang_meanings [<lang1_id>=>[<lemma1_num>=><meaning1_obj>, ...], ...]
     */
    public static function addTranslations($lang_meanings) {
        $langs = array_keys($lang_meanings);
        for ($i=0; $i<sizeof($langs)-1; $i++) {
            for ($j=$i+1; $j<sizeof($langs); $j++) {
                foreach($lang_meanings[$langs[$i]] as $lemma1_num => $meaning1_obj) {
                    foreach($lang_meanings[$langs[$j]] as $lemma2_num => $meaning2_obj) {
//print "<p>".$langs[$i].$lemma1_num."-".$langs[$j].$lemma2_num."</p>";             
                        if (!$meaning1_obj->translations()->where('meaning2_id', $meaning2_obj->id)->first()) {
                            $meaning1_obj->translations()->attach($langs[$j],['meaning2_id'=>$meaning2_obj->id]);
                        }
                        if (!$meaning2_obj->translations()->where('meaning2_id', $meaning1_obj->id)->first()) {
                            $meaning2_obj->translations()->attach($langs[$i],['meaning2_id'=>$meaning1_obj->id]);
                        }
                    }
                }
            }
        }
    }
    
    public static function checkConcepts($blocks) {
        $place_dialects = self::placeDialects();                    
//dd($blocks);        
        foreach ($blocks as $category_id => $concept_blocks) {
//dd($concept_blocks);           
            foreach ($concept_blocks as $concept_code => $concept_block) {
                $concept_id = (int)$concept_code;
//dd($category_id, $concept_id, $concept_block);            
                $concept_obj = Concept::find($concept_id); 
                if (!$concept_obj) {
                    die("<b>НЕТ ПОНЯТИЯ $concept_code</b>");
                }
                if ($concept_obj->text_ru !=$concept_block['meaning']) {
                    die("<b>НЕВЕРНОЕ ПОНЯТИЕ $concept_code - ".$concept_block['meaning']."</b>");
                }
                if ($concept_obj->pos_id != $concept_block['pos_id']
                        || $concept_obj->concept_category_id != $category_id) {
                    die("<b>НЕВЕРНАЯ ЧАСТЬ РЕЧИ ".$concept_block['pos_id']." ПОНЯТИЯ $concept_code - ".$concept_block['meaning']."</b>");
                }
                if ($concept_obj->concept_category_id != $category_id) {
                    die("<b>НЕВЕРНАЯ КАТЕГОРИЯ $category_id ПОНЯТИЯ $concept_code - ".$concept_block['meaning']."</b>");
                }
print "<p><b>Понятие ".$concept_obj->id.": ".$concept_obj->text_ru."</b></p>";               
                $lemma_dialects = self::chooseDialectsForLemmas($concept_block['place_lemmas'], $concept_block['lemmas'], $place_dialects);
//                list($lang_lemmas, $lang_meanings) = self::addLemmas($concept_block['pos_id'], $concept_block['lemmas'], $lemma_dialects, $concept_obj);
//dd($lang_lemmas);                
//                self::addPhoneticVariants($lang_lemmas);
//                self::addTranslations($lang_meanings);
//dd($lang_meanings);                
            }
        }
    }

    /**
     * 
     * @param array $letter_dialects ["a1" => 
     *                                  [<lang1_id> => 
     *                                      [<dialect1_id> => [<place1_id>, ...], 
     *                                  ...], 
     *                               ...]
     * @param array $letter_lemmas  ["a1" => <phonetic>, ...]
     */
/*    public static function phoneticsToLemmas($letter_dialects, $letter_phonetics) {
        $lemmas = [];
        foreach ($letter_phonetics as $letter => $phonetic) {
            $lemma = Grammatic::phoneticsToLemma($phonetic);
            if (isset($lemmas[$lemma])) {
                
            } else {
                
            }
        }
    }    */
}
