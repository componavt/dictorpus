<?php

namespace App\Library;

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
        
        if ($pos_id == PartOfSpeech::getVerbID()) {
            if ($lang_id == 1) {
            $gramsets = [26,  27,  28,  29,  30,  31, 295, 296, 
                         70,  71,  72,  73,  78,  79, 
                         32,  33,  34,  35,  36,  37, 297, 298,
                         80,  81,  82,  83,  84,  85, 
/*                         86,  87,  88,  89,  90,  91,  92,  93,  94,  95,  96,  97,
                         98,  99, 100, 101, 102, 103, 104, 105, 107, 108, 106, 109,*/
                              51,  52,  53,  54,  55, 299, 300,
                         50,  74,       76,  77, 
                         38,  39,  40,  41,  42,  43, 301,
                /* conditional presence negative */
                         44,  45,  46,  47,  48,  49, 302,
                        116, 117, 118, 119, 120, 121,
/*                        135, 125, 136, 137, 138, 139, 140, 141, 142, 143, 144, 145,
                        146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 157,
                        158, 159, 160, 161, 162, 163, 164, 165, 166, 167, 168, 169,*/
                        170, 171, 172, 173, 174, 175, 176, 177,
                        178, 179, 180, 181];
            } else {
            $gramsets = [26,  27,  28,  29,  30,  31,  70,  71,  72,  73,  78,  79, 
                         32,  33,  34,  35,  36,  37,  80,  81,  82,  83,  84,  85, 
                         86,  87,  88,  89,  90,  91,  92,  93,  94,  95,  96,  97,
                         98,  99, 100, 101, 102, 103, 104, 105, 107, 108, 106, 109,
                              51,  52,       54,  55,       50,  74,       76,  77,  
                         44,  45,  46,  47,  48,  49, 116, 117, 118, 119, 120, 121,
                        135, 125, 136, 137, 138, 139, 140, 141, 142, 143, 144, 145,
                        146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 157,
                        158, 159, 160, 161, 162, 163, 164, 165, 166, 167, 168, 169,
                        170, 171, 172, 173, 174, 175, 176, 177,
                        178, 179, 282, 180, 181];
            }
        } elseif (in_array($pos_id, PartOfSpeech::getNameIDs())) {
            if ($lang_id == 1) {
                $gramsets = [1, 56,  3,  4,  7,  5,  8,  9, 10, 11, 12, 13, 6,  14, 15, 17, 20, 16, 19,
                             2, 57, 24, 22, 58, 59, 23, 60, 61, 25, 62, 63, 64, 65, 66, 18, 69, 67, 68];
            } else {
                $gramsets = [1,  3,  4, 277,  5,  8,  9, 10, 278, 12, 6,  14, 15, 
                             2, 24, 22, 279, 59, 23, 60, 61, 280, 62, 64, 65, 66, 281];
            }
        }
        return $gramsets;
    }

    public static function rightConsonant($d, $l) {
        $consonants = ["d" => ["b"=>"b", "d"=>"d", "g"=>"g"],
                       "t" => ["b"=>"p", "d"=>"t", "g"=>"k"]];
        if (isset($consonants[$d][$l])) {
            return $consonants[$d][$l];
        }
    }
    
    public static function ringConsonant($l) {
        $consonants = ["k"=>"g", "p"=>"b", "s"=>"z", "š"=>"ž", "t"=>"d"];
        if (isset($consonants[$l])) {
            return $consonants[$l];
        }
    }
    
    /**
     * stems = [0 => основа инфинитива, 
     *          1 => основа презенса, 
     *          2 => основа имперфекта,
     *          3 => основа актив 2-го причастия
     *          4 => основа кондиционала, 
     *          5 => основа потенциала, 
     *          6 => d/t - предпоследняя буква инфинитива
     *          7 => a/ä - последняя буква инфинитива]
     * 
     * @param Array $regs
     * @param Int $lang_id
     * @param Int $pos_id
     * @return array
     */
    public static function verbStemsFromVepsTemplate($regs, $lang_id, $pos_id) {
//dd($regs);        
        $stems = [];
//dd(sizeof($regs));        
        if (sizeof($regs)!=5) {
            return $stems;
        }
        $base  = $regs[1];
        $past_suff = $regs[4];
//dd($regs[2]);        
        if (!preg_match("/^(.*)([dt])([aä])$/u", $regs[2], $regs1)) {
            return null;
        }
        $inf_suff = $regs1[1];
        $cons = $regs1[2];
        $harmony = $regs1[3];
//dd($regs[3]);        
        if (!preg_match("/^(.*)b$/u", $regs[3], $regs1)) {
            return null;
        }        
        $pres_suff = $regs1[1];
        
        $inf_stem = $base. $inf_suff;
        $pres_stem = $base. $pres_suff; 
        if (!preg_match("/[aeiouüäö]$/u", $pres_stem)) { // должен оканчиваться на гласную
            return null;
        }
        $past_stem = $base. $past_suff;
        if (!preg_match("/i$/u", $past_stem)) { // должен оканчиваться на i
            return null;
        }
        $cond_stem = $pres_stem;
//        if (preg_match("/^(.*[aeiouüäö-]+[^aeiouüäö]+)[eiä]$/u", $pres_stem)) {
        if (preg_match("/^(.+[^aeiouüäö]+)[eiä]$/u", $pres_stem, $regs1) 
                || preg_match("/^(.+[aeiouüäö]+)i$/u", $pres_stem, $regs1)) {
            $cond_stem = $regs1[1];
        }
//dd($pres_stem);        
        $past_actv_ptcp_stem = $inf_stem;
        if (preg_match("/^(.+)([kpsšt])$/u", $inf_stem, $regs1)) {
            $inf_stem_voiced = $regs1[1]. self::ringConsonant($regs1[2]);
            $pres_stem_novowel = preg_replace("/[aeiouüäö]+$/", "", $pres_stem);
            if ($inf_stem_voiced == $pres_stem_novowel) {
                $past_actv_ptcp_stem = $inf_stem_voiced;
            }
        }
        $potn_stem = $past_actv_ptcp_stem;
        if (preg_match("/[aeiouüäö]$/u", $inf_stem, $regs1)) {
            $potn_stem = $pres_stem;
        }
        return [$inf_stem, $pres_stem, $past_stem, $past_actv_ptcp_stem,
                $cond_stem, $potn_stem, $cons, $harmony];        
    }
    
    /**
     * template-name|base|nom-sg-suff|gen-sg-suff|par-sg-suff|par-pl-suff
     * vep-decl-stems|adjektiv||an|ad|id
     * 
     * @param Array $regs
     * @param Int $lang_id
     * @param Int $pos_id
     * @return Array
     */
    public static function nameStemsFromVepsTemplate($regs) {
//dd($regs, $name_num);        
        $base = $regs[1];
        $base_suff = $regs[2];
        $gen_sg_suff = $regs[3];
        $par_sg_suff = $regs[4];
        $par_pl_suff = $regs[5];

        if (!preg_match("/^(.*)n$/", $gen_sg_suff, $regs_gen)) {
            return [null, null, null];
        }
        if (!preg_match("/^(.*)d$/", $par_pl_suff, $regs_par)) {
            return [null, null, null];
        }
        
        $stems[0] = $base.$regs[2];
        $stems[1] = $base. $regs_gen[1];
        $stems[3] = $base. ($par_sg_suff ? $par_sg_suff : $regs_gen[1].'d');
        $stems[4] = $base. $regs_par[1];
        $stems[2] = $stems[5] = '';
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
    public static function nameStemsPlFromVepsTemplate($regs) {
        $base = $regs[1];
        $base_suff = $regs[2];
        $par_pl_suff = $regs[3];

        if (!preg_match("/^(.*)d$/", $par_pl_suff, $regs1)) {
            return [null, null, null];
        }
        
        $stems[0] = $base.$base_suff;                
        $stems[4] = $base. $regs1[1];
        $stems[1] = $stems[2] = $stems[3] = $stems[5] = '';
        return [$stems, $base, $base_suff];
    }

    /**
     * template-name|n=sg|base|nom-sg-suff|gen-sg-suff|par-sg-suff
     * vep-decl-stems|n=sg|Amerik||an|ad
     * 
     * @param Array $regs
     * @return Array
     */
    public static function nameStemsSgFromVepsTemplate($regs) {
        $base = $regs[1];
        $base_suff = $regs[2];
        $gen_sg_suff = $regs[3];
        $par_sg_suff = $regs[4];

        if (!preg_match("/^(.*)n$/", $gen_sg_suff, $regs1)) {
            return [null, null, null];
        }
        
        $stems[0] = $base.$base_suff;        
        $stems[1] = $base. $regs1[1];
        $stems[3] = $base. $par_sg_suff;
        $stems[2] = $stems[4] = $stems[5] = '';
        return [$stems, $base, $base_suff];
    }
    
    public static function stemsFromVepsTemplate($template, $lang_id, $pos_id, $name_num = null) {
        $stems = $base = $base_suff = null;
        if (in_array($pos_id, PartOfSpeech::getNameIDs())) { 
            if (preg_match("/^vep-decl-stems\|n=sg\|([^\|]*)\|([^\|]*)\|([^\|]*)\|([^\|]*)$/u",$template, $regs)) {
                $name_num = 'sg';
                list($stems, $base, $base_suff) =  self::nameStemsSgFromVepsTemplate($regs);
            } elseif (preg_match("/^vep-decl-stems\|n=pl\|([^\|]*)\|([^\|]*)\|([^\|]*)\|?([^\|]*)$/u",$template, $regs) ||
                    ($name_num == 'pl' && preg_match("/^([^\s\(\|]+)\|?([^\s\(\|]*)\s*\(-([^\,\;\)]+)\)/", $template, $regs))) {
                $name_num = 'pl';
                list($stems, $base, $base_suff) =  self::nameStemsPlFromVepsTemplate($regs);
            } elseif (preg_match("/^vep-decl-stems\|([^\|]*)\|([^\|]*)\|([^\|]*)\|([^\|]*)\|?([^\|]*)$/u",$template, $regs) ||
                    preg_match("/^([^\s\(\|]+)\|?([^\s\(\|]*)\s*\(-([^\,\;]+)\,\s*-?([^\,\;]*)[\;\,]?\s*-([^\,\;]+)\)/", $template, $regs)) {
                list($stems, $base, $base_suff) = self::nameStemsFromVepsTemplate($regs, $lang_id, $pos_id, $name_num);
            }
        } elseif ($pos_id == PartOfSpeech::getVerbID() && 
            (preg_match('/^vep-conj-stems\|([^\|]*)\|([^\|]*)\|([^\|]*)\|?([^\|]*)$/u',$template, $regs) ||
            preg_match("/^([^\s\(\|]+)\|?([^\s\(\|]*)\s*\(-([^\,\;]+)\,\s*-([^\,\;]+)\)/", $template, $regs))) {                    
//dd($regs);     
            $base = $regs[1];
            $base_suff = $regs[2];
            $stems = self::verbStemsFromVepsTemplate($regs, $lang_id, $pos_id);
        }
        return [$stems, $name_num, $base, $base_suff];
    }
    /**
     * @param String $template
     * @param Int $lang_id
     * @param Int $pos_id
     * @return Array
     */
    public static function stemsFromTemplate($template, $lang_id, $pos_id, $name_num = null) {
        if ($lang_id == 1) {
            return self::stemsFromVepsTemplate($template, $lang_id, $pos_id, $name_num);                
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
                !($lang_id==1 && preg_match("/^([^\s\(]+\s*\([^\,\;]+\,\s*[^\,\;]+[\;\,]?\s*[^\,\;]*\))/", $template, $list))) {
            return [$template, false, $template, NULL];
        }
        
        list($stems, $name_num, $max_stem, $affix) = self::stemsFromTemplate($list[1], $lang_id, $pos_id, $name_num);
//if ($template == "{{vep-conj-stems|voik|ta|ab|i}}") dd($stems);                
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
                $wordforms[$gramset_id] = self::nounWordformByStems($stems, $gramset_id, $lang_id, $dialect_id, $name_num);
            }
        }
        if (!$max_stem) {
            list($max_stem, $affix) = self::maxStem($stems);
        }
        return [$max_stem.$affix, $wordforms, $max_stem, $affix];
    }
    
    public static function nounWordformByStems($stems, $gramset_id, $lang_id, $dialect_id, $name_num) {
        if ($lang_id == 1) {
            return self::nounWordformVepsByStems($stems, $gramset_id, $dialect_id, $name_num);
        }
        return self::nounWordformKarelianByStems($stems, $gramset_id, $lang_id, $dialect_id);
    }
    
    public static function nounWordformKarelianByStems($stems, $gramset_id, $lang_id, $dialect_id) {
        $stem1_i = preg_match("/i$/u", $stems[1]);
        $stem5_oi = preg_match("/[oö]i$/u", $stems[5]);
        
        switch ($gramset_id) {
            case 1: // номинатив, ед.ч. 
                return $stems[0];
            case 3: // генитив, ед.ч. 
                return $stems[1].'n';
            case 4: // партитив, ед.ч. 
                return $stems[3];
            case 277: // эссив, ед.ч. 
                return $stems[2]. 'n'. self::garmVowel($stems[2],'a');
            case 5: // транслатив, ед.ч. 
                return $stems[1] . ($stem1_i ? 'ksi' : 'kši');
            case 8: // инессив, ед.ч. 
                return $stems[1] . ($stem1_i ? 'ss' : 'šš'). self::garmVowel($stems[1],'a');
            case 9: // элатив, ед.ч. 
                return $stems[1] . ($stem1_i ? 'st' : 'št'). self::garmVowel($stems[1],'a');
            case 10: // иллатив, ед.ч. 
                return $stems[2].'h';
            case 278: // адессив-аллатив, ед.ч. 
                return $stems[1] . 'll'. self::garmVowel($stems[1],'a');
            case 12: // аблатив, ед.ч. 
                return $stems[1] . 'ld'. self::garmVowel($stems[1],'a');
            case 6: // абессив, ед.ч. 
                return $stems[1] . 'tt'. self::garmVowel($stems[1],'a');
            case 14: // комитатив, ед.ч. 
                return $stems[1].'nke';
            case 15: // пролатив, ед.ч. 
                return $stems[1].'čči';
                                
            case 2: // номинатив, мн.ч. 
                return $stems[1]. 't';
            case 24: // генитив, мн.ч. 
                return $stems[4]. 'n';
            case 22: // партитив, мн.ч. 
                return $stems[5] . ($stem5_oi ? 'd'.self::garmVowel($stems[5],'a') : 'e' );
            case 279: // эссив, мн.ч.
                return $stems[5] . 'n'. self::garmVowel($stems[5],'a');
            case 59: // транслатив, мн.ч. 
                return $stems[4].'ksi';
            case 23: // инессив, мн.ч.
                return $stems[4] . 'ss'. self::garmVowel($stems[5],'a');
            case 60: // элатив, мн.ч.
                return $stems[4] . 'st'. self::garmVowel($stems[5],'a');
            case 61: // иллатив, мн.ч. 
                return $stems[5].'h';
            case 280: // адессив-аллатив, мн.ч.
                return $stems[4] . 'll'. self::garmVowel($stems[5],'a');
            case 62: // аблатив, мн.ч.
                return $stems[4] . 'ld'. self::garmVowel($stems[5],'a');
            case 64: // абессив, мн.ч.
                return $stems[4] . 'tt'. self::garmVowel($stems[5],'a');
            case 65: // комитатив, мн.ч. 
                return $stems[4].'nke';
            case 66: // пролатив, мн.ч. 
                return $stems[4].'čči';
            case 281: // инструктив, мн.ч. 
                return $stems[4].'n';
        }
        return '';
    }

    public static function illSgEnding($stem) {
        $last_let = mb_substr($stem, -1, 1);
        $before_last_let = mb_substr($stem, -2, 1);
        if ($before_last_let == 'h' && self::isVowel($last_let)) {
            $ill_sg_ending = preg_match("/i$/u", $stem) ? 'že' : 'ze';
        } else {
            $ill_sg_ending = 'h'. (self::isVowel($last_let) 
                                ? ($last_let=='i' ? 'e' : $last_let) 
                                : 'a');
        }  
        return $ill_sg_ending;
    }
    
    public static function illPlEnding($stem) {
        $last_let = mb_substr($stem, -1, 1);
        $before_last_let = mb_substr($stem, -2, 1);
        if ($before_last_let == 'h' && self::isVowel($last_let)) {
            $ill_ending = preg_match("/i$/u", $stem) ? 'že' : 'ze';
        } else {
            $ill_ending = 'h'. (self::isVowel($last_let) 
                                ? ($last_let=='i' ? 'e' : $last_let) 
                                : 'i');
        }  
        return $ill_ending;
    }
    
    /**
     * 
     * @param Array $stems [nom_sg, gen_sg, '', part_sg, part_pl, '']
     * @param Int $gramset_id
     * @param Int $dialect_id
     * @param String $name_num 'sg', 'pl' or null
     * @return string
     */
    public static function nounWordformVepsByStems($stems, $gramset_id, $dialect_id, $name_num) {
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
            /*case 10: // иллатив, ед.ч. 
                return $stems[2].'h';*/
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
                return $stems[4] ? $stems[4]. 'k'. $s_pl : '';
            case 23: // инессив, мн.ч.
                return $stems[4] ? $stems[4] . $s_pl : '';
            case 60: // элатив, мн.ч.
                return $stems[4] ? $stems[4] . $s_pl. 'päi' : '';
            case 61: // иллатив, мн.ч. 
                return $stems[4] ? $stems[4].self::illPlEnding($stems[4]) : '';
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
                return $stems[4] ? $stems[4].self::illPlEnding($stems[4]).'sai, '. $stems[4].'lesai' : '';
            case 68: //адитив, мн.ч. 
                return $stems[4] ? $stems[4].self::illPlEnding($stems[4]).'päi, '. $stems[4].'lepäi' : '';
        }
        return '';
    }

    public static function verbWordformByStems($stems, $gramset_id, $lang_id, $dialect_id) {
        if ($lang_id == 1) {
            return self::verbWordformVepsByStems($stems, $gramset_id, $dialect_id);
        }
        return self::verbWordformKarelianByStems($stems, $gramset_id, $lang_id, $dialect_id);
    }
    
    public static function verbWordformKarelianByStems($stems, $gramset_id, $lang_id, $dialect_id) {
        $stem4_modify = self::stemModify($stems[4], $stems[0], "ua|iä", ['o'=>'a', 'i'=> ['a','ä']]);
        switch ($gramset_id) {
            case 26: // 1. индикатив, презенс, 1 л., ед.ч., пол. 
                return $stems[1].'n';
            case 27: // 2. индикатив, презенс, 2 л., ед.ч., пол. 
                return $stems[1].'t';
            case 28: // 3. индикатив, презенс, 3 л., ед.ч., пол. 
                return self::indPres1SingByStem($stems[2]);
            case 29: // 4. индикатив, презенс, 1 л., мн.ч., пол. 
                return $stems[1] . 'mm'. self::garmVowel($stems[1],'a');
            case 30: // 5. индикатив, презенс, 2 л., мн.ч., пол. 
                return $stems[1] . 'tt'. self::garmVowel($stems[1],'a');
            case 31: // 6. индикатив, презенс, 3 л., мн.ч., пол. 
                return $stems[6].'h';

            case 70: // 7. индикатив, презенс, 1 л., ед.ч., отриц. 
                return self::negativeForm(70, $lang_id). $stems[1];
            case 71: // 8. индикатив, презенс, 2 л., ед.ч., отриц. 
                return self::negativeForm(71, $lang_id). $stems[1];
            case 72: // 9. индикатив, презенс, 3 л., ед.ч., отриц. 
                return self::negativeForm(72, $lang_id). $stems[1];
            case 73: //10. индикатив, презенс, 1 л., мн.ч., отриц. 
                return self::negativeForm(73, $lang_id). $stems[1];
            case 78: // 11. индикатив, презенс, 2 л., мн.ч., отриц. 
                return self::negativeForm(78, $lang_id). $stems[1];
            case 79: // 12. индикатив, презенс, 3 л., мн.ч., отриц. 
                return self::negativeForm(79, $lang_id). $stems[6];

            case 32: // 13. индикатив, имперфект, 1 л., ед.ч., пол. 
                return $stems[3] . 'n';
            case 33: // 14. индикатив, имперфект, 2 л., ед.ч., пол. 
                return $stems[3] . 't';
            case 34: // 15. индикатив, имперфект, 3 л., ед.ч., пол. 
                return $stems[4];
            case 35: // 16. индикатив, имперфект, 1 л., мн.ч., пол. 
                return self::indImp1PlurByStem($stems[4]);
            case 36: // 17. индикатив, имперфект, 2 л., мн.ч., пол. 
                return self::indImp2PlurByStem($stems[4]);
            case 37: // 18. индикатив, имперфект, 3 л., мн.ч., пол. 
                return $stems[7] . 'ih';

            case 80: // 19. индикатив, имперфект, 1 л., ед.ч., отриц. 
                return self::negativeForm(80, $lang_id). self::perfectForm($stems[5], $lang_id);
            case 81: // 20. индикатив, имперфект, 2 л., ед.ч., отриц. 
                return self::negativeForm(81, $lang_id). self::perfectForm($stems[5], $lang_id);
            case 82: // 21. индикатив, имперфект, 3 л., ед.ч., отриц. 
                return self::negativeForm(82, $lang_id). self::perfectForm($stems[5], $lang_id);
            case 83: // 22. индикатив, имперфект, 1 л., мн.ч., отриц. 
                return self::negativeForm(83, $lang_id). self::perfectForm($stems[5], $lang_id);
            case 84: // 23. индикатив, имперфект, 2 л., мн.ч., отриц. 
                return self::negativeForm(84, $lang_id). self::perfectForm($stems[5], $lang_id);
            case 85: // 24. индикатив, имперфект, 3 л., мн.ч., отриц. 
                return self::negativeForm(85, $lang_id). $stems[7]. self::garmVowel($stems[7],'u');

            case 86: // 25. индикатив, перфект, 1 л., ед.ч., пол. 
                return self::auxForm(86, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id);
            case 87: // 26. индикатив, перфект, 2 л., ед.ч., пол. 
                return self::auxForm(87, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id);
            case 88: // 27. индикатив, перфект, 3 л., ед.ч., пол. 
                return self::auxForm(88, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id);
            case 89: // 28. индикатив, перфект, 1 л., мн.ч., пол. 
                return self::auxForm(89, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id);
            case 90: // 29. индикатив, перфект, 2 л., мн.ч., пол. 
                return self::auxForm(90, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id);
            case 91: // 30. индикатив, перфект, 3 л., мн.ч., пол. 
                return self::auxForm(91, $lang_id, $dialect_id). $stems[7]. self::garmVowel($stems[7],'u');

            case 92: // 31. индикатив, перфект, 1 л., ед.ч., отриц. 
                return self::auxForm(92, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id);
            case 93: // 32. индикатив, перфект, 2 л., ед.ч., отриц. 
                return self::auxForm(93, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id);
            case 94: // 33. индикатив, перфект, 3 л., ед.ч., отриц. 
                return self::auxForm(94, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id);
            case 95: // 34. индикатив, перфект, 1 л., мн.ч., отриц. 
                return self::auxForm(95, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id);
            case 96: // 35. индикатив, перфект, 2 л., мн.ч., отриц. 
                return self::auxForm(96, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id);
            case 97: // 36. индикатив, перфект, 3 л., мн.ч., отриц. 
                return 'ei ole './/self::auxForm(97, $lang_id, $dialect_id). 
                       $stems[7]. self::garmVowel($stems[7],'u');

            case 98: // 37. индикатив, плюсквамперфект, 1 л., ед.ч., пол. 
                return self::auxForm(98, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id);
            case 99: // 38. индикатив, плюсквамперфект, 2 л., ед.ч., пол. 
                return self::auxForm(99, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id);
            case 100: // 39. индикатив, плюсквамперфект, 3 л., ед.ч., пол. 
                return self::auxForm(100, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id);
            case 101: // 40. индикатив, плюсквамперфект, 1 л., мн.ч., пол. 
                return self::auxForm(101, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id);
            case 102: // 41. индикатив, плюсквамперфект, 2 л., мн.ч., пол. 
                return self::auxForm(102, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id);
            case 103: // 42. индикатив, плюсквамперфект, 3 л., мн.ч., пол. 
                return self::auxForm(103, $lang_id, $dialect_id). $stems[7]. self::garmVowel($stems[7],'u');

            case 104: // 43. индикатив, плюсквамперфект, 1 л., ед.ч., отриц. 
                return self::auxForm(104, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id);
            case 105: // 44. индикатив, плюсквамперфект, 2 л., ед.ч., отриц. 
                return self::auxForm(105, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id);
            case 106: // 45. индикатив, плюсквамперфект, 3 л., ед.ч., отриц. 
                return self::auxForm(106, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id);
            case 107: // 46. индикатив, плюсквамперфект, 1 л., мн.ч., отриц. 
                return self::auxForm(107, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id);
            case 108: // 47. индикатив, плюсквамперфект, 2 л., мн.ч., отриц. 
                return self::auxForm(108, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id);
            case 109: // 48. индикатив, плюсквамперфект, 3 л., мн.ч., отриц. 
                return 'ei oldu '. $stems[7]. self::garmVowel($stems[7],'u'); //self::auxForm(109, $lang_id, $dialect_id)

            case 51: // 49. императив, 2 л., ед.ч., пол 
                return $stems[1];
            case 52: // 50. императив, 3 л., ед.ч., пол 
            case 55: // 53. императив, 3 л., мн.ч., пол 
                return self::imp3SingPolByStem($stems[5], $stems[0], $dialect_id);
            case 54: // 52. императив, 2 л., мн.ч., пол 
                return self::imp2PlurPolByStem($stems[5], $stems[0], $dialect_id);
            case 50: // 54. императив, 2 л., ед.ч., отр. 
                return self::negativeForm(50, $lang_id). $stems[1];
            case 74: // 55. императив, 3 л., ед.ч., отр. 
                return self::negativeForm(74, $lang_id). self::imp3SingPolByStem($stems[5], $stems[0], $dialect_id);
            case 76: // 57. императив, 2 л., мн.ч., отр. 
                return self::negativeForm(76, $lang_id). self::imp2PlurPolByStem($stems[5], $stems[0], $dialect_id);
            case 77: // 58. императив, 3 л., мн.ч., отр. 
                return self::negativeForm(77, $lang_id). self::imp3SingPolByStem($stems[5], $stems[0], $dialect_id);

            case 44: // 71. кондиционал, имперфект, 1 л., ед.ч., пол. 
                return $stem4_modify . 'zin';
            case 45: // 72. кондиционал, имперфект, 2 л., ед.ч., пол. 
                return $stem4_modify . 'zit';
            case 46: // 73. кондиционал, имперфект, 3 л., ед.ч., пол. 
                return self::condImp3SingPolByStem($stems[4], $stems[0], $dialect_id);
            case 47: // 74. кондиционал, имперфект, 1 л., мн.ч., пол. 
                return $stem4_modify . 'zim'. self::garmVowel($stems[4],'a');
            case 48: // 75. кондиционал, имперфект, 2 л., мн.ч., пол. 
                return $stem4_modify . 'zij'. self::garmVowel($stems[4],'a');
            case 49: // 76. кондиционал, имперфект, 3 л., мн.ч., пол. 
                return $stems[7]. self::garmVowel($stems[7],'a'). 'is’';

            case 116: // 77. кондиционал, имперфект, 1 л., ед.ч., отр. 
                return self::negativeForm(116, $lang_id). self::condImp3SingPolByStem($stems[4], $stems[0], $dialect_id);
            case 117: // 78. кондиционал, имперфект, 1 л., ед.ч., отр. 
                return self::negativeForm(117, $lang_id). self::condImp3SingPolByStem($stems[4], $stems[0], $dialect_id);
            case 118: // 79. кондиционал, имперфект, 1 л., ед.ч., отр. 
                return self::negativeForm(118, $lang_id). self::condImp3SingPolByStem($stems[4], $stems[0], $dialect_id);
            case 119: // 80. кондиционал, имперфект, 1 л., ед.ч., отр. 
                return self::negativeForm(119, $lang_id). self::condImp3SingPolByStem($stems[4], $stems[0], $dialect_id);
            case 120: // 81. кондиционал, имперфект, 1 л., ед.ч., отр. 
                return self::negativeForm(120, $lang_id). self::condImp3SingPolByStem($stems[4], $stems[0], $dialect_id);
            case 121: // 82. кондиционал, имперфект, 1 л., ед.ч., отр. 
                return self::negativeForm(121, $lang_id). $stems[7]. self::garmVowel($stems[7],'a'). 'is’';
                
            case 135: // 95. кондиционал, плюсквамперфект, 1 л., ед.ч., пол. 
                return 'olizin '. self::perfectForm($stems[5], $lang_id);
            case 125: // 96. кондиционал, плюсквамперфект, 2 л., ед.ч., пол. 
                return 'olizit '. self::perfectForm($stems[5], $lang_id);
            case 136: // 97. кондиционал, плюсквамперфект, 3 л., ед.ч., пол. 
                return 'olis’ '. self::perfectForm($stems[5], $lang_id);
            case 137: // 98. кондиционал, плюсквамперфект, 1 л., мн.ч., пол. 
                return 'olizima '. self::perfectForm($stems[5], $lang_id);
            case 138: // 99. кондиционал, плюсквамперфект, 2 л., мн.ч., пол. 
                return 'olizija '. self::perfectForm($stems[5], $lang_id);
            case 139: // 100. кондиционал, плюсквамперфект, 3 л., мн.ч., пол. 
                return 'olis’ '. $stems[7] . self::garmVowel($stems[7],'u');
                
            case 140: // 101. кондиционал, плюсквамперфект, 1 л., ед.ч., отр. 
                return 'en olis’ '. self::perfectForm($stems[5], $lang_id);
            case 141: // 102. кондиционал, плюсквамперфект, 2 л., ед.ч., отр. 
                return 'et olis’ '. self::perfectForm($stems[5], $lang_id);
            case 142: // 103. кондиционал, плюсквамперфект, 3 л., ед.ч., отр. 
                return 'ei olis’ '. self::perfectForm($stems[5], $lang_id);
            case 143: // 104. кондиционал, плюсквамперфект, 1 л., мн.ч., отр. 
                return 'emmä olis’ '. self::perfectForm($stems[5], $lang_id);
            case 144: // 105. кондиционал, плюсквамперфект, 2 л., мн.ч., отр. 
                return 'että olis’ '. self::perfectForm($stems[5], $lang_id);
            case 145: // 106. кондиционал, плюсквамперфект, 3 л., мн.ч., отр. 
                return 'ei olis’ '. $stems[7] . self::garmVowel($stems[7],'u');
                
            case 146: // 107. потенциал, презенс, 1 л., ед.ч., пол. 
                return self::potencialForm($stems[5], 'en', $lang_id, $dialect_id);
            case 147: // 108. потенциал, презенс, 2 л., ед.ч., пол. 
                return self::potencialForm($stems[5], 'et', $lang_id, $dialect_id);
            case 148: // 109. потенциал, презенс, 3 л., ед.ч., пол. 
                return self::potencialForm($stems[5], self::garmVowel($stems[5], 'ou'), $lang_id, $dialect_id);
            case 149: // 110. потенциал, презенс, 1 л., мн.ч., пол. 
                return self::potencialForm($stems[5], 'emm'. self::garmVowel($stems[5], 'a'), $lang_id, $dialect_id);
            case 150: // 111. потенциал, презенс, 2 л., мн.ч., пол. 
                return self::potencialForm($stems[5], 'ett'.self::garmVowel($stems[5], 'a'), $lang_id, $dialect_id);
            case 151: // 112. потенциал, презенс, 3 л., мн.ч., пол. 
                return $stems[7]. self::garmVowel($stems[7], 'anneh');

            case 152: // 113. потенциал, презенс, 1 л., ед.ч., отр. 
                return self::negativeForm(152, $lang_id). self::potencialForm($stems[5], 'e', $lang_id, $dialect_id);
            case 153: // 114. потенциал, презенс, 1 л., ед.ч., отр. 
                return self::negativeForm(153, $lang_id). self::potencialForm($stems[5], 'e', $lang_id, $dialect_id);
            case 154: // 115. потенциал, презенс, 1 л., ед.ч., отр. 
                return self::negativeForm(154, $lang_id). self::potencialForm($stems[5], 'e', $lang_id, $dialect_id);
            case 155: // 116. потенциал, презенс, 1 л., ед.ч., отр. 
                return self::negativeForm(155, $lang_id). self::potencialForm($stems[5], 'e', $lang_id, $dialect_id);
            case 156: // 117. потенциал, презенс, 1 л., ед.ч., отр. 
                return self::negativeForm(156, $lang_id). self::potencialForm($stems[5], 'e', $lang_id, $dialect_id);
            case 157: // 118. потенциал, презенс, 1 л., ед.ч., отр. 
                return self::negativeForm(157, $lang_id). $stems[7]. self::garmVowel($stems[7], 'anne');
                
            case 158: // 119. потенциал, перфект, 1 л., ед.ч., пол. 
                return 'lienen '. self::perfectForm($stems[5], $lang_id);
            case 159: // 120. потенциал, перфект, 2 л., ед.ч., пол. 
                return 'lienet '. self::perfectForm($stems[5], $lang_id);
            case 160: // 121. потенциал, перфект, 3 л., ед.ч., пол. 
                return 'lienöy '. self::perfectForm($stems[5], $lang_id);
            case 161: // 122. потенциал, перфект, 1 л., мн.ч., пол. 
                return 'lienemmä '. self::perfectForm($stems[5], $lang_id);
            case 162: // 123. потенциал, перфект, 2 л., мн.ч., пол. 
                return 'lienettä '. self::perfectForm($stems[5], $lang_id);
            case 163: // 124. потенциал, перфект, 3 л., мн.ч., пол. 
                return 'lienöy '. $stems[7]. self::garmVowel($stems[7], 'u');
                
            case 164: // 125. потенциал, перфект, 1 л., ед.ч., отр. 
                return 'en liene '. self::perfectForm($stems[5], $lang_id);
            case 165: // 126. потенциал, перфект, 2 л., ед.ч., отр. 
                return 'et liene '. self::perfectForm($stems[5], $lang_id);
            case 166: // 127. потенциал, перфект, 3 л., ед.ч., отр. 
                return 'ei liene '. self::perfectForm($stems[5], $lang_id);
            case 167: // 128. потенциал, перфект, 1 л., мн.ч., отр. 
                return 'emmä liene '. self::perfectForm($stems[5], $lang_id);
            case 168: // 129. потенциал, перфект, 2 л., мн.ч., отр. 
                return 'että liene '. self::perfectForm($stems[5], $lang_id);
            case 169: // 130. потенциал, перфект, 3 л., мн.ч., отр. 
                return 'ei liene '. $stems[7]. self::garmVowel($stems[7], 'u');

            case 170: // 131. I инфинитив 
                return $stems[0];
            case 171: // 132. II инфинитив, инессив 
                return self::inf2Ines($stems[0]);
            case 172: // 133. II инфинитив, инструктив  
                return self::inf2Inst($stems[0]);
            case 173: // 134. III инфинитив, адессив
                return $stems[2]. self::garmVowel($stems[2], 'malla');
            case 174: // 135. III инфинитив, иллатив 
                return $stems[2]. self::garmVowel($stems[2], 'mah');
            case 175: // 136. III инфинитив, инессив 
                return $stems[2]. self::garmVowel($stems[2], 'mašša');
            case 176: // 137. III инфинитив, элатив 
                return $stems[2]. self::garmVowel($stems[2], 'mašta');
            case 177: // 138. III инфинитив, абессив 
                return $stems[2]. self::garmVowel($stems[2], 'matta');
                
            case 178: // 139. актив, 1-е причастие 
                return self::replaceSingVowel($stems[2], 'e', 'i'). self::garmVowel($stems[7], 'ja');
            case 179: // 140. актив, 2-е причастие 
                return self::partic2active($stems[5], $lang_id);
            case 282: // 141. актив, 2-е причастие  (сокращенная форма); перфект (форма основного глагола)
                return self::perfectForm($stems[5], $lang_id);
            case 180: // 142. пассив, 1-е причастие 
                return $stems[7]. self::garmVowel($stems[7], 'ava');
            case 181: // 143. пассив, 2-е причастие 
                return $stems[7]. self::garmVowel($stems[7], 'u');
        }
        return '';
    }

    /**
     * stems = [0 => основа инфинитива, 
     *          1 => основа презенса, 
     *          2 => основа имперфекта,
     *          3 => основа актив 2-го причастия
     *          4 => основа кондиционала, 
     *          5 => основа потенциала, 
     *          6 => d/t - предпоследняя буква инфинитива
     *          7 => a/ä - последняя буква инфинитива]
     */
    public static function verbWordformVepsByStems($stems, $gramset_id, $dialect_id) {
        $lang_id = 1;
        $g = self::rightConsonant($stems[6], 'g');
        switch ($gramset_id) {
            case 26: // 1. индикатив, презенс, 1 л., ед.ч., пол. 
                return $stems[1].'n';
            case 27: // 2. индикатив, презенс, 2 л., ед.ч., пол. 
                return $stems[1].'d';
            case 28: // 3. индикатив, презенс, 3 л., ед.ч., пол. 
                return $stems[1].'b';
            case 29: // 4. индикатив, презенс, 1 л., мн.ч., пол. 
                return $stems[1].'m';
            case 30: // 5. индикатив, презенс, 2 л., мн.ч., пол. 
                return $stems[1].'t';
            case 31: // 6. индикатив, презенс, 3 л., мн.ч., пол. 
                return $stems[0]. $stems[6]. 'as, '.$stems[1]. 'ba';
            case 295: // 144. индикатив, презенс, коннегатив, ед.ч.
                return $stems[1];
            case 296: // 145. индикатив, презенс, коннегатив, мн.ч.
                return $stems[0]. $g. 'oi';

            case 32: // 13. индикатив, имперфект, 1 л., ед.ч., пол. 
                return $stems[2]. 'n';
            case 33: // 14. индикатив, имперфект, 2 л., ед.ч., пол. 
                return $stems[2]. 'd';
            case 34: // 15. индикатив, имперфект, 3 л., ед.ч., пол. 
                return $stems[2];
            case 35: // 16. индикатив, имперфект, 1 л., мн.ч., пол. 
                return $stems[2]. 'm';
            case 36: // 17. индикатив, имперфект, 2 л., мн.ч., пол. 
                return $stems[2]. 't';
            case 37: // 18. индикатив, имперфект, 3 л., мн.ч., пол. 
                return $stems[2]. 'ba';
            case 297: // 146. индикатив, имперфект, коннегатив, ед.ч.
                return $stems[1]. 'nd';
            case 298: // 147. индикатив, имперфект, коннегатив, мн.ч.
                return $stems[3]. 'nugoi';

            case 51: // 49. императив, 2 л., ед.ч., пол 
                return $stems[1];
            case 52: // 50. императив, 3 л., ед.ч., пол 
            case 55: // 53. императив, 3 л., мн.ч., пол 
                return '';
            case 53: // 51. императив, 1 л., мн.ч., пол 
                return $stems[0]. $g. 'am';
            case 54: // 52. императив, 2 л., мн.ч., пол 
                return $stems[0]. $g. 'at';
            case 299: // 148. императив, коннегатив, ед.ч.
                return $stems[1];
            case 300: // 149. императив, коннегатив, мн.ч.
                return $stems[0]. $g. 'oi';
                
            case 38: // 59. кондиционал, презенс, 1 л., ед.ч., пол. 
                return $stems[4].'ižin';
            case 39: // 60. кондиционал, презенс, 2 л., ед.ч., пол. 
                return $stems[4].'ižid';
            case 40: // 61. кондиционал, презенс, 3 л., ед.ч., пол. 
            case 301: // 150. кондиционал, презенс, коннегатив
                return $stems[4].'iži';
            case 41: // 62. кондиционал, презенс, 1 л., мн.ч., пол. 
                return $stems[4].'ižim';
            case 42: // 63. кондиционал, презенс, 2 л., мн.ч., пол. 
                return $stems[4].'ižit';
            case 43: // 64. кондиционал, презенс, 3 л., мн.ч., пол. 
                return $stems[4]. 'ižiba';
            case 301: // 150. кондиционал, презенс, коннегатив, ед.ч. 
                return $stems[4]. 'iži';
                
            case 44: // 71. кондиционал, имперфект, 1 л., ед.ч., пол. 
                return $stems[3].'nuižin';
            case 45: // 72. кондиционал, имперфект, 2 л., ед.ч., пол. 
                return $stems[3].'nuižid';
            case 46: // 73. кондиционал, имперфект, 3 л., ед.ч., пол. 
            case 302: // 151. кондиционал, презенс, коннегатив
                return $stems[3].'nuiži';
            case 47: // 74. кондиционал, имперфект, 1 л., мн.ч., пол. 
                return $stems[3].'nuižim';
            case 48: // 75. кондиционал, имперфект, 2 л., мн.ч., пол. 
                return $stems[3].'nuižit';
            case 49: // 76. кондиционал, имперфект, 3 л., мн.ч., пол. 
                return $stems[3].'nuižiba';
            case 302: // 152. кондиционал, имперфект, коннегатив, ед.ч. 
                return $stems[3]. 'nuiži';

            case 170: // 131. I инфинитив 
                return $stems[0]. $stems[6]. $stems[7];
            case 171: // 132. II инфинитив, инессив 
                return $stems[0]. $stems[6]. 'es';
            case 172: // 133. II инфинитив, инструктив  
                return $stems[0]. $stems[6]. 'en';
            case 173: // 134. III инфинитив, адессив
                return $stems[5]. 'm'. $stems[7]. 'l';
            case 174: // 135. III инфинитив, иллатив 
                return self::inf3Ill($stems[5], $stems[7]);
            case 175: // 136. III инфинитив, инессив 
                return $stems[5]. 'm'. $stems[7]. 's';
            case 176: // 137. III инфинитив, элатив 
                return $stems[5]. 'm'. $stems[7]. 'späi';
            case 177: // 138. III инфинитив, абессив 
                return $stems[5]. 'm'. $stems[7]. 't';
                
            case 178: // 139. актив, 1-е причастие 
                return self::partic1active($stems[1]);
            case 179: // 140. актив, 2-е причастие 
                return $stems[5]. 'nu';
            case 180: // 142. пассив, 1-е причастие 
                return '';
            case 181: // 143. пассив, 2-е причастие 
                return $stems[0]. $stems[6]. 'ud';

            case 70: // 7. индикатив, презенс, 1 л., ед.ч., отриц. 
            case 71: // 8. индикатив, презенс, 2 л., ед.ч., отриц. 
            case 72: // 9. индикатив, презенс, 3 л., ед.ч., отриц. 
                return self::negativeForm($gramset_id, $lang_id). $stems[1];
            case 73: //10. индикатив, презенс, 1 л., мн.ч., отриц. 
            case 78: // 11. индикатив, презенс, 2 л., мн.ч., отриц. 
            case 79: // 12. индикатив, презенс, 3 л., мн.ч., отриц. 
                return self::negativeForm($gramset_id, $lang_id). $stems[0]. $g. 'oi';

            case 80: // 19. индикатив, имперфект, 1 л., ед.ч., отриц. 
            case 81: // 20. индикатив, имперфект, 2 л., ед.ч., отриц. 
            case 82: // 21. индикатив, имперфект, 3 л., ед.ч., отриц. 
                return self::negativeForm($gramset_id, $lang_id). $stems[1]. 'nd';
            case 83: // 22. индикатив, имперфект, 1 л., мн.ч., отриц. 
            case 84: // 23. индикатив, имперфект, 2 л., мн.ч., отриц. 
            case 85: // 24. индикатив, имперфект, 3 л., мн.ч., отриц. 
                return self::negativeForm($gramset_id, $lang_id). $stems[3]. 'nugoi';
/*
            case 86: // 25. индикатив, перфект, 1 л., ед.ч., пол. 
            case 87: // 26. индикатив, перфект, 2 л., ед.ч., пол. 
            case 88: // 27. индикатив, перфект, 3 л., ед.ч., пол. 
                return self::auxForm(88, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id);
            case 89: // 28. индикатив, перфект, 1 л., мн.ч., пол. 
            case 90: // 29. индикатив, перфект, 2 л., мн.ч., пол. 
            case 91: // 30. индикатив, перфект, 3 л., мн.ч., пол. 
                return self::auxForm(91, $lang_id, $dialect_id). $stems[7]. self::garmVowel($stems[7],'u');

            case 92: // 31. индикатив, перфект, 1 л., ед.ч., отриц. 
            case 93: // 32. индикатив, перфект, 2 л., ед.ч., отриц. 
            case 94: // 33. индикатив, перфект, 3 л., ед.ч., отриц. 
                return self::auxForm(94, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id);
            case 95: // 34. индикатив, перфект, 1 л., мн.ч., отриц. 
            case 96: // 35. индикатив, перфект, 2 л., мн.ч., отриц. 
            case 97: // 36. индикатив, перфект, 3 л., мн.ч., отриц. 
                return 'ei ole './/self::auxForm(97, $lang_id, $dialect_id). 
                       $stems[7]. self::garmVowel($stems[7],'u');

            case 98: // 37. индикатив, плюсквамперфект, 1 л., ед.ч., пол. 
            case 99: // 38. индикатив, плюсквамперфект, 2 л., ед.ч., пол. 
            case 100: // 39. индикатив, плюсквамперфект, 3 л., ед.ч., пол. 
                return self::auxForm(100, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id);
            case 101: // 40. индикатив, плюсквамперфект, 1 л., мн.ч., пол. 
            case 102: // 41. индикатив, плюсквамперфект, 2 л., мн.ч., пол. 
            case 103: // 42. индикатив, плюсквамперфект, 3 л., мн.ч., пол. 
                return self::auxForm(103, $lang_id, $dialect_id). $stems[7]. self::garmVowel($stems[7],'u');

            case 104: // 43. индикатив, плюсквамперфект, 1 л., ед.ч., отриц. 
            case 105: // 44. индикатив, плюсквамперфект, 2 л., ед.ч., отриц. 
            case 106: // 45. индикатив, плюсквамперфект, 3 л., ед.ч., отриц. 
                return self::auxForm(106, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id);
            case 107: // 46. индикатив, плюсквамперфект, 1 л., мн.ч., отриц. 
            case 108: // 47. индикатив, плюсквамперфект, 2 л., мн.ч., отриц. 
            case 109: // 48. индикатив, плюсквамперфект, 3 л., мн.ч., отриц. 
                return 'ei oldu '. $stems[7]. self::garmVowel($stems[7],'u'); //self::auxForm(109, $lang_id, $dialect_id)
*/
            case 50: // 54. императив, 2 л., ед.ч., отр. 
            case 74: // 55. императив, 3 л., ед.ч., отр. 
                return self::negativeForm($gramset_id, $lang_id). $stems[1];
            case 76: // 57. императив, 2 л., мн.ч., отр. 
            case 77: // 58. императив, 3 л., мн.ч., отр. 
                return self::negativeForm($gramset_id, $lang_id). $stems[0]. $g. 'oi';

            case 303: // 151. кондиционал, презенс, коннегатив, мн.ч. 
                return $stems[4]. 'ižigoi';
            case 110: // 65. кондиционал, презенс, 1 л., ед.ч., отр. 
            case 111: // 66. кондиционал, презенс, 1 л., ед.ч., отр. 
            case 112: // 67. кондиционал, презенс, 1 л., ед.ч., отр. 
                return self::negativeForm($gramset_id, $lang_id). $stems[4]. 'iži';
            case 113: // 68. кондиционал, презенс, 1 л., мн.ч., отр. 
            case 114: // 69. кондиционал, презенс, 1 л., мн.ч., отр. 
            case 115: // 70. кондиционал, презенс, 1 л., мн.ч., отр. 
                return self::negativeForm($gramset_id, $lang_id). $stems[4]. 'ižigoi';
 
            case 304: // 152. кондиционал, имперфект, коннегатив, мн.ч. 
                return $stems[3]. 'nuižigoi';
            case 116: // 77. кондиционал, имперфект, 1 л., ед.ч., отр. 
            case 117: // 78. кондиционал, имперфект, 1 л., ед.ч., отр. 
            case 118: // 79. кондиционал, имперфект, 1 л., ед.ч., отр. 
                return self::negativeForm($gramset_id, $lang_id). $stems[3]. 'nuiži';
            case 119: // 80. кондиционал, имперфект, 1 л., ед.ч., отр. 
            case 120: // 81. кондиционал, имперфект, 1 л., ед.ч., отр. 
            case 121: // 82. кондиционал, имперфект, 1 л., ед.ч., отр. 
                return self::negativeForm($gramset_id, $lang_id). $stems[3]. 'nuižigoi';
                
/*            кондиционал, перфект
 
            case 135: // 95. кондиционал, плюсквамперфект, 1 л., ед.ч., пол. 
            case 125: // 96. кондиционал, плюсквамперфект, 2 л., ед.ч., пол. 
            case 136: // 97. кондиционал, плюсквамперфект, 3 л., ед.ч., пол. 
                return 'olis’ '. self::perfectForm($stems[5], $lang_id);
            case 137: // 98. кондиционал, плюсквамперфект, 1 л., мн.ч., пол. 
            case 138: // 99. кондиционал, плюсквамперфект, 2 л., мн.ч., пол. 
            case 139: // 100. кондиционал, плюсквамперфект, 3 л., мн.ч., пол. 
                return 'olis’ '. $stems[7] . self::garmVowel($stems[7],'u');
                
            case 140: // 101. кондиционал, плюсквамперфект, 1 л., ед.ч., отр. 
            case 141: // 102. кондиционал, плюсквамперфект, 2 л., ед.ч., отр. 
            case 142: // 103. кондиционал, плюсквамперфект, 3 л., ед.ч., отр. 
                return 'ei olis’ '. self::perfectForm($stems[5], $lang_id);
            case 143: // 104. кондиционал, плюсквамперфект, 1 л., мн.ч., отр. 
            case 144: // 105. кондиционал, плюсквамперфект, 2 л., мн.ч., отр. 
            case 145: // 106. кондиционал, плюсквамперфект, 3 л., мн.ч., отр. 
                return 'ei olis’ '. $stems[7] . self::garmVowel($stems[7],'u');
*/                
        }
        return '';
    }

    public static function isConsonant($letter) {
        $consonants = ['p', 't', 'k', 's', 'h', 'j', 'v', 'l', 'r', 'm', 'n', 'č', 'd'];
        if (in_array($letter, $consonants)) {
            return true;
        } 
        return false;
    }
    
    public static function isVowel($letter) {
        $vowels = ['i', 'y', 'u', 'e', 'ö', 'o', 'ä', 'a'];
        if (in_array($letter, $vowels)) {
            return true;
        } 
        return false;
    }
    
    /**
     * Is exists back vowels in the word
     * @param String $word
     * @return Boolean
     */
    public static function isBackVowels($word) {
        if (preg_match("/[aou]/u", $word)) { 
            return true;
        }
        return false;
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

        $word = str_replace('ü','y',$word);
        $word = str_replace('Ü','Y',$word);
        
        if (self::isBackVowels($word)) { 
            $word = str_replace('w','u',$word);
            $word = str_replace('W','U',$word);            
        } else {
            $word = str_replace('w','y',$word);
            $word = str_replace('W','Y',$word);            
        }
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
    
    /**
     * Indicative, Presence, 3rd Person, Singular, Positive
     * 
     * основа 2 + u / y (при этом, если основа 2 заканчивается на одиночный e 
     * (т.е. любой согласный + e) то e переходит в o / ö: Ce > Co / Cö1)
     * 
     * @param String $stem 2nd stem
     */

    public static function indPres1SingByStem($stem) {
//        if (mb_substr($stem, -1, 1) == 'e' && self::isConsonant(mb_substr($stem, -2, 1))) {
        $is_backV = self::isBackVowels($stem);
        if (preg_match("/^(.+)(.)e$/u", $stem, $regs) && self::isConsonant($regs[2])) {
            $stem = $regs[1].$regs[2].($is_backV ? 'o': 'ö');
        }
        
        return $stem . ($is_backV ? 'u': 'y');
    }
    
    /**
     * 16. индикатив, имперфект, 1 л., мн.ч., положительная форма 
     * 
     * основа 4 + ma / mä (если основа 4 заканчивается согласный и гласный: СV) + mma / mmä (если основа 4 заканчивается два гласных: VV)
     * 
     * @param String $stem
     */
    public static function indImp1PlurByStem($stem) {
        $last_let = mb_substr($stem, -1, 1);
        if (!self::isVowel($last_let)) {
            return '';
        }
        $stem_a = (self::isBackVowels($stem) ? 'a': 'ä');
        $before_last_let = mb_substr($stem, -2, 1);
        if (self::isConsonant($before_last_let)) {
            return $stem.'m'.$stem_a;             
        } else {
            return $stem.'mm'.$stem_a;             
        }
    }
    
    /**
     * 17. индикатив, имперфект, 2 л., мн.ч., пол.
     * 
     * основа 4 + ja / jä (если основа 4 заканчивается согласный и гласный: СV) + tta / ttä (если основа 4 заканчивается два гласных: VV)
     * 
     * @param String $stem
     */
    public static function indImp2PlurByStem($stem) {
        $last_let = mb_substr($stem, -1, 1);
        if (!self::isVowel($last_let)) {
            return '';
        }
        $stem_a = (self::isBackVowels($stem) ? 'a': 'ä');
        $before_last_let = mb_substr($stem, -2, 1);
        if (self::isConsonant($before_last_let)) {
            return $stem.'j'.$stem_a;             
        } else {
            return $stem.'tt'.$stem_a;             
        }
    }
    
    /**
     * 50. императив, 3 л., ед.ч., пол
     * 
     * основа 5 + kkah / kkäh (если основа 5 оканчивается на одиночный гласный (т.е. любой согласный + любой гласный): СV)
     * + gah / gäh (если основа 5 оканчивается на дифтонг (т.е. два гласных> VV) или согласные l, n, r)
     * + kah / käh (если основа 5 оканчивается на s, š)
     * + kah / käh (если основа 5 оканчивается на n, а начальная форма заканчивается на ta / tä, при этом конечный согласный n основы 7 переходит в k: n > k)

     * 
     * @param String $stem 2nd stem
     */

    public static function imp3SingPolByStem($stem, $lemma, $dialect_id) {
        $last_let = mb_substr($stem, -1, 1);
        $before_last_let = mb_substr($stem, -2, 1);
        $stem_a = (self::isBackVowels($stem) ? 'a': 'ä');

        if (self::isConsonant($before_last_let) && self::isVowel($last_let)) {
            return $stem. 'kk'. $stem_a. 'h';
        } elseif ($last_let=='n' && preg_match("/t[aä]$/u", $lemma)) {
//            return preg_replace("/^(.+)(n)$/u", "\1k", $stem). 'k'. $stem_a. 'h';
            return mb_substr($stem, 0, -1). 'kk'. $stem_a. 'h';
        } elseif (self::isVowel($before_last_let) && self::isVowel($last_let) 
                || in_array($last_let, ['l', 'n', 'r'])) {
            return $stem. 'g'. $stem_a. 'h';
        } elseif (in_array($last_let, ['s', 'š'])) {
            return $stem. 'k'. $stem_a. 'h';
        }
        return $stem;
    }
    
    /**
     * 52. императив, 2 л., мн.ч., пол
     * 
     * основа 5 + kkua / kkiä (если основа 5 оканчивается на одиночный гласный (т.е. любой согласный + любой гласный): CV)
     * + gua / giä (если основа 5 оканчивается на дифтонг (т.е. два гласных: VV) или согласные l, n, r)
     * + kua / kiä (если основа 5 оканчивается на s, š)
     * + kua / kiä (если основа 5 оканчивается на n, а начальная форма заканчивается на ta / tä, при этом конечный согласный n основы 7 переходит в k: n > k)

     * 
     * @param String $stem 2nd stem
     */

    public static function imp2PlurPolByStem($stem, $lemma, $dialect_id) {
        $last_let = mb_substr($stem, -1, 1);
        $before_last_let = mb_substr($stem, -2, 1);
        $stem_ua = (self::isBackVowels($stem) ? 'ua': 'iä');

        if (self::isConsonant($before_last_let) && self::isVowel($last_let)) {
            return $stem. 'kk'. $stem_ua;
        } elseif (in_array($last_let, ['s', 'š'])) {
            return $stem. 'k'. $stem_ua;
        } elseif ($last_let=='n' && preg_match("/t[aä]$/u", $lemma)) {
            return mb_substr($stem, 0, -1). 'kk'. $stem_ua;
        } elseif (self::isVowel($before_last_let) && self::isVowel($last_let)
                || in_array($last_let, ['l', 'n', 'r'])) {
            return $stem. 'g'. $stem_ua;
        }
    }
    
    /**
     * 73. кондиционал, имперфект, 3 л., ед.ч., пол
     * 
     * основа 4 + s’ (если основа 4 заканчивается на i)
     * + is’ (если основа 4 НЕ заканчивается на i, при этом, 
     * если начальная форма заканчивается на ua / iä, 
     * то последний гласный основы 4 o или i меняется на a / ä: o > a, i > a / ä)
     * 
     * @param String $stem 2nd stem
     */

    public static function condImp3SingPolByStem($stem, $lemma, $dialect_id) {
//dd("$stem, $lemma");
        if (preg_match("/^(.+)i$/u",$stem, $regs)) {
            if (preg_match("/(ua|iä)$/u",$lemma)) {
                return $regs[1]. self::garmVowel($lemma,'a'). 'is’';
            }            
            return $stem. 's’';
        }
        if (preg_match("/(ua|iä)$/u",$lemma) && preg_match("/^(.+)o$/u", $stem, $regs)) {
            $stem = $regs[1]. 'a';
        }
        return $stem. 'is’';
    }
    
    /**
     * 132. II инфинитив, инессив 
     * начальная форма + s’s’a / ssä (если начальная форма заканчивается на дифтонг (т.е. два гласных): VV)
     * + šša / ššä (если начальная форма заканчивается на согласный + a / ä: Ca / Cä, при этом a / ä переходит в e: a > e, ä > e)     
     * 
     * @param String $lemma
     */
    public static function inf2Ines($lemma) {
        $last_let = mb_substr($lemma, -1, 1);
        $before_last_let = mb_substr($lemma, -2, 1);
        
        if (self::isVowel($before_last_let) && self::isVowel($last_let)) {
            return $lemma. (self::isBackVowels($lemma) ? 's’s’a': 'ssä');
        } elseif (self::isConsonant($before_last_let) && preg_match("/^(.+)[aä]$/u", $lemma, $regs)) {
            return $regs[1]. 'ešš'. self::garmVowel($lemma,'a');
        }
        return $lemma;
    }
    
    /**
     * 133. II инфинитив, инструктив  
     * начальная форма + n (при этом, если начальная форма заканчивается на согласный + a / ä: Ca / Cä, то a / ä переходит в e: a > e, ä > e)
     * 
     * @param String $lemma
     */
    public static function inf2Inst($lemma) {
        $last_let = mb_substr($lemma, -1, 1);
        $before_last_let = mb_substr($lemma, -2, 1);
        
        if (self::isConsonant($before_last_let) && preg_match("/^(.+)[aä]$/u", $lemma, $regs)) {
            $lemma = $regs[1]. 'e';
        }
        return $lemma. 'n';
    }
    
    /**
     * 135. III инфинитив, иллатив   
     * основа 5 + mh + a/ä (если основа 5 оканчивается на Vi, и это единственные гласные в основе 5)
     *          + m + a/ä + h + a/ä (если основа 5 оканчивается на C)
     * 
     * @param String $lemma
     */
    public static function inf3Ill($lemma, $harmony) {
        if (preg_match("/^[^aeiouüäö-][aeiouüäö]i?$/u", $lemma)) {
            return $lemma. 'mh'. $harmony;
        } elseif (preg_match("/[^aeiouüäö]$/u", $lemma)) {
//var_dump($lemma);        
            return $lemma. 'm'. $harmony. 'h'. $harmony;
        }
        return '';
    }
    
    /**
     * stemModify($stems[4], $stems[0], "ua|iä", ['o'=>'a', 'i'=> ['a','ä']])
     * если $lemma заканчивается на $lemma_okon, то последний гласный $stem меняется на $replacements
     * 
     * @param String $stem 
     * @param String $lemma  
     * @param String $lemma_okon  template for matching
     * @param Array $replacements [<letter1>=><replacement>, <letter2>=>[<back vowel>, <front vowel>]] 
     */

    public static function stemModify($stem, $lemma, $lemma_okon, $replacements) {
        if (!preg_match("/(".$lemma_okon.")$/u",$lemma)) {
            return $stem;
        }
        
        foreach ($replacements as $stem_okon => $replacement) {
            if (preg_match("/^(.+)(".$stem_okon.")$/u", $stem, $regs)) {
                if (is_array($replacement)) {
                    return $regs[1]. (self::isBackVowels($stem) ? $replacement[0]: $replacement[1]);
                } else {
                    return $regs[1]. $replacement;
                }
            }
        }
        return $stem;
    }

    /**
     * 141. актив, 2-е причастие  (сокращенная форма); перфект (форма основного глагола)
     * 
     * основа 5 + n (если основа 5 заканчивается на согласный + гласный: СV)
     * + nun (если основа 5 заканчивается на дифтонг (два гласных подряд: VV) или n)
     * + lun (если основа 5 заканчивается на l)
     * + run (если основа 5 заканчивается на r)
     * + sun (если основа 5 заканчивается на s)
     * + šun (если основа 5 заканчивается на š)
     * 
     * @param String $stem
     */
    public static function perfectForm($stem, $lang_id) {
        $last_let = mb_substr($stem, -1, 1);
        $before_last_let = mb_substr($stem, -2, 1);
        if (self::isConsonant($before_last_let) && self::isVowel($last_let)) {
            return $stem. 'n';
        } elseif (self::isVowel($before_last_let) && self::isVowel($last_let)) {
            return $stem. self::garmVowel($stem, 'nun');
        } elseif (in_array($last_let, ['n', 'l', 'r', 's', 'š'])) {
            return $stem. $last_let. self::garmVowel($stem, 'un');
        }
    }
    
    /**
     * 139. актив, 1-е причастие (Veps)
     * 
     * основа 1 (если основа 1 оканчивается на Vi)
     *         + i (при этом если основа 1 оканчивается на Ce, у основы 1 последняя e заменяется на i)
     * 
     * @param String $stem
     */
    public static function partic1active($stem) {
        if (preg_match("/[aeiouüäö]i$/u", $stem)) {
            return $stem;
        } else {
            if (preg_match("/^(.*[aeiouüäö-][^aeiouüäö]+)e$/u", $stem, $regs)) {
                $stem = $regs[1];
            }
            return $stem. 'i';
        }
    }
    
    /**
     * 140. актив, 2-е причастие (karelian proper)
     * 
     * основа 5 + nnun (если основа 5 заканчивается на согласный + гласный: СV)
     * + nun (если основа 5 заканчивается на дифтонг (два гласных подряд: VV) или n)
     * + lun (если основа 5 заканчивается на l)
     * + run (если основа 5 заканчивается на r)
     * + sun (если основа 5 заканчивается на s)
     * + šun (если основа 5 заканчивается на š)
     * 
     * @param String $stem
     */
    public static function partic2active($stem, $lang_id) {
        $last_let = mb_substr($stem, -1, 1);
        $before_last_let = mb_substr($stem, -2, 1);
        if (self::isConsonant($before_last_let) && self::isVowel($last_let)) {
            return $stem. 'nnun';
        } elseif (self::isVowel($before_last_let) && self::isVowel($last_let)) {
            return $stem. 'nun';
        } elseif (in_array($last_let, ['n', 'l', 'r', 's', 'š'])) {
            return $stem. $last_let. 'un';
        }
    }
    
    public static function auxForm($gramset_id, $lang_id, $dialect_id) {
        if ($lang_id != 4) {
            return '';
        }
        $lemma = 'olla';
        $aux_lemma = Lemma::where('lang_id', $lang_id)->whereLemma($lemma)
                          ->where('pos_id',PartOfSpeech::getIDByCode('VERB'))->first();
        if (!$aux_lemma) {
            return '';
        }
        $gramset = Gramset::find($gramset_id);
        if (!$gramset) {
            return '';
        }

        if ($gramset->gram_id_tense == 26) { // perfect
            $aux_tense = 24; // present
        } elseif ($gramset->gram_id_tense == 49) { // pluperfect
            $aux_tense = 25; // imperfect
        } else {
            return '';
        }

        $aux_number = $gramset->gram_id_number;
/*        if ($gramset->gram_id_person==23 && $gramset->gram_id_number==2) { // perfect, 3rd, plural //  && $gramset->gram_id_tense != 49
            $aux_number = 1; // singular
        } */
        $aux_gramset = Gramset::where('gram_id_mood', $gramset->gram_id_mood)
                              ->where('gram_id_person', $gramset->gram_id_person)
                              ->where('gram_id_number', $aux_number)
                              ->where('gram_id_negation', $gramset->gram_id_negation)
                              ->where('gram_id_tense', $aux_tense)->first();
        if (!$aux_gramset) {
            return '';
        }
//dd($aux_gramset->id);        
        $aux_wordform = $aux_lemma->wordforms()
                ->wherePivot('dialect_id', $dialect_id)
                ->wherePivot('gramset_id', $aux_gramset->id)->first();
//dd($aux_wordform);        
        if (!$aux_wordform) {
            return '';
        }
        return $aux_wordform->wordform. ' ';
    }
    
    /**
     * 
     * @param String $stem
     * @param String $affix
     * @param Int $lang_id
     * @param Int $dialect_id
     */
    public static function potencialForm($stem, $affix, $lang_id, $dialect_id) {
        $last_let = mb_substr($stem, -1, 1);
        if (self::isVowel($last_let)) {
            return $stem. 'nn'.$affix;
        } elseif (in_array($last_let, ['n', 'l', 'r', 's', 'š'])) {
            return $stem. $last_let. $affix;
        }
    }
    
    public static function garmVowel($stem, $vowel) {
        if (!$vowel) {
            return '';
        }
        $frontVowels = ['a'=>'ä', 'o'=>'ö', 'u'=>'y'];
        if (self::isBackVowels($stem)) {
            return $vowel;
        }
        $vowels = preg_split("//", $vowel);
        $new_vowels = '';
        foreach ($vowels as $v) {
            if (isset($frontVowels[$v])) {
                $new_vowels .= $frontVowels[$v];
            } else {
                $new_vowels .= $v;
            }
        } 
        return $new_vowels;
    }
    
    /**
     * Если $stem заканчивается на одиночный $vowel 
     * т.е. любой согласный + $vowel, то $vowel переходит в $replacement
     * @param String $stem
     * @param String $vowel - one char
     * @param String $replacement - one char
     */
    public static function replaceSingVowel($stem, $vowel, $replacement) {
        $before_last_let = mb_substr($stem, -2, 1);
        if (self::isConsonant($before_last_let) && preg_match("/^(.+)".$vowel."$/u", $stem, $regs)) {
            return $regs[1].$replacement;
        }
        return $stem;
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
}
