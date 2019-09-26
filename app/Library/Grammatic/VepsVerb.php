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
    public static function isMonobasic($stem0) {
        if (preg_match("/[".vepsGram::vowelSet()."]$/",$stem0)) {
            return true;
        }
    }

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

    public static function parseInf($inf, $is_reflexive) {
        if (!$is_reflexive && preg_match("/^(.*)([dt])([aä])$/u", $inf, $regs)
            || $is_reflexive && preg_match("/^(.*)([dt])([aä])[s|kso|ze]$/u", $inf, $regs)) {
            return $regs;
        }
        return null;
    }

    public static function parsePres3Sg($wordform, $is_reflexive) {
        if (!$is_reflexive && preg_match("/^(.*[aeiouüäö])b$/u", $wordform, $regs)
            || $is_reflexive && preg_match("/^(.*[aeiouüäö])[sšzž][eo]?i?$/u", $wordform, $regs)) {
            return $regs[1];
        }
        return '';
    }

    public static function parseImperf3Sg($wordform, $is_reflexive) {
        if (!$is_reflexive) {
            return $wordform;
        }
        if (preg_match("/^(.+)he$/u", $wordform, $regs)) {
            return $regs[1];
        }
        return '';
    }

    /**
     * @param type $lemma
     * @param type $stem_n
     * @param type $dialect_id
     * @return string
     */
    public static function getStemFromWordform($lemma, $stem_n, $dialect_id, $is_reflexive=false) {
        switch ($stem_n) {
            case 0: 
                $regs = self::parseInf($lemma->lemma, $is_reflexive);
                if (isset($regs[1])) {
                    return $regs[1];
                }
                return '';
            case 1:  // indicative presence 3 sg
                return self::parsePres3Sg($lemma->wordform(28, $dialect_id), $is_reflexive);
            case 2: // indicative imperfect 3 sg
                return self::parseImperf3Sg($lemma->wordform(34, $dialect_id), $is_reflexive);
            case 3: // base of 2 active particle
                return self::getStemPAP(self::getStemFromWordform($lemma, 0, $dialect_id), self::getStemFromWordform($lemma, 1, $dialect_id));
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
    public static function stemsFromTemplate($regs, $is_reflexive=false) {
        $stems = [];
        if (sizeof($regs)<5) {
            return $stems;
        }
        $base  = $regs[1];
        $past_suff = $regs[4];

        $regs1 = self::parseInf($regs[2], $is_reflexive);
        if (!$regs1) {
            return null;
        }
        $inf_stem = $base. $regs1[1]; // = lemma without [dt][aä]
        $cons = $regs1[2];
        $harmony = $regs1[3];

        $pres_stem = self::parsePres3Sg($base. $regs[3], $is_reflexive);
        if (!$pres_stem) {
            return null;
        }        
        
        $past_stem = $base. $past_suff;
        if (!preg_match("/i$/u", $past_stem)) { // должен оканчиваться на i
            return null;
        }
        
        $past_actv_ptcp_stem = self::getStemPAP($inf_stem, $pres_stem);       
        $cond_stem = self::getStemCond($pres_stem);        
        $potn_stem = self::getStemPoten($inf_stem, $pres_stem, $past_actv_ptcp_stem);
        
        return [$inf_stem, $pres_stem, $past_stem, $past_actv_ptcp_stem,
                $cond_stem, $potn_stem, $cons, $harmony];        
    }
    
    public static function getStemFromStems($stems, $stem_n, $dialect_id) {
        switch ($stem_n) {
            case 3: 
                return self::getStemPAP($stems[0], $stems[1]);
            case 4: 
                return self::getStemCond($stems[1]);
            case 5: 
                return self::getStemPoten($stems[0], $stems[1], $stems[3]);
            default: 
                return null;
        }
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
    
    public static function getStemPoten($inf_stem, $pres_stem, $past_actv_ptcp_stem=null) {
        $potn_stem = isset($past_actv_ptcp_stem) ? $past_actv_ptcp_stem : self::getStemPAP($inf_stem, $pres_stem);
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
    
    public static function getListPas() {
        return [305, 306, 307, 308]; 
    }
    
    public static function getListForAutoComplete() {
        return array_merge(self::getListIndPres(), self::getListIndImperf(),
                           self::getListIndPerf(), self::getListIndPlus(),
                           self::getListImper(), self::getListCondPres(),
                           self::getListCondImperf(), self::getListInf(), self::getListPas());
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
        } elseif (in_array($gramset_id, self::getListIndPerf())) {
            return self::wordformByStemsIndPerf($stems, $gramset_id, $dialect_id);
        } elseif (in_array($gramset_id, self::getListIndPlus())) {
            return self::wordformByStemsIndPlus($stems, $gramset_id, $dialect_id);
        } elseif (in_array($gramset_id, self::getListImper())) {
            return self::wordformByStemsImper($stems, $gramset_id, $dialect_id);
        } elseif (in_array($gramset_id, self::getListCondPres())) {
            return self::wordformByStemsCondPres($stems, $gramset_id, $dialect_id);
        } elseif (in_array($gramset_id, self::getListCondImperf())) {
            return self::wordformByStemsCondImperf($stems, $gramset_id, $dialect_id);
        } elseif (in_array($gramset_id, self::getListInf())) {
            return self::wordformByStemsInf($stems, $gramset_id, $dialect_id);
        } elseif (in_array($gramset_id, self::getListPas())) {
            return self::wordformByStemsPas($stems, $gramset_id, $dialect_id);
        }
        
        return '';
    }
    
    public static function wordformByStemsIndPres($stems, $gramset_id, $dialect_id) {
        $neg_verb = self::negVerb($gramset_id, $dialect_id);

        switch ($gramset_id) {
            case 26: // 1. индикатив, презенс, 1 л., ед.ч., +
                return $stems[1] ? $stems[1].'n' : '';
            case 27: // 2. индикатив, презенс, 2 л., ед.ч., пол. 
                return self::indPres2Sg($stems[1], $dialect_id);
            case 28: // 3. индикатив, презенс, 3 л., ед.ч., пол. 
                return self::indPres3Sg($stems[0], $stems[1], $stems[6], $dialect_id);
            case 29: // 4. индикатив, презенс, 1 л., мн.ч., +
                return self::indPres1Pl($stems[1], $dialect_id);
            case 30: // 5. индикатив, презенс, 2 л., мн.ч., +
                return self::indPres2Pl($stems[1], $dialect_id);
            case 31: // 6. индикатив, презенс, 3 л., мн.ч., +
                return self::indPres3Pl($stems[0], $stems[1], $stems[6], $dialect_id);
            case 295: // 144. индикатив, презенс, коннегатив, ед.ч.
                return $stems[1] ? $stems[1] : '';
            case 296: // 145. индикатив, презенс, коннегатив, мн.ч.
                return self::indPresConnegPl($stems[0], $stems[1], $stems[6], $dialect_id);

            case 70: // 7. индикатив, презенс, 1 л., ед.ч., -
            case 71: // 8. индикатив, презенс, 2 л., ед.ч., -
            case 72: // 9. индикатив, презенс, 3 л., ед.ч., -
                return $stems[1] ? $neg_verb. $stems[1] : '';
            case 73: //10. индикатив, презенс, 1 л., мн.ч., -
            case 78: // 11. индикатив, презенс, 2 л., мн.ч., -
            case 79: // 12. индикатив, презенс, 3 л., мн.ч., -
                return Grammatic::interLists($neg_verb, self::indPresConnegPl($stems[0], $stems[1], $stems[6], $dialect_id));
        }
    }
    
    public static function wordformByStemsIndImperf($stems, $gramset_id, $dialect_id) {
        $neg_verb = self::negVerb($gramset_id, $dialect_id);
        switch ($gramset_id) {
            case 32: // 13. индикатив, имперфект, 1 л., ед.ч., +
                return self::indImperf1Sg($stems[2], $dialect_id);
            case 33: // 14. индикатив, имперфект, 2 л., ед.ч., +
                return self::indImperf2Sg($stems[2], $dialect_id);
            case 34: // 15. индикатив, имперфект, 3 л., ед.ч., +
                return $stems[2] ? $stems[2] : '';
            case 35: // 16. индикатив, имперфект, 1 л., мн.ч., +
                return self::indImperf1Pl($stems[2], $dialect_id);
            case 36: // 17. индикатив, имперфект, 2 л., мн.ч., +
                return self::indImperf2Pl($stems[2], $dialect_id);
            case 37: // 18. индикатив, имперфект, 3 л., мн.ч., +
                return self::indImperf3Pl($stems[0], $stems[2], $stems[6], $dialect_id);
            case 297: // 146. индикатив, имперфект, коннегатив, ед.ч.
                return self::indImperfConnegSg($stems[1], $stems[3], $dialect_id);
            case 298: // 147. индикатив, имперфект, коннегатив, мн.ч.
                return self::indImperfConnegPl($stems[0], $stems[1], $stems[3], $stems[6], $dialect_id);

            case 80: // 19. индикатив, имперфект, 1 л., ед.ч., -
            case 81: // 20. индикатив, имперфект, 2 л., ед.ч., -
            case 82: // 21. индикатив, имперфект, 3 л., ед.ч., -
                return Grammatic::interLists($neg_verb, self::indImperfConnegSg($stems[1], $stems[3], $dialect_id));
            case 83: // 22. индикатив, имперфект, 1 л., мн.ч., -
            case 84: // 23. индикатив, имперфект, 2 л., мн.ч., -
            case 85: // 24. индикатив, имперфект, 3 л., мн.ч., -
                return Grammatic::interLists($neg_verb, self::indImperfConnegPl($stems[0], $stems[1], $stems[3], $stems[6], $dialect_id));
        }
    }
    
    public static function wordformByStemsIndPerf($stems, $gramset_id, $dialect_id) {
        $neg_verb = self::negVerb($gramset_id, $dialect_id);
        $aux_verb = self::auxVerb($gramset_id, $dialect_id);

        switch ($gramset_id) {
            case 86: // 25. индикатив, перфект, 1 л., ед.ч., +
            case 87: // 26. индикатив, перфект, 2 л., ед.ч., +
            case 88: // 27. индикатив, перфект, 3 л., ед.ч., +
                return Grammatic::interLists($aux_verb, self::partic2activeSg($stems[1], $stems[5], $dialect_id));
            case 89: // 28. индикатив, перфект, 1 л., мн.ч., +
            case 90: // 29. индикатив, перфект, 2 л., мн.ч., +
            case 91: // 30. индикатив, перфект, 3 л., мн.ч., +
                return Grammatic::interLists($aux_verb, self::partic2activePl($stems[0], $stems[1], $stems[5], $stems[6], $dialect_id));
            case 92: // 31. индикатив, перфект, 1 л., ед.ч., -
            case 93: // 32. индикатив, перфект, 2 л., ед.ч., -
            case 94: // 33. индикатив, перфект, 3 л., ед.ч., -
                return Grammatic::interLists(Grammatic::interLists($neg_verb, $aux_verb), self::partic2activeSg($stems[1], $stems[5], $dialect_id));
            case 95: // 34. индикатив, перфект, 1 л., мн.ч., -
            case 96: // 35. индикатив, перфект, 2 л., мн.ч., -
            case 97: // 36. индикатив, перфект, 3 л., мн.ч., -
                return Grammatic::interLists(Grammatic::interLists($neg_verb, $aux_verb),  self::partic2activePl($stems[0], $stems[1], $stems[5], $stems[6], $dialect_id));
        }
    }
    
    public static function wordformByStemsIndPlus($stems, $gramset_id, $dialect_id) {
        $neg_verb = self::negVerb($gramset_id, $dialect_id);
        $aux_verb = self::auxVerb($gramset_id, $dialect_id);

        switch ($gramset_id) {
            case 98: // 37. индикатив, плюсквамперфект, 1 л., ед.ч., +
            case 99: // 38. индикатив, плюсквамперфект, 2 л., ед.ч., +
            case 100: // 39. индикатив, плюсквамперфект, 3 л., ед.ч., +
                return Grammatic::interLists($aux_verb, self::partic2activeSg($stems[1], $stems[5], $dialect_id));
            case 101: // 40. индикатив, плюсквамперфект, 1 л., мн.ч., +
            case 102: // 41. индикатив, плюсквамперфект, 2 л., мн.ч., +
            case 103: // 42. индикатив, плюсквамперфект, 3 л., мн.ч., +
                return Grammatic::interLists($aux_verb, self::partic2activePl($stems[0], $stems[1], $stems[5], $stems[6], $dialect_id));

            case 104: // 43. индикатив, плюсквамперфект, 1 л., ед.ч., -
            case 105: // 44. индикатив, плюсквамперфект, 2 л., ед.ч., -
            case 107: // 45. индикатив, плюсквамперфект, 3 л., ед.ч., -
                return Grammatic::interLists(Grammatic::interLists($neg_verb, $aux_verb), self::partic2activeSg($stems[1], $stems[5], $dialect_id));
            case 108: // 46. индикатив, плюсквамперфект, 1 л., мн.ч., -
            case 106: // 47. индикатив, плюсквамперфект, 2 л., мн.ч., -
            case 109: // 48. индикатив, плюсквамперфект, 3 л., мн.ч., -
                return Grammatic::interLists(Grammatic::interLists($neg_verb, $aux_verb), self::partic2activePl($stems[0], $stems[1], $stems[5], $stems[6], $dialect_id));
        }
    }
    
    public static function wordformByStemsImper($stems, $gramset_id, $dialect_id) {
        $g = VepsGram::rightConsonant($stems[6], 'g');
        $neg_verb = self::negVerb($gramset_id, $dialect_id);

        switch ($gramset_id) {
            case 51: // 49. императив, 2 л., ед.ч., + 
            case 299: // 148. императив, коннегатив, ед.ч.
                return $stems[1] ? $stems[1] : '';
            case 52: // 50. императив, 3 л., ед.ч., + 
            case 55: // 53. императив, 3 л., мн.ч., + 
                return self::imper3($stems[0], $stems[6]);
            case 53: // 51. императив, 1 л., мн.ч., + 
                return $stems[0] ? $stems[0]. $g. 'am' : '';
            case 54: // 52. императив, 2 л., мн.ч., + 
                return $stems[0] ? $stems[0]. $g. 'at' : '';
            case 300: // 149. императив, коннегатив, мн.ч.
                return self::imperConnegPl($stems[0], $stems[1], $stems[6], $dialect_id);
 
            case 50: // 54. императив, 2 л., ед.ч., - 
                return $stems[1] ? $neg_verb. $stems[1] : '';
            case 74: // 55. императив, 3 л., ед.ч., -                 
            case 75: // 56. императив, 1 л., мн.ч., - 
            case 76: // 57. императив, 2 л., мн.ч., - 
            case 77: // 58. императив, 3 л., мн.ч., - 
                return $stems[0] ? $neg_verb. self::imperConnegPl($stems[0], $stems[1], $stems[6], $dialect_id) : '';
        }
    }
    
    public static function wordformByStemsCondPres($stems, $gramset_id, $dialect_id) {
        $neg_verb = self::negVerb($gramset_id, $dialect_id);
        $stems[4] = self::stemForCond($stems[4], $dialect_id);

        switch ($gramset_id) {
            case 38: // 59. кондиционал, презенс, 1 л., ед.ч., +
                return self::condPres1Sg($stems[4], $dialect_id);
            case 39: // 60. кондиционал, презенс, 2 л., ед.ч., пол. 
                return self::condPres2Sg($stems[4], $dialect_id);
            case 40: // 61. кондиционал, презенс, 3 л., ед.ч., пол. 
            case 301: // 150. кондиционал, презенс, коннегатив
                return self::condPresConSg($stems[4], $dialect_id);
            case 41: // 62. кондиционал, презенс, 1 л., мн.ч., +
                return self::condPres1Pl($stems[4], $dialect_id);
            case 42: // 63. кондиционал, презенс, 2 л., мн.ч., +
                return self::condPres2Pl($stems[4], $dialect_id);
            case 43: // 64. кондиционал, презенс, 3 л., мн.ч., +
                return self::condPres3Pl($stems[0], $stems[4], $stems[6], $dialect_id);
            case 303: // 151. кондиционал, презенс, коннегатив, мн.ч. 
                return self::condPresConSg($stems[0], $stems[4], $stems[6], $dialect_id);
                                
            case 110: // 65. кондиционал, презенс, 1 л., ед.ч., отр. 
            case 111: // 66. кондиционал, презенс, 2 л., ед.ч., отр. 
            case 112: // 67. кондиционал, презенс, 3 л., ед.ч., отр. 
                return $stems[4] ? Grammatic::interLists($neg_verb, self::condPresConSg($stems[4], $dialect_id)) : '';
            case 113: // 68. кондиционал, презенс, 1 л., мн.ч., отр. 
            case 114: // 69. кондиционал, презенс, 2 л., мн.ч., отр. 
            case 115: // 70. кондиционал, презенс, 3 л., мн.ч., отр. 
                return $stems[4] ? $neg_verb. self::condPresConPl($stems[0], $stems[4], $stems[6], $dialect_id) : ''; 
        }
    }
    
    public static function wordformByStemsCondImperf($stems, $gramset_id, $dialect_id) {
        $neg_verb = self::negVerb($gramset_id, $dialect_id);

        switch ($gramset_id) {
            case 44: // 71. кондиционал, имперфект, 1 л., ед.ч., +
                return self::condImperf1Sg($stems[3], $dialect_id);
            case 45: // 72. кондиционал, имперфект, 2 л., ед.ч., +
                return self::condImperf2Sg($stems[3], $dialect_id);
            case 46: // 73. кондиционал, имперфект, 3 л., ед.ч., +
                return self::condImperf3Sg($stems[3], $dialect_id);
            case 47: // 74. кондиционал, имперфект, 1 л., мн.ч., +
                return self::condImperf1Pl($stems[3], $dialect_id);
            case 48: // 75. кондиционал, имперфект, 2 л., мн.ч., +
                return self::condImperf2Pl($stems[3], $dialect_id);
            case 49: // 76. кондиционал, имперфект, 3 л., мн.ч., +
                return self::condImperf3Pl($stems[0], $stems[3], $stems[6], $dialect_id);
            case 302: // 152. кондиционал, имперфект, коннегатив, ед.ч. 
            case 304: // 153. кондиционал, имперфект, коннегатив, мн.ч. 
                return self::condImperfConSg($stems[4], $dialect_id);
                
            case 116: // 77. кондиционал, имперфект, 1 л., ед.ч., отр. 
            case 117: // 78. кондиционал, имперфект, 1 л., ед.ч., отр. 
            case 118: // 79. кондиционал, имперфект, 1 л., ед.ч., отр. 
            case 119: // 80. кондиционал, имперфект, 1 л., ед.ч., отр. 
            case 120: // 81. кондиционал, имперфект, 1 л., ед.ч., отр. 
            case 121: // 82. кондиционал, имперфект, 1 л., ед.ч., отр. 
                return $stems[4] ? Grammatic::interLists($neg_verb, self::condImperfConSg($stems[4], $dialect_id)) : '';
        }
    }
    
    public static function wordformByStemsInf($stems, $gramset_id, $dialect_id) {      
        switch ($gramset_id) {                
            case 170: // 131. I инфинитив 
                return $stems[0] && $stems[6] && $stems[7] ? $stems[0]. $stems[6]. $stems[7] : '';
            case 171: // 132. II инфинитив, инессив 
                return $stems[0] && $stems[6] ? $stems[0]. $stems[6]. 'es' : '';
            case 172: // 133. II инфинитив, инструктив  
                return $stems[0] && $stems[6] ? $stems[0]. $stems[6]. 'en' : '';
            case 173: // 134. III инфинитив, адессив
                return self::inf3Ades($stems[5], $stems[7], $dialect_id);
            case 174: // 135. III инфинитив, иллатив 
                return self::inf3Ill($stems[5], $stems[7]);
            case 175: // 136. III инфинитив, инессив 
                return $stems[5] && $stems[7] ? $stems[5]. 'm'. $stems[7]. 's' : '';
            case 176: // 137. III инфинитив, элатив 
                return self::inf3Elat($stems[5], $stems[7], $dialect_id);
            case 177: // 138. III инфинитив, абессив 
                return $stems[5] && $stems[7] ? $stems[5]. 'm'. $stems[7]. 't' : '';
                
            case 178: // 139. актив, 1-е причастие 
                return self::partic1active($stems[1]);
            case 179: // 140. актив, 2-е причастие, ед.ч. 
                return self::partic2activeSg($stems[1], $stems[5], $dialect_id);
            case 309: // 141. актив, 2-е причастие, мн.ч. 
                return self::partic2activePl($stems[0], $stems[1], $stems[5], $stems[6], $dialect_id);
            case 181: // 143. пассив, 2-е причастие 
                return $stems[0] && $stems[6] ? $stems[0]. $stems[6]. 'ud' : '';
        }
    }
    
    public static function wordformByStemsPas($stems, $gramset_id, $dialect_id) {       
        switch ($gramset_id) {                
            case 305: // Пассив презенс 
                return $stems[0] && $stems[6] 
                    ? $stems[0]. $stems[6]. ($dialect_id==1 ? 'aze' : 'as') 
                    : '';
            case 306: // Пассив имперфект 
                return $stems[0] && $stems[6] ? $stems[0]. $stems[6]. 'ihe' : '';
            case 307: // Пассив перфект 
                return $stems[0] && $stems[6] ? 'om '. $stems[0]. $stems[6]. 'ud' : '';
            case 308: // Пассив имперфект 
                return $stems[0] && $stems[6] ? 'oli '. $stems[0]. $stems[6]. 'ud' : '';
        }
    }
    
    public static function negVerb($gramset_id, $dialect_id) {
        if (in_array($gramset_id,[70, 80, 92, 104, 110, 116])) { // 1Sg IndPres, IndImperf, IndPerf, IndPlus
            return 'en ';
        } elseif (in_array($gramset_id,[71, 81, 93, 105, 111, 117])) { // 2Sg
            return 'ed ';
        } elseif (in_array($gramset_id,[72, 82, 94, 107, 112, 118])) { // 3Sg
            return self::negVerb3Sg($gramset_id, $dialect_id);        
        } elseif (in_array($gramset_id,[73, 83, 95, 108, 113, 119])) { // 1Pl
            return self::negVerb1Pl($dialect_id);        
        } elseif (in_array($gramset_id,[78, 84, 96, 106, 114, 120])) { // 2Pl
            return self::negVerb2Pl($dialect_id);        
        } elseif (in_array($gramset_id,[79, 85, 97, 109, 115, 121])) { // 3Pl
            return self::negVerb3Pl($dialect_id);        
        } elseif ($gramset_id ==50) { // Imperative2Sg
            return 'ala ';
        } elseif (in_array($gramset_id,[74, 77])) { // Imperative3SgPl
            return self::negVerb3Imper($dialect_id);        
        } elseif ($gramset_id ==75) { // Imperative1Pl
            return self::negVerb1PlImper($dialect_id);        
        } elseif ($gramset_id ==76) { // Imperative2Pl
            return self::negVerb2PlImper($dialect_id);        
        }
    }

    public static function negVerb3Sg($gramset_id, $dialect_id) {
        switch ($dialect_id) {
            case 1: // северновепсский 
                if ($gramset_id ==94) { // Perf
                    return '';
                } else {
                    return 'ii ';
                }
            case 3: // южновепсский 
                return 'ii ';
            case 4: // средневепсский восточный 
/*                if ($gramset_id ==107) { // Perf
                    return '';
                } else {*/
                    return 'ii ';
                //}
            case 5: // средневепсский западный 
                if ($gramset_id ==94) { // Perf
                    return '';
                } else {
                    return 'ei ';
                }
            default:
                return 'ei ';
        }
    }

    public static function negVerb1Pl($dialect_id) {
            switch ($dialect_id) {
                case 3: // южновепсский 
                    return 'emaa ';
                case 5: // средневепсский западный 
                    return 'emei ';
                default:
                    return 'em ';
            }        
    }

    public static function negVerb2Pl($dialect_id) {
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
    }

    public static function negVerb3Pl($dialect_id) {
        switch ($dialect_id) {
            case 1: // северновепсский 
            case 4: // средневепсский восточный 
                return 'ii ';
            case 3: // южновепсский 
                return 'ebad ';
            default:
                return 'ei ';
        }        
    }
    
    public static function negVerb1PlImper($dialect_id) {
        switch ($dialect_id) {
            case 3: // южновепсский 
                return 'aagam ';
            case 4: // средневепсский восточный 
            case 5: // средневепсский западный 
                return 'uugam ';
            default:
                return 'algam ';
        }        
    }

    public static function negVerb2PlImper($dialect_id) {
        switch ($dialect_id) {
            case 3: // южновепсский 
                return 'aagat ';
            case 4: // средневепсский восточный 
            case 5: // средневепсский западный 
                return 'uugat ';
            default:
                return 'algat ';
        }        
    }

    public static function negVerb3Imper($dialect_id) {
        switch ($dialect_id) {
            case 43: // младописьменный
                return 'algha ';
            case 1: // северновепсский 
                return 'algii ';
            case 3: // южновепсский 
                return 'laske ii ';
            default:
                return 'uugha ';
        }        
    }
        
    public static function auxVerb($gramset_id, $dialect_id) {
        if (in_array($gramset_id,[86])) { // Perf1Sg
            return 'olen ';
        } elseif (in_array($gramset_id,[87])) { // Perf2Sg
            return 'oled ';
        } elseif (in_array($gramset_id,[88])) { // Perf3Sg
            return 'om ';
        } elseif (in_array($gramset_id,[89])) { // Perf1Pl
            return self::auxVerbPerf1Pl($dialect_id);
        } elseif (in_array($gramset_id,[90])) { // Perf2Pl
            return self::auxVerbPerf2Pl($dialect_id);
        } elseif (in_array($gramset_id,[91])) { // Perf3Pl
            return 'oma ';
        } elseif (in_array($gramset_id,[92, 93])) { // Perf1SgNeg, Perf2SgNeg
            return 'ole ';
        } elseif (in_array($gramset_id,[94])) { // Perf3SgNeg
            return self::auxVerbPerf3SgNeg($dialect_id);
        } elseif (in_array($gramset_id,[95, 96, 97])) { // PerfPlNeg
            return self::auxVerbPerfPlNeg($dialect_id);
        } elseif (in_array($gramset_id,[98])) { // Plus1Sg
            return self::auxVerbPlus1Sg($dialect_id);
        } elseif (in_array($gramset_id,[99])) { // Plus2Sg
            return self::auxVerbPlus2Sg($dialect_id);
        } elseif (in_array($gramset_id,[100])) { // Perf3Sg
            return 'oli ';
        } elseif (in_array($gramset_id,[101])) { // Plus1Pl
            return self::auxVerbPlus1Pl($dialect_id);
        } elseif (in_array($gramset_id,[102])) { // Plus2Pl
            return self::auxVerbPlus2Pl($dialect_id);
        } elseif (in_array($gramset_id,[103])) { // Plus3Pl
            return self::auxVerbPlus3Pl($dialect_id);
        } elseif (in_array($gramset_id,[104, 105, 107])) { // PlusSgNeg
            return self::auxVerbPlusSgNeg($dialect_id);
        } elseif (in_array($gramset_id,[108, 106, 109])) { // PlusSgNeg
            return self::auxVerbPlusPlNeg($dialect_id);
        }
    }

    public static function auxVerbPerf1Pl($dialect_id) {
        switch ($dialect_id) {
            case 3: // южновепсский 
                return 'olemaa ';
            case 5: // средневепсский западный 
                return 'olemei ';
            default:
                return 'olem ';
        }        
    }

    public static function auxVerbPerf2Pl($dialect_id) {
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

    public static function auxVerbPerf3SgNeg($dialect_id) {
        switch ($dialect_id) {
            case 1: // северновепсский 
                return 'iilä ';
            case 5: // средневепсский западный 
                return 'ele ';
            default:
                return 'ole ';
        }        
    }

    public static function auxVerbPerfPlNeg($dialect_id) {
        switch ($dialect_id) {
            case 43: // младописьменный 
                return 'olgoi ';
            case 1: // северновепсский 
                return 'olgii ';
            case 3: // южновепсский 
                return 'ole ';
            default:
                return 'uugoi ';
        }        
    }

    public static function auxVerbPlus1Sg($dialect_id) {
        switch ($dialect_id) {
            case 3: // южновепсский 
                return 'oliin’ ';
            case 4: // средневепсский восточный 
            case 5: // средневепсский западный 
                return 'olin’ ';
            default:
                return 'olin ';
        }        
    }

    public static function auxVerbPlus2Sg($dialect_id) {
        switch ($dialect_id) {
            case 3: // южновепсский 
                return 'oliid’ ';
            case 4: // средневепсский восточный 
            case 5: // средневепсский западный 
                return 'olid’ ';
            default:
                return 'olid ';
        }        
    }

    public static function auxVerbPlus1Pl($dialect_id) {
        switch ($dialect_id) {
            case 3: // южновепсский 
                return 'oliimaa ';
            case 5: // средневепсский западный 
                return 'olimei ';
            default:
                return 'olim ';
        }        
    }
    
    public static function auxVerbPlus2Pl($dialect_id) {
        switch ($dialect_id) {
            case 1: // северновепсский 
                return 'olid ';
            case 3: // южновепсский 
                return 'oliita ';
            case 4: // средневепсский восточный 
                return 'olid’ ';
            case 5: // средневепсский западный 
                return 'olitei ';
            default:
                return 'olit ';
        }        
    }
    
    public static function auxVerbPlus3Pl($dialect_id) {
        switch ($dialect_id) {
            case 1: // северновепсский 
                return 'ol’d’he ';
            case 3: // южновепсский 
                return 'oliiba ';
            default:
                return 'oliba ';
        }        
    }

    public static function auxVerbPlusSgNeg($dialect_id) {
        switch ($dialect_id) {
            case 1: // северновепсский 
                return 'olnu ';
            case 3: // южновепсский 
                return 'olen, olend ';
            case 4: // средневепсский восточный 
                return 'olend ';
            case 5: // средневепсский западный 
                return 'olen ';
            default:
                return 'olend, olnu ';
        }        
    }
    
    public static function auxVerbPlusPlNeg($dialect_id) {
        switch ($dialect_id) {
            case 1: // северновепсский 
                return 'oldud ';
            case 3: // южновепсский 
                return 'olen, olend ';
            case 4: // средневепсский восточный 
            case 5: // средневепсский западный 
                return 'uunugoi ';
            default:
                return 'olnugoi ';
        }        
    }
    
    public static function indPres2Sg($stem1, $dialect_id){
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
    public static function indPres3Sg($stem1, $dialect_id){
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

    public static function indPres1Pl($stem1, $dialect_id){
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
    
    public static function indPres2Pl($stem1, $dialect_id){
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
    
    public static function indPres3Pl($stem0, $stem1, $dt, $dialect_id){
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem0 && $dt ? $stem0. $dt. 'aze' : '';
            case 4: // средневепсский восточный 
            case 5: // средневепсский западный 
                return $stem0 && $dt ? $stem0. $dt. 'as' : '';
            default:
                return $stem1 ? $stem1. 'ba': '';
        }        
    }
    
    public static function indPresConnegPl($stem0, $stem1, $dt, $dialect_id){
        $gk = VepsGram::rightConsonant($dt, 'g');
        
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem0 && $gk ? $stem0. $gk. 'ii' : '';
            case 3: // южновепсский 
                return $stem1 ? $stem1 : '';
            default:
                return $stem0 && $gk ? $stem0. $gk. 'oi': '';
        }        
    }
    
    public static function indImperf1Sg($stem2, $dialect_id){
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
    
    public static function indImperf2Sg($stem2, $dialect_id){
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
    
    public static function indImperf1Pl($stem2, $dialect_id){
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
    
    public static function indImperf2Pl($stem2, $dialect_id){
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
    
    public static function indImperf3Pl($stem0, $stem2, $dt, $dialect_id){
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem0 && $dt ? $stem0. $dt. 'ihe' : '';
            case 3: // южновепсский 
                return $stem2 ? $stem2. 'iba' : '';
            default:
                return $stem2 ? $stem2. 'ba': '';
        }        
    }
    
    public static function indImperfConnegSg($stem1, $stem3, $dialect_id){
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
    
    public static function indImperfConnegPl($stem0, $stem1, $stem3, $dt, $dialect_id){
        $kg = VepsGram::rightConsonant($dt, 'g');
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem0 && $dt ? $stem0. $dt. 'ud' : '';
            case 3: // южновепсский 
                return $stem1 ? $stem1. 'n, '. $stem1. 'nd' : '';
            default:
                return $stem3 ? $stem3. 'nu'. $kg. 'oi' : '';
        }        
    }
    
    public static function imper3($stem0, $dt){
        if (!$stem0) {
            return '';
        }
        if (self::isMonobasic($stem0) && in_array(VepsGram::countSyllable($stem0), [1,3])
            || !self::isMonobasic($stem0) && preg_match("/[".VepsGram::sonantSet()."]$/")) {
            $a = '';
        } else {
            $a = 'a';
        }
        return $stem0. VepsGram::rightConsonant($dt, 'g'). $a. 'ha';

    }
    
    public static function imperConnegPl($stem0, $stem1, $dt, $dialect_id){
        $gk = VepsGram::rightConsonant($dt, 'g');
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem0 && $dt ? $stem0. $gk. 'ii' : '';
            case 3: // южновепсский 
                return $stem1 ? $stem1 : '';
            default:
                return $stem0 ? $stem0. $gk. 'oi' : '';
        }        
    }
    
    public static function condForSouth($stem4, $affix) {
        if (!$stem4) {
            return '';
        }
        if (preg_match("/^(.+)([aou])$/",$stem4, $regs)) {
            $affix = mb_substr($affix, 1);
            switch ($regs[2]) {
                case 'a': return $regs[1].'ä'.$affix;
                case 'o': return $regs[1].'ö'.$affix;
                case 'u': return $regs[1].'ü'.$affix;
            }
        }
        return $stem4.$affix;
        
    }

    public static function condPres1Sg($stem4, $dialect_id){
        if (!$stem4) {
            return '';
        }
        switch ($dialect_id) {
            case 43: // младописьменный 
            case 1: // северновепсский 
                return $stem4. 'ižin';
            case 3: // южновепсский 
                return self::condForSouth($stem4, 'ižin’');
            default:
                return $stem4. 'ižin’';
        }        
    }
    
    public static function condPres2Sg($stem4, $dialect_id){
        if (!$stem4) {
            return '';
        }
        switch ($dialect_id) {
            case 43: // младописьменный 
            case 1: // северновепсский 
                return $stem4. 'ižid';
            case 3: // южновепсский 
                return self::condForSouth($stem4, 'ižid’');
            default:
                return $stem4. 'ižid’';
        }        
    }
    
    public static function condPres1Pl($stem4, $dialect_id){
        if (!$stem4) {
            return '';
        }
        switch ($dialect_id) {
            case 5: // средневепсский западный 
                return $stem4. 'ižimei';
            case 3: // южновепсский 
                return self::condForSouth($stem4, 'ižimaa');
            default:
                return $stem4. 'ižim';
        }        
    }
    
    public static function condPres2Pl($stem4, $dialect_id){
        if (!$stem4) {
            return '';
        }
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem4. 'ižid';
            case 3: // южновепсский 
                return self::condForSouth($stem4, 'ižitaa');
            case 4: // средневепсский восточный 
                return $stem4. 'ižid’';
            case 5: // средневепсский западный 
                return $stem4. 'ižitei';
            default:
                return $stem4. 'ižit';
        }        
    }
    
    public static function condPres3Pl($stem0, $stem4, $dt, $dialect_id){
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem0 && $dt ? $stem0. $dt. 'eiž' : '';
            case 3: // южновепсский 
                return self::condForSouth($stem4, 'ižiba');
            default:
                return $stem4 ? $stem4. 'ižiba' : '';
        }        
    }
    
    public static function condPresConSg($stem4, $dialect_id){
        if (!$stem4) {
            return '';
        }
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem4. 'iž, '. $stem4. 'iži';
            case 3: // южновепсский 
                return self::condForSouth($stem4, 'iž');
            default:
                return $stem4. 'iži';
        }        
    }
    
    public static function condPresConPl($stem0, $stem4, $dt, $dialect_id){
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem0 && $dt ? $stem0. $dt. 'eiž' : '';
            case 3: // южновепсский 
                return self::condForSouth($stem4, 'iž');
            case 5: // средневепсский западный 
                return $stem4 ? $stem4. 'ižigoi' : '';
            default:
                return $stem4 ? $stem4. 'iži' : '';
        }        
    }
    
    public static function stemForCond($stem4, $dialect_id) {
        if (!$stem4 || $dialect_id == 1 || $dialect_id == 3) { // северновепсский, южновепсский
            return $stem4;
        }
        
        if (VepsGram::countSyllable($stem4)>1 && preg_match("/^(.+)e$/",$stem4, $regs)) {
            return $regs[1];
        }        
        return $stem4;
    }

    public static function condImperf1Sg($stem3, $dialect_id){
        if (!$stem3) {
            return '';
        }
        switch ($dialect_id) {
            case 43: // младописьменный 
                return $stem3. 'nuižin';
            case 1: // северновепсский 
                return $stem3. 'nižin';
            default:
                return $stem3. 'nuižin’';
        }        
    }
    
    public static function condImperf2Sg($stem3, $dialect_id){
        if (!$stem3) {
            return '';
        }
        switch ($dialect_id) {
            case 43: // младописьменный 
                return $stem3. 'nuižid';
            case 1: // северновепсский 
                return $stem3. 'nižid';
            default:
                return $stem3. 'nuižid’';
        }        
    }
    
    public static function condImperf3Sg($stem3, $dialect_id){
        if (!$stem3) {
            return '';
        }
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem3. 'niži';
            case 3: // южновепсский 
                return $stem3. 'nuiž';
            default:
                return $stem3. 'nuiži';
        }        
    }
    
    public static function condImperf1Pl($stem3, $dialect_id){
        if (!$stem3) {
            return '';
        }
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem3. 'nižim';
            case 3: // южновепсский 
                return $stem3. 'nuižimaa';
            case 5: // средневепсский западный 
                return $stem3. 'nuižimei';
            default:
                return $stem3. 'nuižim';
        }        
    }
    
    public static function condImperf2Pl($stem3, $dialect_id){
        if (!$stem3) {
            return '';
        }
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem3. 'nižid';
            case 3: // южновепсский 
                return $stem3. 'nuižitaa';
            case 4: // средневепсский восточный 
                return $stem3. 'nuižid’';
            case 5: // средневепсский западный 
                return $stem3. 'nuižitei';
            default:
                return $stem3. 'nuižit';
        }        
    }
    
    public static function condImperf3Pl($stem0, $stem3, $dt, $dialect_id){
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem0 && $dt ? $stem0. $dt. 'eniž' : '';
            default:
                return $stem3 ? $stem3. 'nuižiba' : '';
        }        
    }
    
    public static function condImperfConSg($stem4, $dialect_id){
        if (!$stem4) {
            return '';
        }
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem4. 'niž, '. $stem4. 'niži';
            case 3: // южновепсский 
                return $stem4. 'nuiž';
            default:
                return $stem4. 'nuiži';
        }        
    }

    public static function inf3Ades($stem5, $a, $dialect_id){
        if (!$stem5) {
            return '';
        }
        switch ($dialect_id) {
            case 3: // южновепсский 
                return $stem5. 'm'. $a.$a;
            case 4: // средневепсский восточный 
                return $stem5. 'mou';
            case 5: // средневепсский западный 
                return $stem5. 'mau';
            default:
                return $stem5. 'm'. $a. 'l';
        }        
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

    public static function inf3Elat($stem5, $a, $dialect_id){
        if (!$stem5) {
            return '';
        }
        switch ($dialect_id) {
            case 43: // младописьменный 
                return $stem5. 'm'. $a. 'späi';
            case 3: // южновепсский 
                return $stem5. 'm'. $a. 'spää';
            default:
                return $stem5. 'm'. $a. 'spei';
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
    
    /**
     * 140. актив, 2-е причастие, ед.ч. 
     * основа 1 + n; основа 1 + nd (южновепсский)
     * основа 5 + nu (остальные)
     * 
     * @param String $stem1
     * @param String $stem5
     * @param Int $dialect_id
     * @return String
     */
    public static function partic2activeSg($stem1, $stem5, $dialect_id) {
        switch ($dialect_id) {
            case 3: // южновепсский 
                return $stem1 ? $stem1. 'n, '. $stem1. 'nd' : '';
            default:
                return $stem5 ? $stem5. 'nu' : '';
        }        
    }
    
    /**
     * 141. актив, 2-е причастие, мн. ч.
     * основа 0 + d/t1 + ud (северновепсский)
     * основа 1 + n; основа 1 + nd (южновепсский)
     * основа 5 + nuded (остальные)
     * 
     * @param String $stem0
     * @param String $stem1
     * @param String $stem5
     * @param String $dt d/t
     * @param Int $dialect_id
     * @return String
     */
    public static function partic2activePl($stem0, $stem1, $stem5, $dt, $dialect_id) {
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem0 && $dt ? $stem0. $dt. 'ud' : '';
            case 3: // южновепсский 
                return $stem1 ? $stem1. 'n, '. $stem1. 'nd' : '';
            default:
                return $stem5 ? $stem5. 'nuded' : '';
        }        
    }
}