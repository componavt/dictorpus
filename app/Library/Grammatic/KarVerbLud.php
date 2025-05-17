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
        switch ($stem_n) {
            case 5: // 2 active participle
                if (preg_match("/^(.+)n?n[uy]$/", $lemma->wordform(179, $dialect_id), $regs)) { 
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
    public static function getListForAutoComplete() {
        return [26,   27,  28,  29,  30,  31, 295, 296, // indPres
                70,   71,  72,  73,  78,  79, 
                32,   33,  34,  35,  36,  37, // indImperf 297, 298, 
                80,   81,  82,  83,  84,  85, 
                86,   87,  88,  89,  90,  91,  92,  93,  94,  95,  96,  97, // indPerf
                98,   99, 100, 101, 102, 103, 104, 105, 107, 108, 106, 109, // indPlus
                      51,  52,  53,  54,  55,       50,  74,  75,  76,  77, // Imperat 
                38,   39,  40,  41,  42,  43, 301, 303, // condPres 
                110, 111, 112, 113, 114, 115,
                 44,  45,  46,  47,  48,  49, 302, 304, // condImperf
                116, 117, 118, 119, 120, 121,
                122, 123, 124, 126, 127, 128, 129, 130, 131, 132, 133, 134, // condPerf
                135, 125, 136, 137, 138, 139, 140, 141, 142, 143, 144, 145, // condPlus
                146, 147, 148, 149, 150, 151, 310, 311, // potPres
                152, 153, 154, 155, 156, 157,
                158, 159, 160, 161, 162, 163, 164, 165, 166, 167, 168, 169, // potPerf
                170, 171, 172, 173, 174, 175, 176, 177, // Inf 312,
                178, 179, 180, 181]; 
    }
    
    public static function wordformByStems($stems, $gramset_id, $dialect_id, $def=NULL) {        
        $condBase = self::condBase($stems[2]);
        $inf2Base = self::inf2Base($stems[0]);
        
        switch ($gramset_id) {
            case 27: // 2. индикатив, презенс, 2 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[1], 'd,t') : '';        
            case 29: // 4. индикатив, презенс, 1 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[1], 'mme') : '';
            case 30: // 5. индикатив, презенс, 2 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[1], 'tte') : '';
                
            case 32: // 13. индикатив, имперфект, 1 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[3], 'n') : '';
            case 33: // 14. индикатив, имперфект, 2 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[3], 'd,t') : '';
            case 34: // 15. индикатив, имперфект, 3 л., ед.ч., пол. 
                return $stems[4];
            case 35: // 16. индикатив, имперфект, 1 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[3], 'mme') : '';
            case 36: // 17. индикатив, имперфект, 2 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[3], 'tte') : '';
                
            case 37: // 18. индикатив, имперфект, 3 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[7], 'ih') : '';

            case 38: // 59. кондиционал, презенс, 1 л., ед.ч., пол. 
                return !$def && $condBase ? $condBase. 'žin' : '';
            case 39: // 60. кондиционал, презенс, 2 л., ед.ч., пол. 
                return !$def && $condBase ? Grammatic::joinMorfToBases($condBase, 'žid,žit') : '';
            case 40: // 61. кондиционал, презенс, 3 л., ед.ч., пол. 
            case 301: // 150. кондиционал, презенс, коннегатив 
                return $condBase ? $condBase. 'š' : '';
            case 41: // 62. кондиционал, презенс, 1 л., мн.ч., пол. 
                return !$def && $condBase ? $condBase. 'žimme' : '';
            case 42: // 63. кондиционал, презенс, 2 л., мн.ч., пол. 
                return !$def && $condBase ? $condBase. 'žitte' : '';
            case 43: // 64. кондиционал, презенс, 3 л., мн.ч., пол. 
            case 303: // 151. кондиционал, презенс, коннегатив, 3 л., мн.ч.
                return !$def && $stems[6] ?  $stems[6].'iš' : '';
                
            case 110: // 65. кондиционал, презенс, 1 л., ед.ч., отр. 
            case 117: // 78. кондиционал, презенс, 2 л., ед.ч., отр. 
            case 119: // 80. кондиционал, презенс, 1 л., мн.ч., отр. 
            case 120: // 81. кондиционал, презенс, 2 л., мн.ч., отр. 
                return !$def && $condBase ? Grammatic::interLists(self::negVerb($gramset_id, $dialect_id), $condBase. 'š') : '';
            case 118: // 79. кондиционал, презенс, 3 л., ед.ч., отр. 
                return $condBase ?  Grammatic::interLists(self::negVerb($gramset_id, $dialect_id), $condBase. 'š') : '';
            case 121: // 82. кондиционал, презенс, 3 л., мн.ч., отр. 
                return !$def && $stems[6] ? Grammatic::interLists(self::negVerb($gramset_id, $dialect_id), $stems[6].'iš') : '';
                
            case 171: // 132. II инфинитив, инессив 
                return $inf2Base ? $inf2Base.'s' : '';
            case 172: // 133. II инфинитив, инструктив  
                return $inf2Base ? $inf2Base.'n' : '';
        }
        
        if (empty($stems[10])) { return ''; }
        
        $activeBase = self::activeBase($stems[0], $stems[5]);
        $active = $activeBase ? $activeBase.KarGram::garmVowel($stems[10],'u') : '';
        $passive = Grammatic::joinMorfToBases($stems[7], KarGram::garmVowel($stems[10],'u'));
        $impBase = self::imperBase($stems[0], $stems[5]);   
        $potBase = self::potPres3PlBase($stems[6], $stems[7], $stems[10]);
        $A = KarGram::garmVowel($stems[10],'a');
        
        switch ($gramset_id) {
            case 28: // 3. индикатив, презенс, 3 л., ед.ч., пол. 
                return $stems[2] ? KarVerb::indPres1SingByStem($stems[2], $stems[10]) : ''; 
                
            case 80: // 19. индикатив, имперфект, 1 л., ед.ч., отриц. 
            case 81: // 20. индикатив, имперфект, 2 л., ед.ч., отриц. 
            case 83: // 22. индикатив, имперфект, 1 л., мн.ч., отриц. 
            case 84: // 23. индикатив, имперфект, 2 л., мн.ч., отриц. 
                return !$def ? Grammatic::interLists(self::negVerb($gramset_id, $dialect_id), $active) : '';
            case 82: // 21. индикатив, имперфект, 3 л., ед.ч., отриц. 
                return Grammatic::interLists(self::negVerb($gramset_id, $dialect_id), $active);
            case 85: // 24. индикатив, имперфект, 3 л., мн.ч., отриц. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm(85, $lang_id), $passive) : '';
                
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
                return !$def ? Grammatic::interLists(self::auxVerb($gramset_id, $dialect_id), $active) : '';
            case 88: // 27. индикатив, перфект, 3 л., ед.ч., пол. 
            case 100: // 39. индикатив, плюсквамперфект, 3 л., ед.ч., пол. 
            case 124: // 85. кондиционал, перфект, 3 л., ед.ч., пол. 
            case 136: // 97. кондиционал, плюсквамперфект, 3 л., ед.ч., пол. 
            case 160: // 121. потенциал, перфект, 3 л., ед.ч., пол. 
                return Grammatic::interLists(self::auxVerb($gramset_id, $dialect_id), $active);                
            case 91: // 30. индикатив, перфект, 3 л., мн.ч., пол. 
            case 103: // 42. индикатив, плюсквамперфект, 3 л., мн.ч., пол. 
            case 128: // 88. кондиционал, перфект, 3 л., мн.ч., пол. 
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
            case 129: // 89. кондиционал, перфект, 1 л., ед.ч., отр. 
            case 130: // 90. кондиционал, перфект, 2 л., ед.ч., отр. 
            case 132: // 92. кондиционал, перфект, 1 л., мн.ч., отр. 
            case 133: // 93. кондиционал, перфект, 2 л., мн.ч., отр. 
            case 140: // 101. кондиционал, плюсквамперфект, 1 л., ед.ч., отр. 
            case 141: // 102. кондиционал, плюсквамперфект, 2 л., ед.ч., отр. 
            case 143: // 104. кондиционал, плюсквамперфект, 1 л., мн.ч., отр. 
            case 144: // 105. кондиционал, плюсквамперфект, 2 л., мн.ч., отр. 
            case 164: // 125. потенциал, перфект, 1 л., ед.ч., отр. 
            case 165: // 126. потенциал, перфект, 2 л., ед.ч., отр. 
            case 167: // 128. потенциал, перфект, 1 л., мн.ч., отр. 
            case 168: // 129. потенциал, перфект, 2 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(self::auxVerb($gramset_id, $dialect_id, '-'), $active) : '';
            case 94: // 33. индикатив, перфект, 3 л., ед.ч., отриц. 
            case 106: // 45. индикатив, плюсквамперфект, 3 л., ед.ч., отриц. 
            case 131: // 91. кондиционал, перфект, 3 л., ед.ч., отр. 
            case 142: // 103. кондиционал, плюсквамперфект, 3 л., ед.ч., отр. 
            case 166: // 127. потенциал, перфект, 3 л., ед.ч., отр. 
                return Grammatic::interLists(self::auxVerb($gramset_id, $dialect_id, '-'), $active);
            case 97: // 36. индикатив, перфект, 3 л., мн.ч., отриц. 
            case 109: // 48. индикатив, плюсквамперфект, 3 л., мн.ч., отриц. 
            case 134: // 94. кондиционал, перфект, 3 л., мн.ч., отр. 
            case 145: // 106. кондиционал, плюсквамперфект, 3 л., мн.ч., отр. 
            case 169: // 130. потенциал, перфект, 3 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(self::auxVerb($gramset_id, $dialect_id, '-'), $passive) : '';                

            case 52: // 50. императив, 3 л., ед.ч., пол 
                return $impBase ? $impBase.$A.'h' : '';
            case 53: // 51. императив, 1 л., мн.ч., пол 
                return !$def && $impBase ? $impBase.$A.'mme' : '';
            case 54: // 52. императив, 2 л., мн.ч., пол 
                return !$def && $impBase ? $impBase.$A.'tte' : '';
            case 55: // 53. императив, 3 л., мн.ч., пол 
                return !$def && $impBase ? $impBase.$A.'h' : '';

            case 74: // 55. императив, 3 л., ед.ч., отр. 
                return $impBase ? Grammatic::interLists(self::negVerb($gramset_id, $dialect_id), $impBase.$A.'h') : '';
            case 75: // 56. императив, 1 л., мн.ч., отр 
                return !$def && $impBase ? Grammatic::interLists(self::negVerb($gramset_id, $dialect_id), $impBase.$A.'mme') : '';
            case 76: // 57. императив, 2 л., мн.ч., отр. 
                return !$def && $impBase ? Grammatic::interLists(self::negVerb($gramset_id, $dialect_id), $impBase.$A.'tte') : '';
            case 77: // 58. императив, 3 л., мн.ч., отр. 
                return !$def && $impBase ? Grammatic::interLists(self::negVerb($gramset_id, $dialect_id), $impBase.$A.'h') : '';
                
            case 44: // 71. кондиционал, имперфект, 1 л., ед.ч., пол. 
                return !$def ? $active. 'ižin' : '';
            case 45: // 72. кондиционал, имперфект, 2 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($active, 'ižid,ižit') : '';
            case 46: // 73. кондиционал, имперфект, 3 л., ед.ч., пол. 
            case 302: // 152. кондиционал, имперфект, коннегатив 
                return $active. 'iš';
            case 47: // 74. кондиционал, имперфект, 1 л., мн.ч., пол. 
                return !$def ? $active. 'ižimme' : '';
            case 48: // 75. кондиционал, имперфект, 2 л., мн.ч., пол. 
                return !$def ? $active. 'ižitte' : '';
            case 49: // 76. кондиционал, имперфект, 3 л., мн.ч., пол. 
            case 304: // 153. кондиционал, имперфект, коннегатив, 3 л., мн.ч.
                return !$def ?  self::condImp3Pl($stems[6], $stems[7], $stems[10]) : '';
                
            case 116: // 77. кондиционал, имперфект, 1 л., ед.ч., отр. 
            case 117: // 78. кондиционал, имперфект, 2 л., ед.ч., отр. 
            case 119: // 80. кондиционал, имперфект, 1 л., мн.ч., отр. 
            case 120: // 81. кондиционал, имперфект, 2 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(self::negVerb($gramset_id, $dialect_id), $active. 'iš') : '';
            case 118: // 79. кондиционал, имперфект, 3 л., ед.ч., отр. 
                return Grammatic::interLists(self::negVerb($gramset_id, $dialect_id), $active. 'iš');
            case 121: // 82. кондиционал, имперфект, 3 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(self::negVerb($gramset_id, $dialect_id), self::potImp3Pl($stems[6], $stems[7], $stems[10]). 'iš') : '';
            
            case 146: // 107. потенциал, презенс, 1 л., ед.ч., пол. 
                return !$def && $activeBase ? $activeBase. 'n' : '';
            case 147: // 108. потенциал, презенс, 2 л., ед.ч., пол. 
                return !$def && $activeBase ? Grammatic::joinMorfToBases($activeBase, 'ed,et') : '';
            case 148: // 109. потенциал, презенс, 3 л., ед.ч., пол. 
                return $activeBase ? $activeBase. KarGram::garmVowel($stems[10],'ou') : '';
            case 149: // 110. потенциал, презенс, 1 л., мн.ч., пол. 
                return !$def && $activeBase ? $activeBase. 'emme' : '';
            case 150: // 111. потенциал, презенс, 2 л., мн.ч., пол. 
                return !$def && $activeBase ? $activeBase. 'ette' : '';
            case 151: // 112. потенциал, презенс, 3 л., мн.ч., пол. 
            case 311: // 159. потенциал, презенс, коннегатив, 3 л. мн.ч.
                return !$def && $potBase ? $potBase. 'h' : '';
            case 310: // 158. потенциал, презенс, коннегатив 
                return $activeBase ? $activeBase. 'e' : '';
                
            case 152: // 113. потенциал, презенс, 1 л., ед.ч., отр. 
            case 153: // 114. потенциал, презенс, 2 л., ед.ч., отр. 
            case 155: // 116. потенциал, презенс, 1 л., мн.ч., отр. 
            case 156: // 117. потенциал, презенс, 2 л., мн.ч., отр. 
                return !$def && $activeBase ? Grammatic::interLists(self::negVerb($gramset_id, $dialect_id), $activeBase. 'e') : '';
            case 154: // 115. потенциал, презенс, 3 л., ед.ч., отр. 
                return $activeBase ? Grammatic::interLists(self::negVerb($gramset_id, $dialect_id), $activeBase. 'e') : '';
            case 157: // 118. потенциал, презенс, 3 л., мн.ч., отр. 
                return $potBase ? Grammatic::interLists(self::negVerb($gramset_id, $dialect_id), $potBase) : '';
                
            case 173: // 134. III инфинитив, адессив
                return $stems[2] ? $stems[2]. KarGram::garmVowel($stems[10],'mal') : '';
            case 174: // 135. III инфинитив, иллатив 
                return $stems[2] ? $stems[2]. KarGram::garmVowel($stems[10],'mah') : '';
            case 175: // 136. III инфинитив, инессив 
            case 176: // 137. III инфинитив, элатив 
                return $stems[2] ? $stems[2]. KarGram::garmVowel($stems[10],'mas') : '';
            case 177: // 138. III инфинитив, абессив 
                return $stems[2] ? $stems[2]. KarGram::garmVowel($stems[10],'mata') : '';
                
            case 178: // 139. актив, 1-е причастие 
                return Grammatic::joinMorfToBases(KarGram::replaceCV($stems[2], 'e', 'i'), 'i');
            case 179: // 140. актив, 2-е причастие 
                return $active;
            case 180: // 142. пассив, 1-е причастие 
                return !$def && $stems[7] ? Grammatic::joinMorfToBases($stems[7], KarGram::garmVowel($stems[10], 'av')) : '';
            case 181: // 143. пассив, 2-е причастие 
                return !$def ? $passive : '';
        }
    }

    
    public static function auxVerb($gramset_id, $dialect_id, $negative=NULL, $is_reflexive=false) {
        if (in_array($gramset_id,[86])) { // Perf1Sg
            $aux = 'olen';
        } elseif (in_array($gramset_id,[87])) { // Perf2Sg
            $aux =  'oled,olet';
        } elseif (in_array($gramset_id,[88])) { // Perf3Sg
            $aux =  'on';
        } elseif (in_array($gramset_id,[89])) { // Perf1Pl
            $aux =  'olemme';
        } elseif (in_array($gramset_id,[90])) { // Perf2Pl
            $aux = 'olette';
        } elseif (in_array($gramset_id,[91])) { // Perf3Pl
            $aux =  'on';
        } elseif (in_array($gramset_id,[92,93,94,95,96,97])) { // PerfNeg
            $aux = 'ole';
            
        } elseif (in_array($gramset_id,[98])) { // Plus1Sg
            $aux = 'olin';
        } elseif (in_array($gramset_id,[99])) { // Plus2Sg
            $aux = 'olid,olit';
        } elseif (in_array($gramset_id,[100,103])) { // Perf3
            $aux = 'oli';
        } elseif (in_array($gramset_id,[101])) { // Plus1Pl
            $aux = 'olimme';
        } elseif (in_array($gramset_id,[102])) { // Plus2Pl
            $aux = 'olitte';
        } elseif (in_array($gramset_id,[104,105,107,108,106])) { // PlusNeg without PlusSgNeg
            $aux = 'olnu';
        } elseif (in_array($gramset_id,[109])) { // PlusSgNeg
            $aux = 'oldu';
            
        } elseif (in_array($gramset_id,[122])) { // CondPerf1Sg
            $aux = 'oližin';
        } elseif (in_array($gramset_id,[123])) { // CondPerf2Sg
            $aux = 'oližid,oližit';
        } elseif (in_array($gramset_id,[124,129,130,131,132,133])) { // CondPerf3Sg, CondPerfNeg wuthout CondPerf3PlNeg
            $aux = 'oliš';
        } elseif (in_array($gramset_id,[126])) { // CondPerf1Pl
            $aux = 'oližimme';
        } elseif (in_array($gramset_id,[127])) { // CondPerf2Pl
            $aux = 'oližitte';
        } elseif (in_array($gramset_id,[128,134])) { // CondPerf3Pl, CondPerf3PlNeg
            $aux = 'oldaiš';
            
        } elseif (in_array($gramset_id,[135])) { // CondPlus1Sg
            $aux = 'olnuižin';
        } elseif (in_array($gramset_id,[125])) { // CondPlus2Sg
            $aux = 'olnuižid,olnuižit';
        } elseif (in_array($gramset_id,[136,140,141,142,143,144])) { // CondPerf3Sg, CondPlusNeg without CondPlusSgNeg
            $aux = 'olnuiš';
        } elseif (in_array($gramset_id,[137])) { // CondPlus1Pl
            $aux = 'olnuižimme';
        } elseif (in_array($gramset_id,[138])) { // CondPlus2Pl
            $aux = 'olnuižitte';
        } elseif (in_array($gramset_id,[139, 145])) { // CondPlus3Pl, CondPlus3PlNeg
            $aux = 'oldanuiš';
            
        } elseif (in_array($gramset_id,[158])) { // PotPerf1Sg
            $aux = 'lienen';
        } elseif (in_array($gramset_id,[159])) { // PotPerf2Sg
            $aux = 'liened,lienet';
        } elseif (in_array($gramset_id,[160,163])) { // PotPerf3
            $aux = 'lienöy';
        } elseif (in_array($gramset_id,[161])) { // PotPerf1Pl
            $aux = 'lienemme';
        } elseif (in_array($gramset_id,[162])) { // PotPerf2Pl
            $aux = 'lienette';
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
        if (in_array($gramset_id,[70, 80, 92, 104, 110, 116, 129, 140, 152, 164])) { // 1Sg IndPres, IndImperf, IndPerf, IndPlus, CondImp, CondPlus, PotPrs
            return 'en';
        } elseif (in_array($gramset_id,[71, 81, 93, 105, 111, 117, 130, 141, 153, 165])) { // 2Sg
            return 'ed,et';
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
     * @param type $gramset_id
     * @return type
     */
    public static function getAffixesForGramset($gramset_id) {
        switch ($gramset_id) {
            case 26: // 1. индикатив, презенс, 1 л., ед.ч., пол. 
            case 32: // 13. индикатив, имперфект, 1 л., ед.ч., пол. 
                return ['n'];
            case 172: // 133. II инфинитив, инструктив  
                return ['en'];
            case 27: // 2. индикатив, презенс, 2 л., ед.ч., пол. 
            case 33: // 14. индикатив, имперфект, 2 л., ед.ч., пол. 
                return ['d','t'];
            case 28: // 3. индикатив, презенс, 3 л., ед.ч., пол. 
                return ['u', 'y'];
            case 80: // 19. индикатив, имперфект, 1 л., ед.ч., отриц. 
            case 81: // 20. индикатив, имперфект, 2 л., ед.ч., отриц. 
            case 82: // 21. индикатив, имперфект, 3 л., ед.ч., отриц. 
            case 83: // 22. индикатив, имперфект, 1 л., мн.ч., отриц. 
            case 84: // 23. индикатив, имперфект, 2 л., мн.ч., отриц.                 
            case 86: // 25. индикатив, перфект, 1 л., ед.ч., пол. 
            case 87: // 26. индикатив, перфект, 2 л., ед.ч., пол. 
            case 88: // 27. индикатив, перфект, 3 л., ед.ч., пол. 
            case 89: // 28. индикатив, перфект, 1 л., мн.ч., пол. 
            case 90: // 29. индикатив, перфект, 2 л., мн.ч., пол. 
            case 92: // 31. индикатив, перфект, 1 л., ед.ч., отриц. 
            case 93: // 32. индикатив, перфект, 2 л., ед.ч., отриц. 
            case 94: // 33. индикатив, перфект, 3 л., ед.ч., отриц. 
            case 95: // 34. индикатив, перфект, 1 л., мн.ч., отриц. 
            case 96: // 35. индикатив, перфект, 2 л., мн.ч., отриц. 
            case 98: // 37. индикатив, плюсквамперфект, 1 л., ед.ч., пол. 
            case 99: // 38. индикатив, плюсквамперфект, 2 л., ед.ч., пол. 
            case 100: // 39. индикатив, плюсквамперфект, 3 л., ед.ч., пол. 
            case 101: // 40. индикатив, плюсквамперфект, 1 л., мн.ч., пол. 
            case 102: // 41. индикатив, плюсквамперфект, 2 л., мн.ч., пол. 
            case 104: // 43. индикатив, плюсквамперфект, 1 л., ед.ч., отриц. 
            case 105: // 44. индикатив, плюсквамперфект, 2 л., ед.ч., отриц. 
            case 107: // 45. индикатив, плюсквамперфект, 3 л., ед.ч., отриц. 
            case 108: // 46. индикатив, плюсквамперфект, 2 л., мн.ч., отриц. 
            case 106: // 47. индикатив, плюсквамперфект, 1 л., мн.ч., отриц. 
            case 122: // 83. кондиционал, перфект, 1 л., ед.ч., пол. 
            case 123: // 84. кондиционал, перфект, 2 л., ед.ч., пол. 
            case 124: // 85. кондиционал, перфект, 3 л., ед.ч., пол. 
            case 126: // 86. кондиционал, перфект, 1 л., мн.ч., пол. 
            case 127: // 87. кондиционал, перфект, 2 л., мн.ч., пол.                 
            case 129: // 89. кондиционал, перфект, 1 л., ед.ч., отр. 
            case 130: // 90. кондиционал, перфект, 2 л., ед.ч., отр. 
            case 131: // 91. кондиционал, перфект, 3 л., ед.ч., отр. 
            case 132: // 92. кондиционал, перфект, 1 л., мн.ч., отр. 
            case 133: // 93. кондиционал, перфект, 2 л., мн.ч., отр.                 
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
            case 179: // 140. актив, 2-е причастие 
                return ['nu', 'ny'];
            case 85: // 24. индикатив, имперфект, 3 л., мн.ч., отриц. 
            case 91: // 30. индикатив, перфект, 3 л., мн.ч., пол. 
            case 97: // 36. индикатив, перфект, 3 л., мн.ч., отриц. 
            case 103: // 42. индикатив, плюсквамперфект, 3 л., мн.ч., пол. 
            case 109: // 48. индикатив, плюсквамперфект, 3 л., мн.ч., отриц. 
            case 128: // 88. кондиционал, перфект, 3 л., мн.ч., пол. 
            case 134: // 94. кондиционал, перфект, 3 л., мн.ч., отр. 
            case 139: // 100. кондиционал, плюсквамперфект, 3 л., мн.ч., пол. 
            case 145: // 106. кондиционал, плюсквамперфект, 3 л., мн.ч., отр. 
            case 163: // 124. потенциал, перфект, 3 л., мн.ч., пол. 
            case 169: // 130. потенциал, перфект, 3 л., мн.ч., отр. 
            case 181: // 143. пассив, 2-е причастие 
                return ['tu', 'ty'];
            case 29: // 4. индикатив, презенс, 1 л., мн.ч., пол. 
            case 35: // 16. индикатив, имперфект, 1 л., мн.ч., пол. 
                return ['mme'];
            case 30: // 5. индикатив, презенс, 2 л., мн.ч., пол. 
            case 36: // 17. индикатив, имперфект, 2 л., мн.ч., пол. 
                return ['tte'];
            case 31: // 6. индикатив, презенс, 3 л., мн.ч., пол. 
                return ['tah', 'täh'];
            case 37: // 18. индикатив, имперфект, 3 л., мн.ч., пол. 
                return ['tih'];
            case 52: // 50. императив, 3 л., ед.ч., пол 
            case 55: // 53. императив, 3 л., мн.ч., пол 
            case 74: // 55. императив, 3 л., ед.ч., отр. 
            case 77: // 58. императив, 3 л., мн.ч., отр. 
                return ['kah', 'käh', 'gah', 'gäh']; 
            case 53: // 51. императив, 1 л., мн.ч., пол 
            case 75: // 56. императив, 1 л., мн.ч., отр. 
                return ['kamme', 'kämme', 'gamme', 'gämme']; 
            case 54: // 52. императив, 2 л., мн.ч., пол 
            case 76: // 57. императив, 2 л., мн.ч., отр. 
                return ['katte', 'kätte', 'gatte', 'gätte']; 
            case 38: // 59. кондиционал, презенс, 1 л., ед.ч., пол. 
                return ['ižin'];
            case 39: // 60. кондиционал, презенс, 2 л., ед.ч., пол. 
                return ['ižid','ižit'];
            case 41: // 62. кондиционал, презенс, 1 л., мн.ч., пол. 
                return ['ižimme'];
            case 42: // 63. кондиционал, презенс, 2 л., мн.ч., пол. 
                return ['ižitte'];
            case 40: // 61. кондиционал, презенс, 3 л., ед.ч., пол. 
            case 110: // 65. кондиционал, презенс, 1 л., ед.ч., отр. 
            case 111: // 66. кондиционал, презенс, 2 л., ед.ч., отр. 
            case 112: // 67. кондиционал, презенс, 3 л., ед.ч., отр. 
            case 113: // 65. кондиционал, презенс, 1 л., мн.ч., отр. 
            case 114: // 68. кондиционал, презенс, 2 л., мн.ч., отр. 
            case 43: // 69. кондиционал, презенс, 3 л., мн.ч., пол. 
            case 115: // 70. кондиционал, презенс, 3 л., мн.ч., отр. 
                return ['iš'];
            case 44: // 71. кондиционал, имперфект, 1 л., ед.ч., пол. 
                return ['nuizin', 'nyizin'];
            case 45: // 72. кондиционал, имперфект, 2 л., ед.ч., пол. 
                return ['nuizid', 'nyizid','nuizit', 'nyizit'];
            case 46: // 73. кондиционал, имперфект, 3 л., ед.ч., пол. 
            case 116: // 77. кондиционал, имперфект, 1 л., ед.ч., отр. 
            case 117: // 78. кондиционал, имперфект, 2 л., ед.ч., отр. 
            case 118: // 79. кондиционал, имперфект, 3 л., ед.ч., отр. 
            case 119: // 80. кондиционал, имперфект, 1 л., мн.ч., отр. 
            case 120: // 81. кондиционал, имперфект, 2 л., мн.ч., отр. 
            case 49: // 76. кондиционал, имперфект, 3 л., мн.ч., пол. 
            case 121: // 82. кондиционал, имперфект, 3 л., мн.ч., отр. 
            case 302: // 152. кондиционал, имперфект, коннегатив
            case 304: // 153. кондиционал, имперфект, коннегатив, 3 л. мн.ч.
                return ['nuiš', 'nyiš'];  
            case 47: // 74. кондиционал, имперфект, 1 л., мн.ч., пол. 
                return ['nuižimme', 'nyižimme'];
            case 48: // 75. кондиционал, имперфект, 2 л., мн.ч., пол. 
                return ['nuižitte', 'nyižitte'];
                
            case 146: // 107. потенциал, презенс, 1 л., ед.ч., пол. 
                return ['nen']; 
            case 147: // 108. потенциал, презенс, 2 л., ед.ч., пол. 
                return ['ned', 'net']; 
            case 148: // 109. потенциал, презенс, 3 л., ед.ч., пол. 
                return ['nou', 'nöy']; // 'nnou', 'nnöy'
            case 149: // 110. потенциал, презенс, 1 л., мн.ч., пол. 
                return ['nemme']; 
            case 150: // 111. потенциал, презенс, 2 л., мн.ч., пол. 
                return ['nette'];
            case 151: // 112. потенциал, презенс, 3 л., мн.ч., пол. 
                return ['anneh', 'änneh', 'aneh', 'äneh'];
            case 310: // 158. потенциал, презенс, коннегатив 
            case 152: // 113. потенциал, презенс, 1 л., ед.ч., отр. 
            case 153: // 114. потенциал, презенс, 2 л., ед.ч., отр. 
            case 154: // 115. потенциал, презенс, 3 л., ед.ч., отр. 
            case 155: // 116. потенциал, презенс, 1 л., мн.ч., отр. 
            case 156: // 117. потенциал, презенс, 2 л., мн.ч., отр. 
                return ['ne'];
            case 311: // 159. потенциал, презенс, коннегатив, 3 л. мн.ч.
            case 157: // 118. потенциал, презенс, 3 л., мн.ч., отр. 
                return ['anne', 'änne', 'ane', 'äne']; 
                
                
            case 171: // 132. II инфинитив, инессив 
                return ['es']; 
            case 173: // 134. III инфинитив, адессив
                return ['mal', 'mäl'];
            case 174: // 135. III инфинитив, иллатив 
                return ['mah', 'mäh'];
            case 175: // 136. III инфинитив, инессив 
            case 176: // 137. III инфинитив, элатив 
                return ['mas', 'mäs'];
            case 177: // 138. III инфинитив, абессив 
                return ['mata', 'mätä'];
                
            case 178: // 139. актив, 1-е причастие 
                return ['i'];
            case 180: // 142. пассив, 1-е причастие 
                return ['av', 'äv'];
        }
        return [];
    }

    public static function templateFromWordforms($wordforms) {
        $wordform = $wordforms[26] ?? null; // prs 1st sg
        if (!$wordform) { 
            return null;
        }
        if (preg_match("/^-(.*)n$/", $wordform, $regs) 
                || preg_match("/^(.*)n$/", $wordform, $regs)) {
            $wordform = $regs[1];
        }
        
        return " [".$wordform."]";
    }
    
    /**
     * Основа актива
     * 
     * Если с.ф. заканчивается на
     * 1) VtA, то о.5 + nn
     * 2) в остальных случаях о.5 + n
     * 
     * @param string $stem0
     * @param string $stem5
     */
    public static function activeBase($stem0, $stem5) {
        if (!$stem5) { return ''; }
        
        $V="[".KarGram::vowelSet()."]";
        
        if (preg_match("/".$V."(’?)t[aä]$/u", $stem0)) {
            return $stem5. 'nn';
        }
            return $stem5. 'n';
    }
        
    /**
     * Основа императива
     * 
     * А. Если с.ф. заканчивается на CtA, то = о.5  + k
     * Б. Если с.ф. заканчивается на VtA, то = о.5  + kk
     * В. в остальных случаях = о.5  + g
     * 
     * @param string $stem0
     * @param string $stem5
     */
    public static function imperBase($stem0, $stem5) {
        if (!$stem5) { return ''; }
        
        $C="[".KarGram::consSet()."]’?";        
        $V="[".KarGram::vowelSet()."]";
        
        if (preg_match("/".$C."(’?)t[aä]$/u", $stem0)) { // A
            return $stem5. 'k';
        } elseif (preg_match("/".$V."(’?)t[aä]$/u", $stem0)) { // Б
            return $stem5. 'kk';
        }
        return $stem5. 'g';
    }
        
    /**
     * Основа кондиционала
     * 
     * Если о.2 оканчивается на
     * 1) Ci, то = о.2;
     * 2) Сe, то = о.2-e +i;
     * 3) dA, то = о.2-dA +i;
     * 4) в остальных случаях = о.2+i
     * 
     * @param string $stem0
     * @param string $stem5
     */
    public static function condBase($stem2) {
        if (!$stem5) { return ''; }
        
        $C="[".KarGram::consSet()."]’?";        
        
        if (preg_match("/".$C."i$/u", $stem2)) { // 1
            return $stem2;
        } elseif (preg_match("/^(.*".$C.")e$/u", $stem2, $regs) // 2
                || preg_match("/^(.*".$C.")(’?)d[aä]$/u", $stem2, $regs)) { // 3
            return $regs[1]. 'i';
        }
        return $stem2. 'i';
    }
        
    /**
     * кондиционал имперфект 3л. мн.ч.
     * 
     * А. Если в о.6 3 слога, то о.6+nnUiš,
     * Б. иначе о.7+AnUiš
     * 
     * @param string $stem6
     * @param string $stem7
     * @param string $is_backV
     */
    public static function condImp3Pl($stem6, $stem7, $is_backV) {
        if (!$stem5) { return ''; }
        
        $C="[".KarGram::consSet()."]’?";        
        
        if (countSyllable($stem6)==3) { // A
            return $stem6.'nn'.KarGram::garmVowel($is_backV,'u').'iš';
        }
        return $stem7. KarGram::garmVowel($is_backV,'a'). 'n'.KarGram::garmVowel($is_backV,'u').'iš';
    }
        
    /**
     * Основа потенциала презенс 3л. мн.ч.
     * 
     * А. Если в о.6 3 слога, то о.6+nne,
     * Б. иначе о.7+Ane
     * 
     * @param string $stem6
     * @param string $stem7
     * @param string $is_backV
     */
    public static function potPres3PlBase($stem6, $stem7, $is_backV) {
        if (!$stem5) { return ''; }
        
        $C="[".KarGram::consSet()."]’?";        
        
        if (countSyllable($stem6)==3) { // A
            return $stem6.'nne';
        }
        return $stem7. KarGram::garmVowel($is_backV,'a'). 'ne';
    }
        
    /**
     * Основа II инфинитива
     * 
     * Если с.ф. оканч. на i, то с.ф. -i +de,
     * иначе с.ф. -A +e
     * 
     * @param string $stem6
     * @param string $stem7
     * @param string $is_backV
     */
    public static function inf3Base($stem0) {
        if (!$stem5) { return ''; }
        
        if (preg_match("/^(.*)i$/u", $stem0, $regs)) {
            return $regs.'de';
        } elseif (preg_match("/^(.*)[aä]$/u", $stem0, $regs)) {
            return $regs.'e';
        }
        return '';
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
        
        $stems[2]=self::stem2FromMiniTemplate($base, $regs[2], $regs[3], $stems[1]); // вспом. сильн. гл.
        $stems[3]=self::stem3FromMiniTemplate($stems[1], $harmony);
        $stems[4]=self::stem3FromMiniTemplate($stems[2], $harmony);
        $stems[5]=self::stem5FromMiniTemplate($stems[0]);
        $stems[6]=self::stem6FromMiniTemplate($stems[0], $stems[1], $harmony); 
        $stems[7]=self::stem7FromMiniTemplate($stems[6]);
        
        return [$stems, $name_num, $regs[1], $regs[2]];
    }
    
    /**
     * А. Если неизм. ($base) оканчивается и ok. начинается на одну и ту же согласную Сч  
     * (k, p, t, č, c, š, s или h), 
     * то удваиваем эту согласную, т. е. = неизм.+Сч+п.о

     * Б. В остальных случаях = о.1, 
     * при этом в о.1 če > čče
     * 
     * @param string $stem0
     * @param string $stem1
     */
    public static function stem2FromMiniTemplate($base, $ok, $p, $stem1) {
        if (empty($base) || empty($ok)) {
            return '';
        }
        $base_last = substr($base, -1, 1);
        $ok1 = substr($ok, 0, 1);
        if ($base_last == $ok1 && in_array($ok1, ['k','p','t','č','c','š','s','h'])) {
           return $base.$ok1.$p;
        } else {
            return preg_replace("/če$/u","čče",$stem1);
        }
    }

    /**
     * А. две форма, если о.1 оканчивается на
     * 1) CO, то + i / O>UOi
     * 2) Ca и 2 слога, в обоих слогах есть гласная a, то a>oi / a>uoi
     * 3) V1V2, то + i / V1V2>V2i

     * Б. одна форма = о.1 + i, при этом если о.1 оканчивается на
     * 1) Ce, то e>i
     * 2) dA, то dA>ži
     * 3) CA (кроме dA), то A>i
     * 
     * @param string $stem1
     * @return string
     */
    public static function stem3FromMiniTemplate($stem1, $harmony) {
        $C = "[".KarGram::consSet()."]’?";
        $V = "[".KarGram::vowelSet()."]";

        if (preg_match("/^(.+".$C.")[oö]$/u", $stem1, $regs)){ // A1
            return $stem1.'i/'.$regs[1].KarGram::garmVowel($harmony,'uoi');
        } elseif (preg_match("/^(".$C."*’?".$V."?a".$V."?".$C."*’?)a$/u", $stem1, $regs)) { // A2
            return $regs[1].'oi/'.$regs[1].'uoi';            
        } elseif (preg_match("/^(.+)(".$V.")(".$V.")$/u", $stem1, $regs)) {    // A3
            return $stem1.'i/'.$regs[1].$regs[3].'i';      
            
        } elseif (preg_match("/^(.+)d[aä]$/u", $stem1, $regs)) { // Б2
            return $regs[1].'ži';        
        } elseif (preg_match("/^(.+".$C.")[eaä]$/u", $stem1, $regs)) { // Б1,Б3               
            return $regs[1].'i';        
        }
        return $stem1.'i'; // Б
    }    

    /**
     * с.ф. -dA/i/tA
     * 
     * @param string $stem0
     * @param string $stem2
     * @return string
     */
    public static function stem5FromMiniTemplate($stem0) {
        if (preg_match("/^(.+)[dt][aä]$/u", $stem0, $regs)    
            || preg_match("/^(.+)i$/u", $stem0, $regs)) { // A
            return $regs;
        }
    }    
    
    /**
     * А. Если с.ф. заканчивается на CVdA / CVi / CVtA, 
то   * = о.1 (с заменой A>e) + tA

Б.   * в остальных случаях = с.ф. 
     * 
     * @param string $stem0
     * @param string $stem1
     * @param boolean $harmony
     * @return string
     */
    public static function stem6FromMiniTemplate($stem0, $stem1, $harmony) {
        $C = "[".KarGram::consSet()."]’?";
        $V = "[".KarGram::vowelSet()."]";
        
        if (preg_match("/".$C.$V."[dt][aä]$/u", $stem0)    
            || preg_match("/".$C.$V."i$/u", $stem0)) { // A
            if (preg_match("/^(.+)[aä]$/u", $stem1, $regs)) {
                $stem1 = $regs[1].'e';
            }
            return $stem1.KarGram::garmVowel($harmony,'ta');
        }
        return $stem0;
    }
    
    /**
     * о.6 -V,
     * 1) при этом если оставшаяся часть основы оканч. на Vt, то +t
     * 
     * @param string $stem0
     * @param string $stem1
     * @param string $stem5
     * @return string
     */
    public static function stem7FromMiniTemplate($stem6) {
        $V = "[".KarGram::vowelSet()."]";
        
        if (preg_match("/^(.+)".$V."$/u", $stem6, $regs)) {
            if (preg_match("/".$V."t$/u", $regs[1])) {
                $regs[1] .= 't';
            }
            return $regs[1];
        }
    }
}