<?php

namespace App\Library;

use App\Library\Grammatic\KarGram;
use App\Library\Grammatic\KarName;
use App\Library\Grammatic\KarVerb;
use App\Library\Grammatic\KarVerbOlo;
use App\Library\Grammatic\VepsGram;
use App\Library\Grammatic\VepsName;
use App\Library\Grammatic\VepsVerb;
use App\Library\Grammatic\VepsVerbReflex;

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
    public static function langsWithRules() {
        return [1,4,5,6];
    }

    /**
     * ku|uzi#kymmen (-vven#en, -uttu#dy; -uzii#ii)
     * @param Array $data = ['lemma'=>'lemma_string', 'lang_id'=>lang_int, 'pos_id'=>pos_int, 'wordform_dialect_id'=>dialect_int];
     * @return array
     */
    public static function parseLemmaField($data) {
        $lemma = self::toRightForm($data['lemma']);
        if (isset($data['number']) && $data['number']=='refl') {
            $data['reflexive'] = 1;
        }
        if (isset($data['reflexive'])) {
            $is_reflexive = $data['reflexive']; 
        } else {
            $is_reflexive = null;
        }
        if (isset($data['impersonal']) && $data['impersonal']) {   
            $name_num = 'def';
        } elseif (isset($data['number'])) {    
            $name_num =  self::nameNumFromNumberField($data['number']);
        } else {
            $name_num =  null;
        }
       
        list($stems, $name_num, $max_stem, $affix) = self::stemsFromTemplate($lemma, $data['lang_id'], $data['pos_id'], $name_num, $data['wordform_dialect_id'], $is_reflexive);
        $lemma = preg_replace("/\|\|/", '',$max_stem). $affix;
        
        $gramset_wordforms = self::wordformsByStems($data['lang_id'], $data['pos_id'], $data['wordform_dialect_id'], $name_num, $stems, $is_reflexive);
        if ($gramset_wordforms) {
            return [$lemma, '', $max_stem, $affix, $gramset_wordforms, $stems];
        }
        return self::wordformsFromDict($lemma, $max_stem, $affix);
    }

    public static function getAffixFromtemplate($template, $name_num) {       
        if (!preg_match("/\s/", $template) && preg_match("/^([^\{\(]*)\|([^\|]*)$/", $template, $regs)) {
            $base = $regs[1];
            $base_suff = $regs[2];
            $stems[0] = preg_replace("/ǁ/",'',$base).$base_suff;
        } else {
            $base = $template;
            $stems = null;
            $base_suff = '';
        }
        return [$stems, $name_num, $base, $base_suff];
    }
    
    public static function wordformsFromDict($lemma, $stem, $affix) {       
        $parsing = preg_match("/^([^\s\(]+)\s*\(([^\,\;]+)\,\s*([^\,\;]+)([\;\,]\s*([^\,\;]+))?\)/", $lemma, $regs);
        if ($parsing) {
            $lemma = $regs[1];
        }
        
        $lemma = str_replace('||','',$lemma);
        if (preg_match("/^(.+)\|(.*)$/",$lemma,$rregs)){
            $stem = $rregs[1];
            $affix = $rregs[2];
            $lemma = $stem.$affix;
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
        if (!in_array($lang_id, self::langsWithRules())) {// is not language with rules
            return $gramsets;
        }
        
        if ($lang_id == 1) {
            $gramsets = VepsGram::getListForAutoComplete($pos_id);
        } else {
            $gramsets = KarGram::getListForAutoComplete($pos_id, $lang_id);
        }
        return $gramsets;
    }

    /**
     * @param String $template
     * @param Int $lang_id
     * @param Int $pos_id
     * @return Array [array_of_stems, name_of_number, max_stem, affix]
     */
    public static function stemsFromTemplate($template, $lang_id, $pos_id, $name_num = null, $dialect_id=null, $is_reflexive=null) {       
        $template = trim($template);
        if (!in_array($lang_id, self::langsWithRules())// is not langs with rules 
                || $pos_id != PartOfSpeech::getVerbID() && !in_array($pos_id, PartOfSpeech::getNameIDs())) {
            return Grammatic::getAffixFromtemplate($template, $name_num);
        }

        if (!preg_match("/\{\{/", $template)) {
            $template = preg_replace('/\|\|/','ǁ',$template);
        }
     
        if ($lang_id == 1) {
            list($stems, $name_num, $max_stem, $affix) = VepsGram::stemsFromTemplate($template, $pos_id, $name_num, $is_reflexive);  
        } else {
            if (!$dialect_id) {
                $dialect_id = Lang::mainDialectByID($lang_id);
            }
            list($stems, $name_num, $max_stem, $affix) = KarGram::stemsFromTemplate($template, $pos_id, $name_num, $dialect_id, $is_reflexive);       
        }
        $max_stem = preg_replace('/ǁ/','||',$max_stem);
        
        if ($lang_id != 1 && is_array($stems) && sizeof($stems)>1) {
            $stems[10] = KarGram::isBackVowels($max_stem.$affix);
        }
        return [$stems, $name_num, $max_stem, $affix];
    }

    public static function wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num=null, $stems, $is_reflexive=null) {
//dd($stems);                
        if (!is_array($stems) || !isset($stems[0])/* || sizeof($stems)<6*/) {
            return false;
        }
        
        if (!$dialect_id) {
            $dialect_id = Lang::mainDialectByID($lang_id);
        }
        
        $gramsets = self::getListForAutoComplete($lang_id, $pos_id);
//dd($gramsets);        
        $wordforms = [];
//if ($template == "{{vep-conj-stems|voik|ta|ab|i}}") dd($stems);                
        foreach ($gramsets as $gramset_id) {
            $wordforms[$gramset_id] = self::wordformByStems($lang_id, $pos_id, $dialect_id, $gramset_id, $stems, $name_num, $is_reflexive);
        }
// dd($wordforms);        
        return $wordforms;
    }
    
    public static function wordformByStems($lang_id, $pos_id, $dialect_id, $gramset_id, $stems, $name_num = null, $is_reflexive = null) {
        if ($pos_id == PartOfSpeech::getVerbID()) {
            return self::verbWordformByStems($stems, $gramset_id, $lang_id, $dialect_id, $name_num, $is_reflexive);
        } else {
            return self::nameWordformByStems($stems, $gramset_id, $lang_id, $dialect_id, $name_num);
        }
    }
    
    public static function nameWordformByStems($stems, $gramset_id, $lang_id, $dialect_id, $name_num=null) {
        if ($lang_id == 1) {
            return VepsName::wordformByStems($stems, $gramset_id, $dialect_id, $name_num);
        }
        return KarName::wordformByStems($stems, $gramset_id, $lang_id, $dialect_id, $name_num);
    }
    
    public static function verbWordformByStems($stems, $gramset_id, $lang_id, $dialect_id, $def=null, $is_reflexive=null) {
        if ($lang_id == 1) {
            if ($is_reflexive) {
                return VepsVerbReflex::wordformByStems($stems, $gramset_id, $dialect_id);
            } else {
                return VepsVerb::wordformByStems($stems, $gramset_id, $dialect_id);
            }
        }
        if ($lang_id == 5 && $is_reflexive) {
            return self::removeSoftening(KarVerbOlo::wordformByStemsRef($stems, $gramset_id, $dialect_id, $def));
        }
        return self::removeSoftening(KarVerb::wordformByStems($stems, $gramset_id, $lang_id, $dialect_id, $def));
    }
    
    public static function removeSoftening($word) {
        if (preg_match("/^(.*[^’]l)’([ei].*)$/ui", $word, $regs)) {
            return $regs[1].$regs[2];
        }
        return $word;
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

    public static function hasPhonetics($word) {
        return preg_match("/[i̮̮iń̬ńu̯ŕĺśźηéá|ć/iu", $word);
    }
    
    /**
     * @param string $word
     * @return string
     */
    public static function removeSpaces($word) {
        $word = trim($word);
        $word = preg_replace("/\s{2,}/", " ", $word);
        return $word;
    }
    
    /**
     * @param string $word
     * @return string
     */
    public static function toRightForm($word) {
        $word = trim($word);
        $word = self::removeSpaces($word);
        $word = preg_replace("/['´`΄]+/u", "’", $word);
        return $word;
    }
    
    /**
     * перед 'a' 'o' 'u' и на конце слова важно сохранить смягчение, в остальных заменяем на твердую согласную
     * 
     * @param string $word
     * @param boolean $change_phonetics
     * @return string
     */
    public static function phoneticsToLemma($word) {
//$init = $word;
        $word = self::removeSpaces($word);
        
        $cons = ['ń'=>'n', '̬ń'=>'n', 'ŕ'=>'r', 'ĺ'=>'l', 'ś'=>'s', 'ź'=>'z', 'ć'=>'c']; 
        foreach (['i̬'=>'i', 'i̮'=>'i', '̮i'=>'i', 'i̯'=>'i', 'u̯'=>'u', 'é'=>'e', 'pá'=>'p’a', 'η'=>'n'] as $old =>$new) {
            $word = str_replace($old, $new, $word);
        }
        foreach ($cons as $old =>$new) {
/*                if (preg_match('/^(.+)ńć$/u', $word, $regs)) {
                $word = $regs[1].'n’c’';
            }*/
            $word = preg_replace('/'.$old.'([aou\s])/u', $new.'’$1', $word);
            if (preg_match('/^(.*)'.$old.'$/u', $word, $regs)) {
                $word = $regs[1].$new.'’';
            }
        }                
        foreach ($cons as $old =>$new) {
            $word = preg_replace('/'.$old."(d['´`΄’]ž)([aou\s])/u", $new.'’$1$2', $word);
            $word = preg_replace('/'.$old."(dž)([aou\s])/u", $new.'$1$2', $word);
            $word = preg_replace('/'.$old."(d['´`΄’]ž)$/u", $new.'’$1', $word);
            $word = preg_replace('/'.$old."(dž)$/u", $new.'$1', $word);
            $list='lnbmdghtkžptszvrc';
            $word = preg_replace('/'.$old.'(['.$list."]['´`΄’]?)([aou\s])/u", $new.'’$1$2', $word);
//                $word = preg_replace('/'.$old.'(['.$list."]['´`΄’]?[".$list."]?['´`΄’]?)([aou\s])/u", $new.'’$1$2', $word);
            if (preg_match('/^(.*)'.$old.'(['.$list."]['´`΄’]?)$/u", $word, $regs)) {
//                if (preg_match('/^(.*)'.$old.'(['.$list."]['´`΄’]?[".$list."]?['´`΄’]?)$/u", $word, $regs)) {
                $word = $regs[1].$new.'’'.$regs[2];
            }
        }
        foreach ($cons as $old =>$new) {
            $word = str_replace($old, $new, $word);
        }

        $word = preg_replace("/['´`΄]+/", "’", $word);
//if ($init == 'lat΄ta') {print "\n$word\n";}            
        foreach (['t','l','s'] as $l) {
            $word = preg_replace("/".$l."’".$l."([aou\s])/u", $l."’".$l.'’$1', $word);
            $word = preg_replace("/".$l.$l."’([aou\s])/u", $l."’".$l.'’$1', $word);
            $word = preg_replace("/".$l."’".$l."$/u", $l."’".$l.'’', $word);
            $word = preg_replace("/".$l.$l."’$/u", $l."’".$l.'’', $word);
        }

        foreach (['i', 'e', 'ä', 'ü', 'ö'] as $let) {
            $word = preg_replace('/’(['.$list. ']?)’?(['.$list. ']?)’?'. $let.'/u', '$1$2'.$let, $word);
        }
        $word = str_replace('d’ž', 'dž', $word);
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
        return $neg_wordform->wordform;
    }
    
/*    
    public static function processForWordform($word) {
        $word = trim($word);
        $word = preg_replace("/\s{2,}/", " ", $word);
        $word = preg_replace("/['`]/", "’", $word);
        return $word;
    }
*/    
    public static function maxStem($stems/*, $lang_id=NULL, $pos_id=NULL*/) {
//dd($lang_id, $pos_id);
        $affix = '';
        $stem = $stems[0];
//print "<P>$stem</P>";            

        for ($i=1; $i<sizeof($stems); $i++) {
            if (!$stems[$i]) {
                continue;
            }
            while (!preg_match("/^".$stem."/", $stems[$i])) {
                $affix = mb_substr($stem, -1, 1). $affix;
                $stem = mb_substr($stem, 0, mb_strlen($stem)-1);
            }
//print "<P>".$stems[$i].": $stem</P>";            
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
        } elseif ($number=='refl') {
            return 1;
        } elseif (in_array($number, ['sing','sg','pl','def','impers'])) {
            return $number;            
        }
        return null;
    }
    
    public static function getStemFromWordform($lemma, $stem_n, $lang_id, $pos_id, $dialect_id, $is_reflexive=false) {
        if ($lang_id == 1) {
            return VepsGram::getStemFromWordform($lemma, $stem_n, $pos_id, $dialect_id, $is_reflexive);
        }
        return KarGram::getStemFromWordform($lemma, $stem_n, $pos_id, $dialect_id);
    }
    
    /**
     * 
     * @param Array $stems
     * @param Int $stem_n
     * @param Int $lang_id
     * @param Int $pos_id
     * @param Int $dialect_id
     * @param STRING $lemma
     * @return String
     */
    public static function getStemFromStems($stems, $stem_n, $lang_id, $pos_id, $dialect_id, $lemma) {
        if ($lang_id == 1) {
            return VepsGram::getStemFromStems($stems, $stem_n, $pos_id, $dialect_id, $lemma);
        } else {
            return KarGram::getStemFromStems($stems, $stem_n, $pos_id, $lang_id, $dialect_id, $lemma);
        }
        return null;
    }
    
    public static function interLists($neg_list, $list){
        if (!$list) { return ''; }
        
        if (!preg_match("/,/", $neg_list) && !preg_match("/[,\/]/", $list)) {
            if ($neg_list) {
                return trim($neg_list). ' '. $list;
            } else {
                return $list;
            }
        }
        
        $forms=[];
        foreach (preg_split("/,\s*/", $neg_list) as $neg) {
            foreach (preg_split("/[,\/]\s*/", $list) as $verb) {
                if ($neg) {
                    $forms[] = trim($neg).' '.trim($verb);
                } else {
                    $forms[] = trim($verb);
                }
            }
        }
        return join(", ", $forms);
    }
    
    /**
     * Присоединение морфем к основам, возможно к спискам основ 
     * @param string $list
     * @param string $alom
     */
    public static function joinMorfToBases($bases, $morf){
        if (!$bases) { return ''; }
        $forms=[];
        foreach (preg_split("/[,\/]\s*/", $bases) as $base) {
            $forms[] = $base.$morf;
        }
        return join(", ", $forms);              
    }
    
    public static function getAffixesForGramset($gramset_id, $lang_id) {
        if ($lang_id == 1) {
            return VepsGram::getAffixesForGramset($gramset_id);
        } elseif ($lang_id == 4) {
            return KarGram::getAffixesForGramset($gramset_id, $lang_id);
        }
        return [];
    }
    
    public static function templateFromWordforms($wordforms, $lang_id, $pos_id, $number) {
        if ($lang_id == 1) { // vepsian
            return VepsGram::templateFromWordforms($wordforms);
        } else { 
            return KarGram::templateFromWordforms($wordforms, $lang_id, $pos_id, $number);
        }        
    }
}
