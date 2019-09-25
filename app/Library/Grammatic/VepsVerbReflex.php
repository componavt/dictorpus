<?php

namespace App\Library\Grammatic;

use App\Library\Grammatic;
use App\Library\Grammatic\VepsGram;
use App\Library\Grammatic\VepsVerb;

use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;

class VepsVerbReflex
{
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
        $neg_verb = VepsVerb::negVerb($gramset_id, $dialect_id);

        switch ($gramset_id) {
            case 26: // 1. индикатив, презенс, 1 л., ед.ч., +
                return self::indPres1Sg($stems[1], $dialect_id);
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
                return $stems[0] && $stems[6] ? $stems[0]. $stems[6]. 'e' : '';
            case 296: // 145. индикатив, презенс, коннегатив, мн.ч.
                return self::indPresConnegPl($stems[0], $stems[1], $stems[6], $dialect_id);

            case 70: // 7. индикатив, презенс, 1 л., ед.ч., -
            case 71: // 8. индикатив, презенс, 2 л., ед.ч., -
            case 72: // 9. индикатив, презенс, 3 л., ед.ч., -
                return $stems[0] && $stems[6] ? $stems[0]. $stems[6]. 'e' : '';
            case 73: //10. индикатив, презенс, 1 л., мн.ч., -
            case 78: // 11. индикатив, презенс, 2 л., мн.ч., -
            case 79: // 12. индикатив, презенс, 3 л., мн.ч., -
                return $stems[0] ? $neg_verb. self::indPresConnegPl($stems[0], $stems[1], $stems[6], $dialect_id) : '';
        }
    }
    
    public static function wordformByStemsIndImperf($stems, $gramset_id, $dialect_id) {
        $neg_verb = VepsVerb::negVerb($gramset_id, $dialect_id);
        
        switch ($gramset_id) {
            case 32: // 13. индикатив, имперфект, 1 л., ед.ч., +
                return self::indImperf1Sg($stems[2], $dialect_id);
            case 33: // 14. индикатив, имперфект, 2 л., ед.ч., +
                return self::indImperf2Sg($stems[2], $dialect_id);
            case 34: // 15. индикатив, имперфект, 3 л., ед.ч., +
                return $stems[2] ? $stems[2]. 'he' : '';
            case 35: // 16. индикатив, имперфект, 1 л., мн.ч., +
                return self::indImperf1Pl($stems[2], $dialect_id);
            case 36: // 17. индикатив, имперфект, 2 л., мн.ч., +
                return self::indImperf2Pl($stems[2], $dialect_id);
            case 37: // 18. индикатив, имперфект, 3 л., мн.ч., +
                return self::indImperf3Pl($stems[0], $stems[2], $stems[6], $dialect_id);
            case 297: // 146. индикатив, имперфект, коннегатив, ед.ч.
                return self::indImperfConnegSg($stems[1], $stems[3], $dialect_id);
            case 298: // 147. индикатив, имперфект, коннегатив, мн.ч.
                return self::indImperfConnegPl($stems[1], $stems[3], $dialect_id);

            case 80: // 19. индикатив, имперфект, 1 л., ед.ч., -
            case 81: // 20. индикатив, имперфект, 2 л., ед.ч., -
            case 82: // 21. индикатив, имперфект, 3 л., ед.ч., -
                return self::interLists($neg_verb, self::indImperfConnegSg($stems[1], $stems[3], $dialect_id));
            case 83: // 22. индикатив, имперфект, 1 л., мн.ч., -
            case 84: // 23. индикатив, имперфект, 2 л., мн.ч., -
            case 85: // 24. индикатив, имперфект, 3 л., мн.ч., -
                return self::interLists($neg_verb, self::indImperfConnegPl($stems[0], $stems[1], $stems[3], $stems[6], $dialect_id));
        }
    }
    
    public static function wordformByStemsIndPerf($stems, $gramset_id, $dialect_id) {
        $neg_verb = VepsVerb::negVerb($gramset_id, $dialect_id);
        $aux_verb = VepsVerb::auxVerb($gramset_id, $dialect_id);

        switch ($gramset_id) {
            case 86: // 25. индикатив, перфект, 1 л., ед.ч., +
            case 87: // 26. индикатив, перфект, 2 л., ед.ч., +
            case 88: // 27. индикатив, перфект, 3 л., ед.ч., +
                return self::interLists($aux_verb, self::partic2activeSg($stems[5], $dialect_id));
            case 89: // 28. индикатив, перфект, 1 л., мн.ч., +
            case 90: // 29. индикатив, перфект, 2 л., мн.ч., +
            case 91: // 30. индикатив, перфект, 3 л., мн.ч., +
                return self::interLists($aux_verb, self::partic2activePl($stems[5], $dialect_id));
            case 92: // 31. индикатив, перфект, 1 л., ед.ч., -
            case 93: // 32. индикатив, перфект, 2 л., ед.ч., -
            case 94: // 33. индикатив, перфект, 3 л., ед.ч., -
                return self::interLists(self::interLists($neg_verb, $aux_verb), self::partic2activeSg($stems[5], $dialect_id));
            case 95: // 34. индикатив, перфект, 1 л., мн.ч., -
            case 96: // 35. индикатив, перфект, 2 л., мн.ч., -
            case 97: // 36. индикатив, перфект, 3 л., мн.ч., -
                return self::interLists(self::interLists($neg_verb, $aux_verb),  self::partic2activePl($stems[5], $dialect_id));
        }
    }
    
    public static function wordformByStemsIndPlus($stems, $gramset_id, $dialect_id) {
        $neg_verb = VepsVerb::negVerb($gramset_id, $dialect_id);
        $aux_verb = VepsVerb::auxVerb($gramset_id, $dialect_id);

        switch ($gramset_id) {
            case 98: // 37. индикатив, плюсквамперфект, 1 л., ед.ч., +
            case 99: // 38. индикатив, плюсквамперфект, 2 л., ед.ч., +
            case 100: // 39. индикатив, плюсквамперфект, 3 л., ед.ч., +
                return self::interLists($aux_verb, self::partic2activeSg($stems[5], $dialect_id));
            case 101: // 40. индикатив, плюсквамперфект, 1 л., мн.ч., +
            case 102: // 41. индикатив, плюсквамперфект, 2 л., мн.ч., +
            case 103: // 42. индикатив, плюсквамперфект, 3 л., мн.ч., +
                return self::interLists($aux_verb, self::partic2activePl($stems[5], $dialect_id));

            case 104: // 43. индикатив, плюсквамперфект, 1 л., ед.ч., -
            case 105: // 44. индикатив, плюсквамперфект, 2 л., ед.ч., -
            case 107: // 45. индикатив, плюсквамперфект, 3 л., ед.ч., -
                return self::interLists(self::interLists($neg_verb, $aux_verb), self::partic2activeSg($stems[5], $dialect_id));
            case 108: // 46. индикатив, плюсквамперфект, 1 л., мн.ч., -
            case 106: // 47. индикатив, плюсквамперфект, 2 л., мн.ч., -
            case 109: // 48. индикатив, плюсквамперфект, 3 л., мн.ч., -
                return self::interLists(self::interLists($neg_verb, $aux_verb), self::partic2activePl($stems[5], $dialect_id));
        }
    }
    
    public static function wordformByStemsImper($stems, $gramset_id, $dialect_id) {
        $g = VepsGram::rightConsonant($stems[6], 'g');
        $neg_verb = VepsVerb::negVerb($gramset_id, $dialect_id);

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
        $neg_verb = VepsVerb::negVerb($gramset_id, $dialect_id);
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
                return $stems[4] ? self::interLists($neg_verb, self::condPresConSg($stems[4], $dialect_id)) : '';
            case 113: // 68. кондиционал, презенс, 1 л., мн.ч., отр. 
            case 114: // 69. кондиционал, презенс, 2 л., мн.ч., отр. 
            case 115: // 70. кондиционал, презенс, 3 л., мн.ч., отр. 
                return $stems[4] ? $neg_verb. self::condPresConPl($stems[0], $stems[4], $stems[6], $dialect_id) : ''; 
        }
    }
    
    public static function wordformByStemsCondImperf($stems, $gramset_id, $dialect_id) {
        $neg_verb = VepsVerb::negVerb($gramset_id, $dialect_id);

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
                return $stems[4] ? self::interLists($neg_verb, self::condImperfConSg($stems[4], $dialect_id)) : '';
        }
    }
    
    public static function wordformByStemsInf($stems, $gramset_id, $dialect_id) {
        $neg_verb = VepsVerb::negVerb($gramset_id, $dialect_id);
        
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
                return $stems[5] ? $stems[5]. 'nu' : '';
            case 309: // 141. актив, 2-е причастие, мн.ч. !!!!! TODO
                return '';
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
    
    public static function indPres1Sg($stem1, $dialect_id){
        if (!$stem1) { return ''; }

        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem1. 'mei';
            case 3: // южновепсский 
                return $stem1. 'me';
            case 4: // средневепсский восточный 
                return $stem1. 'mei, '. $stem1. 'mi';
            default:
                return $stem1. 'moi';
        }        
    }

    public static function indPres2Sg($stem1, $dialect_id){
        if (!$stem1) { return ''; }
        
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem1. 'tei';
            case 3: // южновепсский 
                return $stem1. 'te';
            case 4: // средневепсский восточный 
                return $stem1. 'ti';
            default:
                return $stem1. 'toi';
        }        
    }

    /**
     * основа 1 + z/ž+e (северновепсский)
     * основа 1 + zhe (южновепсский)
     * основа 1 + s/š+e(si)(средневепсский восточный) 
     * основа 1 + s/š+oi (средневепсский западный)
     * основа 1 + s/š+e (остальные) 
     * Если основа оканчивается на i, то s→š,  z→ž
     * Признак презенса 3 л. ед.ч. - окончание [sšzž][eo]i?    
     *  
     * @param String $stem1
     * @param Int $dialect_id
     * @return string
     */
    public static function indPres3Sg($stem1, $dialect_id){
        if (!$stem1) { return ''; }
        
        if (preg_match("/i$/",$stem1)) {
            $s = 'š';
            $z = 'ž';
        } else {
            $s = 's';
            $z = 'z';
        }
        
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem1. $z. 'e';
            case 3: // южновепсский 
                return $stem1. 'zhe';
            case 4: // средневепсский восточный 
                return $stem1. $s. 'e, '. $stem1. $s. 'i';
            case 5: // средневепсский западный 
                return $stem1. $s. 'oi';
            default:
                return $stem1. $s. 'e';
        }        
    }

    public static function indPres1Pl($stem1, $dialect_id){
        if (!$stem1) { return ''; }
       
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem1. 'mei';
            case 3: // южновепсский 
                return $stem1. 'moo';
            case 4: // средневепсский восточный 
                return $stem1. 'mei, '. $stem1. 'mi';
            default:
                return $stem1. 'moiš';
        }        
    }
    
    public static function indPres2Pl($stem1, $dialect_id){
        if (!$stem1) { return ''; }
       
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem1. 'tei';
            case 3: // южновепсский 
                return $stem1. 'too';
            case 4: // средневепсский восточный 
                return $stem1. 'ti';
            default:
                return $stem1. 'toiš';
        }        
    }
    
    public static function indPres3Pl($stem0, $stem1, $dt, $dialect_id){
        if (!$stem1) { return ''; }
        
        if (preg_match("/i$/",$stem1)) {
            $s = 'š';
            $z = 'ž';
        } else {
            $s = 's';
            $z = 'z';
        }
        
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem1. $z. 'e';
            case 3: // южновепсский 
                return $stem1. $s. 'oo';
            default:
                return $stem1. $s. 'oiš';
        }        
    }
    
    public static function indPresConnegPl($stem0, $dt, $dialect_id){
        if (!$stem0) { return ''; }
        $gk = VepsGram::rightConsonant($dt, 'g');
        
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem0. $gk. 'iiže';
            case 3: // южновепсский 
            case 4: // средневепсский восточный 
                return $stem0.  'te';
            default:
                return $stem0. $gk. 'oiš';
        }        
    }
    
    public static function indImperf1Sg($stem2, $dialect_id){
        if (!$stem2) { return ''; }
       
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem2. 'mei';
            case 3: // южновепсский 
                return $stem2. 'ime';
            default:
                return $stem2. 'moi';
        }        
    }
    
    public static function indImperf2Sg($stem2, $dialect_id){
        if (!$stem2) { return ''; }
       
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem2. 'tei';
            case 3: // южновепсский 
                return $stem2. 'ite';
            default:
                return $stem2. 'toi';
        }        
    }
    
    public static function indImperf1Pl($stem2, $dialect_id){
        if (!$stem2) { return ''; }
       
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem2. 'mei';
            case 3: // южновепсский 
                return $stem2. 'imoo';
            default:
                return $stem2. 'moiš';
        }        
    }
    
    public static function indImperf2Pl($stem2, $dialect_id){
        if (!$stem2) { return ''; }
       
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem2. 'tei';
            case 3: // южновепсский 
                return $stem2. 'itoo';
            default:
                return $stem2. 'toiš';
        }        
    }
    
    public static function indImperf3Pl($stem0, $stem2, $dt, $dialect_id){
        if (!$stem2) { return ''; }
        
        switch ($dialect_id) {
            case 3: // южновепсский 
                return $stem2. 'ihoo';
            case 4: // средневепсский восточный 
                return $stem2. 'hezoiš';
            case 5: // средневепсский западный 
                return $stem2. 'hezoi';
            default:
                return $stem2. 'he';
        }        
    }
    
    public static function indImperfConnegSg($stem3, $dialect_id){
        if (!$stem3) { return ''; }
        
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem3. 'nuze';
            case 3: // южновепсский 
                return $stem3. 'nuzhe';
            case 4: // средневепсский восточный 
                return $stem3. 'nukse';
            default:
                return $stem3. 'nus';
        }        
    }
    
    public static function indImperfConnegPl($stem3, $dialect_id){
        if (!$stem3) { return ''; }
        
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem3. 'nuze';
            case 3: // южновепсский 
                return $stem3. 'nuzhe';
            case 4: // средневепсский восточный 
                return $stem3. 'nuksoi, '. $stem3. 'nuksoiš';
            case 5: // средневепсский западный 
                return $stem3. 'nusoi';
            default:
                return $stem3. 'nus';
        }        
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
     * 
     * @param String $stem1
     * @param String $stem5
     * @param Int $dialect_id
     * @return String
     */
    public static function partic2activeSg($stem5, $dialect_id) {
        if (!$stem5) { return; }
        
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem5. 'nuze';
            case 3: // южновепсский 
                return $stem5. 'nuzhe';
            case 4: // средневепсский восточный 
                return $stem5. 'nukse';
            default:
                return $stem5. 'nus';
        }        
    }
    
    /**
     * 141. актив, 2-е причастие, мн. ч.
     * 
     * @param String $stem0
     * @param String $stem1
     * @param String $stem5
     * @param String $dt d/t
     * @param Int $dialect_id
     * @return String
     */
    public static function partic2activePl($stem0, $stem1, $stem5, $dt, $dialect_id) {
        if (!$stem5) { return; }
        
        switch ($dialect_id) {
            case 1: // северновепсский 
                return $stem5. 'nuze';
            case 3: // южновепсский 
                return $stem5. 'nuzhoo';
            case 4: // средневепсский восточный 
                return $stem5. 'nukse';
            case 5: // средневепсский западный 
                return $stem5. 'nusoi';
            default:
                return $stem5. 'nus';
        }        
    }
}