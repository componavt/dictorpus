<?php

namespace App\Library\Import;

use App\Library\Grammatic;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
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
        if (!preg_match("/^([^\.]+)\.\s*([^\.]*)\.?\s*\–\s*(.+)\s+\–\s+(.+)$/", $line, $regs)) {
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
//print "<p>Unknown pos</p>";        
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
        if (!(sizeof($base_list)==3 || sizeof($base_list)==2 && $num=='sing' || sizeof($base_list)==1 && $num=='pl')) {
            return $lemma_str;
        }
        if (preg_match("/^([^\/\s]+)\s*[\/\:]\s*([^\s]+)$/", $base_list[0], $regs)) {
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
            if ($num=='sing') {
                $bases[4] = $bases[5] = '';
            } elseif (preg_match("/^([^\/\s]+)\s*[\/\:]\s*([^\s]+)$/", $base_list[2], $regs)) {
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

        if (preg_match("/^([^\/\s]+)\s*[\/\:]\s*([^\s]+)$/", $base_list[0], $regs)) {
            $bases[1] = $regs[1];
            $bases[2] = $regs[2];
        } else {
            if ($num=='impers' || $num=='def') {
                $bases[1] = '';
            } else {
                $bases[1] = $base_list[0];
                
            }
            $bases[2] = $base_list[0];
        }
        
        if (preg_match("/^([^\/\s]+)\s*[\/\:]\s*([^\s]+)$/", $base_list[1], $regs)) {
            $bases[3] = $regs[1];
            $bases[4] = $regs[2];
        } else {
            if ($num=='impers' || $num=='def') {
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
    public static function parseMeaningPart($meanings1, $meanings2) {
        $meanings_r = self::parseMeaningLang($meanings1);
        $meanings_f = self::parseMeaningLang($meanings2);
        for ($i=1; $i<= sizeof($meanings_r); $i++) {
            $meaning_part['meanings'][$i] =
                ['ru' => $meanings_r[$i],
                 'fi' => isset($meanings_f[$i]) ? $meanings_f[$i] : (sizeof($meanings_f)==1 ? $meanings_f[1] : '')];            
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
        if ($num=='pl') {
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
                if (!$meaning_text || preg_match("/[0..9]\./", $meaning_text) 
                        || preg_match("/\s+\-\s+/", $meaning_text)) {
                    print "<p><b>$count. ERROR meaning_$lang:</b> $line</p>\n";                
                    return;                
                }
            }
        }
        return true;
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
    public static function saveEntry($entry, $lang_id, $dialect_id, $label_id) {
        foreach ($entry['lemmas'] as $lemma_template) {
            $data = ['lemma'=>$lemma_template, 
                     'lang_id'=>$lang_id, 
                     'pos_id'=>$entry['pos_id'], 
                     'dialect_id'=>$dialect_id];
            list($new_lemma, $wordforms, $stem, $affix, $gramset_wordforms) 
                 = Lemma::parseLemmaField($data);
            $lemma_in_db = self::findLemma($new_lemma, $entry, $lang_id, $label_id); 
            
            if (!$lemma_in_db) {
                self::storeLemma($new_lemma, $wordforms, $stem, $affix, $gramset_wordforms, $entry, $lang_id, $dialect_id, $label_id);
            } else {
                self::updateLemma($lemma_in_db, $wordforms, $stem, $affix, $gramset_wordforms, $entry, $dialect_id, $label_id);
                
            }
//dd($lemma_in_db);            
//dd($gramset_wordforms);            
            
        }
    }
    
    /**
     * search lemmas and gets founded lemma
     */
    public static function findLemma($stem0, $entry, $lang_id, $label_id) {
        $lemma_founded = false;
        $lemmas=Lemma::where('lemma', 'like', $stem0)
                ->where('lang_id',$lang_id)
                ->where('pos_id', $entry['pos_id']);
        if (!$lemmas->count()) {
            return $lemma_founded;
        }
        
        foreach($lemmas->get() as $lemma) {
            $meanings_match = true;
            for ($i=1; $i<=sizeof($entry['meanings']); $i++) {
                $meaning=$lemma->meanings()->where('meaning_n', $i)->first();
                if (!$meaning) {
                    $meanings_match = false;
                    break;
                }
                $meaning_text = $meaning->meaningTexts()->where('lang_id',Lang::getIDByCode('ru'))->first();
                if (!$meaning_text || $meaning_text->meaning_text != $entry['meanings'][$meaning->meaning_n]['ru']) {
                    $meanings_match = false;
                    break;
                }
            }  
            if ($meanings_match) {
                return $lemma;
            }
        }
    }   
    
    public static function storeLemma($new_lemma, $wordforms, $stem, $affix, $gramset_wordforms, 
                                      $entry, $lang_id, $dialect_id, $label_id) {
        $lemma = Lemma::create(['lemma'=>$new_lemma,'lang_id'=>$lang_id,'pos_id'=>$entry['pos_id']]);
        $lemma->lemma_for_search = Grammatic::toSearchForm($lemma->lemma);
        $lemma->save();
        
        $lemma->labels()->attach($label_id);

        LemmaFeature::store($lemma->id, ['number'=>self::getNumberID($entry['num'])]);
        $lemma->storeReverseLemma($stem, $affix);
        
        $lemma->storeWordformsFromTemplate($gramset_wordforms, $dialect_id); 
        $lemma->createDictionaryWordforms($wordforms, $entry['num'], $dialect_id);
            
        self::storeMeanings($entry['meanings'], $lemma->id);
        
        $lemma->updateTextLinks();
    }
    
    public static function updateLemma($lemma, $wordforms, $stem, $affix, $gramset_wordforms, $entry, $dialect_id, $label_id) {
        $lemma->lemma_for_search = Grammatic::toSearchForm($lemma->lemma);
        $lemma->updated_at = date('Y-m-d H:i:s');
        $lemma->save();

        if (!$lemma->labels()->where('label_id', $label_id)) {
            $lemma->labels()->attach($label_id);
        }
        
        LemmaFeature::store($lemma->id, ['number'=>self::getNumberID($entry['num'])]);
        $lemma->storeReverseLemma($stem, $affix);
        
        $lemma->storeWordformsFromTemplate($gramset_wordforms, $dialect_id); 
        $lemma->createDictionaryWordforms($wordforms, $entry['num'], $dialect_id);
                 
        self::storeMeanings($entry['meanings'], $lemma->id);
        $lemma->updateTextLinks();
    }
    
    public static function storeMeanings($meanings, $lemma_id){
        foreach ($meanings as $meaning_n => $meaning_langs) {
            $meaning_obj = Meaning::firstOrCreate(['lemma_id' => $lemma_id, 'meaning_n' => $meaning_n]);
            foreach ($meaning_langs as $lang=>$meaning_text) {
                $meaning_text_obj = MeaningText::firstOrCreate(['meaning_id' => $meaning_obj->id, 'lang_id' => Lang::getIDByCode('ru')]);
                $meaning_text_obj -> meaning_text = $meaning_text;
                $meaning_text_obj -> save();
            }
        }
    }
}
