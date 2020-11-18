<?php

namespace App\Library\Grammatic;

use App\Library\Grammatic;
use App\Library\Grammatic\KarGram;

/**
 * Functions related to Karelian grammatic for verbs.
 */
class KarVerbLud
{
    public static function getStemFromWordform($lemma, $stem_n, $dialect_id) {
        return '';
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
        switch ($gramset_id) {
            case 170: // 131. I инфинитив 
                return $stems[0];
        }
    }

    
    public static function auxVerb($gramset_id, $dialect_id, $negative=NULL, $is_reflexive=false) {
        if (in_array($gramset_id,[86])) { // Perf1Sg
            $aux = 'olen';
        } elseif (in_array($gramset_id,[87])) { // Perf2Sg
            $aux =  'olet';
        } elseif (in_array($gramset_id,[88])) { // Perf3Sg
            $aux =  'on';
        } elseif (in_array($gramset_id,[89])) { // Perf1Pl
            $aux =  'olemme';
        } elseif (in_array($gramset_id,[90])) { // Perf2Pl
            $aux = 'olette';
        } elseif (in_array($gramset_id,[91])) { // Perf3Pl
            $aux =  'oldah';
        } elseif (in_array($gramset_id,[92,93,94,95,96])) { // PerfNeg without Perf3PlNeg
            $aux = 'ole';
        } elseif (in_array($gramset_id,[97])) { // Perf3PlNeg
            $aux = 'olda';
            
        } elseif (in_array($gramset_id,[98])) { // Plus1Sg
            $aux = 'olin';
        } elseif (in_array($gramset_id,[99])) { // Plus2Sg
            $aux = 'olit';
        } elseif (in_array($gramset_id,[100])) { // Perf3Sg
            $aux = 'oli';
        } elseif (in_array($gramset_id,[101])) { // Plus1Pl
            $aux = 'olimme';
        } elseif (in_array($gramset_id,[102])) { // Plus2Pl
            $aux = 'olitte';
        } elseif (in_array($gramset_id,[103])) { // Plus3Pl
            $aux = 'oldih';
        } elseif (in_array($gramset_id,[104,105,107,108,106])) { // PlusNeg without PlusSgNeg
            $aux = 'ole';
        } elseif (in_array($gramset_id,[109])) { // PlusSgNeg
            $aux = 'oldu';
            
        } elseif (in_array($gramset_id,[122])) { // CondPerf1Sg
            $aux = 'oližin';
        } elseif (in_array($gramset_id,[123])) { // CondPerf2Sg
            $aux = 'oližit';
        } elseif (in_array($gramset_id,[124,129,130,131,132,133])) { // CondPerf3Sg, CondPerfNeg wuthout CondPerf3PlNeg
            $aux = 'oliš';
        } elseif (in_array($gramset_id,[126])) { // CondPerf1Pl
            $aux = 'oližimme';
        } elseif (in_array($gramset_id,[127])) { // CondPerf2Pl
            $aux = 'oližitte';
        } elseif (in_array($gramset_id,[128,134])) { // CondPerf3Pl, CondPerf3PlNeg
            $aux = 'oldaiš';
            
        } elseif (in_array($gramset_id,[135])) { // CondPlus1Sg
            $aux = 'oližin';
        } elseif (in_array($gramset_id,[125])) { // CondPlus2Sg
            $aux = 'oližit';
        } elseif (in_array($gramset_id,[136,140,141,142,143,144])) { // CondPerf3Sg, CondPlusNeg without CondPlusSgNeg
            $aux = 'olnuiš';
        } elseif (in_array($gramset_id,[137])) { // CondPlus1Pl
            $aux = 'oližimmo';
        } elseif (in_array($gramset_id,[138])) { // CondPlus2Pl
            $aux = 'oližitto';
        } elseif (in_array($gramset_id,[139, 145])) { // CondPlus3Pl, CondPlus3PlNeg
            $aux = 'oldanuiš';
            
        } elseif (in_array($gramset_id,[158])) { // PotPerf1Sg
            $aux = 'lienen';
        } elseif (in_array($gramset_id,[159])) { // PotPerf2Sg
            $aux = 'lienet';
        } elseif (in_array($gramset_id,[160])) { // PotPerf3Sg
            $aux = 'lienou';
        } elseif (in_array($gramset_id,[161])) { // PotPerf1Pl
            $aux = 'lienemme';
        } elseif (in_array($gramset_id,[162])) { // PotPerf2Pl
            $aux = 'lienette';
        } elseif (in_array($gramset_id,[163])) { // PotPerf3Pl
            $aux = 'lienou';
        } elseif (in_array($gramset_id,[164,165,166,167,168])) { // PotPerfNeg without PotPerf3PlNeg
            $aux = 'liene';
        } elseif (in_array($gramset_id,[169])) { // PotPerf3PlNeg
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
        if (in_array($gramset_id,[70, 80, 92, 104, 110, 116, 129, 140, 152, 164])) { // 1Sg IndPres, IndImperf, IndPerf, IndPlus, CondImp, CondPlus, PotPrs
            return 'en';
        } elseif (in_array($gramset_id,[71, 81, 93, 105, 111, 117, 130, 141, 153, 165])) { // 2Sg
            return 'et';
        } elseif (in_array($gramset_id,[72, 82, 94, 107, 112, 118, 131, 142, 154, 166, 79, 85, 97, 109, 115, 121, 134, 145, 157, 169])) { // 3Sg, 3Pl
            return 'ei';
        } elseif (in_array($gramset_id,[73, 83, 95, 108, 113, 119, 132, 143, 155, 167])) { // 1Pl
            return 'emme';
        } elseif (in_array($gramset_id,[78, 84, 96, 106, 114, 120, 133, 144, 156, 168])) { // 2Pl
            return 'ette';
        } elseif ($gramset_id ==50) { // Imperative2Sg
            return 'älä';
        } elseif (in_array($gramset_id,[74, 77])) { // Imperative3
            return 'älgäh';        
        } elseif ($gramset_id ==75) { // Imperative1Pl
            return 'älgämme';        
        } elseif ($gramset_id ==76) { // Imperative2Pl
            return 'älgätte';        
        }
    }
    
    /**
     * TODO!!! Проверить для людиковского
     * 
     * @param type $gramset_id
     * @return type
     */
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

    public static function templateFromWordforms($wordforms/*, $number*/) { //Безличные?
//        return " (".$wordforms[26].", ".$wordforms[28]."; ".$wordforms[31]."; ".$wordforms[34].", ".$wordforms[37].")";
    }
}