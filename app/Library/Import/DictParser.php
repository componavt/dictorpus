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
class DictParser
{
    public static function splitLine($line, $dialect_id) {
        $line = preg_replace("~\x{00a0}~siu", " ", $line); // non-break space
        $line = preg_replace("~\x{01c0}~siu", "|", $line); // dental click ǀ

        // split by '. - ' into lemma and meanings parts
        if (!preg_match("/^([^\.]+)\.\s*([^\.]*)\.?\s*\–\s*(.+)\s+\–\s*(.*)$/", $line, $regs)) {
            return false;
        }
        return $regs;
    }
    
    public static function parseEntry($line, $dialect_id) {
        $regs = self::splitLine($line, $dialect_id);
        if (!$regs) {
            return false;
        }
        $num = trim($regs[2]);
        $lemma_part = self::parseLemmaPart(trim($regs[1]), $num, $dialect_id);
        $meaning_part = self::parseMeaningPart(trim($regs[3]), trim($regs[4]));

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
                $lemma_arr[$i] = Grammatic::toRightTemplate(trim($lemma_arr[$i]), $num, $pos_id);
            }
        }
        return $lemma_arr;
    }    
    
    /** Splits text line to meanings, e.g. "1. first meaning 2. second meaning" 
     * will return ["first meaning", "second meaning"]
     * 
     * @param type $meanings text line from the dictionary
     * @return type array of strings, that is array of meanings
     */
    public static function parseMeaningPart($meanings1, $meanings2) {
        $meanings_r = self::parseMeaningLang($meanings1);
        $meanings_f = self::parseMeaningLang($meanings2);
        for ($i=1; $i<= sizeof($meanings_r); $i++) {
            $meaning_part['meanings'][$i] =
                ['ru' => $meanings_r[$i]
                 //,'fi' => isset($meanings_f[$i]) ? $meanings_f[$i] : (sizeof($meanings_f)==1 ? $meanings_f[1] : '')
                ];            
        }
        return $meaning_part;
    }    
    
    public static function parseMeaningLang($meaning_string) {
        $count = 1;
        $meanings[$count] = $meaning_string;
        // only one meaning
        if (!preg_match("/^".$count."\.\s*(.+)$/", $meanings[$count], $regs)) {
            return $meanings;
        }
        $meanings[$count++] = $regs[1];
        while (preg_match("/^(.+)\s*".$count."\.\s*(.+)$/", $meanings[$count-1], $regs)) {
            $meanings[$count-1] = trim($regs[1]);
            $meanings[$count++] = trim($regs[2]);
        }
        return $meanings;        
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
            return false;
        } else {
            $pos_code = $names_to_codes[$name];
            return PartOfSpeech::getIDByCode($pos_code);
            
        }
    }

    public static function getNumberID($num) {
        if ($num=='pl' || $num=='impers' || $num=='def') {
            return 1;            
        } else if ($num=='sing') {
            return 2;
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
        if (!isset($entry['pos_id']) || !$entry['pos_id']) {
            print "<p><b>$count. ERROR pos:</b> $line</p>\n";                
            return;
        } 
        if (!$entry['lemmas'][0] || preg_match("/.+\{/",$entry['lemmas'][0]) || mb_strpos('|',$entry['lemmas'][0])) {
            print "<p><b>$count. ERROR lemma:</b> $line (".$entry['lemmas'][0].")</p>\n";                
            return;
        } 
        if (!isset($entry['meanings']) || !is_array($entry['meanings'])) {
            print "<p><b>$count. ERROR meanings:</b> $line</p>\n";                
            return;
        }
        foreach ($entry['meanings'] as $meaning_n =>$meaning_lang) {
            foreach ($meaning_lang as $lang => $meaning_text) {
                if (!$meaning_text || preg_match("/\d\./", $meaning_text) 
                        || preg_match("/\s+\-\s+/", $meaning_text)) {
                    print "<p><b>$count. ERROR meaning_$lang:</b> $line</p>\n";                
                    return;                
                }
            }
        }
        return true;
    }
    
    
    /**
     * search lemmas and gets founded lemma
     */
    public static function findLemma($stem0, $entry, $lang_id, $label_id/*, $time_checking*/) {       
        $lemma_founded = false;
        $lemmas=Lemma::where('lemma', 'like', $stem0)
                ->where('lang_id',$lang_id)
                ->where('pos_id', $entry['pos_id']);
        if (!$lemmas->count()) {
            return $lemma_founded;
        }        
//$time_finding_lemmas = microtime(true);    
//print "<p><b>Time finding lemmas ".$entry['lemmas'][0]." :".round($time_finding_lemmas-$time_checking, 2).'</p>';
        
        foreach($lemmas->get() as $lemma) {
//$time_start_finding_meaning = microtime(true);            
            $meanings_match = true;
            $i=1;
            while ($meanings_match && $i<=sizeof($entry['meanings'])) {
                $meaning=$lemma->meanings()->where('meaning_n', $i)->first();
                if (!$meaning) {
                    $meanings_match = false;
                } else {
                    $meaning_text = $meaning->meaningTexts()->where('lang_id',Lang::getIDByCode('ru'))->first();
                    if (!$meaning_text || $meaning_text->meaning_text != $entry['meanings'][$meaning->meaning_n]['ru']) {
                        $meanings_match = false;
                    }
                }
//$time_finish_finding_meaning = microtime(true);            
//print "<p><b>Time finding meaning $i ".$entry['lemmas'][0]." :".round($time_finish_finding_meaning-$time_start_finding_meaning, 2).'</p>';
                $i++;
            }
            if ($meanings_match) {
                return $lemma;
            }
        }
    }   
    
    /**
     * Gets entry like
     * "pos_id" => 3
     * "lemmas" => [0 => "a"]
     * "num" => ""
     * "meanings" => [
     *      1 => [
     *          "r" => "а, но"
     *          "f" => "mutta, vaan, ja"
     *      ]
     * ]
     * OR
     * "pos_id" => 11
     * "lemmas" => [0 => "{avauduo, , avaudu, , avaudu, avaudu, avauvuta, avauvutt}"]
     * "num" => "def"
     * "meanings" => [
     *      1 => [
     *          "r" => "открываться, раскрываться; распускаться"
     *          "f" => "avautua"
     *      ]
     *      2 => [
     *          "r" => "освобождаться (ото льда и т.д.)"
     *          "f" => "avautua"
     *      ]
     * ]
     * and saves to DB
     * 
     * @param Array $entry
     */
    public static function saveEntry($entry, $lang_id, $dialect_id, $label_id/*, $time_checking*/) {       
        foreach ($entry['lemmas'] as $lemma_template) {
            $data = ['lemma'=>$lemma_template, 
                     'lang_id'=>$lang_id, 
                     'number'=>$entry['num'],
                     'pos_id'=>$entry['pos_id'], 
                     'dialect_id'=>$dialect_id];            
            list($new_lemma, $wordforms, $stem, $affix, $gramset_wordforms, $stems) 
                 = Grammatic::parseLemmaField($data);
//dd($gramset_wordforms);            
            $lemma_in_db = self::findLemma($new_lemma, $entry, $lang_id, $label_id/*, $time_checking*/); 
//$time_finding = microtime(true);            
//print "<p><b>Time finding ".$entry['lemmas'][0]." :".round($time_finding-$time_checking, 2).'</p>';
            $features = ['number'=>self::getNumberID($entry['num'])];
            
            $is_label = false;
            if (!$lemma_in_db) {
                $lemma_in_db=Lemma::store($new_lemma, $entry['pos_id'], $lang_id);
                $action = 'storing';
            } else {
                $is_label = $lemma_in_db->labels()->where('label_id', $label_id)->count();
//                if (!$is_label) { // временно выключаем обновление при повторном прогоне тверского словаря
                    $lemma_in_db->modify();
 //               }
                $action = 'updating';
            }
  //          if (!$is_label) { // временно выключаем обновление при повторном прогоне тверского словаря
                $lemma_in_db->storeAddition($wordforms, $stem, $affix, $gramset_wordforms, $features, $dialect_id, $stems);
                if ($gramset_wordforms || $wordforms) {
                    $lemma_in_db->updateTextLinks();
                }
                self::storeMeanings($entry['meanings'], $lemma_in_db->id);
                if (!$is_label) {
                    $lemma_in_db->labels()->attach($label_id); 
                }
   //         }
print "<p>Lemma <a href=/dict/lemma/".$lemma_in_db->id.">$new_lemma</a> is $action</p>";   

//$time_storing = microtime(true);            
//print "<p><b>Time storing/updating ".$entry['lemmas'][0]." :".round($time_storing-$time_finding, 2).'</p>';
//dd($lemma_in_db);            
//dd($gramset_wordforms);            
            
        }
    }
    
    public static function storeMeanings($meanings, $lemma_id){
        foreach ($meanings as $meaning_n => $meaning_langs) {
            $meaning_obj = Meaning::firstOrCreate(['lemma_id' => $lemma_id, 'meaning_n' => $meaning_n]);
            foreach ($meaning_langs as $lang=>$meaning_text) {
                $meaning_text_obj = MeaningText::firstOrCreate(['meaning_id' => $meaning_obj->id, 'lang_id' => Lang::getIDByCode($lang)]);
                $meaning_text_obj -> meaning_text = $meaning_text;
                $meaning_text_obj -> save();
            }
        }
    }
}
