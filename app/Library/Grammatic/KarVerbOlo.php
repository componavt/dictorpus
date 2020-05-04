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
                32,   33,  34,  35,  36,  37, 297, 298, 
                80,   81,  82,  83,  84,  85, 
                86,   87,  88,  89,  90,  91,  92,  93,  94,  95,  96,  97,
                98,   99, 100, 101, 102, 103, 104, 105, 107, 108, 106, 109,
                      51,  52,  53,  54,  55,       50,  74,  75,  76,  77,  
                38,   39,  40,  41,  42,  43, 301, 303,
                70,   71,  72,  73,  78,  79,
                44,   45,  46,  47,  48,  49, 116, 117, 118, 119, 120, 121,
                122, 123, 124, 126, 127, 128, 129, 130, 131, 132, 133, 134,
                135, 125, 136, 137, 138, 139, 140, 141, 142, 143, 144, 145,
                146, 147, 148, 149, 150, 151, 310, 311, 
                152, 153, 154, 155, 156, 157,
                158, 159, 160, 161, 162, 163, 164, 165, 166, 167, 168, 169,
                170, 171, 172, 173, 174, 175, 176, 177, 312,
                178, 179, 282, 180, 181];
    }
    
    public static function wordformByStems($stems, $gramset_id, $dialect_id, $def=NULL) {
//        $stem4_modify = self::stemModify($stems[4], $stems[0], "ua|iä", ['o'=>'a', 'i'=> ['a','ä']]);
        $lang_id=5;
        switch ($gramset_id) {
            case 28: // 3. индикатив, презенс, 3 л., ед.ч., пол. 
                return $stems[2];
            case 29: // 4. индикатив, презенс, 1 л., мн.ч., пол. 
                return !$def && $stems[1] ? $stems[1] . 'mm'. KarGram::garmVowel($stems[1],'o') : '';
            case 30: // 5. индикатив, презенс, 2 л., мн.ч., пол. 
                return !$def && $stems[1] ? $stems[1] . 'tt'. KarGram::garmVowel($stems[1],'o') : '';

            case 32: // 13. индикатив, имперфект, 1 л., ед.ч., пол. 
                return !$def && $stems[5] ? $stems[5] . 'n' : '';
            case 33: // 14. индикатив, имперфект, 2 л., ед.ч., пол. 
                return !$def && $stems[5] ? $stems[5] . 't' : '';
            case 34: // 15. индикатив, имперфект, 3 л., ед.ч., пол. 
                return $stems[4] ? $stems[4] : '';
            case 35: // 16. индикатив, имперфект, 1 л., мн.ч., пол. 
                return !$def && $stems[5] ? $stems[5] . 'mm'. KarGram::garmVowel($stems[5],'o') : '';
            case 36: // 17. индикатив, имперфект, 2 л., мн.ч., пол. 
                return !$def && $stems[5] ? $stems[5] . 'tt'. KarGram::garmVowel($stems[5],'o') : '';
            case 297: // 146. индикатив, имперфект, коннегатив, ед.ч.
                return self::partic2active($stems[1], $stems[8]);
            case 298: // 147. индикатив, имперфект, коннегатив, мн.ч.
                return self::partic2passive($stems[7]);

            case 80: // 19. индикатив, имперфект, 1 л., ед.ч., отриц. 
            case 81: // 20. индикатив, имперфект, 2 л., ед.ч., отриц. 
            case 83: // 22. индикатив, имперфект, 1 л., мн.ч., отриц. 
            case 84: // 23. индикатив, имперфект, 2 л., мн.ч., отриц. 
                return !$def && $stems[1] && $stems[8] ? Grammatic::negativeForm($gramset_id, $lang_id). self::partic2active($stems[1], $stems[8]) : '';
            case 82: // 21. индикатив, имперфект, 3 л., ед.ч., отриц. 
                return $stems[1] && $stems[8] ? Grammatic::negativeForm($gramset_id, $lang_id). self::partic2active($stems[1], $stems[8]) : '';
            case 85: // 24. индикатив, имперфект, 3 л., мн.ч., отриц. 
                return $stems[7] ? Grammatic::negativeForm(85, $lang_id). self::partic2passive($stems[7]) : '';

            case 86: // 25. индикатив, перфект, 1 л., ед.ч., пол. 
            case 87: // 26. индикатив, перфект, 2 л., ед.ч., пол. 
            case 89: // 28. индикатив, перфект, 1 л., мн.ч., пол. 
            case 90: // 29. индикатив, перфект, 2 л., мн.ч., пол. 
                return !$def && $stems[5] ? self::auxForm($gramset_id, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id) : '';
            case 88: // 27. индикатив, перфект, 3 л., ед.ч., пол. 
                return $stems[5] ? self::auxForm($gramset_id, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id) : '';
            case 91: // 30. индикатив, перфект, 3 л., мн.ч., пол. 
                return $stems[7] ? self::auxForm(91, $lang_id, $dialect_id). $stems[7]. KarGram::garmVowel($stems[7],'u') : '';

            case 92: // 31. индикатив, перфект, 1 л., ед.ч., отриц. 
            case 93: // 32. индикатив, перфект, 2 л., ед.ч., отриц. 
            case 95: // 34. индикатив, перфект, 1 л., мн.ч., отриц. 
            case 96: // 35. индикатив, перфект, 2 л., мн.ч., отриц. 
                return !$def && $stems[5] ? self::auxForm($gramset_id, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id) : '';
            case 94: // 33. индикатив, перфект, 3 л., ед.ч., отриц. 
                return $stems[5] ? self::auxForm($gramset_id, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id) : '';
            case 97: // 36. индикатив, перфект, 3 л., мн.ч., отриц. 
                return $stems[7] ? 'ei ole './/self::auxForm(97, $lang_id, $dialect_id). 
                       $stems[7]. KarGram::garmVowel($stems[7],'u') : '';

            case 98: // 37. индикатив, плюсквамперфект, 1 л., ед.ч., пол. 
            case 99: // 38. индикатив, плюсквамперфект, 2 л., ед.ч., пол. 
            case 101: // 40. индикатив, плюсквамперфект, 1 л., мн.ч., пол. 
            case 102: // 41. индикатив, плюсквамперфект, 2 л., мн.ч., пол. 
                return !$def && $stems[5] ? self::auxForm($gramset_id, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id) : '';
            case 100: // 39. индикатив, плюсквамперфект, 3 л., ед.ч., пол. 
                return $stems[5] ? self::auxForm($gramset_id, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id) : '';
            case 103: // 42. индикатив, плюсквамперфект, 3 л., мн.ч., пол. 
                return $stems[7] ? self::auxForm(103, $lang_id, $dialect_id). $stems[7]. KarGram::garmVowel($stems[7],'u') : '';

            case 104: // 43. индикатив, плюсквамперфект, 1 л., ед.ч., отриц. 
            case 105: // 44. индикатив, плюсквамперфект, 2 л., ед.ч., отриц. 
            case 107: // 46. индикатив, плюсквамперфект, 1 л., мн.ч., отриц. 
            case 108: // 47. индикатив, плюсквамперфект, 2 л., мн.ч., отриц. 
                return !$def && $stems[5] ? self::auxForm($gramset_id, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id) : '';
            case 106: // 45. индикатив, плюсквамперфект, 3 л., ед.ч., отриц. 
                return $stems[5] ? self::auxForm($gramset_id, $lang_id, $dialect_id). self::perfectForm($stems[5], $lang_id) : '';
            case 109: // 48. индикатив, плюсквамперфект, 3 л., мн.ч., отриц. 
                return $stems[7] ? 'ei oldu '. $stems[7]. KarGram::garmVowel($stems[7],'u') : ''; //self::auxForm(109, $lang_id, $dialect_id)

            case 51: // 49. императив, 2 л., ед.ч., пол 
                return !$def && $stems[1] ? $stems[1] : '';
            case 54: // 52. императив, 2 л., мн.ч., пол 
                return !$def && $stems[5] ? self::imp2PlurPolByStem($stems[5], $stems[0], $dialect_id) : '';
            case 52: // 50. императив, 3 л., ед.ч., пол 
            case 55: // 53. императив, 3 л., мн.ч., пол 
                return $stems[5] ? self::imp3SingPolByStem($stems[5], $stems[0], $dialect_id) : '';

            case 50: // 54. императив, 2 л., ед.ч., отр. 
                return !$def && $stems[1] ? Grammatic::negativeForm(50, $lang_id). $stems[1] : '';
            case 74: // 55. императив, 3 л., ед.ч., отр. 
                return $stems[5] ? Grammatic::negativeForm(74, $lang_id). self::imp3SingPolByStem($stems[5], $stems[0], $dialect_id) : '';
            case 76: // 57. императив, 2 л., мн.ч., отр. 
                return !$def && $stems[5] ? Grammatic::negativeForm(76, $lang_id). self::imp2PlurPolByStem($stems[5], $stems[0], $dialect_id) : '';
            case 77: // 58. императив, 3 л., мн.ч., отр. 
                return $stems[5] ? Grammatic::negativeForm(77, $lang_id). self::imp3SingPolByStem($stems[5], $stems[0], $dialect_id) : '';

            case 44: // 71. кондиционал, имперфект, 1 л., ед.ч., пол. 
                return !$def && $stems[4] ? $stem4_modify . 'zin' : '';
            case 45: // 72. кондиционал, имперфект, 2 л., ед.ч., пол. 
                return !$def && $stems[4] ? $stem4_modify . 'zit' : '';
            case 46: // 73. кондиционал, имперфект, 3 л., ед.ч., пол. 
                return $stems[4] ? self::condImp3SingPolByStem($stems[4], $stems[0], $dialect_id) : '';
            case 47: // 74. кондиционал, имперфект, 1 л., мн.ч., пол. 
                return !$def && $stems[4] ? $stem4_modify . 'zim'. KarGram::garmVowel($stems[4],'a') : '';
            case 48: // 75. кондиционал, имперфект, 2 л., мн.ч., пол. 
                return !$def && $stems[4] ? $stem4_modify . 'zij'. KarGram::garmVowel($stems[4],'a') : '';
            case 49: // 76. кондиционал, имперфект, 3 л., мн.ч., пол. 
                return $stems[7] ? $stems[7]. KarGram::garmVowel($stems[7],'a'). 'is’' : '';

            case 116: // 77. кондиционал, имперфект, 1 л., ед.ч., отр. 
            case 117: // 78. кондиционал, имперфект, 2 л., ед.ч., отр. 
            case 119: // 80. кондиционал, имперфект, 1 л., мн.ч., отр. 
            case 120: // 81. кондиционал, имперфект, 2 л., мн.ч., отр. 
                return !$def && $stems[4] ? Grammatic::negativeForm($gramset_id, $lang_id). self::condImp3SingPolByStem($stems[4], $stems[0], $dialect_id) : '';
            case 118: // 79. кондиционал, имперфект, 3 л., ед.ч., отр. 
                return $stems[4] ? Grammatic::negativeForm($gramset_id, $lang_id). self::condImp3SingPolByStem($stems[4], $stems[0], $dialect_id) : '';
            case 121: // 82. кондиционал, имперфект, 3 л., мн.ч., отр. 
                return $stems[7] ? Grammatic::negativeForm(121, $lang_id). $stems[7]. KarGram::garmVowel($stems[7],'a'). 'is’' : '';
                
            case 135: // 95. кондиционал, плюсквамперфект, 1 л., ед.ч., пол. 
                return !$def && $stems[5] ? 'olizin '. self::perfectForm($stems[5], $lang_id) : '';
            case 125: // 96. кондиционал, плюсквамперфект, 2 л., ед.ч., пол. 
                return !$def && $stems[5] ? 'olizit '. self::perfectForm($stems[5], $lang_id) : '';
            case 136: // 97. кондиционал, плюсквамперфект, 3 л., ед.ч., пол. 
                return $stems[5] ? 'olis’ '. self::perfectForm($stems[5], $lang_id) : '';
            case 137: // 98. кондиционал, плюсквамперфект, 1 л., мн.ч., пол. 
                return !$def && $stems[5] ? 'olizima '. self::perfectForm($stems[5], $lang_id) : '';
            case 138: // 99. кондиционал, плюсквамперфект, 2 л., мн.ч., пол. 
                return !$def && $stems[5] ? 'olizija '. self::perfectForm($stems[5], $lang_id) : '';
            case 139: // 100. кондиционал, плюсквамперфект, 3 л., мн.ч., пол. 
                return $stems[7] ? 'olis’ '. $stems[7] . KarGram::garmVowel($stems[7],'u') : '';
                
            case 140: // 101. кондиционал, плюсквамперфект, 1 л., ед.ч., отр. 
                return !$def && $stems[5] ? 'en olis’ '. self::perfectForm($stems[5], $lang_id) : '';
            case 141: // 102. кондиционал, плюсквамперфект, 2 л., ед.ч., отр. 
                return !$def && $stems[5] ? 'et olis’ '. self::perfectForm($stems[5], $lang_id) : '';
            case 142: // 103. кондиционал, плюсквамперфект, 3 л., ед.ч., отр. 
                return $stems[5] ? 'ei olis’ '. self::perfectForm($stems[5], $lang_id) : '';
            case 143: // 104. кондиционал, плюсквамперфект, 1 л., мн.ч., отр. 
                return !$def && $stems[5] ? 'emmä olis’ '. self::perfectForm($stems[5], $lang_id) : '';
            case 144: // 105. кондиционал, плюсквамперфект, 2 л., мн.ч., отр. 
                return !$def && $stems[5] ? 'että olis’ '. self::perfectForm($stems[5], $lang_id) : '';
            case 145: // 106. кондиционал, плюсквамперфект, 3 л., мн.ч., отр. 
                return $stems[7] ? 'ei olis’ '. $stems[7] . KarGram::garmVowel($stems[7],'u') : '';
                
            case 146: // 107. потенциал, презенс, 1 л., ед.ч., пол. 
                return !$def && $stems[5] ? self::potencialForm($stems[5], 'en', $lang_id, $dialect_id) : '';
            case 147: // 108. потенциал, презенс, 2 л., ед.ч., пол. 
                return !$def && $stems[5] ? self::potencialForm($stems[5], 'et', $lang_id, $dialect_id) : '';
            case 148: // 109. потенциал, презенс, 3 л., ед.ч., пол. 
                return $stems[5] ? self::potencialForm($stems[5], KarGram::garmVowel($stems[5], 'ou'), $lang_id, $dialect_id) : '';
            case 149: // 110. потенциал, презенс, 1 л., мн.ч., пол. 
                return !$def && $stems[5] ? self::potencialForm($stems[5], 'emm'. KarGram::garmVowel($stems[5], 'a'), $lang_id, $dialect_id) : '';
            case 150: // 111. потенциал, презенс, 2 л., мн.ч., пол. 
                return !$def && $stems[5] ? self::potencialForm($stems[5], 'ett'.KarGram::garmVowel($stems[5], 'a'), $lang_id, $dialect_id) : '';
            case 151: // 112. потенциал, презенс, 3 л., мн.ч., пол. 
                return $stems[7] ? $stems[7]. KarGram::garmVowel($stems[7], 'anneh') : '';
            case 310: // 158. потенциал, презенс, коннегатив 
                return $stems[5] ? self::potencialForm($stems[5], 'e', $lang_id, $dialect_id) : '';
            case 311: // 159. потенциал, презенс, коннегатив, 3 л. мн.ч.
                return $stems[7] ? $stems[7]. KarGram::garmVowel($stems[7], 'anne') : '';

            case 152: // 113. потенциал, презенс, 1 л., ед.ч., отр. 
            case 153: // 114. потенциал, презенс, 2 л., ед.ч., отр. 
            case 155: // 116. потенциал, презенс, 1 л., мн.ч., отр. 
            case 156: // 117. потенциал, презенс, 2 л., мн.ч., отр. 
                return !$def && $stems[5] ? Grammatic::negativeForm($gramset_id, $lang_id). self::potencialForm($stems[5], 'e', $lang_id, $dialect_id) : '';
            case 154: // 115. потенциал, презенс, 3 л., ед.ч., отр. 
                return $stems[5] ? Grammatic::negativeForm($gramset_id, $lang_id). self::potencialForm($stems[5], 'e', $lang_id, $dialect_id) : '';
            case 157: // 118. потенциал, презенс, 3 л., мн.ч., отр. 
                return $stems[7] ? Grammatic::negativeForm(157, $lang_id). $stems[7]. KarGram::garmVowel($stems[7], 'anne') : '';
                
            case 158: // 119. потенциал, перфект, 1 л., ед.ч., пол. 
                return !$def && $stems[5] ? 'lienen '. self::perfectForm($stems[5], $lang_id) : '';
            case 159: // 120. потенциал, перфект, 2 л., ед.ч., пол. 
                return !$def && $stems[5] ? 'lienet '. self::perfectForm($stems[5], $lang_id) : '';
            case 160: // 121. потенциал, перфект, 3 л., ед.ч., пол. 
                return $stems[5] ? 'lienöy '. self::perfectForm($stems[5], $lang_id) : '';
            case 161: // 122. потенциал, перфект, 1 л., мн.ч., пол. 
                return !$def && $stems[5] ? 'lienemmä '. self::perfectForm($stems[5], $lang_id) : '';
            case 162: // 123. потенциал, перфект, 2 л., мн.ч., пол. 
                return !$def && $stems[5] ? 'lienettä '. self::perfectForm($stems[5], $lang_id) : '';
            case 163: // 124. потенциал, перфект, 3 л., мн.ч., пол. 
                return $stems[7] ? 'lienöy '. $stems[7]. KarGram::garmVowel($stems[7], 'u') : '';
                
            case 164: // 125. потенциал, перфект, 1 л., ед.ч., отр. 
                return !$def && $stems[5] ? 'en liene '. self::perfectForm($stems[5], $lang_id) : '';
            case 165: // 126. потенциал, перфект, 2 л., ед.ч., отр. 
                return !$def && $stems[5] ? 'et liene '. self::perfectForm($stems[5], $lang_id) : '';
            case 166: // 127. потенциал, перфект, 3 л., ед.ч., отр. 
                return $stems[5] ? 'ei liene '. self::perfectForm($stems[5], $lang_id) : '';
            case 167: // 128. потенциал, перфект, 1 л., мн.ч., отр. 
                return !$def && $stems[5] ? 'emmä liene '. self::perfectForm($stems[5], $lang_id) : '';
            case 168: // 129. потенциал, перфект, 2 л., мн.ч., отр. 
                return !$def && $stems[5] ? 'että liene '. self::perfectForm($stems[5], $lang_id) : '';
            case 169: // 130. потенциал, перфект, 3 л., мн.ч., отр. 
                return $stems[7] ? 'ei liene '. $stems[7]. KarGram::garmVowel($stems[7], 'u') : '';

            case 170: // 131. I инфинитив 
                return $stems[0];
            case 171: // 132. II инфинитив, инессив 
                return self::inf2Ines($stems[0]);
            case 172: // 133. II инфинитив, инструктив  
                return self::inf2Inst($stems[0]);
            case 173: // 134. III инфинитив, адессив
                return $stems[2] ? $stems[2]. KarGram::garmVowel($stems[2], 'malla') : '';
            case 174: // 135. III инфинитив, иллатив 
                return $stems[2] ? $stems[2]. KarGram::garmVowel($stems[2], 'mah') : '';
            case 175: // 136. III инфинитив, инессив 
                return $stems[2] ? $stems[2]. KarGram::garmVowel($stems[2], 'mašša') : '';
            case 176: // 137. III инфинитив, элатив 
                return $stems[2] ? $stems[2]. KarGram::garmVowel($stems[2], 'mašta') : '';
            case 177: // 138. III инфинитив, абессив 
                return $stems[2] ? $stems[2]. KarGram::garmVowel($stems[2], 'matta') : '';
                
            case 178: // 139. актив, 1-е причастие 
                return $stems[2] ? KarGram::replaceSingVowel($stems[2], 'e', 'i'). KarGram::garmVowel($stems[7], 'ja') : '';
            case 179: // 140. актив, 2-е причастие 
                return $stems[5] ? self::partic2active($stems[5], $lang_id) : '';
            case 282: // 141. актив, 2-е причастие  (сокращенная форма); перфект (форма основного глагола)
                return $stems[5] ? self::perfectForm($stems[5], $lang_id) : '';
            case 180: // 142. пассив, 1-е причастие 
                return $stems[7] ? $stems[7]. KarGram::garmVowel($stems[7], 'ava') : '';
            case 181: // 143. пассив, 2-е причастие 
                return $stems[7] ? $stems[7]. KarGram::garmVowel($stems[7], 'u') : '';
        }
        return '';
    }

    /**
     * 141. актив, 2-е причастие
     * 
     * 1) если п.о.8 заканчивается на СV и в ней два слога, то п.о.8 + nuh / nyh
     * 2) если п.о.8 заканчивается на СV и в ней три или больше слогов, то п.о.8 > п.о.1 + nnuh / nnyh
     * 3) если п.о.8 заканчивается на VV, то п.о.8 + nnuh / nnyh
     * 4) если п.о.8 заканчивается на l, то п.о.8 + luh / lyh
     * 5) если п.о.8 заканчивается на n, h, то п.о.8 + nuh / nyh
     * 6) если п.о.8 заканчивается на r, то п.о.8 + ruh / ryh
     * 7) если п.о.8 заканчивается на s, то п.о.8 + suh / syh
     * 8) если п.о.8 заканчивается на t, то в п.о.8 t > n → + nuh / nyh
     * 
     * @param string $stem
     */
    public static function partic2active($stem1, $stem8) {
        if (!$stem8) {
            return '';
        }
        $C="[".KarGram::consSet()."]";
        $V="[".KarGram::vowelSet()."]";
        $garm_u1 = KarGram::garmVowel($stem1,'u');
        $garm_u8 = KarGram::garmVowel($stem8,'u');
        
        if (preg_match("/".$C.$V."$/u", $stem8) && KarGram::countSyllable($stem8)==2) {
            return $stem8. 'n'.$garm_u8.'h';
        } elseif (preg_match("/".$C.$V."$/u", $stem8) && KarGram::countSyllable($stem8)>2) {
            if (!$stem1) {
                return '';
            } else {
                return $stem1. 'nn'.$garm_u1.'h';
            }
        } elseif (preg_match("/".$V.$V."$/u", $stem8)) {
            return $stem8. 'nn'.$garm_u8.'h';
        } elseif (preg_match("/([lnrs])$/u", $stem8, $regs)) {
            return $stem8. $regs[1]. $garm_u8.'h';
        } elseif (preg_match("/h$/u", $stem8)) {
            return $stem8. 'n'.$garm_u8.'h';
        } elseif (preg_match("/^(.+)t$/u", $stem8, $regs)) {
            return $regs[1]. 'nn'.$garm_u8.'h';
        }
    }
    
    /**
     * 144. пассив, 2-е причастие
     * 
     * п.о.7 + u / y
     * 
     * @param string $stem
     */
    public static function partic2passive($stem7) {
        if (!$stem7) {
            return '';
        }
        return $stem7.KarGram::garmVowel($stem8,'u');
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
//        if (mb_substr($stem, -1, 1) == 'e' && KarGram::isConsonant(mb_substr($stem, -2, 1))) {
        $is_backV = KarGram::isBackVowels($stem);
        if (preg_match("/^(.+)(.)e$/u", $stem, $regs) && KarGram::isConsonant($regs[2])) {
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
        if (!KarGram::isVowel($last_let)) {
            return '';
        }
        $stem_a = (KarGram::isBackVowels($stem) ? 'a': 'ä');
        $before_last_let = mb_substr($stem, -2, 1);
        if (KarGram::isConsonant($before_last_let)) {
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
        if (!KarGram::isVowel($last_let)) {
            return '';
        }
        $stem_a = (KarGram::isBackVowels($stem) ? 'a': 'ä');
        $before_last_let = mb_substr($stem, -2, 1);
        if (KarGram::isConsonant($before_last_let)) {
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
        $stem_for_search = Grammatic::toSearchForm($stem);
        $last_let = mb_substr($stem_for_search, -1, 1);
        $before_last_let = mb_substr($stem_for_search, -2, 1);
        
        $stem_a = (KarGram::isBackVowels($stem) ? 'a': 'ä');

        if (KarGram::isConsonant($before_last_let) && KarGram::isVowel($last_let)) {
            return $stem. 'kk'. $stem_a. 'h';
        } elseif ($last_let=='n' && preg_match("/t[aä]$/u", $lemma)) {
//            return preg_replace("/^(.+)(n)$/u", "\1k", $stem). 'k'. $stem_a. 'h';
            return mb_substr($stem, 0, -1). 'kk'. $stem_a. 'h';
        } elseif (KarGram::isVowel($before_last_let) && KarGram::isVowel($last_let) 
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
        $stem_for_search = Grammatic::toSearchForm($stem);
        $last_let = mb_substr($stem_for_search, -1, 1);
        $before_last_let = mb_substr($stem_for_search, -2, 1);
        $stem_ua = (KarGram::isBackVowels($stem) ? 'ua': 'iä');

        if (KarGram::isConsonant($before_last_let) && KarGram::isVowel($last_let)) {
            return $stem. 'kk'. $stem_ua;
        } elseif (in_array($last_let, ['s', 'š'])) {
            return $stem. 'k'. $stem_ua;
        } elseif ($last_let=='n' && preg_match("/t[aä]$/u", $lemma)) {
            return mb_substr($stem, 0, -1). 'kk'. $stem_ua;
        } elseif (KarGram::isVowel($before_last_let) && KarGram::isVowel($last_let)
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
                return $regs[1]. KarGram::garmVowel($lemma,'a'). 'is’';
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
     * @param String $stem
     */
    public static function inf2Ines($stem) {
        $stem_for_search = Grammatic::toSearchForm($stem);
        $last_let = mb_substr($stem_for_search, -1, 1);
        $before_last_let = mb_substr($stem_for_search, -2, 1);
        
        if (KarGram::isVowel($before_last_let) && KarGram::isVowel($last_let)) {
            return $stem. (KarGram::isBackVowels($stem) ? 's’s’a': 'ssä');
        } elseif (KarGram::isConsonant($before_last_let) && preg_match("/^(.+)[aä]$/u", $stem, $regs)) {
            return $regs[1]. 'ešš'. KarGram::garmVowel($stem,'a');
        }
        return $stem;
    }
    
    /**
     * 133. II инфинитив, инструктив  
     * начальная форма + n (при этом, если начальная форма заканчивается на согласный + a / ä: Ca / Cä, то a / ä переходит в e: a > e, ä > e)
     * 
     * @param String $stem
     */
    public static function inf2Inst($stem) {
        $stem_for_search = Grammatic::toSearchForm($stem);
        $before_last_let = mb_substr($stem_for_search, -2, 1);
        
        if (KarGram::isConsonant($before_last_let) && preg_match("/^(.+)[aä]$/u", $stem, $regs)) {
            $stem = $regs[1]. 'e';
        }
        return $stem. 'n';
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
    
    public static function indImperfConnegPl($stem7) {
        if (!$stem7) { return; }
        return $stem7. KarGram::garmVowel($stem7,'u');
    }

    /**
     * 
     * @param String $stem
     * @param String $affix
     * @param Int $lang_id
     * @param Int $dialect_id
     */
    public static function potencialForm($stem, $affix, $lang_id, $dialect_id) {
        $stem_for_search = Grammatic::toSearchForm($stem);
        $last_let = mb_substr($stem_for_search, -1, 1);
        
        if (KarGram::isVowel($last_let)) {
            return $stem. 'nn'.$affix;
        } elseif (in_array($last_let, ['n', 'l', 'r', 's', 'š'])) {
            return $stem. $last_let. $affix;
        }
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
       
}