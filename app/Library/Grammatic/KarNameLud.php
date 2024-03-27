<?php

namespace App\Library\Grammatic;

use App\Library\Grammatic;
use App\Library\Grammatic\KarGram;
/*
use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;*/

/**
 * Functions related to Olonets Karelian grammatic for nominals: nouns, adjectives, numerals and pronouns.
 */
class KarNameLud
{
    public static function gramsetListSg() {
        return [1,  56, 3,  4, 277,  5, 6, 8,  9, 10,  11, 12, 13, 14, 15, 17, 16, 19];
    } // 17, 16, 19

    public static function gramsetListPl() {
        return [2, 57, 24, 22, 279, 59, 64, 23, 60, 61,  25, 62, 63, 65, 66, 281, 18, 67, 68];
    } // 18, 67, 68

    public static function initialStemsFromMiniTemplate($base, $ok, $stem0, $ps_list) {
        if (!sizeof($ps_list)) { // mua []
            return [$stem0, $stem0, $stem0];
        }        
        $stem1 = $base.$ps_list[0];
        $c = mb_substr($base,-1,1);
        if ($ok && $c == mb_substr($ok,0,1) && in_array($c, ['k','p','t','č','c','š','s','h'])) {
            $stem2 = $base. $c. $ps_list[0];
        } else {
            $stem2 = $stem1;
        }
        
        if (sizeof($ps_list) == 1) {
            $stem6 = $stem2;
        } else {
            $stem6 = $base.$ps_list[1];
        }
        return [$stem1, $stem2, $stem6];
    }
    
    public static function stem3FromMiniTemplate($stem6) {
        $V = "[".KarGram::vowelSet()."]";
        
        if (preg_match("/".$V."$/u", $stem6)) {
            return $stem6.'d';
        }    
        return $stem6.'te';
    }
    
    /**
     * А. Если о.1 состоит из одного слога и оканчивается на трифтонг, дифтонг или долгую гласную 
     *    (CVVV или CVV) , то  о.1+lUOi 
     * Б. Если о.6 оканч. на С, то = о.1 c конеч. заменами
     *  1) hte>ksi
     *  2) rde>rži
     *  3) de>zi
     *  4) A>i
     * В. Если о.6 оканч. на V, то =о.1 с заменами, если о.1 оканч. на
     *  1) CO и с.ф. оканч. на CO, то O>Oi, O>UOi
     *  2) Ca, то a>i, если в о.6
     *      а) два слога, в первом слоге есть u,o1 ИЛИ 
     *      б) более 2 слогов и о.6 оканч. на mpa/mba, ma ИЛИ является прилагательным с о.6 на va
     *  3) Ca, то a>oi, a>uoi, если в о.6
     *      а) два слога и в первом слоге есть a,e,i2 ИЛИ 
     *      б) более 2 слогов и о.6 НЕ оканч. на mpa/mba, ma и НЕ является прилагательным с о.6 на va
     *  4) Cä, то ä>i, если в о.6 
     *      a) два слога ИЛИ 
     *      б) более 2 слогов и о.6 оканч. на mä, vä, žä, sä, jä, pä, bä
     *  5) Cä, то ä>yöi, ä>öi, если в о.6 более 2 слогов и о.6 оканч. на  dä, tä, gä, kä, čä, hä, lä, nä, rä
     *  6) CAi, то Ai>Oi, Ai>UOi
     *  7) COi, то i>lOi, i>lUOi
     * Г. Если о.1 оканч. на Ce, то = о.1 с заменой e>i
     * Д. В остальных случаях, если о.6 оканч. на V, то о.1+i

     * 
     * @param string $stem1
     * @param string $stem5
     * @param string $stem6
     * @param boolean $harmony
     * @return string
     */
    public static function stemPlFromMiniTemplate($stem0, $stemSg, $stem6, $harmony, $pos_id) {
        $C = "[".KarGram::consSet()."]";
        $V = "[".KarGram::vowelSet()."]";
        $stem6_syll=KarGram::countSyllable($stem6);

        if (preg_match("/^".$C.$V."{2,3}$/u", $stemSg)) {            // А
            return $stemSg.KarGram::garmVowel($harmony,'luoi');
        }                                                     
          
        if (preg_match("/".$C."$/u", $stem6)) {                      // Б
            if (preg_match("/^(.+)hte$/u", $stemSg, $regs)) {        // Б.1
                return $regs[1].'ksi';

            } elseif (preg_match("/^(.+)rde$/u", $stemSg, $regs)) {  // Б.2
                return $regs[1].'rži';

            } elseif (preg_match("/^(.+)de$/u", $stemSg, $regs)) {   // Б.3
                return $regs[1].'zi';

            } elseif (preg_match("/^(.+)[aä]$/u", $stemSg, $regs)) { // Б.4                
                return $regs[1].'i';
            }            
        } else {                                                     // В
            if (preg_match("/^(.+)[oö]$/u", $stemSg, $regs)   // В.1
                    || preg_match("/^(.+)[aä]i$/u", $stem6, $regs)) {                           // В.6
                return $regs[1].KarGram::garmVowel($harmony,'oi').'/'.$regs[1].KarGram::garmVowel($harmony,'uoi');                
            } elseif (preg_match("/^(.+)a$/u", $stemSg, $regs)) {                                // В.2-3
                if ($stem6_syll==2 && preg_match("/^".$C."?".$V."?[uo]/u", $stem6)               // В.2а
                    || $stem6_syll>2 && (preg_match("/m[pb]?a$/u", $stem6) || $pos_id==1 && preg_match("/va$/u", $stem6))) { // В.2б
                        return $regs[1].'i';                
                } elseif ($stem6_syll==2 && preg_match("/^".$C."?".$V."?[aei]/u", $stem6)        // В.3а
                    || $stem6_syll>2 && !preg_match("/m[pb]?a$/u", $stem6) && !($pos_id==1 && preg_match("/va$/u", $stem6))) { // В.3б
                        return $regs[1].'oi/'.$regs[1].'uoi';     
                }
            } elseif (preg_match("/^(.+)ä$/u", $stemSg, $regs)) {                                // В.4-5
                if ($stem6_syll==2                                                               // В.4а
                    || $stem6_syll>2 && preg_match("/[mvžsjpb]ä$/u", $stem6)) {                  // В.4б
                        return $regs[1].'i';                
                } elseif ($stem6_syll>2 && preg_match("/[dtgkčhlnr]ä$/u", $stem6)) {             // В.5
                    return $regs[1].'yöi/'.$regs[1].'öi';                
                }
            } elseif (preg_match("/^(.+[oö])i$/u", $stem6, $regs)) {                             // В.7
                return $regs[1].KarGram::garmVowel($harmony,'loi').'/'.$regs[1].KarGram::garmVowel($harmony,'luoi');                
            } 
        }
            
        if (preg_match("/^(.+)e$/u", $stemSg, $regs)) {                // Г
            return $regs[1].'i';
        }
            
        return $stemSg.'i';                                            // Д                 
    }
                
    public static function wordformByStemsSg($stems, $gramset_id, $dialect_id) {
        switch ($gramset_id) {
            case 5: // транслатив, ед.ч. 
                return $stems[1] ? $stems[1].'ks, '.$stems[1].'kse' : '';
            case 8: // инессив, ед.ч. 
                return $stems[1] ? $stems[1].'s' : '';
            case 9: // элатив, ед.ч. 
                return $stems[1] ? $stems[1].'s, '. $stems[1].'spiäi' : '';
            case 11: // адессив, ед.ч. 
                return $stems[1] ? $stems[1].'l' : '';
            case 12: // аблатив, ед.ч. 
                return $stems[1] ? $stems[1].'l, '. $stems[1].'lpiäi' : '';
            case 13: // аллатив, ед.ч. 
                return $stems[1] ? $stems[1].'l, '. $stems[1].'le' : '';
            case 14: // комитатив, ед.ч. 
                return $stems[1] ? $stems[1].'nke' : '';
            case 15: // пролатив, ед.ч. 
                return $stems[1] ? $stems[1].'či' : '';
            case 16: //терминатив, ед.ч. 
                return $stems[2] ? $stems[2].'ssuai' : '';
            case 19: //адитив, ед.ч. 
                return $stems[2] ? $stems[2].'hpiäi' : '';
        }
        
        if (!isset($stems[10])) { return ''; }
        switch ($gramset_id) {
            case 277: // эссив, ед.ч. 
                return $stems[1] ? $stems[1].'n, '.$stems[1]. KarGram::garmVowel($stems[10],'nnu') : '';
            case 6: // абессив, ед.ч. 
                return $stems[1] ? $stems[1]. KarGram::garmVowel($stems[10],'ta') : '';
            case 17: //аппроксиматив, ед.ч. 
                return Grammatic::addEndToMultiBase($stems[1], KarGram::garmVowel($stems[10],'lloh, lloo, lluo'));
        }
    }

    public static function wordformByStemsPl($stems, $gramset_id, $name_num, $dialect_id) {
        switch ($gramset_id) {
            case 2: // номинатив, мн.ч. 
            case 57: // аккузатив, мн.ч. 
                return $name_num == 'pl' ? $stems[0] : ($stems[1] ? $stems[1]. 'd, '. $stems[1]. 't' : '');
            case 24: // генитив, мн.ч. 
                return Grammatic::addEndToMultiBase($stems[4], 'den');
            case 22: // партитив, мн.ч. 
                return Grammatic::addEndToMultiBase($stems[5], 'd');
            case 59: // транслатив, мн.ч. 
                return Grammatic::addEndToMultiBase($stems[4], 'ks, kse');
            case 23: // инессив, мн.ч.
                return Grammatic::addEndToMultiBase($stems[4], 's');
            case 60: // элатив, мн.ч.
                return Grammatic::addEndToMultiBase($stems[4], 's, spiäi');
            case 61: // иллатив, мн.ч. 
                return Grammatic::addEndToMultiBase($stems[5], 'h');
            case 25: // адессив, мн.ч.
                return Grammatic::addEndToMultiBase($stems[4], 'l');
            case 62: // аблатив, мн.ч.
                return Grammatic::addEndToMultiBase($stems[4], 'l, lpiäi');
            case 63: // аллатив, мн.ч.
                return Grammatic::addEndToMultiBase($stems[4], 'l, le');
            case 65: // комитатив, мн.ч. 
                return Grammatic::addEndToMultiBase($stems[4], 'neh');
            case 66: // пролатив, мн.ч. 
                return Grammatic::addEndToMultiBase($stems[4], 'či');
            case 281: // инструктив, мн.ч. 
                return Grammatic::addEndToMultiBase($stems[4], 'n');
            case 67: //терминатив, мн.ч. 
                return Grammatic::addEndToMultiBase($stems[5], 'ssuai');
            case 68: //адитив, мн.ч. 
                return Grammatic::addEndToMultiBase($stems[5], 'hpiäi');
        }
        
        if (!isset($stems[10])) { return ''; }
        switch ($gramset_id) {
            case 279: // эссив, мн.ч.
                return Grammatic::addEndToMultiBase($stems[4], KarGram::garmVowel($stems[10],'n, nnu'));
            case 64: // абессив, мн.ч. 
                return Grammatic::addEndToMultiBase($stems[4], KarGram::garmVowel($stems[10],'ta'));
            case 18: //аппроксиматив, мн.ч. 
                return Grammatic::addEndToMultiBase($stems[4], KarGram::garmVowel($stems[10],'lloh, lloo, lluo'));
        }
    }
    
    /** 
     * 
     * @param type $gramset_id
     * @return type
     */
    public static function getAffixesForGramset($gramset_id) {
        switch ($gramset_id) {
            case 3: // генитив, ед.ч. 
            case 281: // инструктив, мн.ч. 
                return ['n'];
            case 24: // генитив, мн.ч. 
                return ['den'];
            case 277: // эссив, ед.ч. 
            case 279: // эссив, мн.ч.
                return ['n', 'nnu', 'nny'];
            case 5: // транслатив, ед.ч. 
            case 59: // транслатив, мн.ч. 
                return ['ks', 'kse'];
            case 6: // абессив, ед.ч. 
            case 64: // абессив, ед.ч. 
                return ['ta', 'tä'];
            case 8: // инессив, ед.ч. 
            case 23: // инессив, мн.ч.
                return ['s'];
            case 9: // элатив, ед.ч. 
            case 60: // элатив, мн.ч.
                return ['s', 'spiäi'];
            case 10: // иллатив, ед.ч. 
            case 61: // иллатив, мн.ч. 
                return ['h'];
            case 11: // адессив, ед.ч. 
            case 25: // адессив, мн.ч.
                return ['l'];
            case 12: // аблатив, ед.ч. 
            case 62: // аблатив, мн.ч.
                return ['l', 'lpiäi'];
            case 63: // аллатив, мн.ч.
            case 13: // аллатив, ед.ч. 
                return ['l', 'le'];
            case 14: // комитатив, ед.ч. 
                return ['nke'];
            case 65: // комитатив, мн.ч. 
                return ['neh'];
            case 15: // пролатив, ед.ч. 
            case 66: // пролатив, мн.ч. 
                return ['či'];
            case 17: // апроксиматив, ед.ч. 
            case 18: // апроксиматив, мн.ч. 
                return ['lloh','lloo','lluo','llöh','llöö','llyö'];
            case 16: //терминатив, ед.ч. 
            case 68: //терминатив, мн.ч. 
                return ['ssuai'];
            case 19: //адитив, ед.ч. 
            case 68: //адитив, мн.ч. 
                return ['hpiäi'];
            case 2: // номинатив, мн.ч. 
                return ['d','t'];
            case 22: // партитив, мн.ч. 
                return ['d'];
        }
        return [];
    }
    
    // wordforms - словоформы без неизм. части, только аффиксы
    // [0=>3, 1=>10, 2=>4, 3=>1];    // 3=gen sg, 10=ill sg, 4=part sg, 1=nom sg 
    public static function templateFromWordforms($wordforms) { // , $number
        foreach ($wordforms as $gramset_id => $wordform) {
            if (preg_match("/^-(.+)$/", $wordform, $regs)) {
                $wordforms[$gramset_id] = $regs[1];
            }
        }
        if ($wordforms[3]=='n')  {
            $wordforms[3]='';
        } elseif (preg_match("/^(.*)n$/u", $wordforms[3], $regs)) {
            $wordforms[3]=$regs[1];
        } else {
            return null;
        }
        
        if ($wordforms[10]=='h')  {
            $wordforms[10]='';
        } elseif (preg_match("/^(.*)h$/u", $wordforms[10], $regs)) {
            $wordforms[10]=$regs[1];
        } else {
            return null;
        }
        
        if (!$wordforms[3] && $wordforms[4]=='d') {
            return " []";
        }
        if ($wordforms[3] && $wordforms[4]==$wordforms[10].'d') {
            return " [".$wordforms[3]."]";
        }
        if (preg_match("/^(.*)te$/u", $wordforms[4], $regs)) {
            return " [".$wordforms[3].", ".$regs[1]."]";
        }
    }
}