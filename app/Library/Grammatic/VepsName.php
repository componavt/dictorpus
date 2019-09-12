<?php

namespace App\Library\Grammatic;

use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\LemmaFeature;
use App\Models\Dict\PartOfSpeech;

class VepsName
{
    /**
     * 0 = nominativ sg
     * 1 = base of genetive sg (genetive sg - 'n')
     * 2 = partitive sg
     * 3 = base of illative sg (from stem1)
     * 4 = base of partitive pl (partitive pl - 'd')
     * 5 = null

     * @param Lemma $lemma
     * @param Int $dialect_id
     * @return array
     */
    public static function stemsFromDB($lemma, $dialect_id) {
        for ($i=0; $i<6; $i++) {
            $stems[$i] = self::getStemFromWordform($lemma, $i, $dialect_id);
        }
        return $stems;
    }
    
    public static function getStemFromWordform($lemma, $stem_n, $dialect_id) {
        switch ($stem_n) {
            case 0: 
                return $lemma->lemma;
            case 1:  //genetive sg
                if (preg_match("/^(.+)n$/", $lemma->wordform(3, $dialect_id), $regs)) {
                    return $regs[1];
                }
                return NULL;
            case 2: // partitive sg
                $part_sg = $lemma->wordform(4, $dialect_id); 
                return $part_sg ? $part_sg : NULL;
            case 3: // illative sg
                return self::illSgBase(self::getStemFromWordform($lemma, 1, $dialect_id)); 
            case 4: // partitive pl
                if (preg_match("/^(.+)d$/", $lemma->wordform(22, $dialect_id), $regs)) { 
                    return $regs[1];
                }
                return NULL;
        }
    }

    public static function isRightVowelBase($stem) {
        if (preg_match("/[".self::vowelSet()."]$/u", $stem)) {
            return true;
        }
        return false;
    }

    public static function getListForAutoComplete() {
        return $gramsets = [1, 56,  3,  4,  277,  5,  8,  9, 10, 11, 12, 13, 6,  14, 15, 17, 20, 16, 19,
                            2, 57, 24, 22, 279, 59, 23, 60, 61, 25, 62, 63, 64, 65, 66, 18, 69, 67, 68];
    }
    
    public static function stemsFromTemplate($template, $name_num) {
        $stems[0] = $base = $template;
        $base_suff = null;
        $arg = "([^\|]*)";
        $div_arg = "\|".$arg;
        $base_shab = "([^\s\(\|]+)";
        $base_suff_shab = "([^\s\(\|]*)";
        $okon1_shab = "-([^\,\;\)]+)";
        $lemma_okon1_shab = "/^".$base_shab."\|?".$base_suff_shab."\s*\(".$okon1_shab;
        
        // only plural
        if (preg_match("/^{{vep-decl-stems\|n=pl".$div_arg.$div_arg.$div_arg."}}$/u",$template, $regs) ||
                ($name_num == 'pl' && preg_match($lemma_okon1_shab."\)/", $template, $regs))) {
            $name_num = 'pl';
            list($stems, $base, $base_suff) =  VepsName::stemsPlFromTemplate($regs);
        // only single
        } elseif (preg_match("/^{{vep-decl-stems\|n=sg".$div_arg.$div_arg.$div_arg.$div_arg."}}$/u",$template, $regs) ||
                ($name_num == 'sg' && preg_match($lemma_okon1_shab."\,?\s*-?([^\,\;]*)\)/", $template, $regs)) ||
                (preg_match($lemma_okon1_shab."\)/", $template, $regs))) {
            $name_num = 'sg';
            list($stems, $base, $base_suff) =  VepsName::stemsSgFromTemplate($regs);
        // others
        } elseif (preg_match("/^{{vep-decl-stems".$div_arg.$div_arg.$div_arg.$div_arg."\|?".$arg."}}$/u",$template, $regs) ||
                preg_match($lemma_okon1_shab."\,\s*-?([^\,\;]*)[\;\,]?\s*-([^\,\;]+)\)/", $template, $regs)) {
            list($stems, $base, $base_suff) = VepsName::stemsOthersFromTemplate($regs, $name_num);
        }
        return [$stems, $name_num, $base, $base_suff];
    }
    /**
     * template-name|base|nom-sg-suff|gen-sg-suff|par-sg-suff|par-pl-suff
     * vep-decl-stems|adjektiv||an|ad|id
     * OR
     * abidkirje|ine (-žen, -št, -ižid)
     * abekirj (-an, -oid)
     * 
     * @param Array $regs [0=>template, 1=>base, 2=>nom-sg-suff, 3=>gen-sg-suff, 4=>par-sg-suff, 5=>par-pl-suff]
     * @param Int $lang_id
     * @param Int $pos_id
     * @return Array [stems=[0=>nom_sg, 1=>base_gen_sg, 2=>base_ill_sg, 3=>part_sg, 4=>base_part_pl, 5=>''], $base, $base_suff]
     */
    public static function stemsOthersFromTemplate($regs, $name_num=NULL) {
//dd($regs);    
        $out = [null, null, null];
        $base = $regs[1];
        $base_suff = $regs[2];
        $gen_sg_suff = $regs[3];
        $par_sg_suff = $regs[4];
        $par_pl_suff = $regs[5];

        if (!preg_match("/^(.*)n$/", $gen_sg_suff, $regs_gen)) {
            return $out;
        }
        if (!preg_match("/^(.*)d$/", $par_pl_suff, $regs_par)) {
            return $out;
        }
        
        $stems[0] = $base.$regs[2];
        $stems[1] = $base. $regs_gen[1]; // single genetive base 
        if (!self::isRightVowelBase($stems[1])) {return $out;}
        
        $stems[2] = self::illSgBase($stems[1]); // single illative base
        $stems[3] = $par_sg_suff ? $base.$par_sg_suff : $stems[1].'d'; // single partitive base
        $stems[4] = $base. $regs_par[1]; // plural partitive base
        $stems[5] = '';
//dd('stems:',$stems);        
        return [$stems, $base, $base_suff];
    }

    /**
     * template-name|n=pl|base|base-suff|par-pl-suff
     * vep-decl-stems|n=pl|Alama|d|id
     * 
     * OR
     * base|base-suff (-par-pl-suff)
     * Alama|d (-id)
     * 
     * @param Array $regs [base, base-suff, par-pl-suff]
     * @return Array
     */
    public static function stemsPlFromTemplate($regs) {
        $base = $regs[1];
        $base_suff = $regs[2];
        $par_pl_suff = $regs[3];
//dd($par_pl_suff);        
        if (!preg_match("/^(.*)d$/", $par_pl_suff, $regs1)) {
            return [null, null, null];
        }
        
        $stems[0] = $base.$base_suff;                
        $stems[1] = $stems[2] = $stems[3] = '';
        $stems[4] = $base. $regs1[1];
        $stems[5] = '';
//dd($stems);        
        return [$stems, $base, $base_suff];
    }

    /**
     * template-name|n=sg|base|nom-sg-suff|gen-sg-suff|par-sg-suff
     * vep-decl-stems|n=sg|Amerik||an|ad
     * 
     * @param Array $regs [0=>template, 1=>base, 2=>nom-sg-suff, 3=>gen-sg-suff, 4=>par-sg-suff]
     * @return Array [stems=[0=>nom_sg, 1=>base_gen_sg, 2=>base_ill_sg, 3=>part_sg, 4=>'', 5=>''], $base, $base_suff]
     */
    public static function stemsSgFromTemplate($regs) {
//dd($regs);        
        $base = $regs[1];
        $base_suff = $regs[2];
        $gen_sg_suff = $regs[3];
        $par_sg_suff = (isset($regs[4])) ? $regs[4] : null;

        if (!preg_match("/^(.*)n$/", $gen_sg_suff, $regs1)) {
            return [null, null, null];
        }
       
        $stems[0] = $base.$base_suff;        
        $stems[1] = $base. $regs1[1];
        if (!self::isRightVowelBase($stems[1])) {return $out;}
        
        $stems[2] = self::illSgBase($stems[1]); // single illative base
        $stems[3] = $par_sg_suff ? $base.$par_sg_suff : $stems[1].'d';
        $stems[4] = $stems[5] = '';
//dd($stems);        
        return [$stems, $base, $base_suff];
    }
    
    /**
     * 
     * @param Array $stems [nom_sg, gen_sg, ill_sg, part_sg, part_pl, '']
     * @param Int $gramset_id
     * @param Int $dialect_id
     * @param String $name_num 'sg', 'pl' or null
     * @return string
     */
    public static function wordformByStems($stems, $gramset_id, $dialect_id, $name_num=null) {
        $s_sg = isset($stems[1]) ? (preg_match("/i$/u", $stems[1]) ? 'š' : 's') : '';
        
        switch ($gramset_id) {
            case 1: // номинатив, ед.ч. 
                return $name_num != 'pl' ? $stems[0] : '';
            case 56: // аккузатив, ед.ч. 
                return $name_num != 'pl' ? $stems[0].($stems[1] ? ', '.$stems[1].'n' : '') : '';
            case 3: // генитив, ед.ч. 
                return $stems[1] ? $stems[1].'n' : '';
            case 4: // партитив, ед.ч. 
                return $stems[3];
            case 277: // эссив, ед.ч. 
                return $stems[1] ? $stems[1]. 'n' : '';
            case 5: // транслатив, ед.ч. 
                return $stems[1] ? $stems[1]. 'k'. $s_sg : '';
            case 8: // инессив, ед.ч. 
                return $stems[1] ? $stems[1]. $s_sg : '';
            case 9: // элатив, ед.ч. 
                return self::elatSg($stems[1], $dialect_id);
            case 10: // иллатив, ед.ч. 
                return $stems[1] ? self::illSg($stems[1], $stems[2]) : '';
            case 11: // адессив, ед.ч. 
                return self::adesSg($stems[1], $dialect_id);
            case 12: // аблатив, ед.ч. 
                return self::ablatSg($stems[1], $dialect_id);
            case 13: // аллатив, ед.ч. 
                return self::allatSg($stems[1], $dialect_id);
            case 6: // абессив, ед.ч. 
                return $stems[1] ? $stems[1] . 'ta' : '';
            case 14: // комитатив, ед.ч. 
                return self::comitSg($stems[1], $dialect_id);
            case 15: // пролатив, ед.ч. 
                return self::prolSg($stems[3], $dialect_id);
            case 17: //аппроксиматив, ед.ч. 
                return self::approxSg($stems[1], $dialect_id);
            case 20: //эгрессив, ед.ч. 
                return self::egresSg($stems[1], $dialect_id);
            case 16: //терминатив, ед.ч. 
                return self::terminatSg($stems[1], $dialect_id);
            case 19: //адитив, ед.ч. 
                return self::aditSg($stems[1], $dialect_id);
                
                
            case 2: // номинатив, мн.ч. 
            case 57: // аккузатив, мн.ч. 
                return $name_num == 'pl' ? $stems[0] : ($name_num != 'sg' && $stems[1] ? $stems[1].'d' : '');
            case 24: // генитив, мн.ч. 
                return $stems[4] ? $stems[4]. 'den' : '';
            case 22: // партитив, мн.ч. 
                return self::partPl($stems[4], $dialect_id);
            case 279: // эссив, мн.ч. 
                return self::essPl($stems[4], $dialect_id);
            case 59: // транслатив, мн.ч. 
                return $stems[4] ? $stems[4]. 'kš' : '';
            case 23: // инессив, мн.ч.
                return $stems[4] ? $stems[4]. 'š' : '';
            case 60: // элатив, мн.ч.
                return self::elatPl($stems[4], $dialect_id);
            case 61: // иллатив, мн.ч. 
                return self::illPl($stems[4], $dialect_id);
            case 25: // адессив, мн.ч.
                return self::adesPl($stems[4], $dialect_id);
            case 62: // аблатив, мн.ч.
                return self::ablatPl($stems[4], $dialect_id);
            case 63: // аллатив, ед.ч. 
                return $stems[4] ? $stems[4] . 'le' : '';
            case 64: // абессив, мн.ч.
                return $stems[4] ? $stems[4] . 'ta' : '';
            case 65: // комитатив, мн.ч. 
                return self::comitPl($stems[4], $dialect_id);
            case 66: // пролатив, мн.ч. 
                return self::prolPl($stems[4], $dialect_id);
            case 18: //аппроксиматив, мн.ч. 
                return self::approxPl($stems[4], $dialect_id);
            case 69: //эгрессив, мн.ч. 
                return self::egresPl($stems[4], $dialect_id);
            case 67: //терминатив, мн.ч. 
                return self::terminatPl($stems[4], $dialect_id);
            case 68: //адитив, мн.ч. 
                return self::aditPl($stems[4], $dialect_id);
        }
        return '';
    }
    
    public static function consSet() {
        return "pbtdkgfvsšzžcčjhmnlr";
    }
    
    public static function vowelSet() {
        return "aoueiäöü";
    }
    
    public static function vowelEscapeSet() {
        return "aoueäöü";
    }
    
    /**
     * Сколько слогов в гласной основе?
     * 
     * @param String $stem1
     * @return INT 1 - односложное, 2 - двусложное, 3 - многосложное
     */
    public static function countSyllable($stem1) {
        $consonant = "[".self::consSet()."]";
        $syllable = $consonant."?’?".$consonant."?’?[".self::vowelSet()."][iu]?";
        if (preg_match("/^".$syllable."$/u",$stem1)) {
            return 1;
        } elseif (preg_match("/^".$syllable.$syllable."$/u",$stem1)) {
            return 2;
        }
        return 3;
    }
    
    //consonant after which the vowel escapes before
    public static function consSetEscapeV() {
        return "dpfsšzžcčlt"; // jgvkrbdhmn
    }
    
    /*
     * base of illative singular
     */
    public static function illSgBase($stem1) {
//        if (self::countSyllable($stem1)==2 && preg_match("/^(.+[".self::consSetEscapeV()."])[".self::vowelEscapeSet()."]$/u",$stem1, $regs)) {
        if (self::countSyllable($stem1)==2 && preg_match("/^(.+[".self::consSetEscapeV()."])[".self::vowelSet()."]$/u",$stem1, $regs)) {
            return $regs[1];
        }
        return $stem1;
    }
    /**
     * основа 1 + he (если основа 1 оканчивается на i)
     * основа 2 + ze (если основа 1 оканчивается на hV)
     * основа 2 +  hV
     * 
     * если основа 2 ≠ основа1, то + та же формула с основой 1, 
     * т. е. у двусложных, если выпала гласная, будет по две формы
     * 
     * @param type $stem1
     * @param type $stem2
     * @return string
     */
    public static function illSg($stem1, $stem2=null){
        if (!$stem2) {
            $stem2 = self::illSgBase($stem1);
        }
        
        if (self::countSyllable($stem1)<3 && preg_match("/i$/",$stem1)) {
            $okon = 'he';
        } elseif (self::countSyllable($stem1)>2 && preg_match("/h[".self::vowelSet()."]$/",$stem1)) {
            $okon = 'ze';
        } elseif (preg_match("/([".self::vowelSet()."])$/u",$stem1, $regs)) {
            $okon = 'h'. $regs[1];
        } else {
            return '';
        }
        
        if ($stem1 != $stem2) {
            return $stem1. $okon. ', '.$stem2. $okon;
        } else {
            return $stem1. $okon;
        }
    }

    public static function illPl($stem4) {
        if (!$stem4) {
            return '';
        }
        if (preg_match("/hi$/",$stem4)) {
            return $stem4. 'že';
        }
        return $stem4. 'he';
    }
    
    public static function comitSg($stem1, $dialect_id){
        if (!$stem1) {
            return '';
        }
        switch ($dialect_id) {
            case 3: // южновепсский 
                return $stem1. 'dmu, '.$stem1. 'mu';
            case 4: // средневепсский восточный 
                return $stem1. 'dme, '.$stem1. 'me';
            default:
                return $stem1. 'nke';                
        }        
    }
    
    public static function prolSg($stem3, $dialect_id){
        if (!$stem3) {
            return '';
        }
        $stem3_ = mb_substr($stem3, 0, -1);
        switch ($dialect_id) {
            case 3: // южновепсский 
                return $stem3. 'me, '.$stem3_. 'mu';
            case 4: // средневепсский восточный 
                return $stem3. 'me, '.$stem3_. 'me';
            default:
                return $stem3. 'me';                
        }        
    }
    
    public static function elatSg($stem1, $dialect_id){
        if (!$stem1) {
            return '';
        }
        $stem1 .= preg_match("/i$/u", $stem1) ? 'š' : 's';
        switch ($dialect_id) {
            case 3: // южновепсский 
                return $stem1. 'pää';
            case 4: // средневепсский восточный 
                return $stem1. 'pei';
            default:
                return $stem1. 'päi';                
        }        
    }
        
    public static function adesSg($stem1, $dialect_id){
        if (!$stem1) {
            return '';
        }
        switch ($dialect_id) {
            case 3: // южновепсский 
                return $stem1. 'a';
            case 4: // средневепсский восточный 
                return $stem1. 'ta';
            case 5: // средневепсский западный 
                return $stem1. 'u';
            default:
                return $stem1. 'l';                
        }        
    }
    
    public static function ablatSg($stem1, $dialect_id){
        if (!$stem1) {
            return '';
        }
        
        switch ($dialect_id) {
            case 3: // южновепсский 
                return $stem1. 'apää';
            case 4: // средневепсский восточный 
                return self::base_without_lastV($stem1). 'uu';
            case 5: // средневепсский западный 
                return $stem1. 'upäi';
            default:
                return $stem1. 'lpäi';                
        }        
    }
    
    public static function allatSg($stem1, $dialect_id){
        if (!$stem1) {
            return '';
        }
        
        switch ($dialect_id) {
            case 4: // средневепсский восточный 
                return self::base_without_lastV($stem1). 'uupei';
            default:
                return $stem1. 'le';                
        }        
    }
    
    public static function base_without_lastV($stem){
        if (preg_match("/^(.+)[".self::vowelSet()."]$/u",$stem, $regs)) {
            return $regs[1];
        }
        return $stem;
    }
    
    public static function approxSg($stem1, $dialect_id){
        if (!$stem1) {
            return '';
        }
        
        $approx = $stem1.'nnoks';
        
        if ($dialect_id == 43) {
            $approx = $stem1.'nno, '. $approx;                
        }    
        
        return $approx;
    }
    
    public static function egresSg($stem1, $dialect_id){
        if (!$stem1) {
            return '';
        }
        switch ($dialect_id) {
            case 3: // южновепсский 
                return $stem1. 'nnopää';
            case 4: // средневепсский восточный 
                return $stem1. 'nnoupei';
            default:
                return $stem1. 'nnopäi';                
        }        
    }
    
    public static function terminatSg($stem1, $dialect_id){
        if (!$stem1) {
            return '';
        }
        
        $ill = self::illSg($stem1);
        
        switch ($dialect_id) {
            case 3: // южновепсский 
                return $ill. 'saa';
            case 4: // средневепсский восточный 
                return $ill. 'sei';
            case 5: // средневепсский западный 
                return $ill. 'ssai';
            case 43: // младописьменный
                return $ill. 'sai, '. $stem1. 'lesai';
            default:
                return $ill. 'sai';
        }        
    }
    
    public static function aditSg($stem1, $dialect_id){
        if (!$stem1) {
            return '';
        }
        
        $ill = self::illSg($stem1);
        
        switch ($dialect_id) {
            case 3: // южновепсский 
                return $ill. 'pää';
            case 4: // средневепсский восточный 
                return $ill. 'pei';
            case 43: // младописьменный
                return $ill. 'päi, '. $stem1. 'lepäi';
            default:
                return $ill. 'päi';
        }        
    }
    
    public static function partPl($stem4, $dialect_id){
        if (!$stem4) {
            return '';
        }
        switch ($dialect_id) {
            case 3: // южновепсский 
            case 4: // средневепсский восточный 
            case 5: // средневепсский западный 
                return $stem4. 'd’';
            default:
                return $stem4. 'd';                
        }        
    }
    
    public static function essPl($stem4, $dialect_id){
        if (!$stem4) {
            return '';
        }
        switch ($dialect_id) {
            case 3: // южновепсский 
            case 4: // средневепсский восточный 
            case 5: // средневепсский западный 
                return $stem4. 'n’';
            default:
                return $stem4. 'n';                
        }        
    }
    
    public static function elatPl($stem4, $dialect_id){
        if (!$stem4) {
            return '';
        }
        switch ($dialect_id) {
            case 3: // южновепсский 
                return $stem4. 'špää';
            case 4: // средневепсский восточный 
                return $stem4. 'špei';
            default:
                return $stem4. 'špäi';                
        }        
    }
    
    public static function adesPl($stem4, $dialect_id){
        if (!$stem4) {
            return '';
        }
        switch ($dialect_id) {
            case 3: // южновепсский 
            case 4: // средневепсский восточный 
            case 5: // средневепсский западный 
                return $stem4. 'l’';
            default:
                return $stem4. 'l';                
        }        
    }
    
    public static function ablatPl($stem4, $dialect_id){
        if (!$stem4) {
            return '';
        }
        switch ($dialect_id) {
            case 3: // южновепсский 
                return $stem4. 'l’pää';                
            case 4: // средневепсский восточный 
                return $stem4. 'l’pei';                
            case 5: // средневепсский западный 
                return $stem4. 'l’päi';                
            default:
                return $stem4. 'lpäi';                
        }        
    }
    
    public static function comitPl($stem4, $dialect_id){
        if (!$stem4) {
            return '';
        }
        switch ($dialect_id) {
            case 3: // южновепсский 
                return $stem4. 'mu';                
            case 4: // средневепсский восточный 
                return $stem4. 'd’me';                
            case 5: // средневепсский западный 
                return $stem4. 'deke';                
            default:
                return $stem4. 'denke';                
        }        
    }
    
    public static function prolPl($stem4, $dialect_id){
        if (!$stem4) {
            return '';
        }
        switch ($dialect_id) {
            case 3: // южновепсский 
                return $stem4. 'mu';                
            case 4: // средневепсский восточный 
            case 5: // средневепсский западный 
                return $stem4. 'd’me';                
            default:
                return $stem4. 'dme';                
        }        
    }
    
    public static function approxPl($stem4, $dialect_id){
        if (!$stem4) {
            return '';
        }
        
        $approx = $stem4.'dennoks';
        
        if ($dialect_id == 43) {
            $approx = $stem4.'denno, '. $approx;                
        }    
        
        return $approx;
    }
    
    public static function egresPl($stem4, $dialect_id){
        if (!$stem4) {
            return '';
        }
        switch ($dialect_id) {
            case 3: // южновепсский 
                return $stem4. 'delonpää';
            case 4: // средневепсский восточный 
                return $stem4. 'dennoupei';
            default:
                return $stem4. 'dennopäi';                
        }        
    }
    
    public static function terminatPl($stem4, $dialect_id){
        if (!$stem4) {
            return '';
        }
        
        $ill = self::illPl($stem4);
        
        switch ($dialect_id) {
            case 3: // южновепсский 
                return $ill. 'saa';
            case 4: // средневепсский восточный 
                return $ill. 'sei';
            case 5: // средневепсский западный 
                return $ill. 'ssai';
            case 43: // младописьменный
                return $ill. 'sai, '. $stem4. 'lesai';
            default:
                return $ill. 'sai';
        }        
    }
    
    public static function aditPl($stem4, $dialect_id){
        if (!$stem4) {
            return '';
        }
        
        $ill = self::illPl($stem4);
        
        switch ($dialect_id) {
            case 3: // южновепсский 
                return $ill. 'pää';
            case 4: // средневепсский восточный 
                return $ill. 'pei';
            case 43: // младописьменный
                return $ill. 'päi, '. $stem4. 'lepäi';
            default:
                return $ill. 'päi';
        }        
    }
}