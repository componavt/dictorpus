<?php

namespace App\Library;

use App\Library\Grammatic\KarGram;
use App\Library\Grammatic\VepsGram;

use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;

class Grammatic
{
    public static function getListForAutoComplete($lang_id, $pos_id) {
        $gramsets = [];
        if (!in_array($lang_id, [4, 1])) {// is not Proper Karelian and Vepsian 
            return $gramsets;
        }
        
        if ($lang_id == 1) {
            $gramsets = VepsGram::getListForAutoComplete($pos_id);
        } else {
            $gramsets = KarGram::getListForAutoComplete($pos_id);
        }
        return $gramsets;
    }

    /**
     * @param String $template
     * @param Int $lang_id
     * @param Int $pos_id
     * @return Array
     */
    public static function stemsFromTemplate($template, $lang_id, $pos_id, $name_num = null) {
        if ($lang_id == 1) {
            return VepsGram::stemsFromTemplate($template, $pos_id, $name_num);                
        } else {
            $stems = preg_split('/,/',$template);
            for ($i=0; $i<sizeof($stems); $i++) {
                $stems[$i] = trim($stems[$i]);
            }
        }        
        return [$stems, $name_num, null, null];
    }

    public static function wordformsByTemplate($template, $lang_id, $pos_id, $dialect_id, $name_num=null) {
        if (!in_array($lang_id, [4, 1])) {// is not Proper Karelian and Vepsian 
            return [$template, false, $template, NULL];
        }
        if ($pos_id != PartOfSpeech::getVerbID() && !in_array($pos_id, PartOfSpeech::getNameIDs())) {
            return [$template, false, $template, NULL];
        }
        
        if (!preg_match('/\{+([^\}]+)\}+/', $template, $list) &&
                !($lang_id==1 && preg_match("/".VepsGram::dictTemplate()."/", $template, $list))) {
            return [$template, false, $template, NULL];
        }
        
        list($stems, $name_num, $max_stem, $affix) = self::stemsFromTemplate($list[1], $lang_id, $pos_id, $name_num);
//dd($stems);                
        if (!isset($stems[0])) {
            return [$template, false, $template, NULL];
        }
        
        $gramsets = self::getListForAutoComplete($lang_id, $pos_id);
        $wordforms = [];
//if ($template == "{{vep-conj-stems|voik|ta|ab|i}}") dd($stems);                
        if ($pos_id == PartOfSpeech::getVerbID()) {
            if (sizeof ($stems) != 8) {
                return [$stems[0], false, $template, NULL];
            }
            foreach ($gramsets as $gramset_id) {
                $wordforms[$gramset_id] = self::verbWordformByStems($stems, $gramset_id, $lang_id, $dialect_id);
            }
        } else {
//            if ($lang_id == 1 && sizeof ($stems) != 4 || sizeof ($stems) != 6) {
            if (sizeof ($stems) != 6) {
                return [$stems[0], false, $template, NULL];
            }
            foreach ($gramsets as $gramset_id) {
                $wordforms[$gramset_id] = self::nameWordformByStems($stems, $gramset_id, $lang_id, $dialect_id, $name_num);
            }
        }
        if (!$max_stem) {
            list($max_stem, $affix) = self::maxStem($stems);
        }
        return [$max_stem.$affix, $wordforms, $max_stem, $affix];
    }
    
    public static function nameWordformByStems($stems, $gramset_id, $lang_id, $dialect_id, $name_num) {
        if ($lang_id == 1) {
            return VepsName::wordformByStems($stems, $gramset_id, $dialect_id, $name_num);
        }
        return KarName::wordformByStems($stems, $gramset_id, $lang_id, $dialect_id);
    }
    
    public static function verbWordformByStems($stems, $gramset_id, $lang_id, $dialect_id) {
        if ($lang_id == 1) {
            return VepsVerb::wordformByStems($stems, $gramset_id, $dialect_id);
        }
        return KarVerb::wordformByStems($stems, $gramset_id, $lang_id, $dialect_id);
    }
    
    public static function isLetterChangeable($lang_id) {
        if (in_array($lang_id,[5, 4, 6])) { // karelian languages
            return true;
        }
        return false;
    }

    /**
     * Changes obsolete letters to modern
     * If a parameter lang_id is given, then does the check need such a replacement
     * 
     * Used only for writing word index
     * NB! Remove ’ in dialect texts
     * 
     * @param String $word
     * @param Int $lang_id
     * @return String
     */
    public static function changeLetters($word,$lang_id=null) {
        $word = self::toSearchForm($word);
        $word = str_replace("'",'',$word);
        $word = str_replace("`",'',$word);
        
        if (!$lang_id || $lang_id && !self::isLetterChangeable($lang_id)) {
            return $word;
        }

        $word = KarGram::changeLetters($word);
        return $word;
    }

    public static function toSearchForm($word) {
        $word = str_replace('’','',$word);
        $word = mb_strtolower($word);
        return $word;
    }

    public static function toRightForm($word) {
        $word = trim($word);
        $word = preg_replace("/['´`]+/", "’", $word);
        $word = preg_replace("/\s{2,}/", " ", $word);
        return $word;
    }
    public static function negativeForm($gramset_id, $lang_id) {
        $neg_lemma = Lemma::where('lang_id', $lang_id)->whereLemma('ei')
                          ->where('pos_id',PartOfSpeech::getIDByCode('AUX'))->first();
        if (!$neg_lemma) {
            return '';
        }
        $gramset = Gramset::find($gramset_id);
        if (!$gramset) {
            return '';
        }
        $neg_mood = $gramset->gram_id_mood;
        if (in_array($neg_mood, [48, 28])) { // potencial, conditional
            $neg_mood = 27; // indicative
        }
        $neg_gramset = Gramset::where('gram_id_mood', $neg_mood)
                              ->where('gram_id_person', $gramset->gram_id_person)
                              ->where('gram_id_number', $gramset->gram_id_number)
                              ->whereNull('gram_id_tense')->whereNull('gram_id_negation')->first();
        if (!$neg_gramset) {
            return '';
        }
        $neg_wordform = $neg_lemma->wordforms()
                ->wherePivot('gramset_id', $neg_gramset->id)->first();
//dd($neg_gramset->id);        
        if (!$neg_wordform) {
            return '';
        }
        return $neg_wordform->wordform. ' ';
    }
    
/*    
    public static function processForWordform($word) {
        $word = trim($word);
        $word = preg_replace("/\s{2,}/", " ", $word);
        $word = preg_replace("/['`]/", "’", $word);
        return $word;
    }
*/    
    public static function maxStem($stems) {
        $affix = '';
        $stem = $stems[0];

        for ($i=1; $i<sizeof($stems); $i++) {
            if (!$stems[$i]) {
                continue;
            }
            while (!preg_match("/^".$stem."/", $stems[$i])) {
                $affix = mb_substr($stem, -1, 1). $affix;
                $stem = mb_substr($stem, 0, mb_strlen($stem)-1);
            }
        }
        return [$stem, $affix];
        
    }
    
    public static function nameNumFromNumberField($number) {
        if ($number==1) {
            return 'pl';
        } elseif ($number==2) {
            return 'sg';            
        }
        return null;
    }
}
