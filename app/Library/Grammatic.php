<?php

namespace App\Library;

use App\Library\Grammatic\KarGram;
use App\Library\Grammatic\KarName;
use App\Library\Grammatic\KarVerb;
use App\Library\Grammatic\VepsGram;
use App\Library\Grammatic\VepsName;
use App\Library\Grammatic\VepsVerb;

use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;

/** List of simple and common functions, which do not depends to any language.
 * For example, 
 *      changeLetters() - letters substitution (old and new alphabet in Karelian),
 *      maxStem() - find maximum constant substring of word forms of the lemma,
 **/
class Grammatic
{
    /**
     * 
     * @param Array $data = ['lemma'=>'lemma_string', 'lang_id'=>lang_int, 'pos_id'=>pos_int, 'dialect_id'=>dialect_int];
     * @return type
     */
    public static function parseLemmaField($data) {
//dd($data);        
        $lemma = self::toRightForm($data['lemma']);
        if (isset($data['reflexive'])) {
            $name_num = $data['reflexive'];
        } elseif (isset($data['number'])) {    
            $name_num =  self::nameNumFromNumberField($data['number']);
        } else {
            $name_num =  null;
        }
       
        list($stems, $name_num, $max_stem, $affix) = self::stemsFromTemplate($lemma, $data['lang_id'], $data['pos_id'], $name_num);
//dd('stems:',$stems);        
        $lemma = $max_stem. $affix;
//dd($lemma);        
        $gramset_wordforms = self::wordformsByStems($data['lang_id'], $data['pos_id'], $data['dialect_id'], $name_num, $stems, 
                                                    isset($data['reflexive']) ? $data['reflexive'] : null);
        if ($gramset_wordforms) {
            return [$lemma, '', $max_stem, $affix, $gramset_wordforms, $stems];
        }
        return self::wordformsFromDict($lemma);
    }
/*    
    public static function wordformsByTemplate($template, $lang_id, $pos_id, $dialect_id) {
        list($stems, $name_num) = self::stemsFromTemplate($lemma, $lang_id, $pos_id, $dialect_id, $name_num);
        $gramset_wordforms = self::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems);
        return $gramset_wordforms;
    }
*/
    public static function wordformsFromDict($lemma) {       
        $parsing = preg_match("/^([^\s\(]+)\s*\(([^\,\;]+)\,\s*([^\,\;]+)([\;\,]\s*([^\,\;]+))?\)/", $lemma, $regs);
        if ($parsing) {
            $lemma = $regs[1];
        }
        
        $affix = NULL;
        $lemma = str_replace('||','',$lemma);
        if (preg_match("/^(.+)\|(.*)$/",$lemma,$rregs)){
            $stem = $rregs[1];
            $affix = $rregs[2];
            $lemma = $stem.$affix;
        } else {
            $stem = $lemma;
        }
      
        if (!$parsing) {
//var_dump([$parsing, $lemma, $wordforms, $stem, $affix]);
            return [$lemma, '', $stem, $affix, false, NULL];
        }

        $regs[2] = str_replace('-', $stem, $regs[2]);
        $regs[3] = str_replace('-', $stem, $regs[3]);
        if (isset($regs[5])) {
            $regs[5] = str_replace('-', $stem, $regs[5]);
        }
//dd($regs);
//exit(0);        

        $wordforms = $regs[2].', '.$regs[3];
        if (isset($regs[5])) {
            $wordforms .= '; '.$regs[5];
        }
        
        return [$lemma, $wordforms, $stem, $affix, false, NULL];
    }
    /** Common entry point for all languages. 
     * Lists of ID of gramsets, which have the rules.
     * That is we know how to generate word forms (using stems, endings and rules) for these gramsets IDs.
     * 
     * @param int $lang_id language ID
     * @param int $pos_id part of speech ID
     * @return array
     */
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
     * @return Array [array_of_stems, name_of_number, max_stem, affix]
     */
    public static function stemsFromTemplate($template, $lang_id, $pos_id, $name_num = null) {
        if (!in_array($lang_id, [4, 1])) {// is not Proper Karelian and Vepsian 
            return [NULL, $name_num, $template, NULL];
        }
        if ($pos_id != PartOfSpeech::getVerbID() && !in_array($pos_id, PartOfSpeech::getNameIDs())) {
            return [NULL, $name_num, $template, NULL];
        }

        if (!preg_match("/\{\{/", $template)) {
            $template = preg_replace('/\|\|/','',$template);
        }
     
        if ($lang_id == 1) {
            return VepsGram::stemsFromTemplate($template, $pos_id, $name_num);                
        } 
        
        return KarGram::stemsFromTemplate($template, $pos_id, $name_num);                
    }

    public static function wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num=null, $stems, $reflexive=null) {
//dd($stems);                
        if (!isset($stems[0]) || sizeof($stems)<6) {
            return false;
        }
        
        $gramsets = self::getListForAutoComplete($lang_id, $pos_id);
        $wordforms = [];
//if ($template == "{{vep-conj-stems|voik|ta|ab|i}}") dd($stems);                
        foreach ($gramsets as $gramset_id) {
            if ($pos_id == PartOfSpeech::getVerbID()) {
                $wordforms[$gramset_id] = self::verbWordformByStems($stems, $gramset_id, $lang_id, $dialect_id, $name_num, $reflexive);
            } else {
                $wordforms[$gramset_id] = self::nameWordformByStems($stems, $gramset_id, $lang_id, $dialect_id, $name_num);
            }
        }
        return $wordforms;
    }
    
    public static function nameWordformByStems($stems, $gramset_id, $lang_id, $dialect_id, $name_num=null) {
        if ($lang_id == 1) {
            return VepsName::wordformByStems($stems, $gramset_id, $dialect_id, $name_num);
        }
        return KarName::wordformByStems($stems, $gramset_id, $lang_id, $dialect_id, $name_num);
    }
    
    public static function verbWordformByStems($stems, $gramset_id, $lang_id, $dialect_id, $def=null, $reflexive=null) {
        if ($lang_id == 1) {
            if ($reflexive) {
                return VepsVerbReflex::wordformByStems($stems, $gramset_id, $dialect_id);
            } else {
                return VepsVerb::wordformByStems($stems, $gramset_id, $dialect_id);
            }
        }
        return KarVerb::wordformByStems($stems, $gramset_id, $lang_id, $dialect_id, $def);
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
        $number = (string)$number;
        if ($number==1) {
            return 'pl';
        } elseif ($number==2) {
//            return 'sing';            
            return 'sg'; // изменено 5.09.2019, проверить при импорте тверского словаря           
        } elseif (in_array($number, ['sing','sg','pl','def','impers'])) {
            return $number;            
        }
        return null;
    }
    
    public static function getStemFromWordform($lemma, $stem_n, $lang_id, $pos_id, $dialect_id, $is_reflexive) {
        if ($lang_id == 1) {
            return VepsGram::getStemFromWordform($lemma, $stem_n, $pos_id, $dialect_id, $is_reflexive);
        }
        return KarGram::getStemFromWordform($lemma, $stem_n, $pos_id, $dialect_id);
    }
    
    public static function getStemFromStems($stems, $stem_n, $lang_id, $pos_id, $dialect_id) {
        if ($lang_id == 1) {
            return VepsGram::getStemFromStems($stems, $stem_n, $pos_id, $dialect_id);
        }
        return null;
    }
    
    public static function interLists($neg, $list){
        if (!$list) { return ''; }
        
        if (!preg_match("/,/", $list)) {
            return $neg.$list;
        }
        
        $forms=[];
        foreach (preg_split("/,\s*/", $list) as $verb) {
            $forms[] = $neg.$verb;
        }
        return join(", ", $forms);
    }
    
}
