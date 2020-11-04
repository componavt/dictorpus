<?php

namespace App\Library\Grammatic;

use App\Library\Grammatic;
use App\Library\Grammatic\KarGram;
use App\Library\Grammatic\KarVerbOlo;

use App\Models\Dict\Gramset;
//use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;

/**
 * Functions related to Karelian grammatic for verbs.
 */
class KarVerb
{ 
    /**
     * Proper and Olo have different bases!!!
     * 
     * @param type $template
     * @param type $lang_id
     * @param type $name_num
     * @param type $is_reflexive
     * @return type
     */
    public static function stemsFromTemplate($template, $lang_id, $name_num, $is_reflexive=null) {      
        $base_shab = "([^\s\(\]\|]+)";
        $base_suff_shab = "([^\s\(\]\|]*)";
        $okon_shab = "(-?[^\-\,\;\)]+?\/?-?[^\-\,\;\)]*)";
        $lemma_okon_shab = "/^".$base_shab."\|?".$base_suff_shab."\s*\(".$okon_shab;

        // mini template
        if ($lang_id==4 && preg_match("/^".$base_shab."\|?".$base_suff_shab."\s*\[([^\]]*)\]/", $template, $regs)) {
            return self::stemsFromMiniTemplate($regs, $name_num);
        } elseif ($lang_id==5 && preg_match( $lemma_okon_shab."\;\s*".$okon_shab."\)/", $template, $regs)) {
//dd('regs:',$regs);            
            return KarVerbOlo::stemsFromTemplateDef($regs, $is_reflexive);    
        } elseif ($lang_id==5 && preg_match( $lemma_okon_shab."\;\s*".$okon_shab."\;\s*".$okon_shab."\,\s*".$okon_shab."\)/", $template, $regs)) {  // + 3sd pl
//dd('regs:',$regs);            
            return KarVerbOlo::stemsFromTemplateDefPl($regs, $is_reflexive);    
        } elseif ($lang_id==5 && preg_match($lemma_okon_shab."\,\s*".$okon_shab."\;\s*".$okon_shab."\;\s*".$okon_shab."\,\s*".$okon_shab."\)/", $template, $regs)) {  
//dd('regs:',$regs);            
            return KarVerbOlo::stemsFromFullTemplate($regs, $is_reflexive);    
        } else {
//dd("!!!!");                
            return Grammatic::getAffixFromtemplate($template, $name_num);
        }
        
    }
    
    public static function stemsFromMiniTemplate($regs, $name_num=null) {
        $base = preg_replace('/ǁ/','',$regs[1]);
        $harmony = KarGram::isBackVowels($regs[1].$regs[2]); // harmony
        $stems=[
            0=>$base.$regs[2], 
            1=>$base.$regs[3],
            2=>'',
            3=>'',
            4=>'',
            5=>'',
            6=>'',
            7=>''];
        
        $stems[2]=self::stem2FromMiniTemplate($stems[0], $stems[1]); // вспом. сильн. гл.
        $stems[4]=self::stem4FromMiniTemplate($stems[0], $stems[2]);
        $stems[3]=self::stem3FromMiniTemplate($stems[0], $stems[1], $stems[4]);
        $stems[5]=self::stem5FromMiniTemplate($stems[0], $stems[2]);
        $stems[6]=self::stem6FromMiniTemplate($stems[0], $stems[1], $harmony); 
        $stems[7]=self::stem7FromMiniTemplate($stems[0], $stems[1], $stems[5]);
        
        return [$stems, $name_num, $regs[1], $regs[2]];
    }
    
    /**
     * А. Если с.ф. заканч. на VV, то с.ф. – конечные VV → + конечный V из о.1.
     * Б. Если с.ф. заканч. на СV то = о.1, при этом в о.1 če > čče.
     * 
     * @param string $stem0
     * @param string $stem1
     */
    public static function stem2FromMiniTemplate($stem0, $stem1) {
        $C = "[".KarGram::consSet()."]’?";
        $V = "[".KarGram::vowelSet()."]";
        
        if (preg_match("/^(.+)".$V.$V."$/u", $stem0, $regs0)
               && preg_match("/(".$V.")$/u", $stem1, $regs1)) {
           return $regs0[1].$regs1[1];
        } elseif (preg_match("/".$C.$V."$/u", $stem0)) {
            return preg_replace("/če$/u","čče",$stem1);
        }
    }

    /**
     * = о.8, при этом если заканч. на
     * 1) Ce, то e > i;
     * 2) j[aä] и с.ф. на CV, то – ja/jä → + si;
     * 3) Cä и с.ф. на VV, то ä > i;
     * 4) Ca и с.ф. на VV и в о.8 два слога и в первом есть гласный a (a, au, ai, ua), то a > o;
     * 5) Ca и с.ф. на VV и в о.8 три или (два слога и в первом есть гласные o, u), то a > i;
     * 6) VV и с.ф. на [vhj][aä], то – первый V из о.8 + i;
     * 7) VV и с.ф. на ta, tä, то – первый V из о.8 + i ~ si
     * 
     * @param string $stem0
     * @param string $stem2
     * @return string
     */
    public static function stem4FromMiniTemplate($stem0, $stem2) {
        $C = "[".KarGram::consSet()."]’?";
        $V = "[".KarGram::vowelSet()."]";
        
        if (preg_match("/^(.+".$C.")e$/u", $stem2, $regs) // 1
            ||    
                preg_match("/^(.+".$C.")ä$/u", $stem2, $regs) 
                && preg_match("/".$V.$V."$/u", $stem0)) {    // 3
            return $regs[1].'i';            
        } elseif (preg_match("/^(.+)j[aä]$/u", $stem2, $regs) 
                && preg_match("/".$C.$V."$/u", $stem0)) {    // 2
            return $regs[1].'si';            
        } elseif (preg_match("/^(.+".$C.")a$/u", $stem2, $regs) 
                && preg_match("/".$V.$V."$/u", $stem0)
                && KarGram::countSyllable($stem2)==2
                && preg_match("/^(".$C.")*".$V."*a/u", $stem2)) {    // 4
            return $regs[1].'o';            
        } elseif (preg_match("/^(.+".$C.")a$/u", $stem2, $regs) 
                && preg_match("/".$V.$V."$/u", $stem0)
                && (KarGram::countSyllable($stem2)==3 || KarGram::countSyllable($stem2)==2
                && preg_match("/^(".$C.")*".$V."*[ou]/u", $stem2))) {    // 5
            return $regs[1].'i';            
        } elseif (preg_match("/^(.+)".$V."(".$V.")$/u", $stem2, $regs) 
                && preg_match("/[vhj][aä]$/u", $stem0)) {    // 6
            return $regs[1].$regs[2].'i';            
        } elseif (preg_match("/^(.+)".$V."(".$V.")$/u", $stem2, $regs) 
                && preg_match("/t[aä]$/u", $stem0)) {    // 5
            return $regs[1].$regs[2].'i/'.$regs[1].$regs[2].'si';            
        }
        return $stem2;
    }    

    /**
     * = о.1, при этом если о.1 заканч. на
     * 1) C[uyoö], то + i;
     * 2) Ce, то e > i;
     * 3) ja, jä и с.ф. на CV, то – ja/jä → + si;
     * 4) Cä и с.ф. на VV, то ä > i;
     * 5) Ca и с.ф. на VV и в о.1 два слога и в первом есть гласный a (a, au, ai, ua), то a > oi;
     * 6) Ca и с.ф. на VV и в о.1 три или (два слога и в первом есть гласные o, u), то a > i;
     * 7) VV и с.ф. на VV, тo о.1 > о.4 → – конечн. СV → + voi;
     * 8) VV и с.ф. на va, vä, ha, hä, ja, jä, то – первый V из о.1. + i;
     * 9) VV и с.ф. на ta, tä, то – первый V из о.1. + i ~ si
     * 
     * @param string $stem0
     * @param string $stem1
     * @param string $stem4
     * @return string
     */
    public static function stem3FromMiniTemplate($stem0, $stem1, $stem4) {
        $C = "[".KarGram::consSet()."]’?";
        $V = "[".KarGram::vowelSet()."]";

        if (preg_match("/".$C."[uyoö]$/u", $stem1)){ // 1
            return $stem1.'i';
        } elseif (preg_match("/^(.+".$C.")e$/u", $stem1, $regs) // 2
            ||    
                preg_match("/^(.+".$C.")ä$/u", $stem1, $regs) 
                && preg_match("/".$V.$V."$/u", $stem0)) {    // 4
            return $regs[1].'i';            
        } elseif (preg_match("/^(.+)j[aä]$/u", $stem1, $regs) 
                && preg_match("/".$C.$V."$/u", $stem0)) {    // 3
            return $regs[1].'si';            
        } elseif (preg_match("/^(.+".$C.")a$/u", $stem1, $regs) 
                && preg_match("/".$V.$V."$/u", $stem0)
                && KarGram::countSyllable($stem1)==2
                && preg_match("/^(".$C.")*".$V."*a/u", $stem1)) {    // 5
            return $regs[1].'oi';        
        } elseif (preg_match("/^(.+".$C.")a$/u", $stem1, $regs) 
                && preg_match("/".$V.$V."$/u", $stem0)
                && (KarGram::countSyllable($stem1)==3 
                        || KarGram::countSyllable($stem1)==2
                           && preg_match("/^(".$C.")*".$V."*[ou]/u", $stem1))) { // 6
            return $regs[1].'i';        
        } elseif (preg_match("/".$V.$V."$/u", $stem1) 
                && preg_match("/".$V.$V."$/u", $stem0)
                && preg_match("/^(.+)".$C.$V."$/u", $stem4, $regs)) {    // 7
            return $regs[1].'voi';            
        } elseif (preg_match("/^(.+)".$V."(".$V.")$/u", $stem1, $regs) 
                && preg_match("/[vhj][aä]$/u", $stem0)) {    // 6
            return $regs[1].$regs[2].'i';            
        } elseif (preg_match("/^(.+)".$V."(".$V.")$/u", $stem1, $regs) 
                && preg_match("/t[aä]$/u", $stem0)) {    // 6
            return $regs[1].$regs[2].'i/'.$regs[1].$regs[2].'si';            
        }
        return $stem1;
    }    

    /**
     * А. Если с.ф. заканч. на VV ИЛИ [vh][aä] ИЛИ также j[aä], а в с.ф.  2 слога, то = o.8 
     * Б. Если с.ф. заканч. на СV (остальные случаи), то с.ф.  – конечн. СV
     * 1) если получив. форма заканч. НЕ на Vi или C, то + t
     * 
     * @param string $stem0
     * @param string $stem2
     * @return string
     */
    public static function stem5FromMiniTemplate($stem0, $stem2) {
        $C = "[".KarGram::consSet()."]’?";
        $V = "[".KarGram::vowelSet()."]";
        
        if (preg_match("/".$V.$V."$/u", $stem0)    
            || preg_match("/[vh][aä]$/u", $stem0)           
            || preg_match("/j[aä]$/u", $stem0) && KarGram::countSyllable($stem0)==2) { // A
            return $stem2;
        } elseif (preg_match("/^(.+)".$C.$V."$/u", $stem0, $regs)) {
            if (!preg_match("/".$V."i$/u", $regs[1])
                    && !preg_match("/".$C."$/u", $regs[1])) {
                $regs[1] .= 't';
            }
            return $regs[1];
        }
    }    
    
    /**
     * Если с.ф. заканч. на
     * 1) VV, то = о.1 (если в о.1 С[aä], то a/ä > e) + ta/tä;
     * 2) СV, то = с.ф.
     * 
     * @param string $stem0
     * @param string $stem1
     * @param boolean $harmony
     * @return string
     */
    public static function stem6FromMiniTemplate($stem0, $stem1, $harmony) {
        $C = "[".KarGram::consSet()."]’?";
        $V = "[".KarGram::vowelSet()."]";
        
        if (preg_match("/".$V.$V."$/u", $stem0)) {
            if (preg_match("/^(.+".$C.")[aä]$/u", $stem1, $regs)) {
                $stem1=$regs[1].'e';
            }
            return $stem1.KarGram::garmVowel($harmony,'ta');
        }
        return $stem0;
    }
    
    /**
     * Если с.ф. заканчивается на 
     * 1) VV, то = o.1 (если в о.1 Сa/Cä, то a/ä > e) + tt, 
     * 2) СV, то с.ф. > о.5 → + t
     * 
     * @param string $stem0
     * @param string $stem1
     * @param string $stem5
     * @return string
     */
    public static function stem7FromMiniTemplate($stem0, $stem1, $stem5) {
        $C = "[".KarGram::consSet()."]’?";
        $V = "[".KarGram::vowelSet()."]";
        
        if (preg_match("/".$V.$V."$/u", $stem0)) {
            if (preg_match("/^(.+".$C.")[aä]$/u", $stem1, $regs)) {
                $stem1=$regs[1].'e';
            }
            return $stem1.'tt';
        }
        return $stem5.'t';
    }
    
    /**
     * 0 = infinitive 1
     * 1 = base of indicative presence 1 sg (indicative presence 1 sg - 'n')
     * 2 = base of indicative presence 3 sg (3 infinitive illative - 'mah / mäh')
     * 3 = base of indicative imperfect 1 sg (indicative imperfect 1 sg - 'n')
     * 4 = indicative imperfect 3 sg
     * 5 = base of perfect  (2 active participle - 'n?[nlrsš]un')
     * 6 = base of indicative presence 3 pl (indicative presence 3 pl - 'h')
     * 7 = base of indicative imperfect 3 pl (indicative imperfect 3 pl - 'ih')

     * @param Lemma $lemma
     * @param Int $dialect_id
     * @return array
     */
    public static function stemsFromDB($lemma, $dialect_id) {
        for ($i=0; $i<8; $i++) {
            $stems[$i] = self::getStemFromWordform($lemma, $i, $dialect_id);
        }
        return $stems;
    }
        
    /**
     * 
     * @param array $stems
     * @param int $stem_n
     * @param int $dialect_id
     * @param string $lemma
     * @return string
     */
    public static function getStemFromStems($stems, $stem_n, $lang_id, $dialect_id, $lemma) {
        if ($lang_id==5) {
            return KarVerbOlo::getStemFromStems($stems, $stem_n, $dialect_id, $lemma);
        }
        return '';
    }
    
    public static function getStemFromWordform($lemma, $stem_n, $dialect_id) {
        switch ($stem_n) {
            case 0: 
                return $lemma->lemma;
            case 1:  // indicative presence 1 sg
                if (preg_match("/^(.+)n$/", $lemma->wordform(26, $dialect_id), $regs)) {
                    return preg_replace("/,\s*/", '/',$regs[1]);
                }
                return '';
            case 4: // indicative imperfect 3 sg
                $ind_imp_3_sg = $lemma->wordform(34, $dialect_id); 
                return $ind_imp_3_sg ? preg_replace("/,\s*/", '/',$ind_imp_3_sg) : '';
            case 6: // indicative presence 3 pl
                if (preg_match("/^(.+)h$/", $lemma->wordform(31, $dialect_id), $regs)) { 
                    return preg_replace("/,\s*/", '/',$regs[1]);
                }
                return '';
        }
        if ($lang_id==5) {
            return KarVerbOlo::getStemFromWordform($lemma, $stem_n, $dialect_id);
        }
        
        switch ($stem_n) {
            case 2: // 3 infinitive illative
                if (preg_match("/^(.+)m[aä]h$/u", $lemma->wordform(174, $dialect_id), $regs)) { 
                    return $regs[1];
                }
                return '';
            case 3: // indicative imperfect 1 sg
                if (preg_match("/^(.+)n$/", $lemma->wordform(32, $dialect_id), $regs)) {
                    return $regs[1];
                }
                return '';
            case 5: // 2 active participle
                if (preg_match("/^(.+)n?[nlrsš]un$/", $lemma->wordform(179, $dialect_id), $regs)) { 
                    return $regs[1];
                }
                return '';
            case 6: // indicative presence 3 pl
                if (preg_match("/^(.+)h$/", $lemma->wordform(31, $dialect_id), $regs)) { 
                    return $regs[1];
                }
                return '';
            case 7: // indicative imperfect 3 pl
                if (preg_match("/^(.+)ih$/", $lemma->wordform(37, $dialect_id), $regs)) {
                    return $regs[1];
                }
                return '';        
        }
    }

    /** Lists of ID of gramsets, which have the rules.
     * That is we know how to generate word forms (using stems, endings and rules) for this gramset ID.
     * 
     * @return type list of known gramsets ID
     */
    public static function getListForAutoComplete($lang_id) {
        if ($lang_id==5) {
            return KarVerbOlo::getListForAutoComplete();
        }
        return [26,   27,  28,  29,  30,  31, 295, 296, 
                70,   71,  72,  73,  78,  79, 
                32,   33,  34,  35,  36,  37, 297, 298, 
                80,   81,  82,  83,  84,  85, 
                86,   87,  88,  89,  90,  91,  92,  93,  94,  95,  96,  97,
                98,   99, 100, 101, 102, 103, 104, 105, 107, 108, 106, 109,
                      51,  52,  53,  54,  55,       50,  74,  75,  76,  77,  
                44,   45,  46,  47,  48,  49, 116, 117, 118, 119, 120, 121,
                135, 125, 136, 137, 138, 139, 140, 141, 142, 143, 144, 145,
                146, 147, 148, 149, 150, 151, 310, 311, 
                152, 153, 154, 155, 156, 157,
                158, 159, 160, 161, 162, 163, 164, 165, 166, 167, 168, 169,
                170, 171, 172, 173, 174, 175, 176, 177,
                178, 179, 282, 180, 181];
    }
    
    public static function wordformByStems($stems, $gramset_id, $lang_id, $dialect_id, $def=NULL) {
        switch ($gramset_id) {
            case 26: // 1. индикатив, презенс, 1 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[1], 'n') : '';
            case 27: // 2. индикатив, презенс, 2 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[1], 't') : '';        
            case 295: // 144. индикатив, презенс, коннегатив, ед.ч.
                return Grammatic::joinMorfToBases($stems[1], '');
                               
            case 51: // 49. императив, 2 л., ед.ч., пол 
                return !$def ? Grammatic::joinMorfToBases($stems[1], ''): '';
        }
        
        if ($lang_id==5) {
            return KarVerbOlo::wordformByStems($stems, $gramset_id, $dialect_id, $def);
        }
        
        $PF = self::perfectForm($stems[5], $stems[10], $lang_id);
        $passive = Grammatic::joinMorfToBases($stems[7], KarGram::garmVowel($stems[10],'u'));
        
        switch ($gramset_id) {
            case 28: // 3. индикатив, презенс, 3 л., ед.ч., пол. 
                return $stems[2] ? self::indPres1SingByStem($stems[2], $stems[10]) : '';
            case 29: // 4. индикатив, презенс, 1 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[1], KarGram::garmVowel($stems[10],'mma')) : '';
            case 30: // 5. индикатив, презенс, 2 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[1], KarGram::garmVowel($stems[10],'tta')) : '';
            case 31: // 6. индикатив, презенс, 3 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[6],'h') : '';
            case 296: // 145. индикатив, презенс, коннегатив, мн.ч.
                return !$def ? Grammatic::joinMorfToBases($stems[6], '') : '';

            case 70: // 7. индикатив, презенс, 1 л., ед.ч., отриц. 
            case 71: // 8. индикатив, презенс, 2 л., ед.ч., отриц. 
            case 73: //10. индикатив, презенс, 1 л., мн.ч., отриц. 
            case 78: // 11. индикатив, презенс, 2 л., мн.ч., отриц. 
                return !$def ? Grammatic::interLists(self::negVerb($gramset_id, $dialect_id), $stems[1]) : '';
            case 72: // 9. индикатив, презенс, 3 л., ед.ч., отриц. 
                return Grammatic::interLists(self::negVerb($gramset_id, $dialect_id), $stems[1]);
            case 79: // 12. индикатив, презенс, 3 л., мн.ч., отриц. 
                return !$def ? Grammatic::interLists(self::negVerb($gramset_id, $dialect_id), $stems[6]) : '';
                
            case 32: // 13. индикатив, имперфект, 1 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[3], 'n') : '';
            case 33: // 14. индикатив, имперфект, 2 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[3], 't') : '';
            case 34: // 15. индикатив, имперфект, 3 л., ед.ч., пол. 
                return $stems[4] ? $stems[4] : '';
            case 35: // 16. индикатив, имперфект, 1 л., мн.ч., пол. 
                return !$def ? self::indImp1PlurByStem($stems[4], $stems[10], $dialect_id) : '';
            case 36: // 17. индикатив, имперфект, 2 л., мн.ч., пол. 
                return !$def ? self::indImp2PlurByStem($stems[4], $stems[10]) : '';
            case 37: // 18. индикатив, имперфект, 3 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[7], 'ih') : '';
            case 297: // 146. индикатив, имперфект, коннегатив, ед.ч.
                return $PF;
            case 298: // 147. индикатив, имперфект, коннегатив, мн.ч.
                return !$def ? self::indImperfConnegPl($stems[7], $stems[10]) : '';

            case 80: // 19. индикатив, имперфект, 1 л., ед.ч., отриц. 
            case 81: // 20. индикатив, имперфект, 2 л., ед.ч., отриц. 
            case 83: // 22. индикатив, имперфект, 1 л., мн.ч., отриц. 
            case 84: // 23. индикатив, имперфект, 2 л., мн.ч., отриц. 
                return !$def ? Grammatic::interLists(self::negVerb($gramset_id, $dialect_id), $PF) : '';
            case 82: // 21. индикатив, имперфект, 3 л., ед.ч., отриц. 
                return Grammatic::interLists(self::negVerb($gramset_id, $dialect_id), $PF);
            case 85: // 24. индикатив, имперфект, 3 л., мн.ч., отриц. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm(85, $lang_id), self::indImperfConnegPl($stems[7], $stems[10])) : '';

            case 86: // 25. индикатив, перфект, 1 л., ед.ч., пол. 
            case 87: // 26. индикатив, перфект, 2 л., ед.ч., пол. 
            case 89: // 28. индикатив, перфект, 1 л., мн.ч., пол. 
            case 90: // 29. индикатив, перфект, 2 л., мн.ч., пол. 
            case 98: // 37. индикатив, плюсквамперфект, 1 л., ед.ч., пол. 
            case 99: // 38. индикатив, плюсквамперфект, 2 л., ед.ч., пол. 
            case 101: // 40. индикатив, плюсквамперфект, 1 л., мн.ч., пол. 
            case 102: // 41. индикатив, плюсквамперфект, 2 л., мн.ч., пол. 
            case 135: // 95. кондиционал, плюсквамперфект, 1 л., ед.ч., пол. 
            case 125: // 96. кондиционал, плюсквамперфект, 2 л., ед.ч., пол. 
            case 137: // 98. кондиционал, плюсквамперфект, 1 л., мн.ч., пол. 
            case 138: // 99. кондиционал, плюсквамперфект, 2 л., мн.ч., пол. 
            case 158: // 119. потенциал, перфект, 1 л., ед.ч., пол. 
            case 159: // 120. потенциал, перфект, 2 л., ед.ч., пол. 
            case 161: // 122. потенциал, перфект, 1 л., мн.ч., пол. 
            case 162: // 123. потенциал, перфект, 2 л., мн.ч., пол.                
                return !$def ? Grammatic::interLists(self::auxVerb($gramset_id, $dialect_id), $PF) : '';
            case 88: // 27. индикатив, перфект, 3 л., ед.ч., пол. 
            case 100: // 39. индикатив, плюсквамперфект, 3 л., ед.ч., пол. 
            case 136: // 97. кондиционал, плюсквамперфект, 3 л., ед.ч., пол. 
            case 160: // 121. потенциал, перфект, 3 л., ед.ч., пол. 
                return Grammatic::interLists(self::auxVerb($gramset_id, $dialect_id), $PF);                
            case 91: // 30. индикатив, перфект, 3 л., мн.ч., пол. 
            case 103: // 42. индикатив, плюсквамперфект, 3 л., мн.ч., пол. 
            case 139: // 100. кондиционал, плюсквамперфект, 3 л., мн.ч., пол. 
            case 163: // 124. потенциал, перфект, 3 л., мн.ч., пол. 
                return !$def ? Grammatic::interLists(self::auxVerb($gramset_id, $dialect_id), $passive) : '';

            case 92: // 31. индикатив, перфект, 1 л., ед.ч., отриц. 
            case 93: // 32. индикатив, перфект, 2 л., ед.ч., отриц. 
            case 95: // 34. индикатив, перфект, 1 л., мн.ч., отриц. 
            case 96: // 35. индикатив, перфект, 2 л., мн.ч., отриц. 
            case 104: // 43. индикатив, плюсквамперфект, 1 л., ед.ч., отриц. 
            case 105: // 44. индикатив, плюсквамперфект, 2 л., ед.ч., отриц. 
            case 107: // 46. индикатив, плюсквамперфект, 1 л., мн.ч., отриц. 
            case 108: // 47. индикатив, плюсквамперфект, 2 л., мн.ч., отриц. 
            case 140: // 101. кондиционал, плюсквамперфект, 1 л., ед.ч., отр. 
            case 141: // 102. кондиционал, плюсквамперфект, 2 л., ед.ч., отр. 
            case 143: // 104. кондиционал, плюсквамперфект, 1 л., мн.ч., отр. 
            case 144: // 105. кондиционал, плюсквамперфект, 2 л., мн.ч., отр. 
            case 164: // 125. потенциал, перфект, 1 л., ед.ч., отр. 
            case 165: // 126. потенциал, перфект, 2 л., ед.ч., отр. 
            case 167: // 128. потенциал, перфект, 1 л., мн.ч., отр. 
            case 168: // 129. потенциал, перфект, 2 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(self::auxVerb($gramset_id, $dialect_id, '-'), $PF) : '';
            case 94: // 33. индикатив, перфект, 3 л., ед.ч., отриц. 
            case 106: // 45. индикатив, плюсквамперфект, 3 л., ед.ч., отриц. 
            case 142: // 103. кондиционал, плюсквамперфект, 3 л., ед.ч., отр. 
            case 166: // 127. потенциал, перфект, 3 л., ед.ч., отр. 
                return Grammatic::interLists(self::auxVerb($gramset_id, $dialect_id, '-'), $PF);
            case 97: // 36. индикатив, перфект, 3 л., мн.ч., отриц. 
            case 109: // 48. индикатив, плюсквамперфект, 3 л., мн.ч., отриц. 
            case 145: // 106. кондиционал, плюсквамперфект, 3 л., мн.ч., отр. 
            case 169: // 130. потенциал, перфект, 3 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(self::auxVerb($gramset_id, $dialect_id, '-'), $passive) : '';

            case 50: // 54. императив, 2 л., ед.ч., отр. 
                return !$def ? Grammatic::interLists(self::negVerb($gramset_id, $dialect_id), $stems[1]) : '';
            case 52: // 50. императив, 3 л., ед.ч., пол 
                return self::imp3PolByStem($stems[5], $stems[0], $stems[10], $dialect_id);
            case 53: // 51. императив, 1 л., мн.ч., пол 
                return $dialect_id==47 ? '' : (!$def && $stems[5] ? self::impBase($stems[5]). KarGram::garmVowel($stems[10],'a') : '');
            case 54: // 52. императив, 2 л., мн.ч., пол 
                return !$def ? self::imp2PlurPolByStem($stems[5], $stems[0], $stems[10], $dialect_id) : '';
            case 55: // 53. императив, 3 л., мн.ч., пол 
                return !$def ? self::imp3PolByStem($stems[5], $stems[0], $stems[10], $dialect_id) : '';

            case 74: // 55. императив, 3 л., ед.ч., отр. 
                return Grammatic::interLists(self::negVerb($gramset_id, $dialect_id), self::imp3PolByStem($stems[5], $stems[0], $stems[10], $dialect_id));
            case 75: // 56. императив, 1 л., мн.ч., отр 
                return $dialect_id==47 ? '' : (!$def ? Grammatic::interLists(self::negVerb($gramset_id, $dialect_id), self::impBase($stems[5]). KarGram::garmVowel($stems[10],'a')) : '');
            case 76: // 57. императив, 2 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(self::negVerb($gramset_id, $dialect_id), self::imp2PlurPolByStem($stems[5], $stems[0], $stems[10], $dialect_id)) : '';
            case 77: // 58. императив, 3 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(self::negVerb($gramset_id, $dialect_id), self::imp3PolByStem($stems[5], $stems[0], $stems[10], $dialect_id)) : '';

            case 44: // 71. кондиционал, имперфект, 1 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases(self::condBase($stems[0], $stems[2], $stems[4], $dialect_id), 'n') : '';
            case 45: // 72. кондиционал, имперфект, 2 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases(self::condBase($stems[0], $stems[2], $stems[4], $dialect_id), 't') : '';
            case 46: // 73. кондиционал, имперфект, 3 л., ед.ч., пол. 
                return $stems[4] ? self::condImp3SingPolByStem($stems[0], $stems[2], $stems[4], $stems[10], $dialect_id) : '';
            case 47: // 74. кондиционал, имперфект, 1 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases(self::condBase($stems[0], $stems[2], $stems[4], $dialect_id), KarGram::garmVowel($stems[10],'ma')) : '';
            case 48: // 75. кондиционал, имперфект, 2 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases(self::condBase($stems[0], $stems[2], $stems[4], $dialect_id), KarGram::garmVowel($stems[10],'ja')) : '';
            case 49: // 76. кондиционал, имперфект, 3 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[7], KarGram::garmVowel($stems[10],'a'). 'is'. ($dialect_id==47 ? '’' : '')) : '';

            case 116: // 77. кондиционал, имперфект, 1 л., ед.ч., отр. 
            case 117: // 78. кондиционал, имперфект, 2 л., ед.ч., отр. 
            case 119: // 80. кондиционал, имперфект, 1 л., мн.ч., отр. 
            case 120: // 81. кондиционал, имперфект, 2 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(self::negVerb($gramset_id, $dialect_id), self::condImp3SingPolByStem($stems[0], $stems[2], $stems[4], $stems[10], $dialect_id)) : '';
            case 118: // 79. кондиционал, имперфект, 3 л., ед.ч., отр. 
                return Grammatic::interLists(self::negVerb($gramset_id, $dialect_id), self::condImp3SingPolByStem($stems[0], $stems[2], $stems[4], $stems[10], $dialect_id));
            case 121: // 82. кондиционал, имперфект, 3 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm(121, $lang_id), Grammatic::joinMorfToBases($stems[7], KarGram::garmVowel($stems[10],'a'). 'is'. ($dialect_id==47 ? '’' : ''))) : '';
                                
            case 146: // 107. потенциал, презенс, 1 л., ед.ч., пол. 
                return !$def ? self::potencialForm($stems[5], 'en', $dialect_id) : '';
            case 147: // 108. потенциал, презенс, 2 л., ед.ч., пол. 
                return !$def ? self::potencialForm($stems[5], 'et', $dialect_id) : '';
            case 148: // 109. потенциал, презенс, 3 л., ед.ч., пол. 
                return self::potencialForm($stems[5], KarGram::garmVowel($stems[10], 'ou'), $dialect_id);
            case 149: // 110. потенциал, презенс, 1 л., мн.ч., пол. 
                return !$def ? self::potencialForm($stems[5], KarGram::garmVowel($stems[10], 'emma'), $dialect_id) : '';
            case 150: // 111. потенциал, презенс, 2 л., мн.ч., пол. 
                return !$def ? self::potencialForm($stems[5], KarGram::garmVowel($stems[10], 'etta'), $dialect_id) : '';
            case 151: // 112. потенциал, презенс, 3 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[7], KarGram::garmVowel($stems[10], 'a'). ($dialect_id==47 ? 'nneh' : 'neh')) : '';
            case 310: // 158. потенциал, презенс, коннегатив 
                return self::potencialForm($stems[5], 'e', $dialect_id);
            case 311: // 159. потенциал, презенс, коннегатив, 3 л. мн.ч.
                return !$def ? Grammatic::joinMorfToBases($stems[7], KarGram::garmVowel($stems[10], 'a'). ($dialect_id==47 ? 'nne' : 'ne')) : '';

            case 152: // 113. потенциал, презенс, 1 л., ед.ч., отр. 
            case 153: // 114. потенциал, презенс, 2 л., ед.ч., отр. 
            case 155: // 116. потенциал, презенс, 1 л., мн.ч., отр. 
            case 156: // 117. потенциал, презенс, 2 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(self::negVerb($gramset_id, $dialect_id), self::potencialForm($stems[5], 'e', $dialect_id)) : '';
            case 154: // 115. потенциал, презенс, 3 л., ед.ч., отр. 
                return Grammatic::interLists(self::negVerb($gramset_id, $dialect_id), self::potencialForm($stems[5], 'e', $dialect_id));
            case 157: // 118. потенциал, презенс, 3 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm(157, $lang_id), Grammatic::joinMorfToBases($stems[7], KarGram::garmVowel($stems[10], 'a'). ($dialect_id==47 ? 'nne' : 'ne'))) : '';
                
            case 170: // 131. I инфинитив 
                return $stems[0];
            case 171: // 132. II инфинитив, инессив 
                return self::inf2Ines($stems[0], $stems[10], $dialect_id);
            case 172: // 133. II инфинитив, инструктив  
                return self::inf2Inst($stems[0], $stems[2], $dialect_id);
            case 173: // 134. III инфинитив, адессив
                return self::Inf3Form($stems[2], 'malla', $stems[10], $dialect_id);
            case 174: // 135. III инфинитив, иллатив 
                return self::Inf3Form($stems[2], 'mah', $stems[10], $dialect_id);
            case 175: // 136. III инфинитив, инессив 
                return self::Inf3Form($stems[2], 'mašša', $stems[10], $dialect_id);
            case 176: // 137. III инфинитив, элатив 
                return self::Inf3Form($stems[2], 'mašta', $stems[10], $dialect_id);
            case 177: // 138. III инфинитив, абессив 
                return self::Inf3Form($stems[2], 'matta', $stems[10], $dialect_id);
                
            case 178: // 139. актив, 1-е причастие 
                return Grammatic::joinMorfToBases(KarGram::replaceCV($stems[2], 'e', 'i'), KarGram::garmVowel($stems[10], 'ja'));
            case 179: // 140. актив, 2-е причастие 
                return self::partic2active($stems[5], $stems[10], $dialect_id);
            case 282: // 141. актив, 2-е причастие  (сокращенная форма); перфект (форма основного глагола)
                return $PF;
            case 180: // 142. пассив, 1-е причастие 
                return !$def ? Grammatic::joinMorfToBases($stems[7], KarGram::garmVowel($stems[10], 'ava')) : '';
            case 181: // 143. пассив, 2-е причастие 
                return !$def ? $passive : '';
        }
        return '';
    }

    /**
     * Indicative, Presence, 3rd Person, Singular, Positive
     * 
     * основа 2 + u / y (при этом, если основа 2 заканчивается на одиночный e 
     * (т.е. любой согласный + e) то e переходит в o / ö: Ce > Co / Cö1)
     * 
     * @param String $stem 2nd stem
     */

    public static function indPres1SingByStem($stem, $is_backV) {
        $C = "[".KarGram::consSet()."]’?";
        if (preg_match("/^(.+".$C.")e$/u", $stem, $regs)) {
            $stem = $regs[1].KarGram::garmVowel($is_backV,'o');
        }
        
        return $stem. KarGram::garmVowel($is_backV,'u');
    }
    
    /**
     * 16. индикатив, имперфект, 1 л., мн.ч., положительная форма 
     * 
     * TVER:
     * = основа 4, при этом если основа 4 заканчивается на
     * 1) СV: + ma / mä 
     * 2) VV: + mma / mmä
     * 
     * OTHERS:
     * о.4 + ma / mä
     * 
     * @param String $stem
     */
    public static function indImp1PlurByStem($stem, $harmony, $dialect_id) {
        if (!$stem) {
            return '';
        }
        $C="[".KarGram::consSet()."]’?";        
        $V="[".KarGram::vowelSet()."]";
        $out = [];
        
        foreach (preg_split("/\s*\/\s*/",$stem) as $base) {
            if ($dialect_id == 47 && preg_match("/".$V.$V."$/u", $base)) {
                $out[] = $base. KarGram::garmVowel($harmony, 'mma');
            } else {
                $out[] = $base. KarGram::garmVowel($harmony, 'ma');
            }            
        }
        return join(', ', $out);    
        
/*        $last_let = mb_substr($stem, -1, 1);
        if (!KarGram::isVowel($last_let)) {
            return '';
        }
        $stem_a = (KarGram::isBackVowels($stem) ? 'a': 'ä');
        $before_last_let = mb_substr($stem, -2, 1);
        if (KarGram::isConsonant($before_last_let)) {
            return $stem.'m'.$stem_a;             
        } else {
            return $stem.'mm'.$stem_a;             
        }*/
    }
    
    /**
     * 17. индикатив, имперфект, 2 л., мн.ч., пол.
     * 
     * если о.4 заканч. на 
     * 1) VV: + tta/ttä
     * 2) CV: + ja/jä
     * 
     * @param String $stem
     */
    public static function indImp2PlurByStem($stem, $harmony) {
        if (!$stem) {
            return '';
        }
        $V="[".KarGram::vowelSet()."]";
        $out = [];
        
        foreach (preg_split("/\s*\/\s*/",$stem) as $base) {
            if (preg_match("/".$V.$V."$/u", $base)) {
                $out[] = $base. KarGram::garmVowel($harmony, 'tta');
            } else {
                $out[] = $base. KarGram::garmVowel($harmony, 'ja');
            }            
        }
        return join(', ', $out);    
        
/*        $last_let = mb_substr($stem, -1, 1);
        if (!KarGram::isVowel($last_let)) {
            return '';
        }
        $stem_a = (KarGram::isBackVowels($stem) ? 'a': 'ä');
        $before_last_let = mb_substr($stem, -2, 1);
        if (KarGram::isConsonant($before_last_let)) {
            return $stem.'j'.$stem_a;             
        } else {
            return $stem.'tt'.$stem_a;             
        }*/
    }
    
    /**
     * Imperative base (BESIDES TVER)
     * Если о.5 (конеч. t > k) заканч. на
     * 1) СV, то + kk
     * 2) VV или C, то + k
     * 
     * @param string $stem5
     */
    public static function impBase($stem5) {
        if (!$stem5) {
            return '';
        }
        $C="[".KarGram::consSet()."]’?";
        $V="[".KarGram::vowelSet()."]";
        
        if (preg_match("/^(.+)t$/", $stem5, $regs)) {
            $stem5 = $regs[1].'k';
        }
        
        if (preg_match("/".$C.$V."$/u", $stem5)) {
            return $stem5. 'kk';
        } else {
            return $stem5. 'k';
        }
    }

    
    /**
     * 50. императив, 3 л., ед.ч., пол
     * 
     * FOR TVER:
     * основа 5 + kkah / kkäh (если основа 5 оканчивается на одиночный гласный (т.е. любой согласный + любой гласный): СV)
     * + gah / gäh (если основа 5 оканчивается на дифтонг (т.е. два гласных> VV) или согласные l, n, r)
     * + kah / käh (если основа 5 оканчивается на s, š)
     * + kah / käh (если основа 5 оканчивается на n, а начальная форма заканчивается на ta / tä, при этом конечный согласный n основы 7 переходит в k: n > k)

     * 
     * OTHERS:
     * impBase($stem) +ah/äh
     * 
     * @param String $stem 2nd stem
     */

    public static function imp3PolByStem($stem, $lemma, $harmony, $dialect_id) {
        if (!$stem) {
            return '';
        }
        
        if ($dialect_id != 47) {
            return self::impBase($stem). KarGram::garmVowel($harmony,'ah');
        }
        
        $stem_for_search = Grammatic::toSearchForm($stem);
        $last_let = mb_substr($stem_for_search, -1, 1);
        $before_last_let = mb_substr($stem_for_search, -2, 1);
        
        if (KarGram::isConsonant($before_last_let) && KarGram::isVowel($last_let)) {
            return $stem. KarGram::garmVowel($harmony,'kkah');
        } elseif ($last_let=='n' && preg_match("/t[aä]$/u", $lemma)) {
            return mb_substr($stem, 0, -1). KarGram::garmVowel($harmony,'kkah');
        } elseif (KarGram::isVowel($before_last_let) && KarGram::isVowel($last_let) 
                || in_array($last_let, ['l', 'n', 'r'])) {
            return $stem. KarGram::garmVowel($harmony,'gah');
        } elseif (in_array($last_let, ['s', 'š'])) {
            return $stem. KarGram::garmVowel($harmony,'kah');
        }
        return $stem;
    }
    
    /**
     * 52. императив, 2 л., мн.ч., пол
     * 
     * FOR TVER:
     * основа 5 + kkua / kkiä (если основа 5 оканчивается на одиночный гласный (т.е. любой согласный + любой гласный): CV)
     * + gua / giä (если основа 5 оканчивается на дифтонг (т.е. два гласных: VV) или согласные l, n, r)
     * + kua / kiä (если основа 5 оканчивается на s, š)
     * + kua / kiä (если основа 5 оканчивается на n, а начальная форма заканчивается на ta / tä, при этом конечный согласный n основы 7 переходит в k: n > k)

     * 
     * OTHERS:
     * impBase($stem) + ua/yä
     * 
     * @param String $stem 2nd stem
     */

    public static function imp2PlurPolByStem($stem, $lemma, $harmony, $dialect_id) {
        if (!$stem) {
            return '';
        }
        
        if ($dialect_id != 47) {
            return self::impBase($stem). ($harmony ? 'ua' : 'yä');
        }
        
        $stem_for_search = Grammatic::toSearchForm($stem);
        $last_let = mb_substr($stem_for_search, -1, 1);
        $before_last_let = mb_substr($stem_for_search, -2, 1);
        $UA = $harmony ? 'ua': 'iä';

        if (KarGram::isConsonant($before_last_let) && KarGram::isVowel($last_let)) {
            return $stem. 'kk'. $UA;
        } elseif (in_array($last_let, ['s', 'š'])) {
            return $stem. 'k'. $UA;
        } elseif ($last_let=='n' && preg_match("/t[aä]$/u", $lemma)) {
            return mb_substr($stem, 0, -1). 'kk'. $UA;
        } elseif (KarGram::isVowel($before_last_let) && KarGram::isVowel($last_let)
                || in_array($last_let, ['l', 'n', 'r'])) {
            return $stem. 'g'. $UA;
        }
    }
    
    /**
     * Conditional base
     * 
     * FOR TVER:
     * основа 4 + zi 
     * при этом, если с.ф. заканчивается на ua/iä, то в основе 4 замены конеч. гл.: o > a, i > a / ä)
     * 
     * OTHERS:
     * Если о.2 (Сe > Ci) заканч. на 
     * 1) Ci, то + si;
     * 2) CV (кроме Сi) и в о.2 2 или 4 слога, то + si;
     * 3) CV (кроме Сi) и в о.2 3 слога, то + isi;
     * 4) VV и с.ф. на ta, tä, то + si;
     * 5) VV и с.ф. на va, vä, ha, hä, ja, jä, то – первый V из VV → + isi
     * 
     * Упрощаем правила:
     * Если о.2 (Сe > Ci) заканч. на 
     * 1) CV (кроме Сi) и в о.2 3 слога, то + isi;
     * 2) VV и с.ф. на [vhj][aä], то – первый V из VV → + isi
     * 3) в остальных случаях + si
     * 
     * @param string $stem0
     * @param string $stem2
     * @param string $stem4
     * @param int $dialect_id
     * @return string
     */
    public static function condBase($stem0, $stem2, $stem4, $dialect_id) {
        if ($dialect_id == 47) {
            return self::stemModify($stem4, $stem0, "ua|iä", ['o'=>'a', 'i'=> ['a','ä']]). 'zi';
        }
        
        $C = "[".KarGram::consSet()."]’?";
        $V = "[".KarGram::vowelSet()."]";
        $V_wI = "[".KarGram::vowelSetWithoutI()."]";
        if (preg_match("/^(.+".$C.")e$/u", $stem2, $regs)) {
            $stem2 = $regs[1].'i';
        }
        
        if (preg_match("/".$C.$V_wI."$/u", $stem2) 
                && KarGram::countSyllable($stem2)==3) {
            return $stem2. 'isi';
        } elseif (preg_match("/^(.+)".$V."(".$V.")$/u", $stem2, $regs)
                && preg_match("/[vhj][aä]$/u", $stem0)) {
            return $regs[1]. $regs[2]. 'isi';            
        }
        return $stem2. 'si';
    }

    /**
     * 73. кондиционал, имперфект, 3 л., ед.ч., пол
     * 
     * TVER:
     * основа 4 + s’ (если основа 4 заканчивается на i)
     * + is’ (если основа 4 НЕ заканчивается на i, при этом, 
     * если начальная форма заканчивается на ua / iä, 
     * то последний гласный основы 4 o или i меняется на a / ä: o > a, i > a / ä)
     * 
     * OTHERS:
     * Если о.2 (Сe > Ci) заканч. на 
     * 1) Ci, то + s;
     * 2) VV и с.ф. на va, vä, ha, hä, ja, jä, то – первый V из VV → + is
     * 3) в остальных случаях + is;
     * 
     * @param String $stem 2nd stem
     */

    public static function condImp3SingPolByStem($stem0, $stem2, $stem4, $harmony, $dialect_id) {
        if ($dialect_id != 47) {
            if (!$stem2) {
                return '';
            }
            $stem2 = KarGram::replaceCV($stem2, 'e', 'i');
            $C = "[".KarGram::consSet()."]’?";
            $V = "[".KarGram::vowelSet()."]";
            
            if (preg_match("/".$C."i$/u", $stem2)) {
                return $stem2. 's';
            } elseif (preg_match("/^(.+)".$V."(".$V.")$/u", $stem2, $regs)
                    && preg_match("/[vhj][aä]$/u", $stem0)) {
                return $regs[1]. $regs[2]. 'is';            
            }
            return $stem2. 'is';
        }
        
        if (!$stem4) {
            return '';
        }
//dd("$stem, $lemma");
        if (preg_match("/^(.+)i$/u",$stem4, $regs)) {
            if (preg_match("/(ua|iä)$/u",$stem0)) {
                return $regs[1]. KarGram::garmVowel($harmony,'a'). 'is’';
            }            
            return $stem4. 's’';
        }
        if (preg_match("/(ua|iä)$/u",$stem0) && preg_match("/^(.+)o$/u", $stem4, $regs)) {
            $stem4 = $regs[1]. 'a';
        }
        return $stem4. 'is’';
    }
    
    /**
     * 132. II инфинитив, инессив 
     * 
     * FOR TVER:
     * с.ф. + s’s’a / ssä (если с.ф. заканчивается на VV)
     * + šša / ššä (если начальная форма заканчивается на Ca / Cä, при этом a/ä > e)     
     * 
     * OTHERS:
     * 1) Если с.ф. заканч. на ua, uo, то + s’s’a;
     * 2) в остальных случаях = с.ф. (Сa, Cä > Ce) + šša~ššä
     * 
     * @param String $stem
     */
    public static function inf2Ines($stem, $harmony, $dialect_id) {
        if ($dialect_id != 47) {
            if (preg_match("/u[ao]$/u", $stem, $regs)) {
                return $stem. "s’s’a";
            }
            
            $C = "[".KarGram::consSet()."]’?";
            if (preg_match("/^(.+".$C.")[aä]$/u", $stem, $regs)) {
                $stem = $regs[1].'e';
            }
            return $stem. KarGram::garmVowel($harmony,'šša');
        }
        
        $stem_for_search = Grammatic::toSearchForm($stem);
        $last_let = mb_substr($stem_for_search, -1, 1);
        $before_last_let = mb_substr($stem_for_search, -2, 1);
        
        if (KarGram::isVowel($before_last_let) && KarGram::isVowel($last_let)) {
            return $stem. (KarGram::isBackVowels($stem) ? 's’s’a': 'ssä');
        } elseif (KarGram::isConsonant($before_last_let) && preg_match("/^(.+)[aä]$/u", $stem, $regs)) {
            return $regs[1]. 'ešš'. KarGram::garmVowel($harmony,'a');
        }
        return $stem;
    }
    
    /**
     * 133. II инфинитив, инструктив  
     * 
     * FOR TVER:
     * начальная форма + n (Ca/Cä > Ce)
     * 
     * OTHERS:
     * 1) Если с.ф. заканч. на VV (кроме ie), то = о.2 + en 
     * 2) в остальных случаях = с.ф. (Сa/Cä > Ce) + n
     * 
     * @param String $stem0
     */
    public static function inf2Inst($stem0, $stem2, $dialect_id) {
        if ($dialect_id != 47) {
            $V = "[".KarGram::vowelSet()."]";
            if (!preg_match("/ie$/u", $stem0) && preg_match("/".$V.$V."$/u", $stem0)) {
               return $stem2. 'en'; 
            }          
        }
        $C = "[".KarGram::consSet()."]’?";
        if (preg_match("/^(.+".$C.")[aä]$/u", $stem0, $regs)) {
            $stem0 = $regs[1].'e';
        }
        return $stem0. 'n';
        
/*        $stem_for_search = Grammatic::toSearchForm($stem0);
        $before_last_let = mb_substr($stem_for_search, -2, 1);
        
        if (KarGram::isConsonant($before_last_let) && preg_match("/^(.+)[aä]$/u", $stem0, $regs)) {
            $stem0 = $regs[1]. 'e';
        }
        return $stem0. 'n';*/
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
                    return $regs[1]. (KarGram::isBackVowels($stem) ? $replacement[0]: $replacement[1]);
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
     * Ввожу замену в  о.5 (t > n) 
     * 
     * основа 5 + n (если основа 5 заканчивается на СV)
     * + nun (если основа 5 заканчивается на дифтонг (два гласных подряд: VV) или n)
     * + lun (если основа 5 заканчивается на l)
     * + run (если основа 5 заканчивается на r)
     * + sun (если основа 5 заканчивается на s)
     * + šun (если основа 5 заканчивается на š)
     * 
     * @param String $stem
     */
    public static function perfectForm($stem, $harmony, $lang_id) {
        if (!$stem) {
            return '';
        }
        $stem = preg_replace("/t$/","n",$stem);
        
        
        $stem_for_search = Grammatic::toSearchForm($stem);
        $last_let = mb_substr($stem_for_search, -1, 1);
        $before_last_let = mb_substr($stem_for_search, -2, 1);
        
        if (KarGram::isConsonant($before_last_let) && KarGram::isVowel($last_let)) {
            return $stem. 'n';
        } elseif (KarGram::isVowel($before_last_let) && KarGram::isVowel($last_let)) {
            return $stem. KarGram::garmVowel($harmony, 'nun');
        } elseif (in_array($last_let, ['n', 'l', 'r', 's', 'š'])) {
            return $stem. $last_let. KarGram::garmVowel($harmony, 'un');
        }
    }
    
    /**
     * Active base
     * 
     * FOR TVER:
     * Если о.5 заканчивается на
     * 1) СV: + nn
     * 2) VV: + n
     * 3) [nlrsš], то + эта буква;
     * 
     * FOR OTHERS:
     * Если о.5 (t > n) заканч. на 
     * 1) V, h, то + n;
     * 2) [nlrsš], то + эта буква;
     * 
     * @param type $stem
     * @param type $dialect_id
     * @return string
     */
    public static function activeBase($stem5, $dialect_id) {
        if (!$stem5) {
            return '';
        }

        if (preg_match("/^(.+)([nlrsš]’?)$/u", $stem5, $regs)) {
            return $regs[1].$regs[2].$regs[2];
        }
        
        $C="[".KarGram::consSet()."]’?";
        $V="[".KarGram::vowelSet()."]";
//        $V_h="[".KarGram::vowelSet()."h]";
        
        if ($dialect_id !=47) {
            $stem5 = preg_replace("/t$/","n",$stem5);
        } elseif (preg_match("/".$C.$V."$/u", $stem5)) {
            return $stem5. 'nn';
        }
        
        return $stem5. 'n';
        
    }
    
    /**
     * 140. актив, 2-е причастие (karelian proper)
     * 
     * 
     * @param String $stem
     */
    public static function partic2active($stem, $harmony, $dialect_id) {
        if (!$stem) {
            return '';
        }
        $U = $dialect_id != 47 ? KarGram::garmVowel($harmony, 'u') : 'u';
        
        return self::activeBase($stem, $dialect_id). $U. ($dialect_id == 47 ? 'n' : 't');
/*        
        $stem_for_search = Grammatic::toSearchForm($stem);
        $last_let = mb_substr($stem_for_search, -1, 1);
        $before_last_let = mb_substr($stem_for_search, -2, 1);
        
        if (KarGram::isConsonant($before_last_let) && KarGram::isVowel($last_let)) {
            return $stem. 'nnun';
        } elseif (KarGram::isVowel($before_last_let) && KarGram::isVowel($last_let)) {
            return $stem. 'nun';
        } elseif (in_array($last_let, ['n', 'l', 'r', 's', 'š'])) {
            return $stem. $last_let. 'un';
        }*/
    }
    
    public static function indImperfConnegPl($stem7, $harmony) {
        if (!$stem7) { return ''; }
        return $stem7. KarGram::garmVowel($harmony,'u');
    }

    /**
     * 
     * @param String $stem
     * @param String $affix
     * @param Int $dialect_id
     */
    public static function potencialForm($stem, $affix, $dialect_id) {
        if (!$stem) {
            return '';
        }
        
        if ($dialect_id!=47) {
            return self::activeBase($stem, $dialect_id). $affix;
        }
        
        if (preg_match("/^(.+)([nlrsš]’?)$/u", $stem, $regs)) {
            return $regs[1]. $regs[2]. $regs[2]. $affix;
        }
        
        $V="[".KarGram::vowelSet()."]";
        if (preg_match("/".$V."$/u", $stem)) {
            return $stem. 'nn'. $affix;
        }
/*        
        $stem_for_search = Grammatic::toSearchForm($stem);
        $last_let = mb_substr($stem_for_search, -1, 1);
        
        if (KarGram::isVowel($last_let)) {
            return $stem. 'nn'.$affix;
        } elseif (in_array($last_let, ['n', 'l', 'r', 's', 'š'])) {
            return $stem. $last_let. $affix;
        }*/
    }
    
    /**
     * Infinitive III forms
     * 
     * @param type $stem2
     * @param type $dialect_id
     */
    public static function Inf3Form($stem2, $morf, $harmony, $dialect_id) {
        if ($dialect_id != 47) {
            $stem2 = KarGram::replaceCV($stem2, 'e', KarGram::garmVowel($harmony, 'o'));
        }
        return $stem2. KarGram::garmVowel($harmony, $morf);
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
    public static function toRightTemplate($bases, $base_list, $lemma_str, $num) {
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
    
    public static function getAffixesForGramset($gramset_id) {
        switch ($gramset_id) {
            case 26: // 1. индикатив, презенс, 1 л., ед.ч., пол. 
            case 32: // 13. индикатив, имперфект, 1 л., ед.ч., пол. 
            case 172: // 133. II инфинитив, инструктив  
                return ['n'];
            case 27: // 2. индикатив, презенс, 2 л., ед.ч., пол. 
            case 33: // 14. индикатив, имперфект, 2 л., ед.ч., пол. 
                return ['t'];
            case 28: // 3. индикатив, презенс, 3 л., ед.ч., пол. 
            case 298: // 147. индикатив, имперфект, коннегатив, мн.ч.
            case 85: // 24. индикатив, имперфект, 3 л., мн.ч., отриц. 
            case 91: // 30. индикатив, перфект, 3 л., мн.ч., пол. 
            case 97: // 36. индикатив, перфект, 3 л., мн.ч., отриц. 
            case 103: // 42. индикатив, плюсквамперфект, 3 л., мн.ч., пол. 
            case 109: // 48. индикатив, плюсквамперфект, 3 л., мн.ч., отриц. 
            case 139: // 100. кондиционал, плюсквамперфект, 3 л., мн.ч., пол. 
            case 145: // 106. кондиционал, плюсквамперфект, 3 л., мн.ч., отр. 
            case 163: // 124. потенциал, перфект, 3 л., мн.ч., пол. 
            case 169: // 130. потенциал, перфект, 3 л., мн.ч., отр. 
            case 181: // 143. пассив, 2-е причастие 
                return ['u', 'y'];
            case 29: // 4. индикатив, презенс, 1 л., мн.ч., пол. 
                return ['mma', 'mmä'];
            case 30: // 5. индикатив, презенс, 2 л., мн.ч., пол. 
                return ['tta', 'ttä'];
            case 31: // 6. индикатив, презенс, 3 л., мн.ч., пол. 
                return ['h'];

            case 35: // 16. индикатив, имперфект, 1 л., мн.ч., пол. 
                return ['ma', 'mä']; // 'mma', 'mmä'
            case 36: // 17. индикатив, имперфект, 2 л., мн.ч., пол. 
                return ['ja', 'jä', 'tta', 'ttä'];
            case 37: // 18. индикатив, имперфект, 3 л., мн.ч., пол. 
                return ['ih'];
            case 282: // 141. актив, 2-е причастие  (сокращенная форма); перфект (форма основного глагола)
            case 297: // 146. индикатив, имперфект, коннегатив, ед.ч.
            case 80: // 19. индикатив, имперфект, 1 л., ед.ч., отриц. 
            case 81: // 20. индикатив, имперфект, 2 л., ед.ч., отриц. 
            case 83: // 22. индикатив, имперфект, 1 л., мн.ч., отриц. 
            case 84: // 23. индикатив, имперфект, 2 л., мн.ч., отриц. 
            case 82: // 21. индикатив, имперфект, 3 л., ед.ч., отриц. 
            case 86: // 25. индикатив, перфект, 1 л., ед.ч., пол. 
            case 87: // 26. индикатив, перфект, 2 л., ед.ч., пол. 
            case 89: // 28. индикатив, перфект, 1 л., мн.ч., пол. 
            case 90: // 29. индикатив, перфект, 2 л., мн.ч., пол. 
            case 88: // 27. индикатив, перфект, 3 л., ед.ч., пол. 
            case 92: // 31. индикатив, перфект, 1 л., ед.ч., отриц. 
            case 93: // 32. индикатив, перфект, 2 л., ед.ч., отриц. 
            case 95: // 34. индикатив, перфект, 1 л., мн.ч., отриц. 
            case 96: // 35. индикатив, перфект, 2 л., мн.ч., отриц. 
            case 94: // 33. индикатив, перфект, 3 л., ед.ч., отриц. 
            case 98: // 37. индикатив, плюсквамперфект, 1 л., ед.ч., пол. 
            case 99: // 38. индикатив, плюсквамперфект, 2 л., ед.ч., пол. 
            case 101: // 40. индикатив, плюсквамперфект, 1 л., мн.ч., пол. 
            case 102: // 41. индикатив, плюсквамперфект, 2 л., мн.ч., пол. 
            case 100: // 39. индикатив, плюсквамперфект, 3 л., ед.ч., пол. 
            case 104: // 43. индикатив, плюсквамперфект, 1 л., ед.ч., отриц. 
            case 105: // 44. индикатив, плюсквамперфект, 2 л., ед.ч., отриц. 
            case 107: // 46. индикатив, плюсквамперфект, 1 л., мн.ч., отриц. 
            case 108: // 47. индикатив, плюсквамперфект, 2 л., мн.ч., отриц. 
            case 106: // 45. индикатив, плюсквамперфект, 3 л., ед.ч., отриц. 
            case 135: // 95. кондиционал, плюсквамперфект, 1 л., ед.ч., пол. 
            case 125: // 96. кондиционал, плюсквамперфект, 2 л., ед.ч., пол. 
            case 136: // 97. кондиционал, плюсквамперфект, 3 л., ед.ч., пол. 
            case 137: // 98. кондиционал, плюсквамперфект, 1 л., мн.ч., пол. 
            case 138: // 99. кондиционал, плюсквамперфект, 2 л., мн.ч., пол.                 
            case 140: // 101. кондиционал, плюсквамперфект, 1 л., ед.ч., отр. 
            case 141: // 102. кондиционал, плюсквамперфект, 2 л., ед.ч., отр. 
            case 142: // 103. кондиционал, плюсквамперфект, 3 л., ед.ч., отр. 
            case 143: // 104. кондиционал, плюсквамперфект, 1 л., мн.ч., отр. 
            case 144: // 105. кондиционал, плюсквамперфект, 2 л., мн.ч., отр. 
            case 158: // 119. потенциал, перфект, 1 л., ед.ч., пол. 
            case 159: // 120. потенциал, перфект, 2 л., ед.ч., пол. 
            case 160: // 121. потенциал, перфект, 3 л., ед.ч., пол. 
            case 161: // 122. потенциал, перфект, 1 л., мн.ч., пол. 
            case 162: // 123. потенциал, перфект, 2 л., мн.ч., пол.                 
            case 164: // 125. потенциал, перфект, 1 л., ед.ч., отр. 
            case 165: // 126. потенциал, перфект, 2 л., ед.ч., отр. 
            case 166: // 127. потенциал, перфект, 3 л., ед.ч., отр. 
            case 167: // 128. потенциал, перфект, 1 л., мн.ч., отр. 
            case 168: // 129. потенциал, перфект, 2 л., мн.ч., отр. 
                return ['n', 'nun', 'lun', 'run', 'sun', 'šun', 'nyn', 'lyn', 'ryn', 'syn', 'šyn'];
            case 52: // 50. императив, 3 л., ед.ч., пол 
            case 55: // 53. императив, 3 л., мн.ч., пол 
            case 74: // 55. императив, 3 л., ед.ч., отр. 
            case 77: // 58. императив, 3 л., мн.ч., отр. 
                return ['kah', 'käh', 'gah', 'gäh']; // 'kkah', 'kkäh'
            case 54: // 52. императив, 2 л., мн.ч., пол 
            case 76: // 57. императив, 2 л., мн.ч., отр. 
                return ['kua', 'kiä', 'gua', 'giä']; // 'kkua', 'kkiä'                
            case 44: // 71. кондиционал, имперфект, 1 л., ед.ч., пол. 
                return ['zin'];
            case 45: // 72. кондиционал, имперфект, 2 л., ед.ч., пол. 
                return ['zit'];
            case 46: // 73. кондиционал, имперфект, 3 л., ед.ч., пол. 
            case 116: // 77. кондиционал, имперфект, 1 л., ед.ч., отр. 
            case 117: // 78. кондиционал, имперфект, 2 л., ед.ч., отр. 
            case 119: // 80. кондиционал, имперфект, 1 л., мн.ч., отр. 
            case 120: // 81. кондиционал, имперфект, 2 л., мн.ч., отр. 
            case 118: // 79. кондиционал, имперфект, 3 л., ед.ч., отр. 
                return ['s’']; // is’ 
            case 47: // 74. кондиционал, имперфект, 1 л., мн.ч., пол. 
                return ['zima', 'zimä'];
            case 48: // 75. кондиционал, имперфект, 2 л., мн.ч., пол. 
                return ['zija', 'zijä'];
            case 49: // 76. кондиционал, имперфект, 3 л., мн.ч., пол. 
            case 121: // 82. кондиционал, имперфект, 3 л., мн.ч., отр. 
                return ['ais’', 'äis’'];                               
            case 146: // 107. потенциал, презенс, 1 л., ед.ч., пол. 
                return ['nen', 'len', 'ren', 'sen', 'šen']; // nnen
            case 147: // 108. потенциал, презенс, 2 л., ед.ч., пол. 
                return ['net', 'let', 'ret', 'set', 'šet']; // nnet
            case 148: // 109. потенциал, презенс, 3 л., ед.ч., пол. 
                return ['nou', 'nöy', 'lou', 'löy', 'rou', 'röy', 'sou', 'söy', 'šou', 'šöy']; // 'nnou', 'nnöy'
            case 149: // 110. потенциал, презенс, 1 л., мн.ч., пол. 
                return ['nemma', 'nemmä', 'lemma', 'lemmä', 'remma', 'remmä', 'semma', 'semmä', 'šemma', 'šemmä']; // 'nnemma', 'nnemmä'
            case 150: // 111. потенциал, презенс, 2 л., мн.ч., пол. 
                return ['netta', 'nettä', 'letta', 'lettä', 'retta', 'rettä', 'setta', 'settä', 'šetta', 'šettä']; // 'nnemma', 'nnemmä'
            case 151: // 112. потенциал, презенс, 3 л., мн.ч., пол. 
                return ['anneh', 'änneh'];
            case 310: // 158. потенциал, презенс, коннегатив 
            case 152: // 113. потенциал, презенс, 1 л., ед.ч., отр. 
            case 153: // 114. потенциал, презенс, 2 л., ед.ч., отр. 
            case 155: // 116. потенциал, презенс, 1 л., мн.ч., отр. 
            case 156: // 117. потенциал, презенс, 2 л., мн.ч., отр. 
            case 154: // 115. потенциал, презенс, 3 л., ед.ч., отр. 
                return ['ne', 'le', 're', 'se', 'še']; // nne
            case 311: // 159. потенциал, презенс, коннегатив, 3 л. мн.ч.
            case 157: // 118. потенциал, презенс, 3 л., мн.ч., отр. 
                return ['anne', 'änne'];                
            case 171: // 132. II инфинитив, инессив 
                return ['s’s’a', 'ssä', 'šša', 'ššä']; 
            case 173: // 134. III инфинитив, адессив
                return ['malla', 'mällä'];
            case 174: // 135. III инфинитив, иллатив 
                return ['mah', 'mäh'];
            case 175: // 136. III инфинитив, инессив 
                return ['mašša', 'mäššä'];
            case 176: // 137. III инфинитив, элатив 
                return ['mašta', 'mäštä'];
            case 177: // 138. III инфинитив, абессив 
                return ['matta', 'mättä'];
                
            case 178: // 139. актив, 1-е причастие 
                return ['ja', 'jä'];
            case 179: // 140. актив, 2-е причастие 
                return ['un', 'yn']; // nnun, 'nun', 'lun', 'run', 'sun', 'šun', 'l’un'
            case 180: // 142. пассив, 1-е причастие 
                return ['ava', 'ävä'];
        }
        return [];
    }
       
    public static function auxVerb($gramset_id, $dialect_id, $negative=NULL) {
        if (in_array($gramset_id,[86])) { // Perf1Sg
            $aux = 'olen';
        } elseif (in_array($gramset_id,[87])) { // Perf2Sg
            $aux =  'olet';
        } elseif (in_array($gramset_id,[88, 91])) { // Perf3
            $aux =  'on';
        } elseif (in_array($gramset_id,[89])) { // Perf1Pl
            $aux =  'olemma';
        } elseif (in_array($gramset_id,[90])) { // Perf2Pl
            $aux = 'oletta';
        } elseif (in_array($gramset_id,[92,93,94,95,96,97])) { // PerfNeg
            $aux = 'ole';
            
        } elseif (in_array($gramset_id,[98])) { // Plus1Sg
            $aux = 'olin';
        } elseif (in_array($gramset_id,[99])) { // Plus2Sg
            $aux = 'olit';
        } elseif (in_array($gramset_id,[100, 103])) { // Perf3
            $aux = 'oli';
        } elseif (in_array($gramset_id,[101])) { // Plus1Pl
            $aux = 'olima';
        } elseif (in_array($gramset_id,[102])) { // Plus2Pl
            $aux = 'olija';
        } elseif (in_array($gramset_id,[104,105,107,108,106])) { // PlusNeg without PlusSgNeg
            $aux = 'ollun';
        } elseif (in_array($gramset_id,[109])) { // PlusSgNeg
            $aux = $dialect_id==47 ? 'oldu': 'oltu';
            
        } elseif (in_array($gramset_id,[135])) { // CondPlus1Sg
            $aux = $dialect_id==47 ? 'olizin' : 'olisin';
        } elseif (in_array($gramset_id,[125])) { // CondPlus2Sg
            $aux = $dialect_id==47 ? 'olizit' : 'olisit';
        } elseif (in_array($gramset_id,[136,139, 140,141,142,143,144,145])) { // CondPerf3, CondPlusNeg
            $aux = $dialect_id==47 ? 'olis’' : 'olis';
        } elseif (in_array($gramset_id,[137])) { // CondPlus1Pl
            $aux = $dialect_id==47 ? 'olizima' : 'olisima';
        } elseif (in_array($gramset_id,[138])) { // CondPlus2Pl
            $aux = $dialect_id==47 ? 'olizija' : 'olisija';
            
        } elseif (in_array($gramset_id,[158])) { // PotPerf1Sg
            $aux = 'lienen';
        } elseif (in_array($gramset_id,[159])) { // PotPerf2Sg
            $aux = 'lienet';
        } elseif (in_array($gramset_id,[160, 163])) { // PotPerf3
            $aux = 'lienöy';
        } elseif (in_array($gramset_id,[161])) { // PotPerf1Pl
            $aux = 'lienemmä';
        } elseif (in_array($gramset_id,[162])) { // PotPerf2Pl
            $aux = 'lienettä';
        } elseif (in_array($gramset_id,[164,165,166,167,168,169])) { // PotPerfNeg
            $aux = 'liene';
        }
        if (!isset($aux)) {
            return '';
        } elseif ($negative=='-') {
            return Grammatic::interLists(trim(self::negVerb($gramset_id, $dialect_id)), $aux);
        } 
        return $aux;
    }
    
    public static function negVerb($gramset_id, $dialect_id) {
        if (in_array($gramset_id,[70, 80, 92, 104, 110, 116, 140, 152, 164])) { // 1Sg IndPres, IndImperf, IndPerf, IndPlus, CondImp, CondPlus, PotPrs
            return 'en';
        } elseif (in_array($gramset_id,[71, 81, 93, 105, 111, 117, 141, 153, 165])) { // 2Sg
            return 'et';
        } elseif (in_array($gramset_id,[72, 82, 94, 107, 112, 118, 142, 154, 166, 79, 85, 97, 109, 115, 121, 145, 157, 169])) { // 3Sg, 3Pl
            return 'ei';
        } elseif (in_array($gramset_id,[73, 83, 95, 108, 113, 119, 143, 155, 167])) { // 1Pl
            return 'emmä';
        } elseif (in_array($gramset_id,[78, 84, 96, 106, 114, 120, 144, 156, 168])) { // 2Pl
            return 'että';
        } elseif ($gramset_id ==50) { // Imperative2Sg
            return 'elä';
        } elseif (in_array($gramset_id,[74, 77])) { // Imperative3SgPl
            return $dialect_id == 47 ? 'elgäh' : 'elkäh';        
        } elseif ($gramset_id ==75) { // Imperative1Pl
            return $dialect_id == 47 ? '' : 'elkä';        
        } elseif ($gramset_id ==76) { // Imperative2Pl
            return $dialect_id == 47 ? 'elgiä' : 'elkyä';        
        }
    }

}