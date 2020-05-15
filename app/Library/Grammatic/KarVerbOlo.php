<?php

namespace App\Library\Grammatic;

use App\Library\Grammatic;
use App\Library\Grammatic\KarGram;

use App\Models\Dict\Gramset;
//use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;

/**
 * Functions related to Karelian grammatic for verbs.
 */
class KarVerbOlo
{
    /**
     * 
     * @param array $stems
     * @param int $stem_n
     * @param int $dialect_id
     * @param string $lemma
     * @return string
     */
    public static function getStemFromStems($stems, $stem_n, $dialect_id, $lemma) {
        switch ($stem_n) {
            case 3: 
                return isset($stems[1]) && isset($stems[2]) 
                    ? self::prsStrongVocalBase($stems[1], $stems[2]) : null;
            case 5: 
                return isset($stems[1]) && isset($stems[4]) 
                    ? self::weakImpBase($stems[1], $stems[4]) : null;
            case 8:
                return isset($stems[0]) && isset($stems[3]) && isset($stems[4]) 
                    ? self::vocalStrongCons($stems[0], $stems[3], $stems[4]) : null;
        }
         return '';
    }
    
    public static function getStemFromWordform($lemma, $stem_n, $dialect_id) {
        switch ($stem_n) {
            case 2: // indicative ptresence 3 sg
                if (preg_match("/^(.+)n$/", $lemma->wordform(28, $dialect_id), $regs)) {
                    return preg_replace("/,\s*/", '/',$regs[1]);
                }
                return '';
            case 3: // 3 infinitive illative
                if (preg_match("/^(.+)m[aä]h$/u", $lemma->wordform(174, $dialect_id), $regs)) { 
                    return preg_replace("/,\s*/", '/',$regs[1]);
                }
                return '';
            case 5: // indicative imperfect 1 sg
                if (preg_match("/^(.+)n$/", $lemma->wordform(32, $dialect_id), $regs)) {
                    return preg_replace("/,\s*/", '/',$regs[1]);
                }
                return '';
            case 7: // indicative imperfect 3 pl
                $stem7 = $lemma->wordform(37, $dialect_id); 
                return $stem7 ? preg_replace("/,\s*/", '/',$stem7) : '';
        }
    }

    /** Lists of ID of gramsets, which have the rules.
     * That is we know how to generate word forms (using stems, endings and rules) for this gramset ID.
     * 
     * @return type list of known gramsets ID
     */
    public static function getListForAutoComplete() {
        return [26,   27,  28,  29,  30,  31, 295, 296, 
                70,   71,  72,  73,  78,  79, 
                32,   33,  34,  35,  36,  37, //297, 298, 
                80,   81,  82,  83,  84,  85, 
                86,   87,  88,  89,  90,  91,  92,  93,  94,  95,  96,  97,
                98,   99, 100, 101, 102, 103, 104, 105, 107, 108, 106, 109,
                      51,  52,  53,  54,  55,       50,  74,  75,  76,  77,  
                38,   39,  40,  41,  42,  43, //301, 303,
                110, 111, 112, 113, 114, 115,
                44,   45,  46,  47,  48,  49, 116, 117, 118, 119, 120, 121,
                122, 123, 124, 126, 127, 128, 129, 130, 131, 132, 133, 134,
                135, 125, 136, 137, 138, 139, 140, 141, 142, 143, 144, 145,
                146, 147, 148, 149, 150, 151, //310, 311, 
                152, 153, 154, 155, 156, 157,
                158, 159, 160, 161, 162, 163, 164, 165, 166, 167, 168, 169,
                170, 171, 172, 173, 174, 175, 176, 177, 312,
                178, 179, 180, 181];
    }
    
    public static function wordformByStems($stems, $gramset_id, $dialect_id, $def=NULL) {
        $lang_id=5;
        switch ($gramset_id) {
            case 28: // 3. индикатив, презенс, 3 л., ед.ч., пол. 
                return Grammatic::joinMorfToBases($stems[2], '');
            case 29: // 4. индикатив, презенс, 1 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[1], 'mm'. KarGram::garmVowel($stems[1],'o')) : '';
            case 30: // 5. индикатив, презенс, 2 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[1], 'tt'. KarGram::garmVowel($stems[1],'o')) : '';

            case 32: // 13. индикатив, имперфект, 1 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[5], 'n') : '';
            case 33: // 14. индикатив, имперфект, 2 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[5], 't') : '';
            case 34: // 15. индикатив, имперфект, 3 л., ед.ч., пол. 
                return Grammatic::joinMorfToBases($stems[4], '');
            case 35: // 16. индикатив, имперфект, 1 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[5], 'mm'. KarGram::garmVowel($stems[5],'o')) : '';
            case 36: // 17. индикатив, имперфект, 2 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[5], 'tt'. KarGram::garmVowel($stems[5],'o')) : '';
/*            case 297: // 146. индикатив, имперфект, коннегатив, ед.ч.
                return self::partic2active($stems[1], $stems[8]);
            case 298: // 147. индикатив, имперфект, коннегатив, мн.ч.
                return self::partic2passive($stems[7]);*/

            case 80: // 19. индикатив, имперфект, 1 л., ед.ч., отриц. 
            case 81: // 20. индикатив, имперфект, 2 л., ед.ч., отриц. 
            case 83: // 22. индикатив, имперфект, 1 л., мн.ч., отриц. 
            case 84: // 23. индикатив, имперфект, 2 л., мн.ч., отриц. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), self::partic2active($stems[0], $stems[1], $stems[8], $def)) : '';
            case 82: // 21. индикатив, имперфект, 3 л., ед.ч., отриц. 
                return Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), self::partic2active($stems[0], $stems[1], $stems[8], $def));
            case 85: // 24. индикатив, имперфект, 3 л., мн.ч., отриц. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), self::partic2passive($stems[7])) : '';

            case 86: // 25. индикатив, перфект, 1 л., ед.ч., пол. 
            case 87: // 26. индикатив, перфект, 2 л., ед.ч., пол. 
            case 89: // 28. индикатив, перфект, 1 л., мн.ч., пол. 
            case 90: // 29. индикатив, перфект, 2 л., мн.ч., пол. 
            case 98: // 37. индикатив, плюсквамперфект, 1 л., ед.ч., пол. 
            case 99: // 38. индикатив, плюсквамперфект, 2 л., ед.ч., пол. 
            case 101: // 40. индикатив, плюсквамперфект, 1 л., мн.ч., пол. 
            case 102: // 41. индикатив, плюсквамперфект, 2 л., мн.ч., пол. 
            case 122: // 83. кондиционал, перфект, 1 л., ед.ч., пол. 
            case 123: // 84. кондиционал, перфект, 2 л., ед.ч., пол. 
            case 126: // 86. кондиционал, перфект, 1 л., мн.ч., пол. 
            case 127: // 87. кондиционал, перфект, 2 л., мн.ч., пол. 
            case 135: // 95. кондиционал, плюсквамперфект, 1 л., ед.ч., пол. 
            case 125: // 96. кондиционал, плюсквамперфект, 2 л., ед.ч., пол. 
            case 137: // 98. кондиционал, плюсквамперфект, 1 л., мн.ч., пол. 
            case 138: // 99. кондиционал, плюсквамперфект, 2 л., мн.ч., пол. 
            case 158: // 119. потенциал, перфект, 1 л., ед.ч., пол. 
            case 159: // 120. потенциал, перфект, 2 л., ед.ч., пол. 
            case 161: // 122. потенциал, перфект, 1 л., мн.ч., пол. 
            case 162: // 123. потенциал, перфект, 2 л., мн.ч., пол. 
                return !$def ? Grammatic::interLists(self::auxVerb($gramset_id, $dialect_id), self::partic2active($stems[0], $stems[1], $stems[8], $def)) : '';
            case 88: // 27. индикатив, перфект, 3 л., ед.ч., пол. 
            case 100: // 39. индикатив, плюсквамперфект, 3 л., ед.ч., пол. 
            case 124: // 85. кондиционал, перфект, 3 л., ед.ч., пол. 
            case 136: // 97. кондиционал, плюсквамперфект, 3 л., ед.ч., пол. 
            case 160: // 121. потенциал, перфект, 3 л., ед.ч., пол. 
                return Grammatic::interLists(self::auxVerb($gramset_id, $dialect_id), self::partic2active($stems[0], $stems[1], $stems[8], $def));
            case 91: // 30. индикатив, перфект, 3 л., мн.ч., пол. 
            case 103: // 42. индикатив, плюсквамперфект, 3 л., мн.ч., пол. 
            case 128: // 88. кондиционал, перфект, 3 л., мн.ч., пол. 
            case 139: // 100. кондиционал, плюсквамперфект, 3 л., мн.ч., пол. 
            case 163: // 124. потенциал, перфект, 3 л., мн.ч., пол. 
                return !$def ? Grammatic::interLists(self::auxVerb($gramset_id, $dialect_id), self::partic2passive($stems[7])) : '';

            case 92: // 31. индикатив, перфект, 1 л., ед.ч., отриц. 
            case 93: // 32. индикатив, перфект, 2 л., ед.ч., отриц. 
            case 95: // 34. индикатив, перфект, 1 л., мн.ч., отриц. 
            case 96: // 35. индикатив, перфект, 2 л., мн.ч., отриц. 
            case 104: // 43. индикатив, плюсквамперфект, 1 л., ед.ч., отриц. 
            case 105: // 44. индикатив, плюсквамперфект, 2 л., ед.ч., отриц. 
            case 106: // 46. индикатив, плюсквамперфект, 1 л., мн.ч., отриц. 
            case 108: // 47. индикатив, плюсквамперфект, 2 л., мн.ч., отриц. 
            case 129: // 89. кондиционал, перфект, 1 л., ед.ч., отриц. 
            case 130: // 90. кондиционал, перфект, 2 л., ед.ч., отриц. 
            case 132: // 92. кондиционал, перфект, 1 л., мн.ч., отриц. 
            case 133: // 93. кондиционал, перфект, 2 л., мн.ч., отриц. 
            case 140: // 101. кондиционал, плюсквамперфект, 1 л., ед.ч., отриц. 
            case 141: // 102. кондиционал, плюсквамперфект, 2 л., ед.ч., отриц. 
            case 143: // 104. кондиционал, плюсквамперфект, 1 л., мн.ч., отриц. 
            case 144: // 105. кондиционал, плюсквамперфект, 2 л., мн.ч., отриц. 
            case 164: // 125. потенциал, перфект, 1 л., ед.ч., отр. 
            case 165: // 126. потенциал, перфект, 2 л., ед.ч., отр. 
            case 167: // 128. потенциал, перфект, 1 л., мн.ч., отр. 
            case 168: // 129. потенциал, перфект, 2 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(self::auxVerb($gramset_id, $dialect_id, '-'), self::partic2active($stems[0], $stems[1], $stems[8], $def)) : '';
            case 94: // 33. индикатив, перфект, 3 л., ед.ч., отриц. 
            case 107: // 45. индикатив, плюсквамперфект, 3 л., ед.ч., отриц. 
            case 131: // 91. кондиционал, перфект, 3 л., ед.ч., отриц. 
            case 142: // 103. кондиционал, плюсквамперфект, 3 л., ед.ч., отриц. 
            case 166: // 127. потенциал, перфект, 3 л., ед.ч., отр. 
                return Grammatic::interLists(self::auxVerb($gramset_id, $dialect_id, '-'), self::partic2active($stems[0], $stems[1], $stems[8], $def));
            case 97: // 36. индикатив, перфект, 3 л., мн.ч., отриц. 
            case 109: // 48. индикатив, плюсквамперфект, 3 л., мн.ч., отриц. 
            case 134: // 94. кондиционал, перфект, 3 л., мн.ч., отриц. 
            case 145: // 106. кондиционал, плюсквамперфект, 3 л., мн.ч., отриц. 
            case 169: // 130. потенциал, перфект, 3 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(self::auxVerb($gramset_id, $dialect_id, '-'), self::partic2passive($stems[7])) : '';

            case 52: // 50. императив, 3 л., ед.ч., пол 
                return Grammatic::joinMorfToBases(self::impBaseSg($stems[8]), KarGram::garmVowel($stems[8],'a').'h');
            case 53: // 51. императив, 1 л., мн.ч., пол 
                return !$def ? Grammatic::joinMorfToBases(self::impBasePl($stems[8]), 'mm'.KarGram::garmVowel($stems[8],'o')) : '';
            case 54: // 52. императив, 2 л., мн.ч., пол 
                return !$def && $stems[8] ? self::impBasePl($stems[8]). ', '. Grammatic::joinMorfToBases(self::impBasePl($stems[8]), 'tt'.KarGram::garmVowel($stems[8],'o')) : '';
            case 55: // 53. императив, 3 л., мн.ч., пол 
                return !$def ? Grammatic::joinMorfToBases($stems[7], KarGram::garmVowel($stems[7],'a').'hes') : '';

            case 74: // 55. императив, 3 л., ед.ч., отр. 
                return Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), Grammatic::joinMorfToBases(self::impBaseSg($stems[8]),KarGram::garmVowel($stems[8],'a').'h'));
            case 75: // 56. императив, 1 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), Grammatic::joinMorfToBases(self::impBasePl($stems[8]), 'mm'.KarGram::garmVowel($stems[8],'o'))): '';
            case 76: // 57. императив, 2 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), self::impBasePl($stems[8])): '';
            case 77: // 58. императив, 3 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), Grammatic::joinMorfToBases($stems[7], KarGram::garmVowel($stems[7],'a').'hes')) : '';

            case 38: // 59. кондиционал, презенс, 1 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases(self::condPrsBase($stems[3]), 'zin') : '';
            case 39: // 60. кондиционал, презенс, 2 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases(self::condPrsBase($stems[3]), 'zit') : '';
            case 40: // 61. кондиционал, презенс, 3 л., ед.ч., пол. 
                return Grammatic::joinMorfToBases(self::condPrsBase($stems[3]), 's');
            case 41: // 62. кондиционал, презенс, 1 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases(self::condPrsBase($stems[3]), 'zimm'. KarGram::garmVowel($stems[4],'o')) : '';
            case 42: // 63. кондиционал, презенс, 2 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases(self::condPrsBase($stems[3]), 'zitt'. KarGram::garmVowel($stems[4],'o')) : '';
            case 43: // 64. кондиционал, презенс, 3 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[7], KarGram::garmVowel($stems[7],'a'). 's') : '';

            case 110: // 70. кондиционал, презенс, 1 л., ед.ч., - 
            case 111: // 71. кондиционал, презенс, 2 л., ед.ч., -
            case 113: // 73. кондиционал, презенс, 1 л., мн.ч., -
            case 114: // 78. кондиционал, презенс, 2 л., мн.ч., -
                return !$def ? Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), Grammatic::joinMorfToBases(self::condPrsBase($stems[3]), 's')) : '';
            case 112: // 72. кондиционал, презенс, 3 л., ед.ч., -
                return Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), Grammatic::joinMorfToBases(self::condPrsBase($stems[3]), 's'));
            case 115: // 79. кондиционал, презенс, 3 л., мн.ч., -
                return !$def ? Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), Grammatic::joinMorfToBases($stems[7], KarGram::garmVowel($stems[7],'a'). 's')) : '';

            case 44: // 71. кондиционал, имперфект, 1 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases(self::condImpBase($stems[0], $stems[1], $stems[8], $def), 'zin') : '';
            case 45: // 72. кондиционал, имперфект, 2 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases(self::condImpBase($stems[0], $stems[1], $stems[8], $def), 'zit') : '';
            case 46: // 73. кондиционал, имперфект, 3 л., ед.ч., пол. 
                return Grammatic::joinMorfToBases(self::condImpBase($stems[0], $stems[1], $stems[8], $def), 's');
            case 47: // 74. кондиционал, имперфект, 1 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases(self::condImpBase($stems[0], $stems[1], $stems[8], $def), 'zimm'. KarGram::garmVowel($stems[8],'o')) : '';
            case 48: // 75. кондиционал, имперфект, 2 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases(self::condImpBase($stems[0], $stems[1], $stems[8], $def), 'zitt'.KarGram::garmVowel($stems[8],'o')) : '';
            case 49: // 76. кондиционал, имперфект, 3 л., мн.ч., пол. 
                return !$def ? self::condImp3Pl($stems[7]) : '';

            case 116: // 77. кондиционал, имперфект, 1 л., ед.ч., отр. 
            case 117: // 78. кондиционал, имперфект, 2 л., ед.ч., отр. 
            case 119: // 80. кондиционал, имперфект, 1 л., мн.ч., отр. 
            case 120: // 81. кондиционал, имперфект, 2 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), Grammatic::joinMorfToBases(self::condImpBase($stems[0], $stems[1], $stems[8], $def), 's')) : '';
            case 118: // 79. кондиционал, имперфект, 3 л., ед.ч., отр. 
                return Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), Grammatic::joinMorfToBases(self::condImpBase($stems[0], $stems[1], $stems[8], $def), 's'));
            case 121: // 82. кондиционал, имперфект, 3 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), self::condImp3Pl($stems[7])) : '';
                
            case 146: // 107. потенциал, презенс, 1 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases(self::potenPrsBase($stems[0], $stems[1], $stems[8], $def), 'n') : '';
            case 147: // 108. потенциал, презенс, 2 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases(self::potenPrsBase($stems[0], $stems[1], $stems[8], $def), 't') : '';
            case 148: // 109. потенциал, презенс, 3 л., ед.ч., пол. 
                return self::potenPrs3Sg($stems[0], $stems[1], $stems[8], $def);
            case 149: // 110. потенциал, презенс, 1 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases(self::potenPrsBase($stems[0], $stems[1], $stems[8], $def), 'mm'. KarGram::garmVowel($stems[5], 'o')) : '';
            case 150: // 111. потенциал, презенс, 2 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases(self::potenPrsBase($stems[0], $stems[1], $stems[8], $def), 'tt'. KarGram::garmVowel($stems[5], 'o')) : '';
            case 151: // 112. потенциал, презенс, 3 л., мн.ч., пол. 
                return !$def ? self::potenPrs3Pl($stems[7]) : '';

            case 152: // 113. потенциал, презенс, 1 л., ед.ч., отр. 
            case 153: // 114. потенциал, презенс, 2 л., ед.ч., отр. 
            case 155: // 116. потенциал, презенс, 1 л., мн.ч., отр. 
            case 156: // 117. потенциал, презенс, 2 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), self::potenPrsBase($stems[0], $stems[1], $stems[8]), $def) : '';
            case 154: // 115. потенциал, презенс, 3 л., ед.ч., отр. 
                return Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), self::potenPrsBase($stems[0], $stems[1], $stems[8], $def));
            case 157: // 118. потенциал, презенс, 3 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), self::potenPrs3PlNeg($stems[7])) : '';
                
            case 170: // 131. I инфинитив 
                return $stems[0];
            case 171: // 132. II инфинитив, инессив 
                return Grammatic::joinMorfToBases(self::inf2Base($stems[0], $stems[3]), 's');
            case 172: // 133. II инфинитив, инструктив  
                return Grammatic::joinMorfToBases(self::inf2Base($stems[0], $stems[3]), 'n');
            case 173: // 134. III инфинитив, адессив
                return Grammatic::joinMorfToBases($stems[3], KarGram::garmVowel($stems[3], 'mal'));
            case 174: // 135. III инфинитив, иллатив 
                return Grammatic::joinMorfToBases($stems[3], KarGram::garmVowel($stems[3], 'mah'));
            case 175: // 136. III инфинитив, инессив 
                return Grammatic::joinMorfToBases($stems[3], KarGram::garmVowel($stems[3], 'mas'));
            case 176: // 137. III инфинитив, элатив 
                return Grammatic::joinMorfToBases($stems[3], KarGram::garmVowel($stems[3], 'ma').'späi');
            case 177: // 138. III инфинитив, абессив 
                return Grammatic::joinMorfToBases($stems[3], KarGram::garmVowel($stems[3], 'mattah'));
            case 312: // 139. III инфинитив, партитив 
                return Grammatic::joinMorfToBases($stems[3], KarGram::isBackVowels($stems[3]) ? 'mua': 'miä');
                
            case 178: // 140. актив, 1-е причастие 
                return self::partic1active($stems[3]);
            case 179: // 141. актив, 2-е причастие 
                return self::partic2active($stems[0], $stems[1], $stems[8], $def);
            case 180: // 143. пассив, 1-е причастие 
                return !$def ? Grammatic::joinMorfToBases($stems[7], KarGram::garmVowel($stems[7], 'avu')) : '';
            case 181: // 144. пассив, 2-е причастие 
                return !$def ? self::partic2passive($stems[7]) : '';
        }
        return '';
    }

    public static function wordformByStemsRef($stems, $gramset_id, $dialect_id, $def=NULL) {
        $lang_id=5;
        switch ($gramset_id) {
            case 26: // 1. индикатив, презенс, 1 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[1], KarGram::garmVowel($stems[1],'mmos')) : '';
            case 27: // 2. индикатив, презенс, 2 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[1], KarGram::garmVowel($stems[1],'ttos')) : '';        
            case 28: // 3. индикатив, презенс, 3 л., ед.ч., пол. 
                return Grammatic::joinMorfToBases($stems[2], '');
            case 29: // 4. индикатив, презенс, 1 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[1], KarGram::garmVowel($stems[1],'mmokseh')) : '';
            case 30: // 5. индикатив, презенс, 2 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[1], KarGram::garmVowel($stems[1],'ttokseh')) : '';
            case 31: // 6. индикатив, презенс, 3 л., мн.ч., пол. 
            case 296: // 145. индикатив, презенс, коннегатив, мн.ч.
                return !$def ? Grammatic::joinMorfToBases($stems[6],'') : '';
            case 295: // 144. индикатив, презенс, коннегатив, ед.ч.
                return self::IndPrsConSgRef($stems[3]);

            case 70: // 7. индикатив, презенс, 1 л., ед.ч., отриц. 
            case 71: // 8. индикатив, презенс, 2 л., ед.ч., отриц. 
            case 73: //10. индикатив, презенс, 1 л., мн.ч., отриц. 
            case 78: // 11. индикатив, презенс, 2 л., мн.ч., отриц. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), self::IndPrsConSgRef($stems[3])) : '';
            case 72: // 9. индикатив, презенс, 3 л., ед.ч., отриц. 
                return Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), self::IndPrsConSgRef($stems[3]));
            case 79: // 12. индикатив, презенс, 3 л., мн.ч., отриц. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm(79, $lang_id), $stems[6]) : '';
                
            case 32: // 13. индикатив, имперфект, 1 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[5], KarGram::garmVowel($stems[5],'mmos')) : '';
            case 33: // 14. индикатив, имперфект, 2 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[5], KarGram::garmVowel($stems[5],'ttos')) : '';
            case 34: // 15. индикатив, имперфект, 3 л., ед.ч., пол. 
                return Grammatic::joinMorfToBases($stems[4], '');
            case 35: // 16. индикатив, имперфект, 1 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[5], KarGram::garmVowel($stems[5],'mmokseh')) : '';
            case 36: // 17. индикатив, имперфект, 2 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[5], KarGram::garmVowel($stems[5],'ttokseh')): '';
            case 37: // 18. индикатив, имперфект, 3 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[7], 'ihes') : '';
                
/*            case 297: // 146. индикатив, имперфект, коннегатив, ед.ч.
                return self::partic2active($stems[1], $stems[8]);
            case 298: // 147. индикатив, имперфект, коннегатив, мн.ч.
                return self::partic2passive($stems[7]);*/

            case 80: // 19. индикатив, имперфект, 1 л., ед.ч., отриц. 
            case 81: // 20. индикатив, имперфект, 2 л., ед.ч., отриц. 
            case 83: // 22. индикатив, имперфект, 1 л., мн.ч., отриц. 
            case 84: // 23. индикатив, имперфект, 2 л., мн.ч., отриц. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), self::partic2active($stems[0], $stems[1], $stems[8], $def, true)) : '';
            case 82: // 21. индикатив, имперфект, 3 л., ед.ч., отриц. 
                return Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), self::partic2active($stems[0], $stems[1], $stems[8], $def, true));
            case 85: // 24. индикатив, имперфект, 3 л., мн.ч., отриц. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), self::partic2passive($stems[7], true)) : '';

            case 86: // 25. индикатив, перфект, 1 л., ед.ч., пол. 
            case 87: // 26. индикатив, перфект, 2 л., ед.ч., пол. 
            case 89: // 28. индикатив, перфект, 1 л., мн.ч., пол. 
            case 90: // 29. индикатив, перфект, 2 л., мн.ч., пол.                 
            case 98: // 37. индикатив, плюсквамперфект, 1 л., ед.ч., пол. 
            case 99: // 38. индикатив, плюсквамперфект, 2 л., ед.ч., пол. 
            case 101: // 40. индикатив, плюсквамперфект, 1 л., мн.ч., пол. 
            case 102: // 41. индикатив, плюсквамперфект, 2 л., мн.ч., пол.                 
            case 122: // 83. кондиционал, перфект, 1 л., ед.ч., пол. 
            case 123: // 84. кондиционал, перфект, 2 л., ед.ч., пол. 
            case 126: // 86. кондиционал, перфект, 1 л., мн.ч., пол. 
            case 127: // 87. кондиционал, перфект, 2 л., мн.ч., пол.                 
            case 135: // 95. кондиционал, плюсквамперфект, 1 л., ед.ч., пол. 
            case 125: // 96. кондиционал, плюсквамперфект, 2 л., ед.ч., пол. 
            case 137: // 98. кондиционал, плюсквамперфект, 1 л., мн.ч., пол. 
            case 138: // 99. кондиционал, плюсквамперфект, 2 л., мн.ч., пол.                 
            case 158: // 119. потенциал, перфект, 1 л., ед.ч., пол. 
            case 159: // 120. потенциал, перфект, 2 л., ед.ч., пол. 
            case 161: // 122. потенциал, перфект, 1 л., мн.ч., пол. 
            case 162: // 123. потенциал, перфект, 2 л., мн.ч., пол. 
                return !$def ? Grammatic::interLists(self::auxVerb($gramset_id, $dialect_id), self::partic2active($stems[0], $stems[1], $stems[8], $def, true)): '';
            case 88: // 27. индикатив, перфект, 3 л., ед.ч., пол. 
            case 100: // 39. индикатив, плюсквамперфект, 3 л., ед.ч., пол. 
            case 124: // 85. кондиционал, перфект, 3 л., ед.ч., пол. 
            case 136: // 97. кондиционал, плюсквамперфект, 3 л., ед.ч., пол. 
            case 160: // 121. потенциал, перфект, 3 л., ед.ч., пол. 
                return Grammatic::interLists(self::auxVerb($gramset_id, $dialect_id), self::partic2active($stems[0], $stems[1], $stems[8], $def, true));
            case 91: // 30. индикатив, перфект, 3 л., мн.ч., пол.                 
            case 103: // 42. индикатив, плюсквамперфект, 3 л., мн.ч., пол. 
            case 128: // 88. кондиционал, перфект, 3 л., мн.ч., пол.                 
            case 139: // 100. кондиционал, плюсквамперфект, 3 л., мн.ч., пол.                 
            case 163: // 124. потенциал, перфект, 3 л., мн.ч., пол. 
                return !$def ? Grammatic::interLists(self::auxVerb($gramset_id, $dialect_id, '+', true), self::partic2passive($stems[7], true)) : '';

            case 92: // 31. индикатив, перфект, 1 л., ед.ч., отриц. 
            case 93: // 32. индикатив, перфект, 2 л., ед.ч., отриц. 
            case 95: // 34. индикатив, перфект, 1 л., мн.ч., отриц. 
            case 96: // 35. индикатив, перфект, 2 л., мн.ч., отриц.                 
            case 104: // 43. индикатив, плюсквамперфект, 1 л., ед.ч., отриц. 
            case 105: // 44. индикатив, плюсквамперфект, 2 л., ед.ч., отриц. 
            case 108: // 46. индикатив, плюсквамперфект, 1 л., мн.ч., отриц. 
            case 106: // 47. индикатив, плюсквамперфект, 2 л., мн.ч., отриц.                 
            case 129: // 89. кондиционал, перфект, 1 л., ед.ч., отриц. 
            case 130: // 90. кондиционал, перфект, 2 л., ед.ч., отриц. 
            case 132: // 92. кондиционал, перфект, 1 л., мн.ч., отриц. 
            case 133: // 93. кондиционал, перфект, 2 л., мн.ч., отриц.                 
            case 140: // 101. кондиционал, плюсквамперфект, 1 л., ед.ч., отриц. 
            case 141: // 102. кондиционал, плюсквамперфект, 2 л., ед.ч., отриц. 
            case 143: // 104. кондиционал, плюсквамперфект, 1 л., мн.ч., отриц. 
            case 144: // 105. кондиционал, плюсквамперфект, 2 л., мн.ч., отриц.                 
            case 164: // 125. потенциал, перфект, 1 л., ед.ч., отр. 
            case 165: // 126. потенциал, перфект, 2 л., ед.ч., отр. 
            case 167: // 128. потенциал, перфект, 1 л., мн.ч., отр. 
            case 168: // 129. потенциал, перфект, 2 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(self::auxVerb($gramset_id, $dialect_id, '-'), self::partic2active($stems[0], $stems[1], $stems[8], $def, true)): '';
            case 94: // 33. индикатив, перфект, 3 л., ед.ч., отриц. 
            case 107: // 45. индикатив, плюсквамперфект, 3 л., ед.ч., отриц. 
            case 131: // 91. кондиционал, перфект, 3 л., ед.ч., отриц. 
            case 142: // 103. кондиционал, плюсквамперфект, 3 л., ед.ч., отриц. 
            case 166: // 127. потенциал, перфект, 3 л., ед.ч., отр. 
                return Grammatic::interLists(self::auxVerb($gramset_id, $dialect_id, '-'), self::partic2active($stems[0], $stems[1], $stems[8], $def, true));
            case 97: // 36. индикатив, перфект, 3 л., мн.ч., отриц.                 
            case 109: // 48. индикатив, плюсквамперфект, 3 л., мн.ч., отриц.                 
            case 134: // 94. кондиционал, перфект, 3 л., мн.ч., отриц.
            case 145: // 106. кондиционал, плюсквамперфект, 3 л., мн.ч., отриц.                 
            case 169: // 130. потенциал, перфект, 3 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(self::auxVerb($gramset_id, $dialect_id, '-'), self::partic2passive($stems[7], true)) : '';

            case 51: // 49. императив, 2 л., ед.ч., пол 
                return !$def ? self::IndPrsConSgRef($stems[3]) : '';                
            case 52: // 50. императив, 3 л., ед.ч., пол 
                return Grammatic::joinMorfToBases(self::impBaseSg($stems[8]), KarGram::garmVowel($stems[8],'ahes'));
            case 53: // 51. императив, 1 л., мн.ч., пол 
                return !$def ? Grammatic::joinMorfToBases(self::impBasePl($stems[8]), KarGram::garmVowel($stems[8],'mmokseh')): '';
            case 54: // 52. императив, 2 л., мн.ч., пол 
                return !$def ? Grammatic::joinMorfToBases(self::impBasePl($stems[8]), KarGram::garmVowel($stems[8],'ttokseh')): '';
            case 55: // 53. императив, 3 л., мн.ч., пол 
                return !$def ? Grammatic::joinMorfToBases($stems[7], KarGram::garmVowel($stems[7],'ahes')): '';

            case 50: // 54. императив, 2 л., ед.ч., отр. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), self::IndPrsConSgRef($stems[3])) : '';
            case 74: // 55. императив, 3 л., ед.ч., отр. 
                return Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), Grammatic::joinMorfToBases(self::impBaseSg($stems[8]),KarGram::garmVowel($stems[8],'ahes')));
            case 75: // 56. императив, 1 л., мн.ч., отр. 
                return '';
            case 76: // 57. императив, 2 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id),  Grammatic::joinMorfToBases(self::impBasePl($stems[8]), KarGram::garmVowel($stems[8],'ttokseh'))) : '';
            case 77: // 58. императив, 3 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), Grammatic::joinMorfToBases($stems[7], KarGram::garmVowel($stems[7],'ahes'))): '';

            case 38: // 59. кондиционал, презенс, 1 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases(self::condPrsBase($stems[3]), KarGram::garmVowel($stems[3],'zimmos')) : '';
            case 39: // 60. кондиционал, презенс, 2 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases(self::condPrsBase($stems[3]), KarGram::garmVowel($stems[3],'zittos')) : '';
            case 40: // 61. кондиционал, презенс, 3 л., ед.ч., пол. 
                return Grammatic::joinMorfToBases(self::condPrsBase($stems[3]), 'zihes');
            case 41: // 62. кондиционал, презенс, 1 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases(self::condPrsBase($stems[3]), KarGram::garmVowel($stems[3],'zimmokseh')) : '';
            case 42: // 63. кондиционал, презенс, 2 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases(self::condPrsBase($stems[3]), KarGram::garmVowel($stems[3],'zittokseh')): '';
            case 43: // 64. кондиционал, презенс, 3 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[7], KarGram::garmVowel($stems[7],'azihes')): '';

            case 110: // 70. кондиционал, презенс, 1 л., ед.ч., - 
            case 111: // 71. кондиционал, презенс, 2 л., ед.ч., -
            case 113: // 73. кондиционал, презенс, 1 л., мн.ч., -
            case 114: // 78. кондиционал, презенс, 2 л., мн.ч., -
                return !$def ? Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), Grammatic::joinMorfToBases(self::condPrsBase($stems[3]), 'zihes')): '';
            case 112: // 72. кондиционал, презенс, 3 л., ед.ч., -
                return Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), Grammatic::joinMorfToBases(self::condPrsBase($stems[3]), 'zihes'));
            case 112: // 72. кондиционал, презенс, 3 л., ед.ч., -
                return Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), Grammatic::joinMorfToBases(self::condPrsBase($stems[3]), 'zihes'));
            case 115: // 79. кондиционал, презенс, 3 л., мн.ч., -
                return !$def ? Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), Grammatic::joinMorfToBases($stems[7], KarGram::garmVowel($stems[7],'azihes'))): '';

            case 44: // 71. кондиционал, имперфект, 1 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases(self::condImpBase($stems[0], $stems[1], $stems[8], $def, true), KarGram::garmVowel($stems[1],'mmos')): '';
            case 45: // 72. кондиционал, имперфект, 2 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases(self::condImpBase($stems[0], $stems[1], $stems[8], $def, true), KarGram::garmVowel($stems[1],'ttos')): '';
            case 46: // 73. кондиционал, имперфект, 3 л., ед.ч., пол. 
                return Grammatic::joinMorfToBases(self::condImpBase($stems[0], $stems[1], $stems[8], $def, true), 'hes');
            case 47: // 74. кондиционал, имперфект, 1 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases(self::condImpBase($stems[0], $stems[1], $stems[8], $def, true), KarGram::garmVowel($stems[1],'mmokseh')): '';
            case 48: // 75. кондиционал, имперфект, 2 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases(self::condImpBase($stems[0], $stems[1], $stems[8], $def, true), KarGram::garmVowel($stems[1],'ttokseh')): '';
            case 49: // 76. кондиционал, имперфект, 3 л., мн.ч., пол. 
                return !$def ? self::condImp3Pl($stems[7], true) : '';

            case 116: // 77. кондиционал, имперфект, 1 л., ед.ч., отр. 
            case 117: // 78. кондиционал, имперфект, 2 л., ед.ч., отр. 
            case 119: // 80. кондиционал, имперфект, 1 л., мн.ч., отр. 
            case 120: // 81. кондиционал, имперфект, 2 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), Grammatic::joinMorfToBases(self::condImpBase($stems[0], $stems[1], $stems[8], $def, true), 'hes')): '';
            case 118: // 79. кондиционал, имперфект, 3 л., ед.ч., отр. 
                return Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), Grammatic::joinMorfToBases(self::condImpBase($stems[0], $stems[1], $stems[8], $def, true), 'hes'));
            case 121: // 82. кондиционал, имперфект, 3 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), self::condImp3Pl($stems[7], true)) : '';
                
            case 146: // 107. потенциал, презенс, 1 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases(self::potenPrsBase($stems[0], $stems[1], $stems[8], $def, true), KarGram::garmVowel($stems[1], 'mmos')) : '';
            case 147: // 108. потенциал, презенс, 2 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases(self::potenPrsBase($stems[0], $stems[1], $stems[8], $def, true), KarGram::garmVowel($stems[1], 'ttos')): '';
            case 148: // 109. потенциал, презенс, 3 л., ед.ч., пол. 
                return Grammatic::joinMorfToBases(self::potenPrsBase($stems[0], $stems[1], $stems[8], $def, true), 'hes');
            case 149: // 110. потенциал, презенс, 1 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases(self::potenPrsBase($stems[0], $stems[1], $stems[8], $def, true), KarGram::garmVowel($stems[1], 'mmokseh')): '';
            case 150: // 111. потенциал, презенс, 2 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases(self::potenPrsBase($stems[0], $stems[1], $stems[8], $def, true), KarGram::garmVowel($stems[1], 'ttokseh')): '';
            case 151: // 112. потенциал, презенс, 3 л., мн.ч., пол. 
                return self::potenPrs3Pl($stems[7], true);

            case 152: // 113. потенциал, презенс, 1 л., ед.ч., отр. 
            case 153: // 114. потенциал, презенс, 2 л., ед.ч., отр. 
            case 155: // 116. потенциал, презенс, 1 л., мн.ч., отр. 
            case 156: // 117. потенциал, презенс, 2 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), Grammatic::joinMorfToBases(self::potenPrsBase($stems[0], $stems[1], $stems[8], $def, true), 'i')) : '';
            case 154: // 115. потенциал, презенс, 3 л., ед.ч., отр. 
                return Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), Grammatic::joinMorfToBases(self::potenPrsBase($stems[0], $stems[1], $stems[8], $def, true), 'i'));
            case 157: // 118. потенциал, презенс, 3 л., мн.ч., отр. 
                return Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), self::potenPrs3Pl($stems[7], true));
                
            case 170: // 131. I инфинитив 
                return $stems[0];
                
            case 179: // 141. актив, 2-е причастие 
                return self::partic2active($stems[0], $stems[1], $stems[8], false, true);
            case 181: // 144. пассив, 2-е причастие 
                return self::partic2passive($stems[7], true);
        }
        return '';
    }

    
    public static function auxVerb($gramset_id, $dialect_id, $negative=NULL, $is_reflexive=false) {
        $lang_id=5;
        if (in_array($gramset_id,[86])) { // Perf1Sg
            $aux = 'olen';
        } elseif (in_array($gramset_id,[87])) { // Perf2Sg
            $aux =  'olet';
        } elseif (in_array($gramset_id,[88])) { // Perf3Sg
            $aux =  'on';
        } elseif (in_array($gramset_id,[89])) { // Perf1Pl
            $aux =  'olemmo';
        } elseif (in_array($gramset_id,[90])) { // Perf2Pl
            $aux = 'oletto';
        } elseif (in_array($gramset_id,[91])) { // Perf3Pl
            $aux =  !$is_reflexive ? 'ollah, on' : 'ollah';
        } elseif (in_array($gramset_id,[92,93,94,95,96])) { // PerfNeg without Perf3PlNeg
            $aux = 'ole';
        } elseif (in_array($gramset_id,[97])) { // Perf3PlNeg
            $aux = 'olla';
            
        } elseif (in_array($gramset_id,[98])) { // Plus1Sg
            $aux = 'olin';
        } elseif (in_array($gramset_id,[99])) { // Plus2Sg
            $aux = 'olit';
        } elseif (in_array($gramset_id,[100])) { // Perf3Sg
            $aux = 'oli';
        } elseif (in_array($gramset_id,[101])) { // Plus1Pl
            $aux = 'olimmo';
        } elseif (in_array($gramset_id,[102])) { // Plus2Pl
            $aux = 'olitto';
        } elseif (in_array($gramset_id,[103])) { // Plus3Pl
            $aux = !$is_reflexive ? 'oldih, oli' : 'oldih';
        } elseif (in_array($gramset_id,[104,105,107,108,106])) { // PlusNeg without PlusSgNeg
            $aux = 'olluh';
        } elseif (in_array($gramset_id,[109])) { // PlusSgNeg
            $aux = 'oldu';
            
        } elseif (in_array($gramset_id,[122])) { // CondPerf1Sg
            $aux = 'olizin';
        } elseif (in_array($gramset_id,[123])) { // CondPerf2Sg
            $aux = 'olizit';
        } elseif (in_array($gramset_id,[124,129,130,131,132,133])) { // CondPerf3Sg, CondPerfNeg wuthout CondPerf3PlNeg
            $aux = 'olis';
        } elseif (in_array($gramset_id,[126])) { // CondPerf1Pl
            $aux = 'olizimmo';
        } elseif (in_array($gramset_id,[127])) { // CondPerf2Pl
            $aux = 'olizitto';
        } elseif (in_array($gramset_id,[128,134])) { // CondPerf3Pl, CondPerf3PlNeg
            $aux = 'oldas';
            
        } elseif (in_array($gramset_id,[135])) { // CondPlus1Sg
            $aux = 'olluzin';
        } elseif (in_array($gramset_id,[125])) { // CondPlus2Sg
            $aux = 'olluzit';
        } elseif (in_array($gramset_id,[136,140,141,142,143,144])) { // CondPerf3Sg, CondPlusNeg without CondPlusSgNeg
            $aux = 'ollus';
        } elseif (in_array($gramset_id,[137])) { // CondPlus1Pl
            $aux = 'olluzimmo';
        } elseif (in_array($gramset_id,[138])) { // CondPlus2Pl
            $aux = 'olluzitto';
        } elseif (in_array($gramset_id,[139, 145])) { // CondPlus3Pl, CondPlus3PlNeg
            $aux = 'oldanus';
            
        } elseif (in_array($gramset_id,[158])) { // PotPerf1Sg
            $aux = 'ollen';
        } elseif (in_array($gramset_id,[159])) { // PotPerf2Sg
            $aux = 'ollet';
        } elseif (in_array($gramset_id,[160])) { // PotPerf3Sg
            $aux = 'ollou';
        } elseif (in_array($gramset_id,[161])) { // PotPerf1Pl
            $aux = 'ollemmo';
        } elseif (in_array($gramset_id,[162])) { // PotPerf2Pl
            $aux = 'olletto';
        } elseif (in_array($gramset_id,[163])) { // PotPerf3Pl
            $aux = 'oldaneh';
        } elseif (in_array($gramset_id,[164,165,166,167,168])) { // PotPerfNeg without PotPerf3PlNeg
            $aux = 'olle';
        } elseif (in_array($gramset_id,[169])) { // PotPerf3PlNeg
            $aux = 'oldane';
        }
        if (!isset($aux)) {
            return '';
        } elseif ($negative=='-') {
            return Grammatic::interLists(trim(Grammatic::negativeForm($gramset_id, $lang_id)), $aux);
        } 
        return $aux;
    }
    
    public static function IndPrsConSgRef($stem3) {
        if (!$stem3) {
            return '';
        }
        $V="[".KarGram::vowelSet()."]";
        $stem3=preg_replace("/(".$V.")".$V."$/u", "$1", $stem3);
        return Grammatic::joinMorfToBases($stem3, 'i');
    }

    /**
     * Base of active 2 participle, conditional imperfect, potential presence
     * 
     * 1) если п.о.8 заканчивается на СV и в ней два слога ИЛИ заканчивается на n, h, 
     *      то п.о.8 + n
     * 2) если п.о.8 заканчивается на СV и в ней >2 слогов, то п.о.1 + nn
     * 3) если п.о.8 заканчивается на VV, то п.о.8 + nnu / nny 
     * 4) если п.о.8 заканчивается на [lrs], то п.о.8 + [lrs] + u/y
     * 5) если п.о.8 заканчивается на t, то в п.о.8 t > n → + nu / ny
     * 
     * @param string $stem1
     * @param string $stem8
     */
    public static function activeBase($stem1,$stem8) {
        if (!$stem8) {
            return '';
        }
        $C="[".KarGram::consSet()."]";
        $V="[".KarGram::vowelSet()."]";
        $out = [];
        foreach (preg_split("/\//",$stem8) as $base) {
            $base = trim($base);
            if (preg_match("/".$C.$V."$/u", $base) && KarGram::countSyllable($base)==2
                    || preg_match("/[nh]$/u", $base)) {
                $out[] = $base. 'n';
            } elseif (preg_match("/".$C.$V."$/u", $base) && KarGram::countSyllable($base)>2) {
                $out[] = $stem1 ? $stem1. 'nn' : '';
            } elseif (preg_match("/".$V.$V."$/u", $base)) {
                $out[] = $base. 'nn';
            } elseif (preg_match("/([lrs])$/u", $base, $regs)) {
                $out[] = $base. $regs[1];
            } elseif (preg_match("/^(.+)t$/u", $base, $regs)) {
                $out[] = $regs[1]. 'nn';
            }
        }
        return join('/',$out);                
    }

    /**
     * Base of active 2 participle, conditional imperfect, potential presence
     * FOR IMPERSONAL VERBS
     * 
     * 1) если с.ф. заканчивается на VV и в ней 2 слога, то:
     * - с.ф. – конечные ua + an
     * - с.ф. – конечные iä + än
     * - с.ф. – конечные uo + un
     * - с.ф. – конечные yö + yn
     * 2) если с.ф. заканчивается на VV и в ней >2 слогов, то:
     * - с.ф. – конечные ua + ann
     * - с.ф. – конечные iä + änn
     * - с.ф. – конечные [uy][öo] + [uy]nn
     * При этом в с.ф tt > t.
     * 3) если с.ф. заканчивается на СV, то:
     * - с.ф. – конечный l[lt][aä] + ll
     * - с.ф. – конечный [tj][aä] + nn
     * 
     * @param string $stem1
     * @param string $stem8
     */
    public static function activeBaseDef($stem0, $is_reflexive=false) {
        if (!$stem0) {
            return '';
        }
        if ($is_reflexive) {
            $stem0 = preg_replace('/kseh$/', '', $stem0);
        }
//dd($stem0);        
        $C="[".KarGram::consSet()."]";
        $V="[".KarGram::vowelSet()."]";
        if (preg_match("/^(.+)(".$V.$V.")$/u", $stem0, $regs)) {
            if (KarGram::countSyllable($stem0)==2) {
                $n = 'n';
            } else {
                $n = 'nn';
                $regs[1] = preg_replace("/tt$/", 't', $regs[1]);
            }
            if ($regs[2]=='ua') {
                return $regs[1].'a'.$n;
            } elseif ($regs[2]=='iä') { 
                return $regs[1].'ä'.$n;
            } elseif ($regs[2]=='uo') { 
                return $regs[1].'u'.$n;
            } elseif ($regs[2]=='yö') { 
                return $regs[1].'y'.$n;
            }
        } elseif (preg_match("/^(.+l)[lt][aä]$/u", $stem0)) {
            return $regs[1].'l';
        } elseif (preg_match("/^(.+)[tj][aä]$/u", $stem0)) {
            return $regs[1].'nn';
        }
    }
    /**
     * 141. актив, 2-е причастие
     * @param string $stem1
     * @param string $stem8
     */
    public static function partic2active($stem0, $stem1, $stem8, $def=false, $is_reflexive=false) {
        $active_base = !$def ? self::activeBase($stem1,$stem8) : self::activeBaseDef($stem0, $is_reflexive);
        $garm_u = !$def ? KarGram::garmVowel($stem1, 'u') : KarGram::garmVowel($stem0, 'u');
        return Grammatic::joinMorfToBases($active_base, $garm_u. (!$is_reflexive ? 'h' : 'hes'));
    }
    
    /**
     * 144. пассив, 2-е причастие
     * 
     * п.о.7 + u / y
     * 
     * @param string $stem
     */
    public static function partic2passive($stem7, $is_reflexive=false) {
        if (!$stem7) {
            return '';
        }
        return $stem7.KarGram::garmVowel($stem7, !$is_reflexive ? 'u' : 'uhes');
    }

    /**
     * Imperative base
     * 1) если п.о.8=%СV ИЛИ =%VV и в ней >2 слогов, то п.о.8 + kk
     * 2) если п.о.8=%VV и в ней <3 слогов ИЛИ =%l и в п.о.8 один слог ИЛИ =%n, то п.о.8 + g 
     * 3) если п.о.8=%l и в п.о.8 >1 слогов ИЛИ =%[rsh] то п.о.8 + k
     * 4) если п.о.8=%t, то в п.о.8 t > k + k
     * 
     * @param string $stem8
     */
    public static function impBaseSg($stem8) {
        if (!$stem8) {
            return '';
        }
        $C="[".KarGram::consSet()."]";
        $V="[".KarGram::vowelSet()."]";
        $out = [];
        foreach (preg_split("/\//",$stem8) as $base) {
            if (preg_match("/".$C.$V."$/u", $base) ||
                    preg_match("/".$V.$V."$/u", $base) && KarGram::countSyllable($base)>2) {
                $out[] = $base. 'kk';
            } elseif (preg_match("/".$V.$V."$/u", $base) && KarGram::countSyllable($base)<3
                    || preg_match("/l$/u", $base) && KarGram::countSyllable($base)==1 
                    || preg_match("/n$/u", $base)) {
                $out[] = $base. 'g';
            } elseif (preg_match("/l$/u", $base) && KarGram::countSyllable($base)>1 
                    || preg_match("/[rsh]$/u", $base)) {
                $out[] = $base. 'k';
            } elseif (preg_match("/^(.+)t$/u", $base, $regs)) {
                $out[] = $regs[1]. 'kk';
            }
        }
        return join('/',$out);        
    }

    public static function impBasePl($stem8) {
        if (!$stem8) {
            return '';
        }
        $out = [];
        foreach (preg_split("/\//",self::impBaseSg($stem8)) as $base) {
            if (KarGram::isBackVowels($base)) {
                $out[] = $base.'ua';
            } else {
                $out[] = $base.'iä';
            }
        }
        return join('/',$out);        
    }    

    /**
     * Conditional imperfect base
     * 
     * @param string $stem1
     * @param string $stem8
     */
    public static function condImpBase($stem0, $stem1,$stem8, $def=false, $is_reflexive=false) {
        $active_base = !$def ? self::activeBase($stem1,$stem8) : self::activeBaseDef($stem0, $is_reflexive);
        $garm_u = !$def ? KarGram::garmVowel($stem8, 'u') : KarGram::garmVowel($stem0, 'u');
        return Grammatic::joinMorfToBases($active_base, $garm_u. (!$is_reflexive ? '' : 'zi'));  
    }

    public static function condPrsBase($stem3) {
        if (!$stem3) {
            return '';
        }
        $C="[".KarGram::consSet()."]";
        $out = [];
        foreach (preg_split("/\//",$stem3) as $base) {
            $base = trim($base);
            $out[] = preg_replace("/(".$C.")e$/", '$1i', $base);
        }
        return join('/',$out);                
    }

    public static function base3PlFrom7($stem7) {
        if (!$stem7) {
            return '';
        }
        $out = [];
        foreach (preg_split("/\//",$stem7) as $base) {
            if (KarGram::countSyllable($base)==2) {
                $out[] = preg_replace("/tt$/", 't', $base). KarGram::garmVowel($base,'ann');
            } else {
                $out[] = $base. KarGram::garmVowel($base,'an');                    
            }
        }
        return join('/',$out);                
    }

    public static function condImp3Pl($stem7, $is_reflexive=false) {
        return Grammatic::joinMorfToBases(self::base3PlFrom7($stem7), KarGram::garmVowel($stem7,'u'.(!$is_reflexive ? 's' : 'zihes')));                
    }

    public static function potenPrsBase($stem0, $stem1, $stem8, $def=false, $is_reflexive=false) {
        $active_base = !$def ? self::activeBase($stem1,$stem8) : self::activeBaseDef($stem0, $is_reflexive);
        return Grammatic::joinMorfToBases($active_base, 'e');                
    }
    
    public static function potenPrs3Sg($stem0, $stem1,  $stem8, $def=false, $is_reflexive=false) {
        $active_base = !$def ? self::activeBase($stem1,$stem8) : self::activeBaseDef($stem0, $is_reflexive);
        return Grammatic::joinMorfToBases($active_base, KarGram::garmVowel($stem8, 'ou'));
    }

    public static function potenPrs3Pl($stem7, $is_reflexive=false) {
        return Grammatic::joinMorfToBases(self::base3PlFrom7($stem7), !$is_reflexive ? 'eh' : 'ehes');                
    }

    public static function potenPrs3PlNeg($stem7, $is_reflexive=false) {
        return Grammatic::joinMorfToBases(self::base3PlFrom7($stem7), 'e');                
    }
    
    /**
     * Infinitive II base
     * 
     * A. если с.ф. заканчивается на ha/hä, то h[aä] > je
     * Б. если с.ф. заканчивается на Сa/Cä (кроме ha/hä), то конечные a/ä > e 
     * В. если с.ф. заканчивается на VV, то = п.о.3+ je,
     *       при этом в п.о.3 конечный e > i
     * 
     * @param type $stem0
     * @param type $stem3
     * @return string
     */
    public static function inf2Base($stem0, $stem3) {
        if (!$stem0) {
            return '';
        }
        $C="[".KarGram::consSet()."]";
        $V="[".KarGram::vowelSet()."]";
        $out = [];
        foreach (preg_split("/\//",$stem0) as $base) {
            $base = trim($base);
            if (preg_match("/^(.+)h[aä]$/", $base, $regs)) {
                $out[] = $regs[1].'je';
            } elseif (preg_match("/^(.*".$C.")[aä]$/", $base, $regs)) {
                $out[] = $regs[1].'e';
            } elseif (preg_match("/".$V.$V."$/", $base)) {
                foreach (preg_split("/\//",$stem3) as $base3) {
                    $out[] = preg_replace("/e$/", 'i', $base3).'je';
                }
            }
        }
        return join('/',$out);                
    }

    public static function partic1active($stem3) {
        if (!$stem3) {
            return '';
        }
        $C="[".KarGram::consSet()."]";
        $V="[".KarGram::vowelSet()."]";
        $out = [];
        foreach (preg_split("/\//",$stem3) as $base) {
            $base = trim($base);
            if (preg_match("/^(.*".$C.")e$/", $base, $regs)) {
                $out[] = $regs[1].'ii, '.$regs[1].KarGram::garmVowel($base,'iju');
            } elseif (preg_match("/".$V.$V."$/", $base)) {
                $out[] = $base.KarGram::garmVowel($base,'ju');
            } else {
                $out[] = $base.'i, '. $base.KarGram::garmVowel($base,'ju');
            }
        }
        return join(', ',$out);                
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
       
}