<?php

namespace App\Library\Grammatic;

use App\Library\Grammatic\KarGram;

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
                if (preg_match("/^(.+)n$/", $lemma->wordform(10, $dialect_id), $regs)) { 
                    return $regs[1];
                }
                return '';
            case 5: //illative pl
                if (preg_match("/^(.+)h$/", $lemma->wordform(22, $dialect_id), $regs)) { 
                    return $regs[1];
                }
                return '';
        }
    }

    public static function gramsetListSg() {
        return [1,  3,  4, 277,  5,  8,  9, 10, 278, 12, 6,  14, 15];
    }

    public static function gramsetListPl() {
        return [2, 24, 22, 279, 59, 23, 60, 61, 280, 62, 64, 65, 66, 281];
    }

    public static function getListForAutoComplete() {
        return array_merge(self::gramsetListSg(), self::gramsetListPl());
    }
        
    public static function wordformByStems($stems, $gramset_id, $lang_id, $dialect_id, $name_num=null) {
        switch ($gramset_id) {
            case 2: // номинатив, мн.ч. 
                return $name_num == 'pl' ? $stems[0] : ($name_num != 'sg' && $stems[1] ? $stems[1].'t' : '');
        }
        
        if ($name_num !='pl' && in_array($gramset_id, self::gramsetListSg())) {
            return self::wordformByStemsSg($stems, $gramset_id, $lang_id, $dialect_id);
        }
        
        if ($name_num !='sg' && in_array($gramset_id, self::gramsetListPl())) {
            return self::wordformByStemsPl($stems, $gramset_id, $lang_id, $dialect_id);
        }
        return '';
    }
    
    public static function wordformByStemsSg($stems, $gramset_id, $lang_id, $dialect_id) {
        $stem1_i = preg_match("/i$/u", $stems[1]);
        
        switch ($gramset_id) {
            case 1: // номинатив, ед.ч. 
                return $stems[0];
            case 3: // генитив, ед.ч. 
                return $stems[1] ? $stems[1].'n' : '';
            case 4: // партитив, ед.ч. 
                return $stems[3] ? $stems[3] : '';
            case 277: // эссив, ед.ч. 
                return $stems[2] ? $stems[2]. 'n'. KarGram::garmVowel($stems[2],'a') : '';
            case 5: // транслатив, ед.ч. 
                return $stems[1] ? $stems[1] . ($stem1_i ? 'ksi' : 'kši') : '';
            case 8: // инессив, ед.ч. 
                return $stems[1] ? $stems[1] . ($stem1_i ? 'ss' : 'šš'). KarGram::garmVowel($stems[1],'a') : '';
            case 9: // элатив, ед.ч. 
                return $stems[1] ? $stems[1] . ($stem1_i ? 'st' : 'št'). KarGram::garmVowel($stems[1],'a') : '';
            case 10: // иллатив, ед.ч. 
                return $stems[2] ? $stems[2].'h' : '';
            case 278: // адессив-аллатив, ед.ч. 
                return $stems[1] ? $stems[1] . 'll'. KarGram::garmVowel($stems[1],'a') : '';
            case 12: // аблатив, ед.ч. 
                return $stems[1] ? $stems[1] . 'ld'. KarGram::garmVowel($stems[1],'a') : '';
            case 6: // абессив, ед.ч. 
                return $stems[1] ? $stems[1] . 'tt'. KarGram::garmVowel($stems[1],'a') : '';
            case 14: // комитатив, ед.ч. 
                return $stems[1] ? $stems[1].'nke' : '';
            case 15: // пролатив, ед.ч. 
                return $stems[1] ? $stems[1].'čči' : '';
        }
    }

    public static function wordformByStemsPl($stems, $gramset_id, $lang_id, $dialect_id, $name_num=null) {
        $stem5_oi = preg_match("/[oö]i$/u", $stems[5]);
        
        switch ($gramset_id) {
            case 24: // генитив, мн.ч. 
                return $stems[4] ? $stems[4]. 'n' : '';
            case 22: // партитив, мн.ч. 
                return $stems[5] ? $stems[5] . ($stem5_oi ? 'd'.KarGram::garmVowel($stems[5],'a') : 'e' ) : '';
            case 279: // эссив, мн.ч.
                return $stems[5] ? $stems[5] . 'n'. KarGram::garmVowel($stems[5],'a') : '';
            case 59: // транслатив, мн.ч. 
                return $stems[4] ? $stems[4].'ksi' : '';
            case 23: // инессив, мн.ч.
                return $stems[4] ? $stems[4] . 'ss'. KarGram::garmVowel($stems[4],'a') : '';
            case 60: // элатив, мн.ч.
                return $stems[4] ? $stems[4] . 'st'. KarGram::garmVowel($stems[4],'a') : '';
            case 61: // иллатив, мн.ч. 
                return $stems[5] ? $stems[5].'h' : '';
            case 280: // адессив-аллатив, мн.ч.
                return $stems[4] ? $stems[4] . 'll'. KarGram::garmVowel($stems[4],'a') : '';
            case 62: // аблатив, мн.ч.
                return $stems[4] ? $stems[4] . 'ld'. KarGram::garmVowel($stems[4],'a') : '';
            case 64: // абессив, мн.ч.
                return $stems[4] ? $stems[4] . 'tt'. KarGram::garmVowel($stems[4],'a') : '';
            case 65: // комитатив, мн.ч. 
                return $stems[4] ? $stems[4].'nke' : '';
            case 66: // пролатив, мн.ч. 
                return $stems[4] ? $stems[4].'čči' : '';
            case 281: // инструктив, мн.ч. 
                return $stems[4] ? $stems[4].'n' : '';
        }
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
     * @param type $lemma_str
     */
    public static function toRightTemplate($bases, $base_list, $lemma_str, $num) {
        if (!(sizeof($base_list)==3 || sizeof($base_list)==2 && $num=='sg' || sizeof($base_list)==1 && $num=='pl')) {
            return $lemma_str;
        }
        if (preg_match("/^([^\/\s]+)\s*[\/\:]\s*([^\s]+)$/", $base_list[0], $regs)) {
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
            } elseif (preg_match("/^([^\/\s]+)\s*[\/\:]\s*([^\s]+)$/", $base_list[2], $regs)) {
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
                return ['lda', 'ldä'];
            case 6: // абессив, ед.ч. 
            case 64: // абессив, мн.ч.
                return ['tta', 'ttä'];
            case 14: // комитатив, ед.ч. 
            case 65: // комитатив, мн.ч. 
                return ['nke'];
            case 15: // пролатив, ед.ч. 
            case 66: // пролатив, мн.ч. 
                return ['čči'];
            case 2: // номинатив, мн.ч. 
                return ['t'];
            case 22: // партитив, мн.ч. 
                return ['e', 'da', 'dä'];
            case 59: // транслатив, мн.ч. 
                return ['ksi'];
            case 23: // инессив, мн.ч.
                return ['ssa', 'ssä'];
            case 60: // элатив, мн.ч.
                return ['sta', 'stä'];
        }
    }
    
}