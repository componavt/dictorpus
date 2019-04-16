<?php

namespace App\Library\Grammatic;

use App\Library\Grammatic\KarGram;

use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;

class KarVerb
{
    public static function getListForAutoComplete() {
        return [26,  27,  28,  29,  30,  31,  70,  71,  72,  73,  78,  79, 
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
    
    public static function wordformByStems($stems, $gramset_id, $lang_id, $dialect_id) {
        $stem4_modify = self::stemModify($stems[4], $stems[0], "ua|iä", ['o'=>'a', 'i'=> ['a','ä']]);
        switch ($gramset_id) {
            case 26: // 1. индикатив, презенс, 1 л., ед.ч., пол. 
                return $stems[1].'n';
            case 27: // 2. индикатив, презенс, 2 л., ед.ч., пол. 
                return $stems[1].'t';
            case 28: // 3. индикатив, презенс, 3 л., ед.ч., пол. 
                return self::indPres1SingByStem($stems[2]);
            case 29: // 4. индикатив, презенс, 1 л., мн.ч., пол. 
                return $stems[1] . 'mm'. KarGram::garmVowel($stems[1],'a');
            case 30: // 5. индикатив, презенс, 2 л., мн.ч., пол. 
                return $stems[1] . 'tt'. KarGram::garmVowel($stems[1],'a');
            case 31: // 6. индикатив, презенс, 3 л., мн.ч., пол. 
                return $stems[6].'h';

            case 70: // 7. индикатив, презенс, 1 л., ед.ч., отриц. 
                return Grammatic::negativeForm(70, $lang_id). $stems[1];
            case 71: // 8. индикатив, презенс, 2 л., ед.ч., отриц. 
                return Grammatic::negativeForm(71, $lang_id). $stems[1];
            case 72: // 9. индикатив, презенс, 3 л., ед.ч., отриц. 
                return Grammatic::negativeForm(72, $lang_id). $stems[1];
            case 73: //10. индикатив, презенс, 1 л., мн.ч., отриц. 
                return Grammatic::negativeForm(73, $lang_id). $stems[1];
            case 78: // 11. индикатив, презенс, 2 л., мн.ч., отриц. 
                return Grammatic::negativeForm(78, $lang_id). $stems[1];
            case 79: // 12. индикатив, презенс, 3 л., мн.ч., отриц. 
                return Grammatic::negativeForm(79, $lang_id). $stems[6];

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
                return Grammatic::negativeForm(80, $lang_id). self::perfectForm($stems[5], $lang_id);
            case 81: // 20. индикатив, имперфект, 2 л., ед.ч., отриц. 
                return Grammatic::negativeForm(81, $lang_id). self::perfectForm($stems[5], $lang_id);
            case 82: // 21. индикатив, имперфект, 3 л., ед.ч., отриц. 
                return Grammatic::negativeForm(82, $lang_id). self::perfectForm($stems[5], $lang_id);
            case 83: // 22. индикатив, имперфект, 1 л., мн.ч., отриц. 
                return Grammatic::negativeForm(83, $lang_id). self::perfectForm($stems[5], $lang_id);
            case 84: // 23. индикатив, имперфект, 2 л., мн.ч., отриц. 
                return Grammatic::negativeForm(84, $lang_id). self::perfectForm($stems[5], $lang_id);
            case 85: // 24. индикатив, имперфект, 3 л., мн.ч., отриц. 
                return Grammatic::negativeForm(85, $lang_id). $stems[7]. KarGram::garmVowel($stems[7],'u');

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
                return self::auxForm(91, $lang_id, $dialect_id). $stems[7]. KarGram::garmVowel($stems[7],'u');

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
                       $stems[7]. KarGram::garmVowel($stems[7],'u');

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
                return self::auxForm(103, $lang_id, $dialect_id). $stems[7]. KarGram::garmVowel($stems[7],'u');

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
                return 'ei oldu '. $stems[7]. KarGram::garmVowel($stems[7],'u'); //self::auxForm(109, $lang_id, $dialect_id)

            case 51: // 49. императив, 2 л., ед.ч., пол 
                return $stems[1];
            case 52: // 50. императив, 3 л., ед.ч., пол 
            case 55: // 53. императив, 3 л., мн.ч., пол 
                return self::imp3SingPolByStem($stems[5], $stems[0], $dialect_id);
            case 54: // 52. императив, 2 л., мн.ч., пол 
                return self::imp2PlurPolByStem($stems[5], $stems[0], $dialect_id);
            case 50: // 54. императив, 2 л., ед.ч., отр. 
                return Grammatic::negativeForm(50, $lang_id). $stems[1];
            case 74: // 55. императив, 3 л., ед.ч., отр. 
                return Grammatic::negativeForm(74, $lang_id). self::imp3SingPolByStem($stems[5], $stems[0], $dialect_id);
            case 76: // 57. императив, 2 л., мн.ч., отр. 
                return Grammatic::negativeForm(76, $lang_id). self::imp2PlurPolByStem($stems[5], $stems[0], $dialect_id);
            case 77: // 58. императив, 3 л., мн.ч., отр. 
                return Grammatic::negativeForm(77, $lang_id). self::imp3SingPolByStem($stems[5], $stems[0], $dialect_id);

            case 44: // 71. кондиционал, имперфект, 1 л., ед.ч., пол. 
                return $stem4_modify . 'zin';
            case 45: // 72. кондиционал, имперфект, 2 л., ед.ч., пол. 
                return $stem4_modify . 'zit';
            case 46: // 73. кондиционал, имперфект, 3 л., ед.ч., пол. 
                return self::condImp3SingPolByStem($stems[4], $stems[0], $dialect_id);
            case 47: // 74. кондиционал, имперфект, 1 л., мн.ч., пол. 
                return $stem4_modify . 'zim'. KarGram::garmVowel($stems[4],'a');
            case 48: // 75. кондиционал, имперфект, 2 л., мн.ч., пол. 
                return $stem4_modify . 'zij'. KarGram::garmVowel($stems[4],'a');
            case 49: // 76. кондиционал, имперфект, 3 л., мн.ч., пол. 
                return $stems[7]. KarGram::garmVowel($stems[7],'a'). 'is’';

            case 116: // 77. кондиционал, имперфект, 1 л., ед.ч., отр. 
                return Grammatic::negativeForm(116, $lang_id). self::condImp3SingPolByStem($stems[4], $stems[0], $dialect_id);
            case 117: // 78. кондиционал, имперфект, 1 л., ед.ч., отр. 
                return Grammatic::negativeForm(117, $lang_id). self::condImp3SingPolByStem($stems[4], $stems[0], $dialect_id);
            case 118: // 79. кондиционал, имперфект, 1 л., ед.ч., отр. 
                return Grammatic::negativeForm(118, $lang_id). self::condImp3SingPolByStem($stems[4], $stems[0], $dialect_id);
            case 119: // 80. кондиционал, имперфект, 1 л., ед.ч., отр. 
                return Grammatic::negativeForm(119, $lang_id). self::condImp3SingPolByStem($stems[4], $stems[0], $dialect_id);
            case 120: // 81. кондиционал, имперфект, 1 л., ед.ч., отр. 
                return Grammatic::negativeForm(120, $lang_id). self::condImp3SingPolByStem($stems[4], $stems[0], $dialect_id);
            case 121: // 82. кондиционал, имперфект, 1 л., ед.ч., отр. 
                return Grammatic::negativeForm(121, $lang_id). $stems[7]. KarGram::garmVowel($stems[7],'a'). 'is’';
                
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
                return 'olis’ '. $stems[7] . KarGram::garmVowel($stems[7],'u');
                
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
                return 'ei olis’ '. $stems[7] . KarGram::garmVowel($stems[7],'u');
                
            case 146: // 107. потенциал, презенс, 1 л., ед.ч., пол. 
                return self::potencialForm($stems[5], 'en', $lang_id, $dialect_id);
            case 147: // 108. потенциал, презенс, 2 л., ед.ч., пол. 
                return self::potencialForm($stems[5], 'et', $lang_id, $dialect_id);
            case 148: // 109. потенциал, презенс, 3 л., ед.ч., пол. 
                return self::potencialForm($stems[5], KarGram::garmVowel($stems[5], 'ou'), $lang_id, $dialect_id);
            case 149: // 110. потенциал, презенс, 1 л., мн.ч., пол. 
                return self::potencialForm($stems[5], 'emm'. KarGram::garmVowel($stems[5], 'a'), $lang_id, $dialect_id);
            case 150: // 111. потенциал, презенс, 2 л., мн.ч., пол. 
                return self::potencialForm($stems[5], 'ett'.KarGram::garmVowel($stems[5], 'a'), $lang_id, $dialect_id);
            case 151: // 112. потенциал, презенс, 3 л., мн.ч., пол. 
                return $stems[7]. KarGram::garmVowel($stems[7], 'anneh');

            case 152: // 113. потенциал, презенс, 1 л., ед.ч., отр. 
                return Grammatic::negativeForm(152, $lang_id). self::potencialForm($stems[5], 'e', $lang_id, $dialect_id);
            case 153: // 114. потенциал, презенс, 1 л., ед.ч., отр. 
                return Grammatic::negativeForm(153, $lang_id). self::potencialForm($stems[5], 'e', $lang_id, $dialect_id);
            case 154: // 115. потенциал, презенс, 1 л., ед.ч., отр. 
                return Grammatic::negativeForm(154, $lang_id). self::potencialForm($stems[5], 'e', $lang_id, $dialect_id);
            case 155: // 116. потенциал, презенс, 1 л., ед.ч., отр. 
                return Grammatic::negativeForm(155, $lang_id). self::potencialForm($stems[5], 'e', $lang_id, $dialect_id);
            case 156: // 117. потенциал, презенс, 1 л., ед.ч., отр. 
                return Grammatic::negativeForm(156, $lang_id). self::potencialForm($stems[5], 'e', $lang_id, $dialect_id);
            case 157: // 118. потенциал, презенс, 1 л., ед.ч., отр. 
                return Grammatic::negativeForm(157, $lang_id). $stems[7]. KarGram::garmVowel($stems[7], 'anne');
                
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
                return 'lienöy '. $stems[7]. KarGram::garmVowel($stems[7], 'u');
                
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
                return 'ei liene '. $stems[7]. KarGram::garmVowel($stems[7], 'u');

            case 170: // 131. I инфинитив 
                return $stems[0];
            case 171: // 132. II инфинитив, инессив 
                return self::inf2Ines($stems[0]);
            case 172: // 133. II инфинитив, инструктив  
                return self::inf2Inst($stems[0]);
            case 173: // 134. III инфинитив, адессив
                return $stems[2]. KarGram::garmVowel($stems[2], 'malla');
            case 174: // 135. III инфинитив, иллатив 
                return $stems[2]. KarGram::garmVowel($stems[2], 'mah');
            case 175: // 136. III инфинитив, инессив 
                return $stems[2]. KarGram::garmVowel($stems[2], 'mašša');
            case 176: // 137. III инфинитив, элатив 
                return $stems[2]. KarGram::garmVowel($stems[2], 'mašta');
            case 177: // 138. III инфинитив, абессив 
                return $stems[2]. KarGram::garmVowel($stems[2], 'matta');
                
            case 178: // 139. актив, 1-е причастие 
                return KarGram::replaceSingVowel($stems[2], 'e', 'i'). KarGram::garmVowel($stems[7], 'ja');
            case 179: // 140. актив, 2-е причастие 
                return self::partic2active($stems[5], $lang_id);
            case 282: // 141. актив, 2-е причастие  (сокращенная форма); перфект (форма основного глагола)
                return self::perfectForm($stems[5], $lang_id);
            case 180: // 142. пассив, 1-е причастие 
                return $stems[7]. KarGram::garmVowel($stems[7], 'ava');
            case 181: // 143. пассив, 2-е причастие 
                return $stems[7]. KarGram::garmVowel($stems[7], 'u');
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
        $last_let = mb_substr($stem, -1, 1);
        $before_last_let = mb_substr($stem, -2, 1);
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
        $last_let = mb_substr($stem, -1, 1);
        $before_last_let = mb_substr($stem, -2, 1);
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
     * @param String $lemma
     */
    public static function inf2Ines($lemma) {
        $last_let = mb_substr($lemma, -1, 1);
        $before_last_let = mb_substr($lemma, -2, 1);
        
        if (KarGram::isVowel($before_last_let) && KarGram::isVowel($last_let)) {
            return $lemma. (KarGram::isBackVowels($lemma) ? 's’s’a': 'ssä');
        } elseif (KarGram::isConsonant($before_last_let) && preg_match("/^(.+)[aä]$/u", $lemma, $regs)) {
            return $regs[1]. 'ešš'. KarGram::garmVowel($lemma,'a');
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
        
        if (KarGram::isConsonant($before_last_let) && preg_match("/^(.+)[aä]$/u", $lemma, $regs)) {
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
        if (KarGram::isConsonant($before_last_let) && KarGram::isVowel($last_let)) {
            return $stem. 'n';
        } elseif (KarGram::isVowel($before_last_let) && KarGram::isVowel($last_let)) {
            return $stem. KarGram::garmVowel($stem, 'nun');
        } elseif (in_array($last_let, ['n', 'l', 'r', 's', 'š'])) {
            return $stem. $last_let. KarGram::garmVowel($stem, 'un');
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
        if (KarGram::isConsonant($before_last_let) && KarGram::isVowel($last_let)) {
            return $stem. 'nnun';
        } elseif (KarGram::isVowel($before_last_let) && KarGram::isVowel($last_let)) {
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
        if (KarGram::isVowel($last_let)) {
            return $stem. 'nn'.$affix;
        } elseif (in_array($last_let, ['n', 'l', 'r', 's', 'š'])) {
            return $stem. $last_let. $affix;
        }
    }
    
}