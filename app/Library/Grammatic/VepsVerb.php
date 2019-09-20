<?php

namespace App\Library\Grammatic;

use App\Library\Grammatic;
use App\Library\Grammatic\VepsGram;

use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;

class VepsVerb
{
    /**
     * 0 = infinitive 1 (=lemma)
     * 1 = base of indicative presence 3 sg  (indicative presence 1 sg - 'b')
     * 2 = indicative imperfect 3 sg
     * 3 = base of 2 active particle  (conditional imperfect 3 sg - 'nuiži')
     * 4 = base of conditional  (conditional presence 3 sg - 'iži')
     * 5 = base of potential (2 active particle - 'nu')

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
            case 1:  // indicative presence 3 sg
                if (preg_match("/^(.+)b$/", $lemma->wordform(28, $dialect_id), $regs)) {
                    return $regs[1];
                }
                return '';
            case 2: // indicative imperfect 3 sg
                $ind_imp_3_sg = $lemma->wordform(34, $dialect_id); 
                return $ind_imp_3_sg ? $ind_imp_3_sg : '';
            case 3: // base of 2 active particle
                return self::getStem3(self::getStemFromWordform($lemma, 0, $dialect_id), self::getStemFromWordform($lemma, 1, $dialect_id));
            case 4: // base of conditional
                return self::getStemCond(self::getStemFromWordform($lemma, 1, $dialect_id));
            case 5: // base of potential
                return self::getStemPoten(self::getStemFromWordform($lemma, 0, $dialect_id), self::getStemFromWordform($lemma, 1, $dialect_id));
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
     * @return array [0=>base_of_infinitive, 1=>base_of_presence, 
     *                2=>base_of_perfect, 3=>base_of_past_actvive_participle,
     *                4=>base_of_conditional, 5=>base_of_potentional, 
     *                6=>consonant (d/t), 7=>vowel (a/ä)]
     */
    public static function stemsFromTemplate($regs) {
        $stems = [];
        if (sizeof($regs)<5) {
            return $stems;
        }
        $base  = $regs[1];
        $past_suff = $regs[4];

        if (!preg_match("/^(.*)([dt])([aä])$/u", $regs[2], $regs1)) {
            return null;
        }
        $inf_suff = $regs1[1];
        $cons = $regs1[2];
        $harmony = $regs1[3];

        if (!preg_match("/^(.*)b$/u", $regs[3], $regs1)) {
            return null;
        }        
        $pres_suff = $regs1[1];
        
        $inf_stem = $base. $inf_suff; // = lemma without [dt][aä]
        $pres_stem = $base. $pres_suff; 
        if (!preg_match("/[aeiouüäö]$/u", $pres_stem)) { // должен оканчиваться на гласную
            return null;
        }
        $past_stem = $base. $past_suff;
        if (!preg_match("/i$/u", $past_stem)) { // должен оканчиваться на i
            return null;
        }
        
        $past_actv_ptcp_stem = self::getStemPAP($inf_stem, $pres_stem);       
        $cond_stem = self::getStemCond($pres_stem);        
        $potn_stem = self::getStemPoten($past_actv_ptcp_stem, $inf_stem, $pres_stem);
        
        return [$inf_stem, $pres_stem, $past_stem, $past_actv_ptcp_stem,
                $cond_stem, $potn_stem, $cons, $harmony];        
    }
    
    /**
     * base of past actvive participle
     */
    public static function getStemPAP($stem0, $stem1) {
        $past_actv_ptcp_stem = $stem0;
        if (preg_match("/^(.+)([kpsšt])$/u", $stem0, $regs1)) {
            $inf_stem_voiced = $regs1[1]. VepsGram::ringConsonant($regs1[2]);
            $pres_stem_novowel = preg_replace("/[aeiouüäö]+$/", "", $stem1);
            if ($inf_stem_voiced == $pres_stem_novowel) {
                $past_actv_ptcp_stem = $inf_stem_voiced;
            }
        }
        return $past_actv_ptcp_stem;
    }
    
    public static function getStemCond($pres_stem) {
        $cond_stem = $pres_stem;
//        if (preg_match("/^(.*[aeiouüäö-]+[^aeiouüäö]+)[eiä]$/u", $pres_stem)) {
        if (preg_match("/^(.+[^aeiouüäö]+)[eiä]$/u", $pres_stem, $regs1) 
                || preg_match("/^(.+[aeiouüäö]+)i$/u", $pres_stem, $regs1)) {
            $cond_stem = $regs1[1];
        }
        return $cond_stem;
    }
    
    public static function getStemPoten($past_actv_ptcp_stem, $inf_stem, $pres_stem) {
        $potn_stem = $past_actv_ptcp_stem;
        if (preg_match("/[aeiouüäö]$/u", $inf_stem, $regs1)) {
            $potn_stem = $pres_stem;
        }
        return $potn_stem;
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
     * @param Lemma $lemma_obj
     * @param Int $dialect_id
     * @return array
     */
    public static function stemsFromWordforms($lemma_obj, $dialect_id) {
        $stems = [null, null, null, null, null, null, null, null];
        $lemma = $lemma_obj->lemma;
        
        if (preg_match("/^(.*)([dt])([aä])$/u", $lemma, $regs)) {
            $stems[0] = $regs[1];
            $stems[6] = $regs[2];
            $stems[7] = $regs[3];
        }
        
        $gramset_pres3sg = 28;
        $wordform_pres3sg = $lemma_obj->wordform($gramset_pres3sg, $dialect_id);
        if ($wordform_pres3sg && preg_match("/^(.*)b$/u", $regs[3], $regs)) {
            $stems[1] = $regs[1];
        }
        
        $gramset_imp3sg = 34;
        $wordform_imp3sg = $lemma_obj->wordform($gramset_imp3sg, $dialect_id);
        if ($wordform_imp3sg && preg_match("/i$/u", $wordform_imp3sg)) {
            $stems[2] = $wordform_imp3sg;
        }
    }
    
    public static function getListIndPres() {
        return [26,  27,  28,  29,  30,  31, 295, 296, 
                70,  71,  72,  73,  78,  79]; 
    }
    
    public static function getListIndImperf() {
        return [32,  33,  34,  35,  36,  37, 297, 298,
                80,  81,  82,  83,  84,  85]; 
    }
    
    public static function getListIndPerf() {
        return [86,  87,  88,  89,  90,  91,  
                92,  93,  94,  95,  96,  97];
    }
    
    public static function getListIndPlus() {
        return [98,  99, 100, 101, 102, 103, 
               104, 105, 107, 108, 106, 109]; 
    }
    
    public static function getListImper() {
        return [51,  52,  55,  53,  54, 299, 300,
                     50,  74,  75,  76,  77]; 
    }
    
    public static function getListCondPres() {
        return [38,  39,  40,  41,  42,  43, 301, 303,
               110, 111, 112, 113, 114, 115];
    }
    
    public static function getListCondImperf() {
        return [44,  45,  46,  47,  48,  49, 302, 304,
               116, 117, 118, 119, 120, 121]; 
    }
    
    public static function getListInf() {
        return [170, 171, 172, 173, 174, 175, 176, 177,
                178, 179, 309, 181]; 
    }
    
    public static function getListForAutoComplete() {
        return array_merge(self::getListIndPres(), self::getListIndImperf(),
//                           self::getListIndPerf(), self::getListIndPlus()
                           self::getListImper(), self::getListCondPres(),
                           self::getListCondImperf(), self::getListInf());
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
    public static function wordformByStems($stems, $gramset_id, $dialect_id) {
        if (in_array($gramset_id, self::getListIndPres())) {
            return self::wordformByStemsIndPres($stems, $gramset_id, $dialect_id);
        } elseif (in_array($gramset_id, self::getListIndImperf())) {
            return self::wordformByStemsIndImperf($stems, $gramset_id, $dialect_id);
/*        } elseif (in_array($gramset_id, self::getListIndPerf())) {
            return self::wordformByStemsIndPerf($stems, $gramset_id, $dialect_id);
        } elseif (in_array($gramset_id, self::getListIndPlus())) {
            return self::wordformByStemsIndPlus($stems, $gramset_id, $dialect_id);
*/        } elseif (in_array($gramset_id, self::getListImper())) {
            return self::wordformByStemsImper($stems, $gramset_id, $dialect_id);
        } elseif (in_array($gramset_id, self::getListCondPres())) {
            return self::wordformByStemsCondPres($stems, $gramset_id, $dialect_id);
        } elseif (in_array($gramset_id, self::getListCondImperf())) {
            return self::wordformByStemsCondImperf($stems, $gramset_id, $dialect_id);
        } elseif (in_array($gramset_id, self::getListInf())) {
            return self::wordformByStemsCondImperf($stems, $gramset_id, $dialect_id);
        }
        
        return '';
    }
    
    public static function wordformByStemsReflex($stems, $gramset_id, $dialect_id) {
        return '';
    }
    
    public static function negVerb($gramset_id, $dialect_id) {
        if (in_array($gramset_id,[70])) { // 1Sg
            return 'en ';
        } elseif (in_array($gramset_id,[71])) { // 2Sg
            return 'ed ';
        } elseif (in_array($gramset_id,[72])) { // 3Sg
            switch ($dialect_id) {
                case 1: // северновепсский 
                case 4: // средневепсский восточный 
                    return 'ii ';
                default:
                    return 'ei ';
            }        
        } elseif (in_array($gramset_id,[73])) { // 1Pl
            switch ($dialect_id) {
                case 3: // южновепсский 
                    return 'emaa ';
                case 5: // средневепсский западный 
                    return 'emei ';
                default:
                    return 'em ';
            }        
        } elseif (in_array($gramset_id,[78])) { // 2Pl
            switch ($dialect_id) {
                case 43: // младописьменный
                    return 'et ';
                case 3: // южновепсский 
                    return 'etaa ';
                case 5: // средневепсский западный 
                    return 'etei ';
                default:
                    return 'ed ';
            }        
        } elseif (in_array($gramset_id,[79])) { // 2Pl
            switch ($dialect_id) {
                case 1: // северновепсский 
                case 5: // средневепсский западный 
                    return 'ii ';
                case 3: // южновепсский 
                    return 'ebad ';
                default:
                    return 'ei ';
            }        
        }
    }
    
    public static function auxVerb($gramset_id, $dialect_id) {
        if (in_array($gramset_id,[86])) { // Perf1Sg
            return 'olen ';
        } elseif (in_array($gramset_id,[87])) { // Perf2Sg
            return 'oled ';
        } elseif (in_array($gramset_id,[88, 91])) { // Perf3Sg
            return 'oma ';
        } elseif (in_array($gramset_id,[89])) { // Perf1Pl
            switch ($dialect_id) {
                case 3: // южновепсский 
                    return 'olemaa ';
                case 5: // средневепсский западный 
                    return 'olemei ';
                default:
                    return 'olem ';
            }        
        } elseif (in_array($gramset_id,[90])) { // Perf1Pl
            switch ($dialect_id) {
                case 1: // северновепсский 
                case 4: // средневепсский восточный 
                    return 'oled ';
                case 3: // южновепсский 
                    return 'oletaa ';
                case 5: // средневепсский западный 
                    return 'oletei ';
                default:
                    return 'olet ';
            }        
        }
    }

    public static function wordformByStemsIndPres($stems, $gramset_id, $dialect_id) {
        $g = VepsGram::rightConsonant($stems[6], 'g');
        $neg_verb = self::negVerb($gramset_id, $dialect_id);

        switch ($gramset_id) {
            case 26: // 1. индикатив, презенс, 1 л., ед.ч., +
                return $stems[1] ? $stems[1].'n' : '';
            case 27: // 2. индикатив, презенс, 2 л., ед.ч., пол. 
                return self::IndPres2Sg($stems[1], $dialect_id);
            case 28: // 3. индикатив, презенс, 3 л., ед.ч., пол. 
                //северновепсский ???????
                return $stems[1] ? $stems[1].'b' : '';
            case 29: // 4. индикатив, презенс, 1 л., мн.ч., +
                return self::IndPres1Pl($stems[1], $dialect_id);
            case 30: // 5. индикатив, презенс, 2 л., мн.ч., +
                return self::IndPres2Pl($stems[1], $dialect_id);
            case 31: // 6. индикатив, презенс, 3 л., мн.ч., +
                return IndPres3Pl($stems[0], $stems[1], $stems[6], $dialect_id);
            case 295: // 144. индикатив, презенс, коннегатив, ед.ч.
                return $stems[1] ? $stems[1] : '';
            case 296: // 145. индикатив, презенс, коннегатив, мн.ч.
                return IndPresConnegPl($stems[0], $stems[1], $g, $dialect_id);

            case 70: // 7. индикатив, презенс, 1 л., ед.ч., -
            case 71: // 8. индикатив, презенс, 2 л., ед.ч., -
            case 72: // 9. индикатив, презенс, 3 л., ед.ч., -
                return $stems[1] ? $neg_verb. $stems[1] : '';
            case 73: //10. индикатив, презенс, 1 л., мн.ч., -
            case 78: // 11. индикатив, презенс, 2 л., мн.ч., -
            case 79: // 12. индикатив, презенс, 3 л., мн.ч., -
                return $stems[0] ? $neg_verb. self::IndPresConnegPl($stems[0], $stems[1], $g, $dialect_id) : '';
        }
    }
    
    public static function wordformByStemsIndImperf($stems, $gramset_id, $dialect_id) {
        switch ($gramset_id) {
            case 32: // 13. индикатив, имперфект, 1 л., ед.ч., +
                return self::IndImperf1Sg($stems[2], $dialect_id);
            case 33: // 14. индикатив, имперфект, 2 л., ед.ч., +
                return self::IndImperf2Sg($stems[2], $dialect_id);
            case 34: // 15. индикатив, имперфект, 3 л., ед.ч., +
                return $stems[2] ? $stems[2] : '';
            case 35: // 16. индикатив, имперфект, 1 л., мн.ч., +
                return self::IndImperf1Pl($stems[2], $dialect_id);
            case 36: // 17. индикатив, имперфект, 2 л., мн.ч., +
                return self::IndImperf2Pl($stems[2], $dialect_id);
            case 37: // 18. индикатив, имперфект, 3 л., мн.ч., +
                return self::IndImperf3Pl($stems[0], $stems[2], $stems[6], $dialect_id);
            case 297: // 146. индикатив, имперфект, коннегатив, ед.ч.
                return IndImperfConnegSg($stems[1], $stems[3], $dialect_id);
            case 298: // 147. индикатив, имперфект, коннегатив, мн.ч.
                return IndImperfConnegPl($stems[0], $stems[1], $stems[3], $stems[6], $dialect_id);

            case 80: // 19. индикатив, имперфект, 1 л., ед.ч., -
            case 81: // 20. индикатив, имперфект, 2 л., ед.ч., -
            case 82: // 21. индикатив, имперфект, 3 л., ед.ч., -
                return self::IndImperfSgNeg($stems[1], $stems[3], $gramset_id, $dialect_id);
            case 83: // 22. индикатив, имперфект, 1 л., мн.ч., -
            case 84: // 23. индикатив, имперфект, 2 л., мн.ч., -
            case 85: // 24. индикатив, имперфект, 3 л., мн.ч., -
                return self::IndImperfPlNeg($stems[0], $stems[1], $stems[3], $stems[6], $gramset_id, $dialect_id);
        }
    }
    
    public static function wordformByStemsIndPerf($stems, $gramset_id, $dialect_id) {
        $neg_verb = self::negVerb($gramset_id, $dialect_id);
        $aux_verb = self::auxVerb($gramset_id, $dialect_id);

        switch ($gramset_id) {
            case 86: // 25. индикатив, перфект, 1 л., ед.ч., +
            case 87: // 26. индикатив, перфект, 2 л., ед.ч., +
            case 88: // 27. индикатив, перфект, 3 л., ед.ч., +
                return self::auxForm($gramset_id, $dialect_id). self::partic2activePl($stems[1], $stems[5], $dialect_id);
            case 89: // 28. индикатив, перфект, 1 л., мн.ч., +
            case 90: // 29. индикатив, перфект, 2 л., мн.ч., +
            case 91: // 30. индикатив, перфект, 3 л., мн.ч., +
                return self::auxForm($gramset_id, $dialect_id). self::partic2activePl($stems[0], $stems[1], $stems[5], $stems[6], $dialect_id);
/*
            case 92: // 31. индикатив, перфект, 1 л., ед.ч., -
            case 93: // 32. индикатив, перфект, 2 л., ед.ч., -
            case 94: // 33. индикатив, перфект, 3 л., ед.ч., -
                return self::auxForm(94, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id);
            case 95: // 34. индикатив, перфект, 1 л., мн.ч., -
            case 96: // 35. индикатив, перфект, 2 л., мн.ч., -
            case 97: // 36. индикатив, перфект, 3 л., мн.ч., -
                return 'ei ole './/self::auxForm(97, $lang_id, $dialect_id). 
                       $stems[7]. self::garmVowel($stems[7],'u');*/
        }
    }
    
    public static function wordformByStemsIndPlus($stems, $gramset_id, $dialect_id) {
        $lang_id = 1;
        $g = VepsGram::rightConsonant($stems[6], 'g');
        $neg_verb = Grammatic::negativeForm($gramset_id, $lang_id);

        switch ($gramset_id) {
/*
            case 98: // 37. индикатив, плюсквамперфект, 1 л., ед.ч., +
            case 99: // 38. индикатив, плюсквамперфект, 2 л., ед.ч., +
            case 100: // 39. индикатив, плюсквамперфект, 3 л., ед.ч., +
                return self::auxForm(100, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id);
            case 101: // 40. индикатив, плюсквамперфект, 1 л., мн.ч., +
            case 102: // 41. индикатив, плюсквамперфект, 2 л., мн.ч., +
            case 103: // 42. индикатив, плюсквамперфект, 3 л., мн.ч., +
                return self::auxForm(103, $lang_id, $dialect_id). $stems[7]. self::garmVowel($stems[7],'u');

            case 104: // 43. индикатив, плюсквамперфект, 1 л., ед.ч., -
            case 105: // 44. индикатив, плюсквамперфект, 2 л., ед.ч., -
            case 107: // 45. индикатив, плюсквамперфект, 3 л., ед.ч., -
                return self::auxForm(106, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id);
            case 108: // 46. индикатив, плюсквамперфект, 1 л., мн.ч., -
            case 106: // 47. индикатив, плюсквамперфект, 2 л., мн.ч., -
            case 109: // 48. индикатив, плюсквамперфект, 3 л., мн.ч., -
                return 'ei oldu '. $stems[7]. self::garmVowel($stems[7],'u'); //self::auxForm(109, $lang_id, $dialect_id)
*/
        }
    }
    
    public static function wordformByStemsImper($stems, $gramset_id, $dialect_id) {
        $lang_id = 1;
        $g = VepsGram::rightConsonant($stems[6], 'g');
        $neg_verb = Grammatic::negativeForm($gramset_id, $lang_id);

        switch ($gramset_id) {
            case 51: // 49. императив, 2 л., ед.ч., пол 
                return $stems[1] ? $stems[1] : '';
            case 52: // 50. императив, 3 л., ед.ч., пол 
            case 55: // 53. императив, 3 л., мн.ч., пол 
                return '';
            case 53: // 51. императив, 1 л., мн.ч., пол 
                return $stems[0] ? $stems[0]. $g. 'am' : '';
            case 54: // 52. императив, 2 л., мн.ч., пол 
                return $stems[0] ? $stems[0]. $g. 'at' : '';
            case 299: // 148. императив, коннегатив, ед.ч.
                return $stems[1] ? $stems[1] : '';
            case 300: // 149. императив, коннегатив, мн.ч.
                return $stems[0] ? $stems[0]. $g. 'oi' : '';
 
            case 50: // 54. императив, 2 л., ед.ч., отр. 
            case 74: // 55. императив, 3 л., ед.ч., отр. 
                return $stems[1] ? $neg_verb. $stems[1] : '';
            case 75: // 56. императив, 1 л., мн.ч., отр. 
            case 76: // 57. императив, 2 л., мн.ч., отр. 
            case 77: // 58. императив, 3 л., мн.ч., отр. 
                return $stems[0] ? $neg_verb. $stems[0]. $g. 'oi' : '';               
        }
    }
    
    public static function wordformByStemsCondPres($stems, $gramset_id, $dialect_id) {
        $lang_id = 1;
        $g = VepsGram::rightConsonant($stems[6], 'g');
        $neg_verb = Grammatic::negativeForm($gramset_id, $lang_id);

        switch ($gramset_id) {
            case 38: // 59. кондиционал, презенс, 1 л., ед.ч., +
                return $stems[4] ? $stems[4].'ižin' : '';
            case 39: // 60. кондиционал, презенс, 2 л., ед.ч., пол. 
                return $stems[4] ? $stems[4].'ižid' : '';
            case 40: // 61. кондиционал, презенс, 3 л., ед.ч., пол. 
            case 301: // 150. кондиционал, презенс, коннегатив
                return $stems[4] ? $stems[4].'iži' : '';
            case 41: // 62. кондиционал, презенс, 1 л., мн.ч., +
                return $stems[4] ? $stems[4].'ižim' : '';
            case 42: // 63. кондиционал, презенс, 2 л., мн.ч., +
                return $stems[4] ? $stems[4].'ižit' : '';
            case 43: // 64. кондиционал, презенс, 3 л., мн.ч., +
                return $stems[4] ? $stems[4]. 'ižiba' : '';
            case 303: // 151. кондиционал, презенс, коннегатив, мн.ч. 
                return $stems[4] ? $stems[4]. 'ižigoi' : '';
                
                
            case 110: // 65. кондиционал, презенс, 1 л., ед.ч., отр. 
            case 111: // 66. кондиционал, презенс, 2 л., ед.ч., отр. 
            case 112: // 67. кондиционал, презенс, 3 л., ед.ч., отр. 
                return $stems[4] ? $neg_verb. $stems[4]. 'iži' : '';
            case 113: // 68. кондиционал, презенс, 1 л., мн.ч., отр. 
            case 114: // 69. кондиционал, презенс, 2 л., мн.ч., отр. 
            case 115: // 70. кондиционал, презенс, 3 л., мн.ч., отр. 
                return $stems[4] ? $neg_verb. $stems[4]. 'ižigoi' : ''; 
        }
    }
    
    public static function wordformByStemsCondImperf($stems, $gramset_id, $dialect_id) {
        $lang_id = 1;
        $g = VepsGram::rightConsonant($stems[6], 'g');
        $neg_verb = Grammatic::negativeForm($gramset_id, $lang_id);

        switch ($gramset_id) {
            case 44: // 71. кондиционал, имперфект, 1 л., ед.ч., +
                return $stems[3] ? $stems[3].'nuižin' : '';
            case 45: // 72. кондиционал, имперфект, 2 л., ед.ч., +
                return $stems[3] ? $stems[3].'nuižid' : '';
            case 46: // 73. кондиционал, имперфект, 3 л., ед.ч., +
            case 302: // 151. кондиционал, презенс, коннегатив
                return $stems[3] ? $stems[3].'nuiži' : '';
            case 47: // 74. кондиционал, имперфект, 1 л., мн.ч., +
                return $stems[3] ? $stems[3].'nuižim' : '';
            case 48: // 75. кондиционал, имперфект, 2 л., мн.ч., +
                return $stems[3] ? $stems[3].'nuižit' : '';
            case 49: // 76. кондиционал, имперфект, 3 л., мн.ч., +
                return $stems[3] ? $stems[3].'nuižiba' : '';
            case 302: // 152. кондиционал, имперфект, коннегатив, ед.ч. 
                return $stems[3] ? $stems[3]. 'nuiži' : '';
            case 304: // 153. кондиционал, имперфект, коннегатив, мн.ч. 
                return $stems[3] ? $stems[3]. 'nuižigoi' : '';
                
            case 116: // 77. кондиционал, имперфект, 1 л., ед.ч., отр. 
            case 117: // 78. кондиционал, имперфект, 1 л., ед.ч., отр. 
            case 118: // 79. кондиционал, имперфект, 1 л., ед.ч., отр. 
                return $stems[3] ? $neg_verb. $stems[3]. 'nuiži' : '';
            case 119: // 80. кондиционал, имперфект, 1 л., ед.ч., отр. 
            case 120: // 81. кондиционал, имперфект, 1 л., ед.ч., отр. 
            case 121: // 82. кондиционал, имперфект, 1 л., ед.ч., отр. 
                return $stems[3] ? $neg_verb. $stems[3]. 'nuižigoi' : '';                
        }
    }
    
    public static function wordformByStemsInf($stems, $gramset_id, $dialect_id) {
        $lang_id = 1;
        $g = VepsGram::rightConsonant($stems[6], 'g');
        $neg_verb = Grammatic::negativeForm($gramset_id, $lang_id);

        switch ($gramset_id) {                
            case 170: // 131. I инфинитив 
                return $stems[0] && $stems[6] && $stems[7] ? $stems[0]. $stems[6]. $stems[7] : '';
            case 171: // 132. II инфинитив, инессив 
                return $stems[0] && $stems[6] ? $stems[0]. $stems[6]. 'es' : '';
            case 172: // 133. II инфинитив, инструктив  
                return $stems[0] && $stems[6] ? $stems[0]. $stems[6]. 'en' : '';
            case 173: // 134. III инфинитив, адессив
                return $stems[5] && $stems[7] ? $stems[5]. 'm'. $stems[7]. 'l' : '';
            case 174: // 135. III инфинитив, иллатив 
                return self::inf3Ill($stems[5], $stems[7]);
            case 175: // 136. III инфинитив, инессив 
                return $stems[5] && $stems[7] ? $stems[5]. 'm'. $stems[7]. 's' : '';
            case 176: // 137. III инфинитив, элатив 
                return $stems[5] && $stems[7] ? $stems[5]. 'm'. $stems[7]. 'späi' : '';
            case 177: // 138. III инфинитив, абессив 
                return $stems[5] && $stems[7] ? $stems[5]. 'm'. $stems[7]. 't' : '';
                
            case 178: // 139. актив, 1-е причастие 
                return self::partic1active($stems[1]);
            case 179: // 140. актив, 2-е причастие, ед.ч. 
                return $stems[5] ? $stems[5]. 'nu' : '';
            case 309: // 141. актив, 2-е причастие, мн.ч. !!!!! TODO
                return '';
            case 181: // 143. пассив, 2-е причастие 
                return $stems[0] && $stems[6] ? $stems[0]. $stems[6]. 'ud' : '';
        }
    }
    
    public static function IndPres2Sg($stem1, $dialect_id){
        if (!$stem1) { return ''; }
        if (in_array($dialect_id, [3, 4, 5]) && 
            preg_match("/i$/u", $stem1, $regs)) {
            return $stem1. 'd’';
        }        
        return $stem1. 'd';
    }

    /**
     * основа 1 + b (кроме северновепсского)
     * в северновепсском:
     * 1) если основа1 оканчивается на -e, то заменяется на -o 
     * 2) если основа1 оканчивается на CV, где С={k, g, t, d, p, b, z}, то С удваивается
     * 
     * @param type $stem1
     * @param type $dialect_id
     * @return string
     */
    public static function IndPres3Sg($stem1, $dialect_id){
        if (!$stem1) { return ''; }
        if ($dialect_id==1) {
            if (preg_match("/^(.+)([".vepsGram::consGeminantSet()."])(.)$/u", $stem1, $regs)) {
                $stem1 = $regs[1].$regs[2].$regs[2].$regs[3];    
            }
            if (preg_match("/^(.+)e$/u", $stem1, $regs)) {
                $stem1 = $regs[1].'o';    
            }
        }        
        return $stem1. 'b';
    }

    public static function IndPres1Pl($stem1, $dialect_id){
        if (!$stem1) { return ''; }
       
        switch ($dialect_id) {
            case 3: // южновепсский 
                return $stem1. 'maa';
            case 5: // средневепсский западный 
                return $stem1. 'mei';
            default:
                return $stem1. 'm';
        }        
    }
    
    public static function IndPres2Pl($stem1, $dialect_id){
        if (!$stem1) { return ''; }
       
        switch ($dialect_id) {
            case 3: // южновепсский 
                return $stem1. 'taa';
            case 5: // средневепсский западный 
                return $stem1. 'tei';
            case 43: // младописьменный
                return $stem1. 't';
            default:
                return $stem1. 'd';
        }        
    }
    
    public static function IndPres3Pl($stem0, $stem1, $dt, $dialect_id){
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem0 && $dt ? $stem0. $dt. 'ze' : '';
            case 4: // средневепсский восточный 
            case 5: // средневепсский западный 
                return $stem0 && $dt ? $stem0. $dt. 'as' : '';
            default:
                return $stem1 ? $stem1. 'ba': '';
        }        
    }
    
    public static function IndPresConnegPl($stem0, $stem1, $gk, $dialect_id){
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem0 && $gk ? $stem0. $gk. 'ii' : '';
            case 3: // южновепсский 
                return $stem1 ? $stem1 : '';
            default:
                return $stem0 && $gk ? $stem0. $gk. 'oi': '';
        }        
    }
    
    public static function IndImperf1Sg($stem2, $dialect_id){
        if (!$stem2) { return ''; }
       
        switch ($dialect_id) {
            case 3: // южновепсский 
                return $stem2. 'in’';
            case 4: // средневепсский восточный 
            case 5: // средневепсский западный 
                return $stem2. 'n’';
            default:
                return $stem2. 'n';
        }        
    }
    
    public static function IndImperf2Sg($stem2, $dialect_id){
        if (!$stem2) { return ''; }
       
        switch ($dialect_id) {
            case 3: // южновепсский 
                return $stem2. 'id’';
            case 4: // средневепсский восточный 
            case 5: // средневепсский западный 
                return $stem2. 'd’';
            default:
                return $stem2. 'd';
        }        
    }
    
    public static function IndImperf1Pl($stem2, $dialect_id){
        if (!$stem2) { return ''; }
       
        switch ($dialect_id) {
            case 3: // южновепсский 
                return $stem2. 'imaa';
            case 5: // средневепсский западный 
                return $stem2. 'mei';
            default:
                return $stem2. 'm';
        }        
    }
    
    public static function IndImperf2Pl($stem2, $dialect_id){
        if (!$stem2) { return ''; }
       
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem2. 'd';
            case 3: // южновепсский 
                return $stem2. 'itaa';
            case 4: // средневепсский восточный 
                return $stem2. 'd’';
            case 5: // средневепсский западный 
                return $stem2. 'tei';
            default:
                return $stem2. 't';
        }        
    }
    
    public static function IndImperf3Pl($stem0, $stem2, $dt, $dialect_id){
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem0 && $dt ? $stem0. $dt. 'ihe' : '';
            case 3: // южновепсский 
                return $stem2 ? $stem2. 'iba' : '';
            default:
                return $stem2 ? $stem2. 'ba': '';
        }        
    }
    
    public static function IndImperfConnegSg($stem1, $stem3, $dialect_id){
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem3 ? $stem3. 'nu': '';
            case 3: // южновепсский 
                return $stem1 ? $stem1. 'n, '. $stem1. 'nd' : '';
            case 4: // средневепсский восточный 
                return $stem1 ? $stem1. 'nd' : '';
            case 5: // средневепсский западный 
                return $stem1 ? $stem1. 'n' : '';
            default:
                return $stem1 ? $stem1. 'nd, '. $stem3. 'nu' : '';
        }        
    }
    
    public static function IndImperfConnegPl($stem0, $stem1, $stem3, $dt, $dialect_id){
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem0 && $dt ? $stem0. $dt. 'ud' : '';
            case 3: // южновепсский 
                return $stem1 ? $stem1. 'n, '. $stem1. 'nd' : '';
            default:
                return $stem3 ? $stem3. 'nugoi' : '';
        }        
    }
    
    public static function IndImperfSgNeg($stem1, $stem3, $gramset_id, $dialect_id){
        $neg_verb = self::negVerb($gramset_id, $dialect_id);
        $conneg_list = self::IndImperfConnegSg($stem1, $stem3, $dialect_id);
        if (!$neg_verb || !$conneg_list) { return; }
        
        $forms=[];
        foreach (preg_split("/,\s*/", $conneg_list) as $conneg) {
            $forms[] = $neg_verb.$conneg;
        }
        return join(", ", $forms);
    }
    
    public static function IndImperfPlNeg($stem0, $stem1, $stem3, $dt, $gramset_id, $dialect_id){
        $neg_verb = self::negVerb($gramset_id, $dialect_id);
        $conneg_list = self::IndImperfConnegPl($stem0, $stem1, $stem3, $dt, $dialect_id);
        if (!$neg_verb || !$conneg_list) { return; }
        
        $forms=[];
        foreach (preg_split("/,\s*/", $conneg_list) as $conneg) {
            $forms[] = $neg_verb.$conneg;
        }
        return join(", ", $forms);
    }
    /**
     * 135. III инфинитив, иллатив   
     * основа 5 + mh + a/ä (если основа 5 оканчивается на Vi, и это единственные гласные в основе 5)
     *          + m + a/ä + h + a/ä (если основа 5 оканчивается на C)
     * 
     * @param String $lemma
     */
    public static function inf3Ill($lemma, $harmony) {
        if (!$lemma || !$harmony) {
            return '';
        }
        if (preg_match("/^[^aeiouüäö-][aeiouüäö]i?$/u", $lemma)) {
            return $lemma. 'mh'. $harmony;
        } elseif (preg_match("/[^aeiouüäö]$/u", $lemma)) {
//var_dump($lemma);        
            return $lemma. 'm'. $harmony. 'h'. $harmony;
        }
        return '';
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
        if (!$stem) {
            return '';
        }
        if (preg_match("/[aeiouüäö]i$/u", $stem)) {
            return $stem;
        } else {
            if (preg_match("/^(.*[aeiouüäö-][^aeiouüäö]+)e$/u", $stem, $regs)) {
                $stem = $regs[1];
            }
            return $stem. 'i';
        }
    }
    
    public static function partic2activeSg($stem1, $stem5, $dialect_id) {
        switch ($dialect_id) {
            case 3: // южновепсский 
                return $stem1 ? $stem1. 'n, '. $stem1. 'nd' : '';
            default:
                return $stem5 ? $stem5. 'nu' : '';
        }        
    }
    
    public static function partic2activePl($stem0, $stem1, $stem5, $dt, $dialect_id) {
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem0 && $dt ? $stem0. $dt. 'nd' : '';
            case 3: // южновепсский 
                return $stem1 ? $stem1. 'n, '. $stem1. 'nd' : '';
            default:
                return $stem5 ? $stem5. 'nuded' : '';
        }        
    }
}