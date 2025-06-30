<?php

namespace App\Library\Grammatic;

use App\Library\Grammatic;
use App\Library\Grammatic\KarGram;
use App\Library\Grammatic\KarNameOlo;

use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;

/**
 * Functions related to Karelian grammatic for nominals: nouns, adjectives, numerals and pronouns.
 */
class KarName
{
    /**
     * 0 = nominativ sg
     * 1 = base of genetive sg (genetive sg - 'n')
     * 2 = base of illative sg (illative sg - 'h')
     * 3 = partitive sg
     * 4 = base of genetive pl (genetive pl - 'n')
     * 5 = base of illative pl (illative pl - 'h')

     * @param Lemma $lemma
     * @param Int $dialect_id
     * @return array
     */
    public static function stemsFromDB($lemma, $dialect_id) {
        for ($i=0; $i<6; $i++) {
            $stems[$i] = self::getStemFromWordform($lemma, $i, $dialect_id);;
        }
        return $stems;
    }
    
    public static function getStemFromStems($stems, $stem_n, $dialect_id) {
        switch ($stem_n) {
            case 2: 
                return isset($stems[0]) && isset($stems[1]) && $stems[1] && isset($stems[3]) && $stems[3] 
                    ?  self::illSgBase($stems[0],$stems[1],$stems[3]) : '';
            case 4: 
                return isset($stems[1]) && isset($stems[5]) && isset($stems[10]) ?  self::genPlBase($stems[1], $stems[5], $stems[10]) : '';
            default: 
                return null;
        }
    }
    
    public static function getStemFromWordform($lemma, $stem_n, $dialect_id) {
        switch ($stem_n) {
            case 0: 
                return $lemma->lemma;
            case 1:  //genetive sg
                if (preg_match("/^(.+)n$/", $lemma->wordform(3, $dialect_id), $regs)) {
                    return $regs[1];
                }
                return '';
            case 2: //illative sg
                if (preg_match("/^(.+)h$/", $lemma->wordform(10, $dialect_id), $regs)) { 
                    return $regs[1];
                }
                return '';
            case 3: // partitive sg
                $part_sg = $lemma->wordform(4, $dialect_id); 
                return $part_sg ? $part_sg : '';
            case 4: //genetive pl
                return self::getStem4FromWordform($lemma, $dialect_id);
            case 5: // illative pl (tver) OR partitive pl (olo)
                return self::getStem5FromWordform($lemma, $dialect_id);
            case 6: // partitive sg base
                return self::getStem6FromWordform($lemma, $dialect_id);
        }
    }
    
    public static function getStem4FromWordform($lemma, $dialect_id) {
        $genPls = preg_split('/,/', $lemma->wordform(24, $dialect_id));
        $stems4=[];
        foreach ($genPls as $g) {
            if ($lemma->lang_id == 4) {
                if (preg_match("/^(.+)n$/", trim($g), $regs)) { 
                    $stems4[] = $regs[1];
                }            
            } elseif ($lemma->lang_id == 6) {
                if (preg_match("/^(.+)den$/", trim($g), $regs)) { 
                    $stems4[] = $regs[1];
                }            
            } else {
                if (preg_match("/^(.+?)e??n$/u", trim($g), $regs)) {
                    $stems4[] = $regs[1];
                }
            }
        }
        sort($stems4);
        return join('/', $stems4);
    }
    
    public static function getStem5FromWordform($lemma, $dialect_id) {
        if ($lemma->lang_id == 4) { // illative pl (tver)
            if (preg_match("/^(.+)h$/", $lemma->wordform(61, $dialect_id), $regs)) { 
                return $regs[1];
            }            
        } else { // partitive pl
            $partPls = preg_split('/,/', $lemma->wordform(22, $dialect_id));
            $stems5=[];
//dd($lemma->wordform(22, $dialect_id));            
            foreach ($partPls as $p) {
                if ($lemma->lang_id == 6) {
                    if (preg_match("/^(.+)d$/", trim($p), $regs)) { 
                        $stems5[] = $regs[1];
                    }            
                } else {                
                    $stems5[] = preg_replace('/ii$/', 'i', trim($p));            
                }
            }
            sort($stems5);
            return join('/', $stems5);
        }
        return '';
    }
    
    public static function getStem6FromWordform($lemma, $dialect_id) {
        if ($lemma->lang_id != 6) {
            return null;
        }
        $part_sg = $lemma->wordform(4, $dialect_id); 
        if (preg_match("/^(.+)d$/", $part_sg, $regs) || preg_match("/^(.+)te$/", $part_sg, $regs)) { 
            return $regs[1];
        }
        return '';
    }
    
    public static function gramsetListSg($lang_id) {
        if ($lang_id==5) {
            return KarNameOlo::gramsetListSg();
        } elseif ($lang_id==6) {
            return KarNameLud::gramsetListSg();
        }
        return [1,  56, 3,  4, 277,  5,    8,  9, 10, 278, 12,  6, 14, 15, 17];
    }

    public static function gramsetListPl($lang_id) {
        if ($lang_id==5) {
            return KarNameOlo::gramsetListPl();
        } elseif ($lang_id==6) {
            return KarNameLud::gramsetListPl();
        }
        return [2, 57, 24, 22, 279, 59,     23, 60, 61, 280, 62, 64, 65, 66, 281, 18];
    }

    public static function getListForAutoComplete($lang_id) {
        return array_merge(self::gramsetListSg($lang_id), self::gramsetListPl($lang_id));
    }
        
    public static function stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id=null) {      
        $base_shab = "([^\s\(\]\|]+)";
        $base_suff_shab = "([^\s\(\]\|]*)";
//        $okon1_shab = "(-?[^\-\,\;\)]+)";
        $okon3_shab = "(-?[^\-\,\;\)]+?\/?-?[^\-\,\;\)]*)";
//        $lemma_okon1_shab = "/^".$base_shab."\|?".$base_suff_shab."\s*\(".$okon1_shab;
        $lemma_okon3_shab = "/^".$base_shab."\|?".$base_suff_shab."\s*\(".$okon3_shab;
        
        // mini template
        if (preg_match("/^".$base_shab."\|?".$base_suff_shab."\s*\[([^\]]*)\]/", $template, $regs)) {
            return self::stemsFromMiniTemplate($lang_id, $pos_id, $regs, $name_num, $dialect_id);
        // only plural
        } elseif ($name_num == 'pl' && preg_match($lemma_okon3_shab.",\s*".$okon3_shab."\)/", $template, $regs)) {
//            $name_num = 'pl';
            return  self::stemsPlFromTemplate($regs);
        // only single
        } elseif ($name_num == 'sg' && preg_match($lemma_okon3_shab.",\s*".$okon3_shab."\)/", $template, $regs)) {
            return  self::stemsSgFromTemplate($regs);
        // others
        } elseif (preg_match($lemma_okon3_shab.",\s*".$okon3_shab.";\s*".$okon3_shab."\)/", $template, $regs)) {
            $name_num = '';
            return self::stemsOthersFromTemplate($regs);
        } else {
            return Grammatic::getAffixFromtemplate($template, $name_num);
        }
    }

    /**
     * mua []
     * nuor|i [e, ]
     * lyhy|t [ö, t]
     * ve|si [je/te, t]
     * 
     * @param type $regs = [0=>template, 1=>base, 2=>base-suff, 3=>list_of_pseudostems]
     */
    public static function stemsFromMiniTemplate($lang_id, $pos_id, $regs, $name_num, $dialect_id) {
//dd($regs);    
        $regs[1] = preg_replace('/\_/',' ',$regs[1]);
        if (preg_match("/^(.+)ǁ(.+)$/", $regs[1], $sword)) {
            $fword = preg_replace('/ǁ/','',$sword[1]);
            $base = $sword[2];
        } else {
            $fword = '';
            $base = $regs[1];
        }
//        $base = preg_replace('/ǁ/','',$regs[1]);
        $stem0 = $base.$regs[2];
        $out = [[$fword.$stem0], null, $regs[0], null];
        $ps_list = preg_split("/\s*,\s*/", $regs[3]);
        $harmony = KarGram::isBackVowels($stem0); // harmony
        $stem0_syll=KarGram::countSyllable($stem0);

        if ($lang_id==6) {
// $ok = $regs[2];            
            list ($stem1, $stem2, $stem6) = KarNameLud::initialStemsFromMiniTemplate($base, $regs[2], $stem0, $ps_list);
            $stem3 = KarNameLud::stem3FromMiniTemplate($stem6);
            $stem4 = KarNameLud::stemPlFromMiniTemplate($stem0, $stem1, $stem6, $harmony, $pos_id);
            $stem5 = KarNameLud::stemPlFromMiniTemplate($stem0, $stem2, $stem6, $harmony, $pos_id);            
        } else {
            list ($stem1, $stem6, $ps1) = self::stems1And6FromMiniTemplate($base, $stem0, $ps_list);   
            if (!$stem6) {
                return $out;
            }
            $stem2 = self::stem2FromMiniTemplate($base, $stem1, $stem6, isset($ps1[1]) ? $ps1[1]: null);
            $stem3 = self::stem3FromMiniTemplate($stem6, $lang_id, $harmony, $dialect_id);
            $stem5 = self::stem5FromMiniTemplate($stem0, $stem1, $stem6, $lang_id, $pos_id, $harmony, $stem0_syll, $dialect_id);
            $stem4 = self::stem4FromMiniTemplate($stem1, $stem5, $stem6, $harmony, $dialect_id);
        }
        $stems = [0 => $fword.$stem0,
                  1 => $fword.$stem1, // single genetive base 
                  2 => $fword.$stem2, // single illative base
                  3 => $fword.$stem3,
                  4 => $fword.$stem4,
                  5 => $fword.$stem5,
                  6 => $fword.$stem6,
                  10 => $harmony
            ];
//dd($stems, $stem0_syll, $regs[1].$regs[2]);        
        return [$stems, $name_num, $regs[1], $regs[2]];
    }
    
    public static function stems1And6FromMiniTemplate($base, $stem0, $ps_list) {
        if (!sizeof($ps_list)) { // mua []
            $stem1 = $stem6 = $stem0;
            $ps1=null;
        } else {
            $V = "[".KarGram::vowelSet()."]";
            $ps1 = preg_split("/\s*\/\s*/", $ps_list[0]);
            $stem1 = $base.$ps1[0];
//dd($ps_list);            
            if (isset($ps_list[1])) {
                $stem6 = $base.$ps_list[1];
            } elseif(preg_match("/^(.+)".$V."$/", $stem0, $regs0) 
                    && preg_match("/(".$V.")$/", $stem1, $regs1)) {
                $stem6 = $regs0[1]. $regs1[1];
            } else {
                $stem6 = null;
            }
        }
        return [$stem1, $stem6, $ps1];
    }
    
    public static function stem2FromMiniTemplate($base, $stem1, $stem6, $ps1_2) {
        $V = "[".KarGram::vowelSet()."]";
        if (preg_match("/".$V."$/", $stem6)) {
            return $stem6;
        } elseif ($ps1_2) {
            return $base.$ps1_2;
        } else {
            return $stem1;
        }
        
    }
    
    // partitive sg
    public static function stem3FromMiniTemplate($stem6, $lang_id, $harmony, $dialect_id) {
        $C = "[".KarGram::consSet()."]";
        $V = "[".KarGram::vowelSet()."]";
        if (preg_match("/^(.+".$C.")(".$V.")$/u", $stem6, $regs)) { // 1)
            if (in_array($regs[2], ['a', 'o'])) {
                return $regs[1].'u'.$regs[2];
            } elseif (in_array($regs[2], ['y', 'ö'])) {
                return $regs[1].'yö';
            } elseif ($regs[2] =='u') {
                return $regs[1].'uo';
            } elseif ($regs[2]=='ä' && $lang_id==5) { // livvic
                return $regs[1].'iä';
            } elseif ($regs[2]=='ä') { // proper
                return $regs[1].'yä';
            } elseif ($regs[2]== 'e') {
                return $regs[1].'ie';
            } elseif ($regs[2] =='i' && $lang_id==5) { // livvic
                return $regs[1].'ii';
            } elseif ($regs[2] =='i') { // proper
                return $regs[1].'ie';
            }
        } elseif (preg_match("/".$V.$V."$/u", $stem6)) { // 2)
            if ($lang_id == 5) {
                return $stem6.KarGram::garmVowel($harmony,'du');
            } elseif ($dialect_id == 47) { // tver
                return $stem6.KarGram::garmVowel($harmony,'da');                
            } else {
                return $stem6.KarGram::garmVowel($harmony,'ta');                
            }
        } else { // ending by consonant
            if ($lang_id == 4) { // proper
                if ($dialect_id == 47 && preg_match("/[lnr]$/u", $stem6)) {
                    return $stem6.KarGram::garmVowel($harmony,'da');                    
                } else {
                    return $stem6.KarGram::garmVowel($harmony,'ta');  
                }
            } elseif (preg_match("/[lnr]$/u", $stem6)) {
                return $stem6.KarGram::garmVowel($harmony,'du');
            } elseif (preg_match("/[hst]$/u", $stem6)) {
                return $stem6.KarGram::garmVowel($harmony,'tu');
            }
        }
    }
    
    /**
     * А. Если о.6 заканч. на
     * 0) твер: C[iuyoö] то о.6 + loi~löi;
     * 1) с.к.: C[oö] и в с.ф. 3 слога, то о.6 + i;     (A.3)
     * 2) C[iuy] (А.1) 
     * ИЛИ (C[oö] и (лив. или с.к. и в с.ф. 2 слога)    (А.2)
     * ИЛИ Vi,                                          (А.7)
     * то о.6 + loi~löi;         
     * 3) Ce, то e > i;                                 (А.4)
     * 4) Ca, то                                        (А.5)
     *  4.1) a > i если 
     *      4.1.1) в о.6 два слога и в о.6 в первом слоге есть гласные u, o (в том числе в составе VV: uu, uo, ui, ou, oi) 
     *  ИЛИ 4.1.2) в о.6 более двух слогов и о.6 заканч. на mpa/mba, ma или является прилагательным с о.6. на va.  
     * 4.2) a > oi, если 
     *      4.2.1) в о.6 два слога и в о.6 в первом слоге есть гласные a, e, i (в том числе в составе VV: ai, au, ua, ea, ii, ie, iu, ie, ee, eu); 
     *  ИЛИ 4.2.2) в о.6 более 2-х слогов и о.2 НЕ заканч. на mpa / mba, ma и не является прилагательным с о.6. на va.
     * 5) Cä, то 
     *  5.1) ä > i если 
     *      5.1.1) в с.ф. 2 слога; 
     *  ИЛИ 5.1.2) в с.ф. более 2-х слогов и о.6 заканч. на [mvžsjpb]ä.
     *  5.2) ä > öi если в с.ф. более 2-х слогов и о.6 заканч. на [dtgkčhlnr]ä.
     * 6) VV (кроме Vi), то: с.к.: – первый V → + i; ливв. и твер.: о.6 + loi~löi.
     * 
     * Б. Если о.6 заканч. на C, то о.6 > о.1 → если о.1 заканч. на
     * 1) Ce и с.ф. заканч. на [sšzž]i, то 
     * твер.: =с.ф+loi~löi
     * остальные:  =с.ф;
     * 2) Ce и с.ф. не заканч. на [sšzž]i, то e > i;
     * 3) C[aä], то a, ä > i;
     * 4) VV, то
     * твер: 4.1) UO, то O > zi, 4.3) ie, то  + loi~löi
     * с.к.: 4.1) uo > ui, 4.2) yö > yi, 4.3) ie > ei; 
     * ливв.: 4.1) если о.1 заканч. на uo, yö, то – o, ö → + zi;
     *        4.2) иначе о.6 + loi~löi.
     * 
     * @param string $stem0
     * @param string $stem1
     * @param string $stem6
     * @param int $lang_id
     * @param boolean $harmony
     * @return string
     */
    public static function stem5FromMiniTemplate($stem0, $stem1, $stem6, $lang_id, $pos_id, $harmony, $stem0_syll, $dialect_id) {
        $C = "[".KarGram::consSet()."]";
        $V = "[".KarGram::vowelSet()."]";
        $stem6_syll=KarGram::countSyllable($stem6);
//dd($stem6.KarGram::garmVowel($harmony,'loi'));        
//dd($stem6);        
        if ($dialect_id == 47 && preg_match("/".$C."’?[iuyoö]$/u", $stem6)) { 
            return $stem6.KarGram::garmVowel($harmony,'loi');            
        } elseif ($lang_id == 4 && preg_match("/".$C."’?[oö]$/u", $stem6) && $stem0_syll==3) { // А.1
            return $stem6.'i';
        } elseif (preg_match("/".$C."’?[iuy]$/u", $stem6) // А.2
                || (preg_match("/".$C."’?[oö]$/u", $stem6) && ($lang_id!=4 || $lang_id==4 && $stem0_syll==2)) 
                || preg_match("/".$V."i$/u", $stem6)) {
            return $stem6.KarGram::garmVowel($harmony,'loi');
        } elseif (preg_match("/^(.+".$C.")e$/u", $stem6, $regs)) { // А.3
            return $regs[1].'i';
        } elseif (preg_match("/^(.+".$C.")a$/u", $stem6, $regs)) { // А.4
//dd($stem6,$stem6_syll, preg_match("/^".$C."*".$V."*[aei]/u", $stem6));            
            if (($stem6_syll==2 && preg_match("/^".$C."*".$V."*[uo]/u", $stem6)) // А.4.1.1
                    || ($stem6_syll>2 && (preg_match("/m[pb]?a$/u", $stem6) || $pos_id==1 && preg_match("/va$/u", $stem6)))) { // А.4.1.2
                return $regs[1].'i';                
            } elseif (($stem6_syll==2 && preg_match("/^".$C."*".$V."*[aei]/u", $stem6)) // А.4.2.1
                    || ($stem6_syll>2 && !preg_match("/m[pb]?a$/u", $stem6) && !($pos_id==1 && preg_match("/va$/u", $stem6)))) { // А.4.2.2
                return $regs[1].'oi';                
            }
        } elseif (preg_match("/^(.+".$C.")ä$/u", $stem6, $regs)) { // А.5
            if ($stem6_syll==2 // А.5.1.1
                    || $stem6_syll>2 && preg_match("/[mvžsjpb]ä$/u", $stem6)) { // А.5.1.2
                return $regs[1].'i';                
            } elseif ($stem6_syll>2 && preg_match("/[dtgkčhlnr]ä$/u", $stem6)) { // А.5.2
                return $regs[1].'öi';                
            }
        } elseif (preg_match("/^(.+)".$V."(".$V.")$/u", $stem6, $regs)) { // А.6
            if ($lang_id == 4 && $dialect_id <> 47) {
                return $regs[1].$regs[2].'i';
            } else {
                return $stem6.KarGram::garmVowel($harmony,'loi');
            }
        } elseif (preg_match("/".$C."$/u", $stem6)) { // Б
//            if (preg_match("/^(.+".$C.")e$/u", $stem1, $regs)) {
            if (preg_match("/^(.+)e$/u", $stem1, $regs) && preg_match("/[sšzž]i$/u", $stem0)) { // Б.1
                if ($dialect_id == 47) {
                    return $stem0.KarGram::garmVowel($harmony,'loi');
                } else {
                    return $stem0;
                }
            } elseif (preg_match("/^(.+".$C.")e$/u", $stem1, $regs) && !preg_match("/[sšzž]i$/u", $stem0)) { // Б.2
                return $regs[1].'i';
            } elseif (preg_match("/^(.+".$C.")[aä]$/u", $stem1, $regs)) { // Б.3
                    return $regs[1].'i';
            } elseif (preg_match("/^(.+?)(".$V.")(".$V.")$/u", $stem1, $regs)) { // Б.4
                if ($dialect_id == 47) {
                    if (preg_match("/^(.+?[uy])[oö]$/u", $stem1, $regs)) {
                        return $regs[1].'zi';
                    } elseif (preg_match("/ie$/u", $stem1)) {
                        return $stem1.KarGram::garmVowel($harmony,'loi');
                    }
                } elseif ($lang_id == 4) {
                    switch ($regs[2].$regs[3]) { // Б.4.1
                        case 'uo': return $regs[1].'ui';
                        case 'yö': return $regs[1].'yi';
                        case 'ie': return $regs[1].'ei';
                        default : return $stem1;
                    }
                } else {
                    if (in_array($regs[2].$regs[3], ['uo', 'yö'])) { // Б.4.1
                        return $regs[1].$regs[2].'zi';
                    } else {
                        return $stem6.KarGram::garmVowel($harmony,'loi');
                    }
                }
            }
        }
    }
        
    /**
     * А. Если o.5 заканч. на С[oö]i и о.6 на C[aä] и кол-во слогов в о.5 и о.6 одинаковое, 
     *    то o.5 > о.1 → если о.1 заканч. на
     * 1) С[aä], то a > oi, ä > öi;
     * 2) ua, то ua > avoi
     * 3) iä, то iä > ävöi.
     * 
     * Б. (не для твер.) Если o.5 заканч. на С[oöuy]i  и о.6 на С[oöuy]  и кол-во слогов в o.5 и о.6 одинаковое, 
     *    то о.1+i.
     * 
     * В. Если o.5 заканч. на l[oö]i (для тверского на С[oö]i) и кол-во слогов в o.5 больше чем в о.6, 
     *    то =o.5.
     * 
     * Г. (не для твер.) Если o.5 заканч. на Vi и о.6 заканч. на VV, 
     *    то =o.5.
     * 
     * Д. Если o.5 заканч. на Ci и перед конечным i есть kk, tt, pp, čč, šš, ss, k, t, p, g, d, b, то o.5 > о.1 → если о.1 заканч. на
     * 1) СV, то V> i; 
     * 2) ie>ei, 
     * кроме твер. ua>ai, iä>äi
     * 
     * Е. если o.5 заканч. на Ci, то =o.5
     * 
     * @param string $stem1
     * @param string $stem5
     * @param string $stem6
     * @param boolean $harmony
     * @return string
     */
    public static function stem4FromMiniTemplate($stem1, $stem5, $stem6, $harmony, $dialect_id) {
        $C = "[".KarGram::consSet()."]";
        $V = "[".KarGram::vowelSet()."]";
        $syl_count5 = KarGram::countSyllable($stem5);
        $syl_count6 = KarGram::countSyllable($stem6);
        
//dd($stem1, $stem5, $stem6);        
        if (preg_match("/".$C."[oö]i$/u", $stem5) && preg_match("/".$C."[aä]$/u", $stem6)
                && $syl_count5==$syl_count6) {                      // А
            if (preg_match("/^(.+".$C.")[aä]$/u", $stem1, $regs)) { // А.1
                return $regs[1].KarGram::garmVowel($harmony,'oi');
            } elseif (preg_match("/^(.+)ua$/u", $stem1, $regs)) {   // А.2
                return $regs[1].'avoi';
            } elseif (preg_match("/^(.+)iä$/u", $stem1, $regs)) {   // А.3
                return $regs[1].'ävöi';
            }
        } elseif ($dialect_id != 47 && preg_match("/".$C."[oöuy]i$/u", $stem5)  
                && preg_match("/".$C."[oöuy]$/u", $stem6) && $syl_count5==$syl_count6) {         // Б
            return $stem1.'i';
            
        } elseif ($syl_count5>$syl_count6 && ($dialect_id == 47 && preg_match("/".$C."[oö]i$/u", $stem5)
                || $dialect_id != 47 && preg_match("/l[oö]i$/u", $stem5))                        // В
                || $dialect_id != 47 && preg_match("/".$V."i$/u", $stem5) 
                    && preg_match("/".$V.$V."$/u", $stem1)) {                                    // Г
            return $stem5;
            
        } elseif (preg_match("/".$C."i$/u", $stem5)) { // Г
            if (preg_match("/[ktpčšs]{2}i$|[ktpgdb]i$/u", $stem5)) {     // Д
                if (preg_match("/^(.+".$C.")".$V."$/u", $stem1, $regs)) { // Д.1
                    return $regs[1].'i';
                } elseif (preg_match("/^(.+)i(e)$/u", $stem1, $regs)
                    || $dialect_id != 47 && preg_match("/^(.+)i(ä)$/u", $stem1, $regs)) {
                    return $regs[1].$regs[2].'i';
                } elseif (preg_match("/^(.+)ua$/u", $stem1, $regs)) {
                    return $regs[1].'ai';
                } else {
                    return $stem1;
                }
            } else {                                                      // E
                return $stem5;
            }
        }
        
    }
    
    /**
     * paik|ku (-an, -kua; -koi)
     * pajo||jouk|ko (-on, -kuo; -koloi)
     * puol|i (-en, -du; -ii)
     * päp|pi (-in, -pii; -pilöi)
     * piä (-n, -dy; -löi)
     * pačkeh (-en, -tu; -ii)
     * pada (puan, padua; padoi)
     * pagan||rengi (-n, -i; -löi)
     * po|udu (-vvan, -udua; -udii)
     * 
     * @param array $regs [0=>template, 1=>base, 2=>nom-sg-suff, 3=>gen-sg-suff, 4=>par-sg-suff, 5=>par-pl-suff]
     *                    [0=>"paik|ku (-an, -kua; -koi)", 1=>"paik", 2=>"ku", 3=>"-an", 4=>"-kua", 5=>"-koi"]
     * @return array [0=>nom_sg, 1=>base_gen_sg, 2=>base_ill_sg, 3=>part_sg, 4=>base_gen_pl, 5=>base_part_pl], $base, $base_suff]
     *               [[0=>'paikku', 1=>'paika', 2=>'paikka', 3=>'paikkua', 4=>'paikoi', 5=>'paikkoi']
     */
    public static function stemsOthersFromTemplate($regs) {
//dd($regs);    
        $base = preg_replace('/ǁ/','',$regs[1]);
        $gen_sg_suff = $regs[3];
        $par_sg_suff = $regs[4];
        $par_pl_suff = $regs[5];
        
        $out = [[$base.$regs[2]], null, $regs[0], null];

        $stem1 = self::parseGenSg(preg_replace("/\-/", $base, $gen_sg_suff));
        if (!$stem1) {
            return $out;
        }
        
        $stem5 = self::partPlBase($base, $par_pl_suff);
        if (!$stem5) {
            return $out;
        }

        $stems = [0 => $base.$regs[2],
                  1 => $stem1, // single genetive base 
                  2 => '',
                  3 => preg_replace("/\-/",$base,$par_sg_suff),
                  4 => '',
                  5 => $stem5,
                  10 => KarGram::isBackVowels($regs[1].$regs[2]) // harmony
            ];
//dd($stems);        
        $stems[2] = self::illSgBase($stems[0], $stems[1], $stems[3]); // single illative base
        $stems[4] = self::genPlBase($stems[1], $stems[5], $stems[10]); // plural partitive base
//dd('stems:',$stems);        
        return [$stems, null, $regs[1], $regs[2]];
    }
    
    /**
     * Viändö|i (-in, -idy)
     * 
     * @param array $regs [0=>template, 1=>base, 2=>nom-sg-suff, 3=>gen-sg-suff, 4=>par-sg-suff]
     *                    [0=>"Viändö|i (-in, -idy)", 1=>"Viändö", 2=>"i", 3=>"-in", 4=>"-idy"]
     * @return array [stems=[0=>nom_sg, 1=>base_gen_sg, 2=>base_ill_sg, 3=>part_sg, 4=>base_gen_pl, 5=>base_part_pl], $base, $base_suff]
     *               [[0=>'Viändöi', 1=>'Viändöi', 2=>'Viändöi', 3=>'Viändöidy', 4=>'', 5=>''], 'Viändö', 'i']
     */
    public static function stemsSgFromTemplate($regs) {
        $base = preg_replace('/ǁ/','',$regs[1]);
        $gen_sg_suff = $regs[3];
        $par_sg_suff = $regs[4];

        $out = [[$base.$regs[2]], null, $regs[0], null];
        
        $stem1 = self::parseGenSg(preg_replace("/\-/", $base, $gen_sg_suff));
        if (!$stem1) {
            return $out;
        }
        
        $stems = [0 => $base.$regs[2],
                  1 => $stem1, // single genetive base 
                  2 => '',
                  3 => preg_replace("/^\-/",$base,$par_sg_suff),
                  4 => '',
                  5 => ''];
        $stems[2] = self::illSgBase($stems[0],$stems[1],$stems[3]); // single illative base
//dd('stems:',$stems);        
        return [$stems, 'sg', $regs[1], $regs[2]];
    }
    
    /**
     * pordah|at (-ien, -ii)
     * 
     * @param array $regs [0=>template, 1=>base, 2=>nom-pl-suff, 3=>gen-pl-suff, 4=>par-pl-suff]
     *                    [0=>"pordah|at (-ien, -ii)", 1=>"pordah", 2=>"at", 3=>"-ien", 4=>"-ii"]
     * @return array [stems=[0=>nom_pl, 1=>base_gen_sg, 2=>base_ill_sg, 3=>part_sg, 4=>base_gen_pl, 5=>base_part_pl], $base, $base_suff]
     *               [[0=>'pordahat', 1=>'', 2=>'', 3=>'', 4=>'pordahi', 5=>'pordahi'], 'pordah', 'at']
     */
    public static function stemsPlFromTemplate($regs) {
        $base = preg_replace('/ǁ/','',$regs[1]);
        $gen_pl_suff = $regs[3];
        $par_pl_suff = $regs[4];
        
        $out = [[$base.$regs[2]], null, $regs[0], null];
        
        $stem4 = self::parseGenPl($base, $gen_pl_suff);
        if (!$stem4) {
            return $out;
        }
        
        $stem5 = self::partPlBase($base, $par_pl_suff);
        if (!$stem5) {
            return $out;
        }
        
        $stems = [0 => $base.$regs[2],
                  1 => '',
                  2 => '',
                  3 => '',
                  4 => $stem4,  // plural genetive base 
                  5 => $stem5];
        return [$stems, 'pl', $regs[1], $regs[2]];
    }
       
    /**
     * п.о. 1
     * 
     * @param type $wordform
     * @return string
     */
    public static function parseGenSg($wordform) {
        if (!$wordform) {
            return '';
        }
        $out = [];
        $V = "[".KarGram::vowelSet()."]";
        $words = preg_split("/\//",$wordform);
        foreach ($words as $word) {
            if (preg_match("/^(.+".$V.")n$/u", $word, $regs)) {
                $out[] = $regs[1];
            } else {
                return '';
            }
        }
        return join('/',$out);
    }
    
    /**
     * @param type $wordform
     * @return string
     */
    public static function parseGenPl($base, $gen_pl_suff) {
        $out = [];
        $V = "[".KarGram::vowelSet()."]";
        $stems = preg_split("/\/\s*/",preg_replace("/\-/", $base, $gen_pl_suff));
        foreach ($stems as $stem) {
            $stem = trim($stem);
            if (preg_match("/^(.+?i)e?n$/u", $stem, $regs)) {
                $out[] = $regs[1];
            } else {
                return '';
            }
        }
        return join('/',$out);
    }
    
    public static function partPlBase($base, $par_pl_suff) {
        $out = [];
        $stems = preg_split("/\/\s*/",preg_replace("/\-/", $base, $par_pl_suff));
        foreach ($stems as $stem) {
            $stem = trim($stem);
            if (!preg_match("/i$/", $stem)) {
                return '';
            }
            $out[] = preg_replace("/ii$/", 'i', $stem);
        }
        return join('/',$out);
    }
    
    /** 
     * А. если $stem3 заканчивается на [dt][uy], то =$stem1, при этом возможны замены:
     *    1) если $stem0 заканчивается на V[uy]zi, а $stem1 на vve,
     *       то vve > [uy]de
     *    2) если $stem0 заканчивается на [uy][oö]zi, а $stem1 на vve,
     *       то vve > [oö]de
     *    3) если $stem0 заканчивается на zi, a $stem1 заканчивается на
     *       а) Vve, то ve > de 
     *       б) rre, то rre > rde
     *       в) nne, то nne > nde
     *       г) je, то je > de
     *       д) ie, то ie > ede
     *       е) äi, то äi > äde
     * Б. если в $stem3 конечные VV, то =$stem3-VV
     *    1) если $stem1 заканчивается на [aä]i, то +e
     *    2) в остальных случаях + конечный V из $stem1
     * 
     * @param string $stem0
     * @param string $stem1
     * @param string $stem3
     * @return string
     */
    public static function illSgBase($stem0, $stem1, $stem3) {
        $V = '['.KarGram::vowelSet().']';
    //dd($stem0, $stem1, $stem3);        
        $out = [];
        $stems1 = preg_split("/\/\s*/", $stem1);
        $stems3 = preg_split("/\/\s*/", $stem3);
        if (sizeof($stems3)==2 && sizeof($stems1)==1) {
            $stems1[1]=$stems1[0];
        } elseif (sizeof($stems3)==1 && sizeof($stems1)==2) {
            $stems3[1]=$stems3[0];
        }
        for ($i=0; $i<sizeof($stems1); $i++) {
            $stem1 = $stems1[$i];
            $stem3 = $stems3[$i];
            if (preg_match('/[dt][uy]$/u', $stem3)){ // А
                if (preg_match('/'.$V.'([uy])zi$/u', $stem0, $regs_u)) {
                    $stem1=preg_replace('/vve$/u', $regs_u[1].'de', $stem1); // A.1
                } elseif (preg_match('/[uy]([oö])zi$/u', $stem0, $regs_o)) {
                    $stem1=preg_replace('/vve$/u', $regs_o[1].'de', $stem1); // A.2                
                } elseif (preg_match('/zi$/', $stem0)) { // А.3
                    if (preg_match('/^(.*'.$V.')ve$/u', $stem1, $regs1) // А.3.а
                            || preg_match('/^(.*r)re$/u', $stem1, $regs1) // А.3.б
                            || preg_match('/^(.*n)ne$/u', $stem1, $regs1) // А.3.в
                            || preg_match('/^(.+)je$/u', $stem1, $regs1) // А.3.г
                            || preg_match('/^(.*ä)i$/u', $stem1, $regs1)) { // А.3.е
                        $stem1 = $regs1[1].'de';
                    } elseif (preg_match('/^(.+)ie$/u', $stem1, $regs1)) { // А.3.д
                        $stem1 = $regs1[1].'ede';
                    }
                }
                $out[] = $stem1;
            } elseif (preg_match('/^(.*)'.$V.$V.'$/u', $stem3, $regs3)) {
                $stem3 = $regs3[1];
                if (preg_match('/[aä]i$/', $stem1)) {
                    $stem3 .= 'e';
                } elseif (preg_match('/('.$V.')$/u', $stem1, $regs1)) {
                    $stem3 .= $regs1[1];
                }
                $out[] = $stem3;
            }
        }
        return join('/',$out);
    }
    
    /**
     * A. если $stem5 заканчивается на kki, tti, ppi, čči, šši, ssi, gi, di, bi,
     *    то = $stem1 – конечный V + i
     * Б. если $stem5 заканчивается на loi, löi или Ci, то = $stem5
     * 
     * В. в остальных случаях = $stem1 со след. изменениями:
     *    1) если $stem1 заканчивается на СV, то – конечный V + oi / öi
     *    2) если $stem1 заканчивается на ua, iä, то – ua, iä → + avoi / ävöi
     * 
     * @param string $stem1
     * @param string $stem5
     * @return string
     */
    public static function genPlBase($stem1, $stem5, $harmony) {
        if (!$stem5) {
            return '';
        }
        $V = '['.KarGram::vowelSet().']';
        $C = '['.KarGram::consSet().']';
        $out = [];
        $stems5 = preg_split("/\//",$stem5);
        foreach ($stems5 as $stem5) {
            if (preg_match('/k’?k’?i$|t’?t’?i$|p’?p’?i$|č’?č’?i$|š’?š’?i$|s’?s’?i$|[gdb]’?i$/u', $stem5)){ // А
                $out[] = preg_replace('/'.$V.'$/u','i',$stem1);
            } elseif (preg_match('/l’?[oö]i$|'.$C.'i$/u', $stem5)){ // Б
                $out[] = $stem5;
            } else { // В
                if (preg_match('/^(.+'.$C.'’?)'.$V.'$/u', $stem1, $regs1)) { // 1
                    $stem1 = $regs1[1].KarGram::garmVowel($harmony, 'o').'i';
                } else { 
                    $stem1=preg_replace('/ua$/u', 'avoi', $stem1); // 2
                    $stem1=preg_replace('/iä$/u', 'ävöi', $stem1); // 2                
                }
                $out[] = $stem1;            
            }
        }
        return join('/',$out);
    }
    
    public static function wordformByStems($stems, $gramset_id, $lang_id, $dialect_id, $name_num=null) {
        if ($lang_id !=6) {
            switch ($gramset_id) {
                case 2: // номинатив, мн.ч. 
                case 57: // аккузатив, мн.ч. 
                    return $name_num == 'pl' ? $stems[0] : ($name_num != 'sg' && $stems[1] ? Grammatic::addEndToMultiBase($stems[1], 't') : '');
            }
        }
        
        if ($name_num !='pl' && in_array($gramset_id, self::gramsetListSg($lang_id))) {
            return self::wordformByStemsSg($stems, $gramset_id, $lang_id, $dialect_id);
        }
        
        if ($name_num !='sg' && in_array($gramset_id, self::gramsetListPl($lang_id))) {
            if ($lang_id==5) {
                return KarNameOlo::wordformByStemsPl($stems, $gramset_id, $dialect_id);
            } elseif ($lang_id==6) {
                return KarNameLud::wordformByStemsPl($stems, $gramset_id, $name_num, $dialect_id);
            } else {        
                return self::wordformByStemsPl($stems, $gramset_id, $dialect_id);
            }
        }
        return '';
    }
    
    public static function wordformByStemsSg($stems, $gramset_id, $lang_id, $dialect_id) {
        switch ($gramset_id) {
            case 1: // номинатив, ед.ч. 
                return $stems[0];
            case 56: // аккузатив, ед.ч. 
                return self::accSg($stems[0],$stems[1]);
            case 3: // генитив, ед.ч. 
                return $stems[1] ? $stems[1].'n' : '';
            case 4: // партитив, ед.ч. 
                return $stems[3] ? ($dialect_id == 47 && preg_match("/^(.+)yä$/u", $stems[3], $regs) ? $regs[1].'iä' : $stems[3] )  : '';
            case 10: // иллатив, ед.ч. 
                return $stems[2] ? $stems[2].'h' : '';
        }
        if ($lang_id==5) {
            return KarNameOlo::wordformByStemsSg($stems, $gramset_id, $dialect_id);
        } elseif ($lang_id==6) {
            return KarNameLud::wordformByStemsSg($stems, $gramset_id, $dialect_id);
        } else {
            return self::wordformByStemsSgProp($stems, $gramset_id, $dialect_id);
        }        
    }
    
    public static function accSg($stem0, $stem1) {
        if (!$stem1) {
            return $stem0;
        }
        
        $w[] =  $stem0;
        $w[] =  $stem1.'n';
        sort($w);
        
        return join(', ', $w);
    }
    /**
     * $stems[10] - harmony
     * 
     * @param type $stems
     * @param type $gramset_id
     * @param type $dialect_id
     * @return type
     */
    public static function wordformByStemsSgProp($stems, $gramset_id, $dialect_id) {
        switch ($gramset_id) {
            case 5: // транслатив, ед.ч. 
                return $stems[1] ? $stems[1]. KarGram::distrCons($stems[1], 'ksi') : '';
            case 14: // комитатив, ед.ч. 
                return $stems[1] ? ($dialect_id==47 ? $stems[1].'nke': self::comPl($stems[4], $stems[5], $dialect_id)) : '';
            case 15: // пролатив, ед.ч. 
                return $dialect_id==47 && $stems[1] ? $stems[1].'čči' : '';
        }
        
        if (!isset($stems[10])) { return ''; }
        $a = KarGram::garmVowel($stems[10],'a');
        
        switch ($gramset_id) {
            case 277: // эссив, ед.ч. 
                return $stems[2] ? $stems[2]. 'n'. $a  : '';
            case 8: // инессив, ед.ч. 
                return $stems[1] ? $stems[1]. KarGram::distrCons($stems[1], 'ss'). $a : '';
            case 9: // элатив, ед.ч. 
                return $stems[1] ? $stems[1]. KarGram::distrCons($stems[1], 'st'). $a : '';
            case 278: // адессив-аллатив, ед.ч. 
                return $stems[1] ? $stems[1]. 'll'. $a : '';
            case 12: // аблатив, ед.ч. 
                return $stems[1] ? $stems[1]. 'l'. ($dialect_id==47 ? 'd': 't'). $a : '';
            case 6: // абессив, ед.ч. 
                return $stems[1] ? $stems[1]. 'tt'. $a : '';
            case 17: // апроксиматив, ед.ч. 
                return $dialect_id==47 && $stems[1] ? $stems[1]. 'll'. KarGram::garmVowel($stems[10],'uo') : '';
        }
    }
    
    public static function wordformByStemsPl($stems, $gramset_id, $dialect_id) {
        switch ($gramset_id) {
            case 24: // генитив, мн.ч. 
                return self::genPl($stems[4], $stems[5], isset($stems[6])? $stems[6]: null, $dialect_id);
            case 59: // транслатив, мн.ч. 
                return $stems[4] ? $stems[4].'ksi' : '';
            case 61: // иллатив, мн.ч. 
                return $stems[5] ? $stems[5].'h' : '';
            case 65: // комитатив, мн.ч. 
                return self::comPl($stems[4], $stems[5], $dialect_id);
            case 66: // пролатив, мн.ч. 
                return $dialect_id==47 && $stems[4] ? $stems[4].'čči' : '';
            case 281: // инструктив, мн.ч. 
                return $stems[4] ? $stems[4].'n' : '';
            case 18: // апроксиматив, мн.ч. 
                return $dialect_id==47 && $stems[4] ? $stems[4]. 'll'. KarGram::garmVowel($stems[10],'uo') : '';
        }
        
        if (!isset($stems[10])) { return ''; }
        $a = KarGram::garmVowel($stems[10],'a');
        
        switch ($gramset_id) {
            case 22: // партитив, мн.ч. 
                return self::partPl($stems[5], isset($stems[6])? $stems[6]: null, $stems[10], $dialect_id);
            case 279: // эссив, мн.ч.
                return $stems[5] ? $stems[5]. 'n'. $a : '';
            case 23: // инессив, мн.ч.
                return $stems[4] ? $stems[4]. 'ss'. $a : '';
            case 60: // элатив, мн.ч.
                return $stems[4] ? $stems[4]. 'st'. $a : '';
            case 280: // адессив-аллатив, мн.ч.
                return $stems[4] ? $stems[4]. 'll'. $a : '';
            case 62: // аблатив, мн.ч.
                return $stems[4] ? $stems[4]. 'l'. ($dialect_id==47 ? 'd': 't'). $a : '';
            case 64: // абессив, мн.ч.
                return $stems[4] ? $stems[4]. 'tt'. $a : '';
        }
    }
    
    /**
     * For tver (dialect_id=47) = stem4+n
     * 
     * For norhern proper karelian:
     * Если o.5 заканч. на
     * 1) Ci, то o.5 + en;
     * 2) l[oö]i и кол-во слогов в o.5 больше чем в о.6, то loi~löi > jen;
     * 3) Vi и кол-во слогов в o.5 и о.6 одинаковое, а о.6 заканч. на
     *  – СV, то i > jen;
     *  – VV, то o.5 + jen.
     * 
     * @param string $stem4
     * @param string $stem5
     * @param string $stem6
     * @param int $dialect_id
     */
    public static function genPl($stem4, $stem5, $stem6, $dialect_id) {
        if ($dialect_id==47) {
            return $stem4 ? $stem4. 'n' : '';
        }
        
        if (!$stem5 || !$stem6) {
            return '';
        }
        $V = "[".KarGram::vowelSet()."]";
        $C = "[".KarGram::consSet()."]";
        if (preg_match("/".$C."i$/u", $stem5)) {
            return $stem5. 'en';            
        } elseif (preg_match("/^(.+)l[oö]i$/u", $stem5, $regs) 
                && KarGram::countSyllable($stem5) > KarGram::countSyllable($stem6)) {
            return $regs[1]. 'jen';            
        } elseif (preg_match("/^(.+".$V.")i$/u", $stem5, $regs) 
                && KarGram::countSyllable($stem5) == KarGram::countSyllable($stem6)) {
            if (preg_match("/".$C.$V."$/u", $stem6)) {
                return $regs[1]. 'jen';  
            } else {
                return $stem5. 'jen';
            }
        } else {return '!!!';}
    }

    /**
     * For tver (dialect_id=47) 
     * если о.5 заканчивается на oi/öi, то = о.5 + da/dä,
     * иначе = о.5 + e
     * 
     * For norhern proper karelian:
     * Eсли o.5 заканч. на
     * 1) Ci, то o.5 + e ;
     * 2) loi~löi и кол-во слогов в o.5 больше чем в о.6, то loi~löi > ja~jä;
     * 3) Vi и кол-во слогов в o.5 и о.6 одинаковое, а о.6 заканч. на
     * – СV, то i > ja~jä;
     * – VV, то o.5 + ta~tä.
     * 
     * @param string $stem5
     * @param string $stem6
     * @param int $dialect_id
     */
    public static function partPl($stem5, $stem6, $harmony, $dialect_id) {
        if (!$stem5) {
            return '';
        }
        if ($dialect_id==47) {
            if (preg_match("/[oö]i$/u", $stem5)) {
                return $stem5. KarGram::garmVowel($harmony,'da');
            } else {
                return $stem5. 'e';
            }
        }
        
        if (!$stem6) {
            return '';
        }
//dd(preg_match("/^(.+)l[oö]i$/u", $stem5, $regs), KarGram::countSyllable($stem5), KarGram::countSyllable($stem6));        
        $V = "[".KarGram::vowelSet()."]";
        $C = "[".KarGram::consSet()."]";
        if (preg_match("/".$C."i$/u", $stem5)) {
            return $stem5. 'e';            
        } elseif (preg_match("/^(.+)l[oö]i$/u", $stem5, $regs) 
                && KarGram::countSyllable($stem5) > KarGram::countSyllable($stem6)) {
            return $regs[1]. KarGram::garmVowel($harmony,'ja');            
        } elseif (preg_match("/^(.+".$V.")i$/u", $stem5, $regs) 
                && KarGram::countSyllable($stem5) == KarGram::countSyllable($stem6)) {
            if (preg_match("/".$C.$V."$/u", $stem6)) {
                return $regs[1]. KarGram::garmVowel($harmony,'ja');  
            } else {
                return $stem5. KarGram::garmVowel($harmony,'ta');
            }
        }
    }

    public static function comPl($stem4, $stem5, $dialect_id) {
        if ($dialect_id == 47) { // tver
            return $stem4 ? $stem4. 'nke' : '';
        }
        return $stem5 ? $stem5. 'neh' : '';        
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
     * @param string $lemma_str
     */
    public static function toRightTemplate($bases, $base_list, $lemma_str, $num) {
        if (!(sizeof($base_list)==3 || sizeof($base_list)==2 && $num=='sg' || sizeof($base_list)==1 && $num=='pl')) {
            return $lemma_str;
        }
        if (preg_match("/^([^\/\s]+)\s*[\/\:]\s*([^\s]+)$/u", $base_list[0], $regs)) {
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
            if ($num=='sg') {
                $bases[4] = $bases[5] = '';
            } elseif (preg_match("/^([^\/\s]+)\s*[\/\:]\s*([^\s]+)$/u", $base_list[2], $regs)) {
                $bases[4] = $regs[1];
                $bases[5] = $regs[2];
            } else {
                $bases[4] = $bases[5] = $base_list[2];
            }
        }
        return '{'.join(', ',$bases).'}';
    }
    
    public static function getAffixesForGramset($gramset_id) {
        switch ($gramset_id) {
            case 3: // генитив, ед.ч. 
            case 24: // генитив, мн.ч. 
            case 281: // инструктив, мн.ч. 
                return ['n'];
            case 277: // эссив, ед.ч. 
            case 279: // эссив, мн.ч.
                return ['na', 'nä'];
            case 5: // транслатив, ед.ч. 
                return ['kši', 'ksi'];
            case 8: // инессив, ед.ч. 
                return ['šša', 'ššä', 'ssa', 'ssä'];
            case 9: // элатив, ед.ч. 
                return ['šta', 'štä', 'sta', 'stä'];
            case 10: // иллатив, ед.ч. 
            case 61: // иллатив, мн.ч. 
                return ['h'];
            case 278: // адессив-аллатив, ед.ч. 
            case 280: // адессив-аллатив, мн.ч.
                return ['lla', 'llä'];
            case 12: // аблатив, ед.ч. 
            case 62: // аблатив, мн.ч.
                return ['lda', 'ldä', 'lta', 'ltä'];
            case 6: // абессив, ед.ч. 
            case 64: // абессив, мн.ч.
                return ['tta', 'ttä'];
            case 14: // комитатив, ед.ч. 
            case 65: // комитатив, мн.ч. 
                return ['nke', 'neh', 'n’eh'];
            case 15: // пролатив, ед.ч. 
            case 66: // пролатив, мн.ч. 
                return ['čči'];
            case 2: // номинатив, мн.ч. 
                return ['t'];
            case 22: // партитив, мн.ч. 
                return ['e', 'da', 'dä', 'ja', 'jä', 'ta', 'tä'];
            case 59: // транслатив, мн.ч. 
                return ['ksi'];
            case 23: // инессив, мн.ч.
                return ['ssa', 'ssä'];
            case 60: // элатив, мн.ч.
                return ['sta', 'stä'];
        }
        return [];
    }
    
    public static function templateFromWordforms($wordforms, $lang_id=4, $number) {
        if ($lang_id == 5) { // livvic
            return KarNameOlo::templateFromWordforms($wordforms, $number);
        } elseif ($lang_id == 6) { 
            return KarNameLud::templateFromWordforms($wordforms); // , $number
        }
        
        foreach ($wordforms as $gramset_id => $wordform) {
            if (preg_match("/^-(.+)$/", $wordform, $regs)) {
                $wordforms[$gramset_id] = $regs[1];
            }
        }
        if ($wordforms[3]=='n')  {
            $wordforms[3]='';
        } elseif (preg_match("/^(.*)n$/u", $wordforms[3], $regs)) {
            $wordforms[3]=$regs[1];
        } else {
            return null;
        }
//dd($wordforms);        
        
        if ($wordforms[10]=='h')  {
            $wordforms[10]='';
        } elseif (preg_match("/^(.*)h$/u", $wordforms[10], $regs)) {
            $wordforms[10]=$regs[1];
        } else {
            return null;
        }
        
        if ($wordforms[3] != $wordforms[10]) {
            $wordforms[3] .= '/'.$wordforms[10];
        }
        
        if (!preg_match("/^(.*)t[aä]$/u", $wordforms[4], $regs1)) {
            return " [".$wordforms[3]."]";            
        }
        
        return " [".$wordforms[3].", $regs1[1]]";
    }
    
    public static function suggestTemplates($lang_id, $word) {
        if ($lang_id == 6) {
            return KarNameLud::suggestTemplates($word);
        }
        
        $C = "[".KarGram::consSet()."]";
        $V = "[".KarGram::vowelSet()."]";
        $is_back = KarGram::isBackVowels($word);
        $A = KarGram::garmVowel($is_back,'a');
        $O = KarGram::garmVowel($is_back,'o');
        $U = KarGram::garmVowel($is_back,'u');
        $templates = [];
        
        if (preg_match('/'.$C.'’[aä]$/u', $word)                        // hil’l’a
                || preg_match('/[uy]{2}$/u', $word)) {                // alušpuu     
            $templates[] = $word.' [, ]';
        }    
        if (preg_match('/'.$C.'’?[aäiuyoö]$/u', $word)                    // šyvä
                || preg_match('/'.$V.$V.'$/u', $word)) {                  // työ     
            $templates[] = $word.' []';
        }    
        if (preg_match('/[rhlnr]$/u', $word)) {                        // penker     
            $templates[] = $word.' [e, ]';
        }    
        if (preg_match('/^(.+?)([hlrn]i)$/u', $word, $r)) {             // jouhi 
            $templates[] = $r[1].'|'.$r[2].' [e, ]';
        }    
        if (preg_match('/^(.+?)([čkpt])\2([aäioöuy])$/u', $word, $r)) { // muččo 
            $templates[] = $r[1].$r[2].'|'.$r[2].$r[3].' ['.$r[3].']';
        }    
        if (preg_match('/^(.+?)([nrv])\2(eh)$/u', $word, $r)) {       // elänneh 
            $templates[] = $r[1].$r[2].'|'.$r[2].$r[3].' [tehe, '.$r[2].$r[3].']';
        }    
        if (preg_match('/^(.+?)([aä])(š)$/u', $word, $r)) {              // eväš 
            $templates[] = $r[1].$r[2].'|'.$r[3].' [h'.$r[2].', '.$r[3].']';
        }    
        if (preg_match('/^(.+?)([čk])([uy])([sš])$/u', $word, $r)) {   // alačuš 
            $templates[] = $r[1].$r[2].'|'.$r[3].$r[4].' ['.$r[2].$r[3].$O.', '.$r[3].'t]';
        }    
        if (preg_match('/^(.+?)([lnrsš])(t)([aäoöuyi])$/u', $word, $r)  // kulta
                || preg_match('/^(.+?)(m)(p)([aäoöuyi])$/u', $word, $r)) {// lämpö
            $templates[] = $r[1].$r[2].'|'.$r[3].$r[4].' ['.$r[2].$r[4].']';
        }    
        if (preg_match('/^(.+?t)(ti)$/u', $word, $r)) {             // alapirtti 
            $templates[] = $r[1].'|'.$r[2].' ['.$A.']';
        }    
        if (preg_match('/^(.+?č)([aä]š)$/u', $word, $r)) {         // hautapačaš 
            $templates[] = $r[1].'|'.$r[2].' [č'.$A.'he, '.$r[2].']';
        }    
        if (preg_match('/^(.+?)(č){2}(i)$/u', $word, $r)) {            // veičči 
            $templates[] = $r[1].'|'.$r[2].$r[2].$r[3].' ['.$r[2].'e, s]';
        }    
        if (preg_match('/^(.+?lli)(t[aä])$/u', $word, $r)) {          // kallita 
            $templates[] = $r[1].'|'.$r[2].' [če]';
        }    
        if (preg_match('/^(.+?)(c)(en)$/u', $word, $r)) {              // joucen 
            $templates[] = $r[1].$r[2].'|'.$r[3].' ['.$r[2].'ene, '.$r[3].']';
        }    
        if (preg_match('/^(.+?č)(in)$/u', $word, $r)) {              // kaklačin 
            $templates[] = $r[1].'|'.$r[2].' [čime, '.$r[2].']';
        }    
        if (preg_match('/^(.+?h)(ti)$/u', $word, $r)                    // lehti
                || preg_match('/^(.+?[šlrkt])(ki)$/u', $word, $r)       // koški
                || preg_match('/^(.+?)(i)$/u', $word, $r)) {             // šuvi
            $templates[] = $r[1].'|'.$r[2].' [e]';
        }    
        if (preg_match('/^(.+?)([čkpt])\2i$/u', $word, $r)) {      // ahventuppi 
            $templates[] = $r[1].$r[2].'|'.$r[2].'i [e]';
        }    
        
        if (preg_match('/^(.+?'.$C.')(is)$/u', $word, $r)) {           // kapris 
            $templates[] = $r[1].'|'.$r[2].' [ehe, '.$r[2].']';
        }    
        if (preg_match('/^(.+?'.$V.$V.')(š)$/u', $word, $r)) {         // autuoš 
            $templates[] = $r[1].'|'.$r[2].' [h'.$A.', '.$r[2].']';
        }    
        if (preg_match('/^(.+?)([aä])(š)$/u', $word, $r)) {            // kankaš 
            $templates[] = $r[1].$r[2].'|'.$r[3].' [h'.$r[2].', '.$r[3].']'; 
        }    
        if (preg_match('/^(.+?e)(š)$/u', $word, $r)                      // mieš
                || preg_match('/^(.+?i)(s)$/u', $word, $r)) {         // kiärmis
            $templates[] = $r[1].'|'.$r[2].' [he, '.$r[2].']';
        }    
        if (preg_match('/^(.+?)(kši)$/u', $word, $r)) {                 // lakši 
            $templates[] = $r[1].'|'.$r[2].' [he]'; 
        }    
        if (preg_match('/^(.+?)(ksi)$/u', $word, $r)) {                  // yksi 
            $templates[] = $r[1].'|'.$r[2].' [he/hte, h]'; 
        }    
        if (preg_match('/^(.+?[ht])(ti)$/u', $word, $r)) {              // vahti 
            $templates[] = $r[1].'|'.$r[2].' [i]'; 
        }    
        if (preg_match('/^(.*?k)([aä])(si)$/u', $word, $r)) {            // käsi 
            $templates[] = $r[1].'|'.$r[2].$r[3].' [i'.$r[2].', '.$r[2].'t]'; 
        }    
        if (preg_match('/^(.+?)([aä]t[aä])$/u', $word, $r)) {      // henkenhätä 
            $templates[] = $r[1].'|'.$r[2].' [i'.$A.']'; 
        }    
        if (preg_match('/^(.+?'.$C.')(e)$/u', $word, $r)) {             // holve 
            $templates[] = $r[1].'|'.$r[2].' [ie, et]'; 
        }    
        if (preg_match('/^(.+?'.$V.$C.')(e)$/u', $word, $r)         // iltakoite
                || preg_match('/^(.+?)(eki)$/u', $word, $r)              // reki
                || preg_match('/^(.+?)(et)$/u', $word, $r)) {         // apposet
            $templates[] = $r[1].'|'.$r[2].' [ie]';
        }    
        if (preg_match('/^(.+?)(et[aä])$/u', $word, $r)) {              // hieta 
            $templates[] = $r[1].'|'.$r[2].' [ij'.$A.']'; 
        }    
        if (preg_match('/^(.+?)(si)$/u', $word, $r)) {                  // hiisi 
            $templates[] = $r[1].'|'.$r[2].' [je, t]'; 
        }    
        if (preg_match('/^(.+?)([kt]i)$/u', $word, $r)) {                // hiki 
            $templates[] = $r[1].'|'.$r[2].' [je]'; 
        }    
        if (preg_match('/^(.+?k)([aä]š)$/u', $word, $r)) {             // jiäkäš 
            $templates[] = $r[1].'|'.$r[2].' [k'.$A.'h'.$A.', '.$r[2].']'; 
        }    
        if (preg_match('/^(.+?)(jeh)$/u', $word, $r)                    // aijeh
                || preg_match('/^(.+?[lk])(eh)$/u', $word, $r)         // eläkeh
                || preg_match('/^(.*?r[uy])(is)$/u', $word, $r)) {       // ruis
            $templates[] = $r[1].'|'.$r[2].' [kehe, '.$r[2].']';
        }    
        if (preg_match('/^(.+?š)(e)(l)$/u', $word, $r)                   // ašel
                || preg_match('/^(.*?i)(je)(n)$/u', $word, $r)) {        // ijen
            $templates[] = $r[1].'|'.$r[2].$r[3].' [ke'.$r[3].'e, '.$r[2].$r[3].']';
        }    
        if (preg_match('/^(.+?)([sš])$/u', $word, $r)) {                // jänis
            $templates[] = $r[1].'|'.$r[2].' [k'.$r[2].'e, '.$r[2].']';
        }    
        if (preg_match('/^(.+?)(kši)$/u', $word, $r)) {                 // šukši 
            $templates[] = $r[1].'|'.$r[2].' [kše, š]'; 
        }    
        if (preg_match('/^(.+?t)([uy]t)$/u', $word, $r)) {              // kätyt 
            $templates[] = $r[1].'|'.$r[2].' [k'.$U.$O.', '.$r[2].']'; 
        }    
        if (preg_match('/^(.+?l)(t[uy][aä])$/u', $word, $r)) {      // huuhaltua 
            $templates[] = $r[1].'|'.$r[2].' [l'.$A.', '.$r[2].']'; 
        }    
        if (preg_match('/^(.+?)(in)$/u', $word, $r)) {                // alačoin 
            $templates[] = $r[1].'|'.$r[2].' [m'.$A.', '.$r[2].']'; 
        }    
        if (preg_match('/^(.+?m)(p[aä])$/u', $word, $r)                 // kampa
                || preg_match('/^(.+?m)([bp]i)$/u', $word, $r)) {    // nuorembi
            $templates[] = $r[1].'|'.$r[2].' [m'.$A.']';
        }    
        if (preg_match('/^(.+?[aäi])(n)$/u', $word, $r)                // kaššin
                || preg_match('/^(.+?V)(in)$/u', $word, $r)) {        // huokain
            $templates[] = $r[1].'|'.$r[2].' [me, '.$r[2].']';
        }    
        if (preg_match('/^(.+?)(mi)$/u', $word, $r)) {                  // liemi 
            $templates[] = $r[1].'|'.$r[2].' [me, n]'; 
        }    
        if (preg_match('/^(.+?m)(pi)$/u', $word, $r)) {                 // lampi 
            $templates[] = $r[1].'|'.$r[2].' [me]'; 
        }    
        if (preg_match('/^(.+?)(ši)$/u', $word, $r)) {                   
            $templates[] = $r[1].'|'.$r[2].' [ne, t]';                  // kynši
            $templates[] = $r[1].'|'.$r[2].' [ne/te, t]';               // kanši
        }    
        if (preg_match('/^(.+?r[uy])([sš])$/u', $word, $r)            // ankaruš
                || preg_match('/^(.+?[uy])(t)$/u', $word, $r)) {      // katonut
            $templates[] = $r[1].'|'.$r[2].' ['.$O.', '.$r[2].']';
        }    
        if (preg_match('/^(.+?[hk][uy])([sš])$/u', $word, $r)) {        // pahuš 
            $templates[] = $r[1].'|'.$r[2].' ['.$O.', t]'; 
        }    
        if (preg_match('/^(.+?m)(m[aä]š)$/u', $word, $r)               // hammaš
                || preg_match('/^(.+?p)([aä]š)$/u', $word, $r)      // jiäruopaš
                || preg_match('/^(.+?)(v[aä]š)$/u', $word, $r)) {      // karvaš
            $templates[] = $r[1].'|'.$r[2].' [p'.$A.'h'.$A.', '.$r[2].']';
        }    
        if (preg_match('/^(.+?p)([aä]n)$/u', $word, $r)) {              // hapan 
            $templates[] = $r[1].'|'.$r[2].' [p'.$A.'me, '.$r[2].']'; 
        }    
        if (preg_match('/^(.+?)(veh)$/u', $word, $r)                   // helveh
                || preg_match('/^(.+?)(vis)$/u', $word, $r)) {         // tarvis
            $templates[] = $r[1].'|'.$r[2].' [pehe, '.$r[2].']';
        }    
        if (preg_match('/^(.+?m)(me)(l)$/u', $word, $r)                // vemmel
                || preg_match('/^(.+?)(ve)(n)$/u', $word, $r)) {        // haven
            $templates[] = $r[1].'|'.$r[2].$r[3].' [pe'.$r[3].'e, '.$r[2].$r[3].']';
        }    
        if (preg_match('/^(.+?)([čkpt])(e)$/u', $word, $r)) {             // ape 
            $templates[] = $r[1].$r[2].'|'.$r[3].' ['.$r[2].'ie, et]'; 
        }    
        if (preg_match('/^(.+?m)(min)$/u', $word, $r)) {               // lämmin 
            $templates[] = $r[1].'|'.$r[2].' [pim'.$A.', '.$r[2].']'; 
        }    
        if (preg_match('/^(.+?)(pši)$/u', $word, $r)) {                 // lapši 
            $templates[] = $r[1].'|'.$r[2].' [pše, š]'; 
        }    
        if (preg_match('/^(.+?m)(m[uy]t)$/u', $word, $r)) {             // immyt 
            $templates[] = $r[1].'|'.$r[2].' [p'.$U.$O.', '.$r[2].']'; 
        }    
        if (preg_match('/^(.+?r)(ši)$/u', $word, $r)) {                  
            $templates[] = $r[1].'|'.$r[2].' [re, t]';                  // karši
            $templates[] = $r[1].'|'.$r[2].' [re]';                 // itkuvirši
            $templates[] = $r[1].'|'.$r[2].' [re/te, t]';               // hirši
        }    
        if (preg_match('/^(.+?s)(k[aä])$/u', $word, $r)) {         // paharaiska 
            $templates[] = $r[1].'|'.$r[2].' [s'.$A.']'; 
        }    
        if (preg_match('/^(.+?[^i])(ni)$/u', $word, $r)) {            // koivuni 
            $templates[] = $r[1].'|'.$r[2].' [se, is]'; 
        }    
        if (preg_match('/^(.+?)(n[ei])$/u', $word, $r)) {            // čirkkuni 
            $templates[] = $r[1].'|'.$r[2].' [se, s]'; 
        }    
        if (preg_match('/^(.+?t)([aä]r)$/u', $word, $r)) {              // tytär 
            $templates[] = $r[1].'|'.$r[2].' [t'.$r[2].'e]'; 
        }    
        if (preg_match('/^(.+?[ht])([aä]š)$/u', $word, $r)              // puhaš
                || preg_match('/^(.+?)(j[aä]š)$/u', $word, $r)) {       // hijaš
            $templates[] = $r[1].'|'.$r[2].' [t'.$A.'h'.$A.', '.$r[2].']';
        }    
        if (preg_match('/^(.+?)([lnr])\2([aä])(š)$/u', $word, $r)) {    // allaš 
            $templates[] = $r[1].$r[2].'|'.$r[2].$r[3].$r[4].' [t'.$r[3].'h'.$r[3].', '.$r[2].$r[3].$r[4].']'; 
        }    
        if (preg_match('/^(.+?)([hj]e)(h)$/u', $word, $r)               // kajeh
                || preg_match('/^(.+?n)(ne)(l)$/u', $word, $r)         // kannel
                || preg_match('/^(.+?n)(ne)(r)$/u', $word, $r)) {      // manner
            $templates[] = $r[1].'|'.$r[2].$r[3].' [te'.$r[3].'e, '.$r[2].$r[3].']';
        }    
        if (preg_match('/^(.+?[^v])(v{1,2}e)(h)$/u', $word, $r)) {      // huuvveh
            $templates[] = $r[1].'|'.$r[2].$r[3].' [pe'.$r[3].'e, '.$r[2].$r[3].']';
        }    
        if (preg_match('/^(.+?i)(je)$/u', $word, $r)                    // voije
                || preg_match('/^(.+?n)(ne)$/u', $word, $r)             // jänne
                || preg_match('/^(.+?[oö]š)(še)$/u', $word, $r)         // košše
                || preg_match('/^(.*?'.$V.$C.')(e)$/u', $word, $r)       // kare
                || preg_match('/^(.+?'.$V.'t)(e)$/u', $word, $r)) {     // kaute
            $templates[] = $r[1].'|'.$r[2].' [tie, '.$r[2].'t]';
        }    
        if (preg_match('/^(.+?[aä]š)(še)$/u', $word, $r)                // kašše
                || preg_match('/^(.+?[aä]t)(e)$/u', $word, $r)) {       // jiäte
            $templates[] = $r[1].'|'.$r[2].' [tie]';
        }    
        if (preg_match('/^(.+?)(jin)$/u', $word, $r)                   // vuajin
                || preg_match('/^(.+?n)(nin)$/u', $word, $r)           // kannin
                || preg_match('/^(.+?š)(šin)$/u', $word, $r)         // jaluššin
                || preg_match('/^(.+?t)(in)$/u', $word, $r)) {         // šoitin
            $templates[] = $r[1].'|'.$r[2].' [time, '.$r[2].']';
        }    
        if (preg_match('/^(.+?t)([oö]in)$/u', $word, $r)) {          // hävitöin 
            $templates[] = $r[1].'|'.$r[2].' [t'.$O.'m'.$A.', '.$r[2].']'; 
        }    
        if (preg_match('/^(.+?s)(s[uy]in)$/u', $word, $r)) {           // issuin 
            $templates[] = $r[1].'|'.$r[2].' [tuime, '.$r[2].']'; 
        }    
        if (preg_match('/^(.+?v)([aä]t)$/u', $word, $r)) {              // kevät 
            $templates[] = $r[1].'|'.$r[2].' ['.$U.$A.', '.$r[2].']'; 
        }    
        if (preg_match('/^(.+?)([aä]k[aä])$/u', $word, $r)               // haka
                || preg_match('/^(.+?)([aä]t[aä])$/u', $word, $r)) {  // holvata
            $templates[] = $r[1].'|'.$r[2].' ['.$U.$A.']';
        }    
        if (preg_match('/^(.+?)([oö]t[aä])$/u', $word, $r)) {          // ahjota 
            $templates[] = $r[1].'|'.$r[2].' ['.$U.$O.']'; 
        }    
        if (preg_match('/^(.+?[uy])([oö]ši)$/u', $word, $r)) {          // vuoši 
            $templates[] = $r[1].'|'.$r[2].' ['.$U.'vve/'.$O.'te, '.$O.'t]'; 
        }    
        if (preg_match('/^(.+?[uy])(ši)$/u', $word, $r)) {           // kuukauši 
            $templates[] = $r[1].'|'.$r[2].' [ve, t]'; 
        }    
        if (preg_match('/^(.+?)([ktp])(i)$/u', $word, $r)) {             // joki 
            $templates[] = $r[1].'|'.$r[2].$r[3].' [ve]'; 
        }    
        if (preg_match('/^(.+?)([ktp])([aäiuyoö])$/u', $word, $r)) {    // hauki 
            $templates[] = $r[1].'|'.$r[2].$r[3].' [v'.$r[3].']'; 
        }    
        if (preg_match('/^(.*?[uy]{2})(ši)$/u', $word, $r)) {            // uuši
            $templates[] = $r[1].'|'.$r[2].' [vve, t]'; 
        }    
        if (preg_match('/^(.+?)(n)(ti)$/u', $word, $r)) {                // anti
            $templates[] = $r[1].$r[2].'|'.$r[3].' ['.$r[2].'i]'; 
        }    
        if (preg_match('/^(.+?[ht])(tu)([aä])$/u', $word, $r)      // juokšuttua
           || preg_match('/^(.+?'.$C.')([uy])([aä])$/u', $word, $r)) {// čirškua
            $templates[] = $r[1].'|'.$r[2].$r[3].' ['.$r[3].', '.$r[2].$r[3].']';
        }    
        if (preg_match('/^(.+?[thlrš])(k)([aäoöuy])$/u', $word, $r)     // pitkä
                || preg_match('/^(.+?[uyhr])(t)([aä])$/u', $word, $r)) {// parta
            $templates[] = $r[1].'|'.$r[2].$r[3].' ['.$r[3].']';
        }    
        if (preg_match('/^(.+?)(et)([oö])$/u', $word, $r)) {            // tieto
            $templates[] = $r[1].'|'.$r[2].$r[3].' [ij'.$r[3].']'; 
        }    
        if (preg_match('/^(.+?)([kt])([aäiuyoö])$/u', $word, $r)        // reikä
                || preg_match('/^(.+?i)(k)([aä])$/u', $word, $r)         // aika
                || preg_match('/^(.+?i)([tv])([oö])$/u', $word, $r)     // hoito
                || preg_match('/^(.+?i)([kt])([uy])$/u', $word, $r)) {  // joiku
            $templates[] = $r[1].'|'.$r[2].$r[3].' [j'.$r[3].']'; 
        }    
        if (preg_match('/^(.+?n)(t)([uy])$/u', $word, $r)) {            // lintu
            $templates[] = $r[1].'|'.$r[2].$r[3].' [n'.$r[3].']'; 
        }    
        if (preg_match('/^(.+?)([uy])([oö][kt])([aäoö])$/u', $word, $r)) {// luota
            $templates[] = $r[1].$r[2].'|'.$r[3].$r[4].' ['.$r[2].'vv'.$r[4].']'; 
        }    
        if (preg_match('/^(.+?'.$V.')([tpk])([aä])$/u', $word, $r)       // hupa
                || preg_match('/^(.+?)([kp])([oö])$/u', $word, $r)       // hako
                || preg_match('/^(.+?V)(t)([oö])$/u', $word, $r)        // huuto
                || preg_match('/^(.+?V)([kpt])([uy])$/u', $word, $r)) {  // šuku
            $templates[] = $r[1].'|'.$r[2].$r[3].' [v'.$r[3].']'; 
        }    
        if (preg_match('/^(.+?[uy]{2})(t)([oö])$/u', $word, $r)) { // kalanpyytö
            $templates[] = $r[1].'|'.$r[2].$r[3].' [vv'.$r[3].']'; 
        }    
        if (preg_match('/^(.+?)([kp])[oö]ni$/u', $word, $r)) {         // näköni
            $templates[] = $r[1].'|'.$r[2].$O.'ni ['.$r[2].$O.'se, v'.$O.'is]'; 
        }    
        
        $templates = array_unique($templates);
        sort($templates);
        return $templates;
    }    
}