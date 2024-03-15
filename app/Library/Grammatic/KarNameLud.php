<?php

namespace App\Library\Grammatic;

//use App\Library\Grammatic;
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

    public static function stems1_2_3FromMiniTemplate($base, $stem0, $ps_list) {
        if (!sizeof($ps_list)) { // mua []
            return [$stem0, $stem0, $stem0.'d'];
        }        
        $V = "[".KarGram::vowelSet()."]";
        $ps1 = preg_split("/\s*\/\s*/", $ps_list[0]);
        $stem1 = $base.$ps1[0];
        $stem2 = empty($ps1[1]) ? $stem1 : $base.$ps1[1];
        $stem3 = empty($ps_list[1]) ? $stem1.'d' : $base.$ps_list[1];
        
        return [$stem1, $stem2, $stem3];
    }
    
    /* partitive sg не нужна, изменили шаблон
     * 
     * А. Если скобки пустые, то = о.1+ d
     * Б. Если в скобках два п.о., то неизм. + п.о.2 + d
     * В.  = о.1 с конечными заменами
     * 1) že >  šte,
     * 2) he > hte, если с.ф. оканчивается на V
     *     he > ste, если с.ф. оканчивается на C
     * 3) hA > ste,
     *     pse > ste, 
     *     kse > ste,
     * 4) rde > rte,
     *      re > rte,
     * 5) dV > tte,
     * 6) le > lte,
     * 7) me > nte,
     *     ne > nte
     * 8) ttOma > tOnte
     * 9) ttAre > tArte
     * 10) ttAhA : tAste
     */
/*    public static function stem3FromMiniTemplate($base, $stem0, $stem1, $ps_list) {
        $C = "[".KarGram::consSet()."]";
        $V = "[".KarGram::vowelSet()."]";
        
        if (empty($ps_list) || empty($ps_list[0])) { // A. mua []
            return $stem1.'d';
        } elseif(!empty($ps_list[1])) { // Б
            return $base.$ps_list[1].'d';
        } 
        
        if (preg_match("/^(.+)iže$/u", $stem1, $regs)) { // В.1
            return $regs[1].'šte';
            
        } elseif (preg_match("/^(.+)he$/u", $stem1, $regs)) { // В.2
            if (preg_match("/".$V."$/u", $stem1)) {
                return $regs[1].'hte';
            } else {
                return $regs[1].'ste';
            }
            
        } elseif (preg_match("/^(.+)h[aä]$/u", $stem1, $regs) 
               || preg_match("/^(.+)[pk]se$/u", $stem1, $regs)) { // В.3
            return $regs[1].'ste'; 
            
        } elseif (preg_match("/^(.+)rd?e$/u", $stem1, $regs)) { // В.4
            return $regs[1].'rte';
            
        } elseif (preg_match("/^(.+)d".$V."$/u", $stem1, $regs)) { // В.5
            return $regs[1].'tte';
            
        } elseif (preg_match("/^(.+)le$/u", $stem1, $regs)) { // В.6
            return $regs[1].'lte';
            
        } elseif (preg_match("/^(.+)[mn]e$/u", $stem1, $regs)) { // В.7
            return $regs[1].'nte';
            
        } elseif (preg_match("/^(.+)tt([oö])m[aä]$/u", $stem1, $regs)) { // В.8
            return $regs[1].'t'.$regs[2].'nte';
            
        } elseif (preg_match("/^(.+)tt([aä])re$/u", $stem1, $regs)) { // В.8
            return $regs[1].'t'.$regs[2].'rte';
            
        } elseif (preg_match("/^(.+)tt([aä])h[aä]$/u", $stem1, $regs)) { // В.8
            return $regs[1].'t'.$regs[2].'ste';
        }
        return $stem1;
    }*/
    
    /**
     * А. Если о.1 состоит из одного слога и оканчивается на трифтонг, дифтонг или долгую гласную 
     *    (CVVV или CVV) , то  о.1+lUOi 
     * Б. В остальных случаях  о.1+i, при этом если о.1 заканч. на
     * 1) O и с.ф. оканчивается на O, то O>UO
     * 2) ka, то a>uo
     * 3) Ai, то Ai>UO
     * 4) Oi, то Oi>OLUO
     * 5) hte, то hte>ks
     * 6) rde, то rde>rž
     * 7) de, то de>z
     * 8) A, то -A
     * 9) e, то -e
     * 
     * @param string $stem1
     * @param string $stem5
     * @param string $stem6
     * @param boolean $harmony
     * @return string
     */
    public static function stem4FromMiniTemplate($stem0, $stem1, $harmony) {
        $C = "[".KarGram::consSet()."]";
        $V = "[".KarGram::vowelSet()."]";

        if (preg_match("/^".$C.$V."{2,3}$/u", $stem1)) {            // А
            return $stem1.KarGram::garmVowel($harmony,'luoi');
        }                                                     
            
        if (preg_match("/^(.+)[oö]$/u", $stem1, $regs) && preg_match("/[oö]$/u", $stem0) // Б.1
            || preg_match("/^(.+k)a$/u", $stem1, $regs) // Б.2
            || preg_match("/^(.+)[aä]i$/u", $stem1, $regs)) { // Б.3
            return $regs[1].KarGram::garmVowel($harmony,'uoi');
            
        } elseif (preg_match("/^(.+)[oö]i$/u", $stem1, $regs)) { // Б.4
            return $regs[1].KarGram::garmVowel($harmony,'oluoi');
            
        } elseif (preg_match("/^(.+)hte$/u", $stem1, $regs)) { // Б.5
            return $regs[1].'ksi';
            
        } elseif (preg_match("/^(.+)rde$/u", $stem1, $regs)) { // Б.6
            return $regs[1].'rži';
            
        } elseif (preg_match("/^(.+)de$/u", $stem1, $regs)) { // Б.7
            return $regs[1].'zi';
            
        } elseif (preg_match("/^(.+)[aä]$/u", $stem1, $regs) // Б.8            
            || preg_match("/^(.+)e$/u", $stem1, $regs)) { // Б.9
            return $regs[1].'i';
        }
        return $stem1.'i';        
    }
    
    /**
     * А. Если о.2 состоит из одного слога и оканчивается на трифтонг, дифтонг или долгую гласную 
     *    (CVVV или CVV) , то  о.2+lUOi 
     * Б. В остальных случаях  о.2+i, при этом если о.2 заканч. на
     * 1) O и с.ф. оканчивается на O, то O>UO
     * 2) ka, то a>uo
     * 3) Ai, то Ai>UO
     * 4) Oi, то Oi>OLUO
     * 5) hte, то hte>ks
     * 6) rde, то rde>rž
     * 7) de, то de>z
     * 8) A, то -A
     * 9) e, то -e
     * 
     * @param string $stem1
     * @param string $stem5
     * @param string $stem6
     * @param boolean $harmony
     * @return string
     */
    public static function stem5FromMiniTemplate($stem0, $stem2, $harmony) {
        $C = "[".KarGram::consSet()."]";
        $V = "[".KarGram::vowelSet()."]";

        if (preg_match("/^".$C.$V."{2,3}$/u", $stem2)) {            // А
            return $stem2.KarGram::garmVowel($harmony,'luoi');
        }                                                     
            
        if (preg_match("/^(.+)[oö]$/u", $stem2, $regs) && preg_match("/[oö]$/u", $stem0) // Б.1
            || preg_match("/^(.+k)a$/u", $stem2, $regs) // Б.2
            || preg_match("/^(.+)[aä]i$/u", $stem2, $regs)) { // Б.3
            return $regs[1].KarGram::garmVowel($harmony,'uoi');
            
        } elseif (preg_match("/^(.+)[oö]i$/u", $stem2, $regs)) { // Б.4
            return $regs[1].KarGram::garmVowel($harmony,'oluoi');
            
        } elseif (preg_match("/^(.+)hte$/u", $stem2, $regs)) { // Б.5
            return $regs[1].'ksi';
            
        } elseif (preg_match("/^(.+)rde$/u", $stem2, $regs)) { // Б.6
            return $regs[1].'rži';
            
        } elseif (preg_match("/^(.+)de$/u", $stem2, $regs)) { // Б.7
            return $regs[1].'zi';
            
        } elseif (preg_match("/^(.+)[aä]$/u", $stem2, $regs) // Б.8            
            || preg_match("/^(.+)e$/u", $stem2, $regs)) { // Б.9
            return $regs[1].'i';
        }
        return $stem2.'i';        
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
                return self::approxPl($stems[1],$stems[10]);
        }
    }

    public static function wordformByStemsPl($stems, $gramset_id, $name_num, $dialect_id) {
        switch ($gramset_id) {
            case 2: // номинатив, мн.ч. 
            case 57: // аккузатив, мн.ч. 
                return $name_num == 'pl' ? $stems[0] : ($stems[1] ? $stems[1]. 'd, '. $stems[1]. 't' : '');
            case 24: // генитив, мн.ч. 
                return $stems[4] ? $stems[4]. 'den' : '';
            case 22: // партитив, мн.ч. 
                return $stems[5] ? $stems[5]. 'd' : '';
            case 59: // транслатив, мн.ч. 
                return $stems[4] ? $stems[4].'ks, '.$stems[4].'kse' : '';
            case 23: // инессив, мн.ч.
                return $stems[4] ? $stems[4]. 's' : '';
            case 60: // элатив, мн.ч.
                return $stems[4] ? $stems[4].'s, '.$stems[4].'spiäi' : '';
            case 61: // иллатив, мн.ч. 
                return $stems[5] ? $stems[5]. 'h' : '';
            case 25: // адессив, мн.ч.
                return $stems[4] ? $stems[4]. 'l' : '';
            case 62: // аблатив, мн.ч.
                return $stems[4] ? $stems[4].'l, '.$stems[4].'lpiäi' : '';
            case 63: // аллатив, мн.ч.
                return $stems[4] ? $stems[4].'l, '.$stems[4].'le' : '';
            case 65: // комитатив, мн.ч. 
                return $stems[4] ? $stems[4]. 'neh' : '';
            case 66: // пролатив, мн.ч. 
                return $stems[4] ? $stems[4]. 'či' : '';
            case 281: // инструктив, мн.ч. 
                return $stems[4] ? $stems[4]. 'n' : '';
            case 67: //терминатив, мн.ч. 
                return $stems[5] ? $stems[5].'ssuai' : '';
            case 68: //адитив, мн.ч. 
                return $stems[5] ? $stems[5].'hpiäi' : '';
        }
        
        if (!isset($stems[10])) { return ''; }
        switch ($gramset_id) {
            case 279: // эссив, мн.ч.
                return $stems[4] ? $stems[4].'n, '.$stems[4]. KarGram::garmVowel($stems[10],'nnu') : '';
            case 64: // абессив, мн.ч. 
                return $stems[4] ? $stems[4]. KarGram::garmVowel($stems[10],'ta') : '';
            case 18: //аппроксиматив, мн.ч. 
                return self::approxPl($stems[4],$stems[10]);
        }
    }
    
    public static function approxPl($stem, $harmony) {
        if (!$stem) {
            return '';
        }
        
        $w[] =  $stem. KarGram::garmVowel($harmony,'lloh');
        $w[] =  $stem. KarGram::garmVowel($harmony,'lloo');
        $w[] =  $stem. KarGram::garmVowel($harmony,'lluo');
        sort($w);
        
        return join(', ', $w);
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
        
        if ($wordforms[3] != $wordforms[10]) {
            $wordforms[3] .= '/'.$wordforms[10];
        }
        
        if (!$wordforms[3] && $wordforms[4]=='d') {
            return " []";
        }
        if ($wordforms[3] && $wordforms[4]==$wordforms[3].'d') {
            return " [".$wordforms[3]."]";
        }
        return " [".$wordforms[3].", ".$wordforms[4]."]";
    }
}