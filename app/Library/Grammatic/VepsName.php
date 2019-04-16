<?php

namespace App\Library\Grammatic;

use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;

class VepsName
{
    public static function isRightVowelBase($stem) {
        if (preg_match("/[".self::vowelSet()."]$/u", $stem)) {
            return true;
        }
        return false;
    }

    public static function getListForAutoComplete($pos_id) {
        return $gramsets = [1, 56,  3,  4,  7,  5,  8,  9, 10, 11, 12, 13, 6,  14, 15, 17, 20, 16, 19,
                            2, 57, 24, 22, 58, 59, 23, 60, 61, 25, 62, 63, 64, 65, 66, 18, 69, 67, 68];
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
    public static function stemsFromTemplate($regs) {
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

        if (!preg_match("/^(.*)d$/", $par_pl_suff, $regs1)) {
            return [null, null, null];
        }
        
        $stems[0] = $base.$base_suff;                
        $stems[1] = $stems[2] = $stems[3] = '';
        $stems[4] = $base. $regs1[1];
        $stems[5] = '';
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
        
        $stems[2] = mb_substr($stems[1],0,-1); // single illative base
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
    public static function wordformByStems($stems, $gramset_id, $dialect_id, $name_num) {
        $s_sg = preg_match("/i$/u", $stems[1]) ? 'š' : 's';
        $s_pl = preg_match("/i$/u", $stems[4]) ? 'š' : 's';
        
        switch ($gramset_id) {
            case 1: // номинатив, ед.ч. 
                return $name_num != 'pl' ? $stems[0] : '';
            case 56: // аккузатив, ед.ч. 
            case 3: // генитив, ед.ч. 
                return $stems[1] ? $stems[1].'n' : '';
            case 4: // партитив, ед.ч. 
                return $stems[3];
            case 7: // эссив-инструктив, ед.ч. 
                return $stems[1] ? $stems[1]. 'n' : '';
            case 5: // транслатив, ед.ч. 
                return $stems[1] ? $stems[1]. 'k'. $s_sg : '';
            case 8: // инессив, ед.ч. 
                return $stems[1] ? $stems[1]. $s_sg : '';
            case 9: // элатив, ед.ч. 
                return $stems[1] ? $stems[1]. $s_sg. 'päi' : '';
            case 10: // иллатив, ед.ч. 
                return self::illSg($stems[1], $stems[2]);
            case 11: // адессив, ед.ч. 
                return $stems[1] ? $stems[1] . 'l' : '';
            case 12: // аблатив, ед.ч. 
                return $stems[1] ? $stems[1] . 'lpäi' : '';
            case 13: // аллатив, ед.ч. 
                return $stems[1] ? $stems[1] . 'le' : '';
            case 6: // абессив, ед.ч. 
                return $stems[1] ? $stems[1] . 'ta' : '';
            case 14: // комитатив, ед.ч. 
                return $stems[1] ? $stems[1].'nke' : '';
            case 15: // пролатив, ед.ч. 
                return $stems[3] ? $stems[3].'me' : '';
            case 17: //аппроксиматив, ед.ч. 
                return $stems[1] ? $stems[1].'nno, '. $stems[1].'nnoks' : '';
            case 20: //эгрессив, ед.ч. 
                return $stems[1] ? $stems[1].'nnopäi' : '';
            case 16: //терминатив, ед.ч. 
                return $stems[1] ? $stems[1].'lesai, '. $stems[1].'ssai' : '';
            case 19: //адитив, ед.ч. 
                return $stems[1] ? $stems[1].'lepäi' : '';
                
                
            case 2: // номинатив, мн.ч. 
            case 57: // аккузатив, мн.ч. 
                return $name_num == 'pl' ? $stems[0] : ($name_num != 'sg' && $stems[1] ? $stems[1].'d' : '');
            case 24: // генитив, мн.ч. 
                return $stems[4] ? $stems[4]. 'den' : '';
            case 22: // партитив, мн.ч. 
                return $stems[4] ? $stems[4] . 'd' : '';
            case 58: // эссив-инструктив, мн.ч. 
                return $stems[4] ? $stems[4]. 'n' : '';
            case 59: // транслатив, мн.ч. 
                return $stems[4] ? $stems[4]. 'kš' : '';
            case 23: // инессив, мн.ч.
                return $stems[4] ? $stems[4]. 'š' : '';
            case 60: // элатив, мн.ч.
                return $stems[4] ? $stems[4]. 'špäi' : '';
            case 61: // иллатив, мн.ч. 
                return $stems[4] ? $stems[4].(preg_match("/hi$/",$stems[4]) ? 'že' : 'he') : '';
            case 25: // адессив, мн.ч.
                return $stems[4] ? $stems[4] . 'l' : '';
            case 62: // аблатив, мн.ч.
                return $stems[4] ? $stems[4] . 'lpäi' : '';
            case 63: // аллатив, ед.ч. 
                return $stems[4] ? $stems[4] . 'le' : '';
            case 64: // абессив, мн.ч.
                return $stems[4] ? $stems[4] . 'ta' : '';
            case 65: // комитатив, мн.ч. 
                return $stems[4] ? $stems[4].'denke' : '';
            case 66: // пролатив, мн.ч. 
                return $stems[4] ? $stems[4].'dme' : '';
            case 18: //аппроксиматив, мн.ч. 
                return $stems[4] ? $stems[4].'denno, '. $stems[4].'dennoks' : '';
            case 69: //эгрессив, мн.ч. 
                return $stems[4] ? $stems[4].'dennopäi' : '';
            case 67: //терминатив, мн.ч. 
                return $stems[4] ? $stems[4].(preg_match("/hi$/",$stems[4]) ? 'ž' : 'h').'esai, '. $stems[4].'lesai' : '';
            case 68: //адитив, мн.ч. 
                return $stems[4] ? $stems[4].(preg_match("/hi$/",$stems[4]) ? 'ž' : 'h').'epäi, '. $stems[4].'lepäi' : '';
        }
        return '';
    }
    
    public static function consSet() {
        return "pbtdkgfvsšzžcčjhmnlr";
    }
    
    public static function vowelSet() {
        return "aoueiäöü";
    }
    
    /**
     * Сколько слогов в гласной основе?
     * 
     * @param String $stem1
     * @return INT 1 - односложное, 2 - двусложное, 3 - многосложное
     */
    public static function countSyllable($stem1) {
        $syllable = "[".self::consSet()."’]{0,2}[".self::vowelSet()."][i]?";
        if (preg_match("/^".$syllable."$/u",$stem1)) {
            return 1;
        } elseif (preg_match("/^".$syllable.$syllable."$/u",$stem1)) {
            return 2;
        }
        return 3;
    }
    
    public static function illSgBase($stem1) {
        mb_substr($stems[1],0,-1);
    }
    /**
     * основа 1 + he (если основа 1 оканчивается на i)
     * основа 4 + ze (если основа 1 оканчивается на hV)
     * основа 4 +  hV
     * 
     * @param type $stem1
     * @param type $stem4
     * @return string
     */
    public static function illSg($stem1, $stem2){
        if (preg_match("/i$/",$stem1)) {
            return $stem1. 'he';
        } elseif (preg_match("/h[auoeüäö]$/",$stem1)) {
            return $stem2. 'ze';
        } else {
            $last_letter = mb_substr($stem1,-1,1);
            return $stem2. 'h'. $last_letter;
        }
        return '';
    }

    public static function illPlEnding($stem) {
        if (preg_match("/hi$/",$stem)) {
            return 'že';
        } else {
            return 'he';
        }
    }
    
}