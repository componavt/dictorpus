<?php

namespace App\Library\Grammatic;

use App\Library\Grammatic;
use App\Library\Grammatic\KarGram;
use App\Library\Grammatic\KarVerbOlo;

use App\Models\Dict\Gramset;
//use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;

/**
 * Functions related to Karelian grammatic for verbs.
 */
class KarVerb
{
    /**
     * п.о. 1
     * 
     * @param type $wordform
     * @param type $is_reflexive
     * @return string
     */
    public static function parsePrs1Sg($wordform, $is_reflexive=false) {
        if (!$wordform) {
            return '';
        }
        $out = [];
        $V = "[".KarGram::vowelSet()."]";
        $words = preg_split("/\//",$wordform);
        foreach ($words as $word) {
            if (!$is_reflexive && preg_match("/^(.+".$V.")n$/u", $word, $regs)
            || $is_reflexive && preg_match("/^(.+".$V.")mm[oö]s$/u", $wordform, $regs)            
                ) {
            $out[] = $regs[1];
            } else {
                return '';
            }
        }
        return join('/',$out);
    }
    
    public static function stem1FromStem2($stem2) {
        $V="[".KarGram::vowelSet()."]";  
        $stem2 = preg_replace("/".$V."$/u", '', $stem2);
        $stem2 = preg_replace("/[oö]$/u", 'e', $stem2);
        if (preg_match("/^(.*t)t(".$V.")$/u", $stem2, $regs)) {
            return $regs[1].$regs[2];
        } elseif (preg_match("/^(.*r)d(".$V.")$/u", $stem2, $regs)) {
            return $regs[1].'r'.$regs[2];
        } elseif (preg_match("/^(.*)d(".$V.")$/u", $stem2, $regs)) {
            return $regs[1].'v'.$regs[2];
        } 
        
    }
    
    public static function stem1FromStem3($stem3, $is_reflexive=false) {
        $V="[".KarGram::vowelSet()."]";        
        return preg_replace("/([ktpšč]){2}(".$V."{1,2})/u", "$1$2", $stem3);
    }
    
    /**
     * п.о. 6
     * 
     * @param type $wordform
     * @return string
     */
    public static function parsePrs3Pl($wordform, $is_reflexive=null) {
        if (!$wordform) {
            return '';
        }
        $out = [];
        $words = preg_split("/\//",$wordform);
        foreach ($words as $word) {
            if (!$is_reflexive && preg_match("/^(.+)h$/u", $word, $regs)) {
                $out[] = $regs[1];
            }elseif ($is_reflexive && preg_match("/hes$/u", $word)) {
                $out[] = $word;
            }
        }
        return join('/',$out);
    }
    
    /**
     * п.о. 7
     * 
     * @param type $wordform
     * @return string
     */
    public static function parseImp3Pl($wordform, $is_reflexive=null) {
        if (!$wordform) {
            return '';
        }
        $out = [];
        $words = preg_split("/\//",$wordform);
        foreach ($words as $word) {
            if (!$is_reflexive && preg_match("/^(.+)ih$/u", $word, $regs)
                    || $is_reflexive && preg_match("/^(.+)ihes$/u", $word, $regs)) {
                $out[] = $regs[1];
            }
        }
        return join('/',$out);
    }
    
    /**
     * п.о.3 
     * 
     * = п.о.2 ($prs3sg) – u или y или bi 
     * Если получившаяся п.о.3 заканчивается на C[oö], и п.о.1 ($base_of_prs1sg) заканчивается на e, то в п.о.3 о/ö > e
     * 
     * @param type $base_of_prs1sg
     * @param type $prs3sg
     * @return string
     */
    public static function prsStrongVocalBase($base_of_prs1sg, $prs3sg) {
        if (!$base_of_prs1sg || !$prs3sg) {
            return '';
        }
        $out = [];
        $bases_of_prs1sg = preg_split("/\//",$base_of_prs1sg);
        $prs3sgs = preg_split("/\//",$prs3sg);
        for ($i=0; $i<sizeof($bases_of_prs1sg); $i++) {
            $base2 = isset($prs3sgs[$i]) ? $prs3sgs[$i] : $prs3sgs[0];
            if (!preg_match("/^(.+)[uy]$/u", $base2, $regs) 
                    && !preg_match("/^(.+)bi$/u", $base2, $regs)) {
                return '';
            }
            if (preg_match("/^(.*[".KarGram::consSet()."])[oö]$/u", $regs[1], $regs1) 
                    && preg_match("/^.+e$/u", $bases_of_prs1sg[$i])) {
                $regs[1] = $regs1[1].'e';
            }
            $out[]=$regs[1];
        }
        return join('/',$out);
    }
    
    public static function prsStrongVocalBaseRef($stem2) {
        if (preg_match("/^.+\s*\/\s*(.+)hes$/u", $stem2, $regs)
                || preg_match("/^([^\/]+)hes$/u", $stem2, $regs)) {
//            return preg_replace("/i$/", 'e', $regs[1]);
            return $regs[1];
        }
        return '';
    }
    
    /**
     * п.о.5 (слабая основа имперфекта)
     * 
     * А. если в п.о.4 перед конечными i или Vi НЕТ kk, tt, pp, čč, g, d, b, 
     * то =п.о.4 ($imp3sg)
     * Б. если в п.о.4 перед конечными i или Vi есть kk, tt, pp, čč, g, d, b, то
     *   1) если в п.о.1 ($base_of_prs1sg) один слог, то изменяем VV этого слога следующим образом: 
     *      ie > iji (если в п.о.4 вторая буква i), 
     *      ie > eji (если в п.о.4 инд. вторая буква e), 
     *      ua > avoi, 
     *      iä > ävöi
     *   2) если в п.о.1 >1 слогов, то =п.о.1 – конечный V или VV (все, что есть до первого согласного с конца) → + конечные i или Vi из  п.о.4
     * 
     * Для рефлексивных:
     * п.о.4 – hes (искомая форма) → Работаем с формой на hes (т.е. если 2 формы: pezih/pezihes, работаем со второй)
     * 
     * A. если в искомой форме (п.о.4–hes) перед конечными i или Vi НЕТ kk, tt, pp, čč, šš, g, d, b, то = искомая форма
     * 
     * Б. если в искомой форме перед конечными i или Vi есть kk, tt, pp, čč, šš, g, d, b, то
     * 1) если в п.о.1 один слог, то изменяем VV этого слога следующим образом:
     *      ie > iji (если в искомой форме вторая буква i), 
     *      ie > eji (если в искомой форме вторая буква e), 
     *      ua > avoi, 
     *      iä > ävöi
     * 2) если в п.о.1 > 1 слогов, то =п.о.1 – конечный V или VV (все, что есть до первого согласного с конца) → + конечные i или Vi из искомой формы 
     */
    public static function weakImpBase($stem1, $stem4, $stem8, $is_reflexive=null) {
        if (!$stem1 && !$is_reflexive || !$stem4) {
            return '';
        }
        $V = "[".KarGram::vowelSet()."]";
        $not_V = "[^".KarGram::vowelSet()."]";
        $sh_i = $V."?i"; 
        $out = [];
        $stems1 = preg_split("/\//",$stem1);
        if ($is_reflexive && preg_match("/^.*?\/?\s*([^\/]+)hes$/", $stem4, $regs)) {
            for ($i=0; $i<sizeof($stems1); $i++) {
                $stems4[$i] = $regs[1];
            }
        } else {
            $stems4 = preg_split("/\//",$stem4);
        }
        for ($i=0; $i<sizeof($stems1); $i++) {
            if (!preg_match('/kk'.$sh_i.'$|tt'.$sh_i.'$|pp'.$sh_i.'$|čč'.$sh_i.'$|šš'.$sh_i.'$|ss'.$sh_i.'$|[gdb]'.$sh_i.'$/u', $stems4[$i])){ // А
                    $out[]= $stems4[$i];
            } else {
                if (KarGram::countSyllable($stems1[$i])==1) { // Б1
                    if (preg_match("/^(.*)ie$/u", $stems1[$i], $regs)
                            && preg_match("/^.([ie])/u",$stems4[$i], $regs1)) {
                        $stems1[$i] = $regs[1]. $regs1[1].'ji';
                    } elseif (preg_match("/^(.*)ua$/u", $stems1[$i], $regs)) {
                        $stems1[$i] = $regs[1]. 'avoi';
                    } elseif (preg_match("/^(.*)iä$/u", $stems1[$i], $regs)) {
                        $stems1[$i] = $regs[1]. 'ävöi';
                    }
                } elseif(preg_match("/^(.+?)".$V."?".$V."$/u", $stems1[$i], $regs)
                        && (preg_match("/^.*?".$not_V."(".$sh_i.")$/u", $stems4[$i], $regs2))) { // Б2
                    $stems1[$i] = $regs[1].$regs2[1];
                }
                $out[]= $stems1[$i];
            }
        }
        return join('/',$out);
    }
    
    /**
     * п.о.8 (сильная гласная / согласная основа)
     * А. если с.ф. ($stem0) заканчивается на j[aä] и в c.ф. больше двух слогов, 
     *      то = п.о.7 ($imp3pl) – tt
     * Б. если с.ф. заканчивается на Сa или Cä (в том числе на ja или jä, в котором два слога), 
     *      то = п.о.7 – t или d
     * В. если с.ф. заканчивается на VV, то =п.о.3 ($stem3)
     */
    public static function vocalStrongCons($stem0, $stem3, $stem7) { 
        $V = "[".KarGram::vowelSet()."]";
        if (preg_match("/^(.*)j[aä]$/u", $stem0) && KarGram::countSyllable($stem0)>2) {
            if (preg_match("/^(.+)tt$/u", $stem7, $regs)) {
                return $regs[1];
            }
        } elseif (preg_match("/^(.*)[".KarGram::consSet()."][aä]$/u", $stem0)) {
            if (preg_match("/^(.+)[td]$/u", $stem7, $regs)) {
                return $regs[1];
            }
        } elseif (preg_match("/".$V.$V."$/u", $stem0)) {
            return $stem3;
        }
        return '';
    }
    
    /**
     * п.о.8 (сильная гласная / согласная основа) для рефлексивного глагола
     * 
     * с.ф. ($stem0) – kseh (искомая форма)
     * 
     * A. если искомая форма заканчивается на Сa или Cä, то 
     * 1) неизм. + ок5 (stem7) -t/d
     * 2) если в скобках только 2 формы, то второе вместо ок5 (stem4) – tihes/dihes
     * 
     * Б. если искомая форма заканчивается на VV, то п.о.8=п.о.3
     */
    public static function vocalStrongConsRef($stem0, $stem3, $stem7or4) { 
        if (!preg_match("/^(.+)kseh$/", $stem0, $regs)) {
            return '';
        }
        $stem0 = $regs[1];
        $V = "[".KarGram::vowelSet()."]";
        if (preg_match("/^(.*)[".KarGram::consSet()."][aä]$/u", $stem0)) {
            if (preg_match("/^(.+)[td](ihes)?$/u", $stem7or4, $regs)) {
                return $regs[1];
            }
        } elseif (preg_match("/".$V.$V."$/u", $stem0)) {
            return $stem3;
        }
        return '';
    }
    
    /**
     * regs = [
     *    0 => вся строка, совпавшая с шаблоном
     *    1 => неизменяемая часть леммы = основа инфинитива
     *    2 => изменяемая часть леммы = суффикс инфинитива
     *    3 => суффикс презенса 1 л. ед.ч.
     *    4 => суффикс презенса 3 л. ед.ч.
     *    5 => суффикс презенса 3 л. мн.ч.
     *    6 => суффикс имперфекта 3 л. ед.ч.
     *    7 => суффикс имперфекта 3 л. мн.ч.]
     * 
     * for example:
     * [  0 => "peit|työ (-yn, -tyy; -ytäh; -yi, -yttih)"
     *    1 => "peit"
     *    2 => "työ"
     *    3 => "-yn"
     *    4 => "-tyy"
     *    5 => "-ytäh"
     *    6 => "-yi"
     *    7 => "-yttih"
     * ]
     * 
     * stems = [0 => основа инфинитива, 
     *          1 => 'слабая гласная у одноосновных или сильная гласная у двуосновных',
     *          2 => 'индикатив презенс 3 л. ед.ч.',
     *          3 => 'сильная гласная основа презенса',
     *          4 => 'индикатив имперфект 3 л. ед.ч.',
     *          5 => 'слабая основа имперфекта',
     *          6 => 'пассивная основа, презенс 3л. мн.ч.',
     *          7 => 'сильноступенная пассивная основа, имперфект 3л. мн.ч.',
     *          8 => 'сильная гласная / согласная основа']
     * 
     * @param Array $regs
     * @return array 
     */
    public static function stemsFromTemplate($regs, $is_reflexive=false) { 
        $base = preg_replace('/ǁ/','',$regs[1]);
        $stems=['', '', '', '', '', '', '', '', ''];
        $stems[0] = $base. $regs[2]; //stem0 = infinitive
        $out = [$stems, null, $regs[0], null];
        
        $stems[1] = self::parsePrs1Sg(preg_replace("/-/", $base, $regs[3]), $is_reflexive); // stem1
        $stems[2] = preg_replace("/-/", $base, $regs[4]); // stem2
        $stems[3] = $is_reflexive ? self::prsStrongVocalBaseRef($stems[2])
                : self::prsStrongVocalBase($stems[1], $stems[2]); //stem3
        
        $stems[4] = preg_replace("/-/", $base, $regs[6]); // stem4
        $stems[6] = self::parsePrs3Pl(preg_replace("/-/", $base, $regs[5]), $is_reflexive); //stem6
        $stems[7] = self::parseImp3Pl(preg_replace("/-/", $base, $regs[7]), $is_reflexive); //stem7
        $stems[8] = $is_reflexive ? self::vocalStrongConsRef($stems[0], $stems[3], $stems[7])
                                    : self::vocalStrongCons($stems[0], $stems[3], $stems[7]); // stem8
        $stems[5] = self::weakImpBase($stems[1], $stems[4], $stems[8], $is_reflexive); //stem5
        return [$stems, null, $regs[1], $regs[2]];
    }

    /**
     * regs = [
     *    0 => вся строка, совпавшая с шаблоном
     *    1 => неизменяемая часть леммы = основа инфинитива
     *    2 => изменяемая часть леммы = суффикс инфинитива
     *    3 => суффикс презенса 3 л. ед.ч.
     *    4 => суффикс имперфекта 3 л. ед.ч.]
     * 
     * for example:
     * [  0 => "pakastu|o (-u; -i)"
     *    1 => "pakastu"
     *    2 => "o"
     *    3 => "-u"
     *    4 => "-i"
     * ]
     * 
     * stems = [0 => основа инфинитива, 
     *          1 => 'слабая гласная у одноосновных или сильная гласная у двуосновных',
     *          2 => 'индикатив презенс 3 л. ед.ч.',
     *          3 => 'сильная гласная основа презенса',
     *          4 => 'индикатив имперфект 3 л. ед.ч.',
     *          5 => 'слабая основа имперфекта',
     *          6 => 'пассивная основа, презенс 3л. мн.ч.',
     *          7 => 'сильноступенная пассивная основа, имперфект 3л. мн.ч.',
     *          8 => 'сильная гласная / согласная основа']
     * 
     * @param Array $regs
     * @return array
     */
    public static function stemsFromTemplateDef($regs, $is_reflexive=false) { 
        $base = preg_replace('/ǁ/','',$regs[1]);
        $stems=['', '', '', '', '', '', '', '', ''];
        $stems[0] = $base. $regs[2]; //stem0 = infinitive
        $out = [$stems, null, $regs[0], null];

        $stems[2] = preg_replace("/-/", $base, $regs[3]); // stem2
        if ($is_reflexive) {
            $stems[3] = self::prsStrongVocalBaseRef($stems[2]); //stem3
            $stems[1] = self::stem1FromStem3($stems[3]);
        } else {
            $stems[1] = self::stem1FromStem2($stems[2]);
            $stems[3] = self::prsStrongVocalBase($stems[1], $stems[2]); //stem3
        }
        $stems[4] = preg_replace("/-/", $base, $regs[4]); // stem4
        $stems[8] = $is_reflexive ? self::vocalStrongConsRef($stems[0], $stems[3], $stems[4])
                                    : self::vocalStrongCons($stems[0], $stems[3], $stems[7]); // stem8
        $stems[5] = self::weakImpBase($stems[1], $stems[4], $stems[8], $is_reflexive); //stem5
        return [$stems, 'def', $regs[1], $regs[2]];
    }

    /**
     * 0 = infinitive 1
     * 1 = base of indicative presence 1 sg (indicative presence 1 sg - 'n')
     * 2 = base of indicative presence 3 sg (3 infinitive illative - 'mah / mäh')
     * 3 = base of indicative imperfect 1 sg (indicative imperfect 1 sg - 'n')
     * 4 = indicative imperfect 3 sg
     * 5 = base of perfect  (2 active participle - 'n?[nlrsš]un')
     * 6 = base of indicative presence 3 pl (indicative presence 3 pl - 'h')
     * 7 = base of indicative imperfect 3 pl (indicative imperfect 3 pl - 'ih')

     * @param Lemma $lemma
     * @param Int $dialect_id
     * @return array
     */
    public static function stemsFromDB($lemma, $dialect_id) {
        for ($i=0; $i<8; $i++) {
            $stems[$i] = self::getStemFromWordform($lemma, $i, $dialect_id);
        }
        return $stems;
    }
        
    /**
     * 
     * @param array $stems
     * @param int $stem_n
     * @param int $dialect_id
     * @param string $lemma
     * @return string
     */
    public static function getStemFromStems($stems, $stem_n, $lang_id, $dialect_id, $lemma) {
        if ($lang_id==5) {
            return KarVerbOlo::getStemFromStems($stems, $stem_n, $dialect_id, $lemma);
        }
        return '';
    }
    
    public static function getStemFromWordform($lemma, $stem_n, $dialect_id) {
        switch ($stem_n) {
            case 0: 
                return $lemma->lemma;
            case 1:  // indicative presence 1 sg
                if (preg_match("/^(.+)n$/", $lemma->wordform(26, $dialect_id), $regs)) {
                    return preg_replace("/,\s*/", '/',$regs[1]);
                }
                return '';
            case 4: // indicative imperfect 3 sg
                $ind_imp_3_sg = $lemma->wordform(34, $dialect_id); 
                return $ind_imp_3_sg ? preg_replace("/,\s*/", '/',$ind_imp_3_sg) : '';
            case 6: // indicative presence 3 pl
                if (preg_match("/^(.+)h$/", $lemma->wordform(31, $dialect_id), $regs)) { 
                    return preg_replace("/,\s*/", '/',$regs[1]);
                }
                return '';
        }
        if ($lang_id==5) {
            return KarVerbOlo::getStemFromWordform($lemma, $stem_n, $dialect_id);
        }
        
        switch ($stem_n) {
            case 2: // 3 infinitive illative
                if (preg_match("/^(.+)m[aä]h$/u", $lemma->wordform(174, $dialect_id), $regs)) { 
                    return $regs[1];
                }
                return '';
            case 3: // indicative imperfect 1 sg
                if (preg_match("/^(.+)n$/", $lemma->wordform(32, $dialect_id), $regs)) {
                    return $regs[1];
                }
                return '';
            case 5: // 2 active participle
                if (preg_match("/^(.+)n?[nlrsš]un$/", $lemma->wordform(179, $dialect_id), $regs)) { 
                    return $regs[1];
                }
                return '';
            case 6: // indicative presence 3 pl
                if (preg_match("/^(.+)h$/", $lemma->wordform(31, $dialect_id), $regs)) { 
                    return $regs[1];
                }
                return '';
            case 7: // indicative imperfect 3 pl
                if (preg_match("/^(.+)ih$/", $lemma->wordform(37, $dialect_id), $regs)) {
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
    public static function getListForAutoComplete($lang_id) {
        if ($lang_id==5) {
            return KarVerbOlo::getListForAutoComplete();
        }
        return [26,   27,  28,  29,  30,  31, 295, 296, 
                70,   71,  72,  73,  78,  79, 
                32,   33,  34,  35,  36,  37, 297, 298, 
                80,   81,  82,  83,  84,  85, 
                86,   87,  88,  89,  90,  91,  92,  93,  94,  95,  96,  97,
                98,   99, 100, 101, 102, 103, 104, 105, 107, 108, 106, 109,
                      51,  52,       54,  55,       50,  74,       76,  77,  
                44,   45,  46,  47,  48,  49, 116, 117, 118, 119, 120, 121,
                135, 125, 136, 137, 138, 139, 140, 141, 142, 143, 144, 145,
                146, 147, 148, 149, 150, 151, 310, 311, 
                152, 153, 154, 155, 156, 157,
                158, 159, 160, 161, 162, 163, 164, 165, 166, 167, 168, 169,
                170, 171, 172, 173, 174, 175, 176, 177,
                178, 179, 282, 180, 181];
    }
    
    public static function wordformByStems($stems, $gramset_id, $lang_id, $dialect_id, $def=NULL) {
        switch ($gramset_id) {
            case 26: // 1. индикатив, презенс, 1 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[1], 'n') : '';
            case 27: // 2. индикатив, презенс, 2 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[1], 't') : '';        
            case 31: // 6. индикатив, презенс, 3 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[6],'h') : '';
            case 295: // 144. индикатив, презенс, коннегатив, ед.ч.
                return Grammatic::joinMorfToBases($stems[1], '');
            case 296: // 145. индикатив, презенс, коннегатив, мн.ч.
                return !$def ? Grammatic::joinMorfToBases($stems[6], ''): '';
                
            case 70: // 7. индикатив, презенс, 1 л., ед.ч., отриц. 
            case 71: // 8. индикатив, презенс, 2 л., ед.ч., отриц. 
            case 73: //10. индикатив, презенс, 1 л., мн.ч., отриц. 
            case 78: // 11. индикатив, презенс, 2 л., мн.ч., отриц. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), $stems[1]) : '';
            case 72: // 9. индикатив, презенс, 3 л., ед.ч., отриц. 
                return Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), $stems[1]);
            case 79: // 12. индикатив, презенс, 3 л., мн.ч., отриц. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm(79, $lang_id), $stems[6]) : '';
                
            case 37: // 18. индикатив, имперфект, 3 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[7], 'ih') : '';
                
            case 51: // 49. императив, 2 л., ед.ч., пол 
                return !$def ? Grammatic::joinMorfToBases($stems[1], ''): '';
            case 50: // 54. императив, 2 л., ед.ч., отр. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), $stems[1]) : '';
        }
        
        if ($lang_id==5) {
            return KarVerbOlo::wordformByStems($stems, $gramset_id, $dialect_id, $def);
        }
        
        $stem4_modify = self::stemModify($stems[4], $stems[0], "ua|iä", ['o'=>'a', 'i'=> ['a','ä']]);
        $U = KarGram::garmVowel($stems[10],'u');
        
        switch ($gramset_id) {
            case 28: // 3. индикатив, презенс, 3 л., ед.ч., пол. 
                return $stems[2] ? self::indPres1SingByStem($stems[2]) : '';
            case 29: // 4. индикатив, презенс, 1 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[1], KarGram::garmVowel($stems[10],'mma')) : '';
            case 30: // 5. индикатив, презенс, 2 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[1], KarGram::garmVowel($stems[10],'tta')) : '';

            case 32: // 13. индикатив, имперфект, 1 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[3], 'n') : '';
            case 33: // 14. индикатив, имперфект, 2 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[3], 't') : '';
            case 34: // 15. индикатив, имперфект, 3 л., ед.ч., пол. 
                return $stems[4] ? $stems[4] : '';
            case 35: // 16. индикатив, имперфект, 1 л., мн.ч., пол. 
                return !$def ? self::indImp1PlurByStem($stems[4]) : '';
            case 36: // 17. индикатив, имперфект, 2 л., мн.ч., пол. 
                return !$def ? self::indImp2PlurByStem($stems[4]) : '';
            case 297: // 146. индикатив, имперфект, коннегатив, ед.ч.
                return self::perfectForm($stems[5], $stems[10], $lang_id);
            case 298: // 147. индикатив, имперфект, коннегатив, мн.ч.
                return !$def ? self::indImperfConnegPl($stems[7], $stems[10]) : '';

            case 80: // 19. индикатив, имперфект, 1 л., ед.ч., отриц. 
            case 81: // 20. индикатив, имперфект, 2 л., ед.ч., отриц. 
            case 83: // 22. индикатив, имперфект, 1 л., мн.ч., отриц. 
            case 84: // 23. индикатив, имперфект, 2 л., мн.ч., отриц. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), self::perfectForm($stems[5], $stems[10], $lang_id)) : '';
            case 82: // 21. индикатив, имперфект, 3 л., ед.ч., отриц. 
                return Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), self::perfectForm($stems[5], $stems[10], $lang_id));
            case 85: // 24. индикатив, имперфект, 3 л., мн.ч., отриц. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm(85, $lang_id), self::indImperfConnegPl($stems[7], $stems[10])) : '';

            case 86: // 25. индикатив, перфект, 1 л., ед.ч., пол. 
            case 87: // 26. индикатив, перфект, 2 л., ед.ч., пол. 
            case 89: // 28. индикатив, перфект, 1 л., мн.ч., пол. 
            case 90: // 29. индикатив, перфект, 2 л., мн.ч., пол. 
                return !$def ? Grammatic::interLists(self::auxForm($gramset_id, $lang_id, $dialect_id), self::perfectForm($stems[5], $stems[10], $lang_id)) : '';
            case 88: // 27. индикатив, перфект, 3 л., ед.ч., пол. 
                return Grammatic::interLists(self::auxForm($gramset_id, $lang_id, $dialect_id), self::perfectForm($stems[5], $stems[10], $lang_id));
            case 91: // 30. индикатив, перфект, 3 л., мн.ч., пол. 
                return !$def ? Grammatic::interLists(self::auxForm(91, $lang_id, $dialect_id), Grammatic::joinMorfToBases($stems[7], $U)) : '';

            case 92: // 31. индикатив, перфект, 1 л., ед.ч., отриц. 
            case 93: // 32. индикатив, перфект, 2 л., ед.ч., отриц. 
            case 95: // 34. индикатив, перфект, 1 л., мн.ч., отриц. 
            case 96: // 35. индикатив, перфект, 2 л., мн.ч., отриц. 
                return !$def ? Grammatic::interLists(self::auxForm($gramset_id, $lang_id, $dialect_id, '-'), self::perfectForm($stems[5], $stems[10], $lang_id)) : '';
            case 94: // 33. индикатив, перфект, 3 л., ед.ч., отриц. 
                return Grammatic::interLists(self::auxForm($gramset_id, $lang_id, $dialect_id, '-'), self::perfectForm($stems[5], $stems[10], $lang_id));
            case 97: // 36. индикатив, перфект, 3 л., мн.ч., отриц. 
                return !$def ? Grammatic::interLists('ei ole ', Grammatic::joinMorfToBases($stems[7], $U)) : '';

            case 98: // 37. индикатив, плюсквамперфект, 1 л., ед.ч., пол. 
            case 99: // 38. индикатив, плюсквамперфект, 2 л., ед.ч., пол. 
            case 101: // 40. индикатив, плюсквамперфект, 1 л., мн.ч., пол. 
            case 102: // 41. индикатив, плюсквамперфект, 2 л., мн.ч., пол. 
                return !$def ? Grammatic::interLists(self::auxForm($gramset_id, $lang_id, $dialect_id), self::perfectForm($stems[5], $stems[10], $lang_id)) : '';
            case 100: // 39. индикатив, плюсквамперфект, 3 л., ед.ч., пол. 
                return Grammatic::interLists(self::auxForm($gramset_id, $lang_id, $dialect_id), self::perfectForm($stems[5], $stems[10], $lang_id));
            case 103: // 42. индикатив, плюсквамперфект, 3 л., мн.ч., пол. 
                return !$def ? Grammatic::interLists(self::auxForm(103, $lang_id, $dialect_id), Grammatic::joinMorfToBases($stems[7], $U)) : '';

            case 104: // 43. индикатив, плюсквамперфект, 1 л., ед.ч., отриц. 
            case 105: // 44. индикатив, плюсквамперфект, 2 л., ед.ч., отриц. 
            case 107: // 46. индикатив, плюсквамперфект, 1 л., мн.ч., отриц. 
            case 108: // 47. индикатив, плюсквамперфект, 2 л., мн.ч., отриц. 
                return !$def ? Grammatic::interLists(self::auxForm($gramset_id, $lang_id, $dialect_id, '-'), self::perfectForm($stems[5], $stems[10], $lang_id)) : '';
            case 106: // 45. индикатив, плюсквамперфект, 3 л., ед.ч., отриц. 
                return Grammatic::interLists(self::auxForm($gramset_id, $lang_id, $dialect_id, '-'), self::perfectForm($stems[5], $stems[10], $lang_id));
            case 109: // 48. индикатив, плюсквамперфект, 3 л., мн.ч., отриц. 
                return !$def ? Grammatic::interLists('ei oldu ', Grammatic::joinMorfToBases($stems[7], $U)) : '';

            case 54: // 52. императив, 2 л., мн.ч., пол 
                return !$def && $stems[5] ? self::imp2PlurPolByStem($stems[5], $stems[0], $dialect_id) : '';
            case 52: // 50. императив, 3 л., ед.ч., пол 
                return $stems[5] ? self::imp3SingPolByStem($stems[5], $stems[0], $dialect_id) : '';
            case 55: // 53. императив, 3 л., мн.ч., пол 
                return !$def && $stems[5] ? self::imp3SingPolByStem($stems[5], $stems[0], $dialect_id) : '';

            case 74: // 55. императив, 3 л., ед.ч., отр. 
                return Grammatic::interLists(Grammatic::negativeForm(74, $lang_id), self::imp3SingPolByStem($stems[5], $stems[0], $dialect_id));
            case 76: // 57. императив, 2 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm(76, $lang_id), self::imp2PlurPolByStem($stems[5], $stems[0], $dialect_id)) : '';
            case 77: // 58. императив, 3 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm(77, $lang_id), self::imp3SingPolByStem($stems[5], $stems[0], $dialect_id)) : '';

            case 44: // 71. кондиционал, имперфект, 1 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stem4_modify, 'zin') : '';
            case 45: // 72. кондиционал, имперфект, 2 л., ед.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stem4_modify, 'zit') : '';
            case 46: // 73. кондиционал, имперфект, 3 л., ед.ч., пол. 
                return $stems[4] ? self::condImp3SingPolByStem($stems[4], $stems[0], $stems[10], $dialect_id) : '';
            case 47: // 74. кондиционал, имперфект, 1 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stem4_modify, KarGram::garmVowel($stems[10],'zima')) : '';
            case 48: // 75. кондиционал, имперфект, 2 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stem4_modify, KarGram::garmVowel($stems[10],'zija')) : '';
            case 49: // 76. кондиционал, имперфект, 3 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[7], KarGram::garmVowel($stems[10],'a'). 'is’') : '';

            case 116: // 77. кондиционал, имперфект, 1 л., ед.ч., отр. 
            case 117: // 78. кондиционал, имперфект, 2 л., ед.ч., отр. 
            case 119: // 80. кондиционал, имперфект, 1 л., мн.ч., отр. 
            case 120: // 81. кондиционал, имперфект, 2 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), self::condImp3SingPolByStem($stems[4], $stems[0], $stems[10], $dialect_id)) : '';
            case 118: // 79. кондиционал, имперфект, 3 л., ед.ч., отр. 
                return Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), self::condImp3SingPolByStem($stems[4], $stems[0], $stems[10], $dialect_id));
            case 121: // 82. кондиционал, имперфект, 3 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm(121, $lang_id), Grammatic::joinMorfToBases($stems[7], KarGram::garmVowel($stems[10],'a'). 'is’')) : '';
                
            case 135: // 95. кондиционал, плюсквамперфект, 1 л., ед.ч., пол. 
                return !$def ? Grammatic::interLists('olizin', self::perfectForm($stems[5], $stems[10], $lang_id)) : '';
            case 125: // 96. кондиционал, плюсквамперфект, 2 л., ед.ч., пол. 
                return !$def ? Grammatic::interLists('olizit', self::perfectForm($stems[5], $stems[10], $lang_id)) : '';
            case 136: // 97. кондиционал, плюсквамперфект, 3 л., ед.ч., пол. 
                return Grammatic::interLists('olis’', self::perfectForm($stems[5], $stems[10], $lang_id));
            case 137: // 98. кондиционал, плюсквамперфект, 1 л., мн.ч., пол. 
                return !$def ? Grammatic::interLists('olizima', self::perfectForm($stems[5], $stems[10], $lang_id)) : '';
            case 138: // 99. кондиционал, плюсквамперфект, 2 л., мн.ч., пол. 
                return !$def ? Grammatic::interLists('olizija', self::perfectForm($stems[5], $stems[10], $lang_id)) : '';
            case 139: // 100. кондиционал, плюсквамперфект, 3 л., мн.ч., пол. 
                return !$def ? Grammatic::interLists('olis’', Grammatic::joinMorfToBases($stems[7], $U)) : '';
                
            case 140: // 101. кондиционал, плюсквамперфект, 1 л., ед.ч., отр. 
                return !$def ? Grammatic::interLists('en olis’', self::perfectForm($stems[5], $stems[10], $lang_id)) : '';
            case 141: // 102. кондиционал, плюсквамперфект, 2 л., ед.ч., отр. 
                return !$def ? Grammatic::interLists('et olis’', self::perfectForm($stems[5], $stems[10], $lang_id)) : '';
            case 142: // 103. кондиционал, плюсквамперфект, 3 л., ед.ч., отр. 
                return Grammatic::interLists('ei olis’', self::perfectForm($stems[5], $stems[10], $lang_id));
            case 143: // 104. кондиционал, плюсквамперфект, 1 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists('emmä olis’', self::perfectForm($stems[5], $stems[10], $lang_id)) : '';
            case 144: // 105. кондиционал, плюсквамперфект, 2 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists('että olis’', self::perfectForm($stems[5], $stems[10], $lang_id)) : '';
            case 145: // 106. кондиционал, плюсквамперфект, 3 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists('ei olis’', Grammatic::joinMorfToBases($stems[7], $U)) : '';
                
            case 146: // 107. потенциал, презенс, 1 л., ед.ч., пол. 
                return !$def ? self::potencialForm($stems[5], 'en', $lang_id, $dialect_id) : '';
            case 147: // 108. потенциал, презенс, 2 л., ед.ч., пол. 
                return !$def ? self::potencialForm($stems[5], 'et', $lang_id, $dialect_id) : '';
            case 148: // 109. потенциал, презенс, 3 л., ед.ч., пол. 
                return self::potencialForm($stems[5], KarGram::garmVowel($stems[10], 'ou'), $lang_id, $dialect_id);
            case 149: // 110. потенциал, презенс, 1 л., мн.ч., пол. 
                return !$def ? self::potencialForm($stems[5], 'emm'. KarGram::garmVowel($stems[10], 'a'), $lang_id, $dialect_id) : '';
            case 150: // 111. потенциал, презенс, 2 л., мн.ч., пол. 
                return !$def ? self::potencialForm($stems[5], 'ett'.KarGram::garmVowel($stems[10], 'a'), $lang_id, $dialect_id) : '';
            case 151: // 112. потенциал, презенс, 3 л., мн.ч., пол. 
                return !$def ? Grammatic::joinMorfToBases($stems[7], KarGram::garmVowel($stems[10], 'anneh')) : '';
            case 310: // 158. потенциал, презенс, коннегатив 
                return self::potencialForm($stems[5], 'e', $lang_id, $dialect_id);
            case 311: // 159. потенциал, презенс, коннегатив, 3 л. мн.ч.
                return !$def ? Grammatic::joinMorfToBases($stems[7], KarGram::garmVowel($stems[10], 'anne')) : '';

            case 152: // 113. потенциал, презенс, 1 л., ед.ч., отр. 
            case 153: // 114. потенциал, презенс, 2 л., ед.ч., отр. 
            case 155: // 116. потенциал, презенс, 1 л., мн.ч., отр. 
            case 156: // 117. потенциал, презенс, 2 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), self::potencialForm($stems[5], 'e', $lang_id, $dialect_id)) : '';
            case 154: // 115. потенциал, презенс, 3 л., ед.ч., отр. 
                return Grammatic::interLists(Grammatic::negativeForm($gramset_id, $lang_id), self::potencialForm($stems[5], 'e', $lang_id, $dialect_id));
            case 157: // 118. потенциал, презенс, 3 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists(Grammatic::negativeForm(157, $lang_id), Grammatic::joinMorfToBases($stems[7], KarGram::garmVowel($stems[10], 'anne'))) : '';
                
            case 158: // 119. потенциал, перфект, 1 л., ед.ч., пол. 
                return !$def ? Grammatic::interLists('lienen', self::perfectForm($stems[5], $stems[10], $lang_id)) : '';
            case 159: // 120. потенциал, перфект, 2 л., ед.ч., пол. 
                return !$def ? Grammatic::interLists('lienet', self::perfectForm($stems[5], $stems[10], $lang_id)) : '';
            case 160: // 121. потенциал, перфект, 3 л., ед.ч., пол. 
                return Grammatic::interLists('lienöy', self::perfectForm($stems[5], $stems[10], $lang_id));
            case 161: // 122. потенциал, перфект, 1 л., мн.ч., пол. 
                return !$def ? Grammatic::interLists('lienemmä', self::perfectForm($stems[5], $stems[10], $lang_id)) : '';
            case 162: // 123. потенциал, перфект, 2 л., мн.ч., пол. 
                return !$def ? Grammatic::interLists('lienettä', self::perfectForm($stems[5], $stems[10], $lang_id)) : '';
            case 163: // 124. потенциал, перфект, 3 л., мн.ч., пол. 
                return !$def ? Grammatic::interLists('lienöy', Grammatic::joinMorfToBases($stems[7], $U)) : '';
                
            case 164: // 125. потенциал, перфект, 1 л., ед.ч., отр. 
                return !$def ? Grammatic::interLists('en liene', self::perfectForm($stems[5], $stems[10], $lang_id)) : '';
            case 165: // 126. потенциал, перфект, 2 л., ед.ч., отр. 
                return !$def ? Grammatic::interLists('et liene', self::perfectForm($stems[5], $stems[10], $lang_id)) : '';
            case 166: // 127. потенциал, перфект, 3 л., ед.ч., отр. 
                return Grammatic::interLists('ei liene', self::perfectForm($stems[5], $stems[10], $lang_id));
            case 167: // 128. потенциал, перфект, 1 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists('emmä liene', self::perfectForm($stems[5], $stems[10], $lang_id)) : '';
            case 168: // 129. потенциал, перфект, 2 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists('että liene', self::perfectForm($stems[5], $stems[10], $lang_id)) : '';
            case 169: // 130. потенциал, перфект, 3 л., мн.ч., отр. 
                return !$def ? Grammatic::interLists('ei liene', Grammatic::joinMorfToBases($stems[7], $U)) : '';

            case 170: // 131. I инфинитив 
                return $stems[0];
            case 171: // 132. II инфинитив, инессив 
                return self::inf2Ines($stems[0], $stems[10]);
            case 172: // 133. II инфинитив, инструктив  
                return self::inf2Inst($stems[0]);
            case 173: // 134. III инфинитив, адессив
                return Grammatic::joinMorfToBases($stems[2], KarGram::garmVowel($stems[10], 'malla'));
            case 174: // 135. III инфинитив, иллатив 
                return Grammatic::joinMorfToBases($stems[2], KarGram::garmVowel($stems[10], 'mah'));
            case 175: // 136. III инфинитив, инессив 
                return Grammatic::joinMorfToBases($stems[2], KarGram::garmVowel($stems[10], 'mašša'));
            case 176: // 137. III инфинитив, элатив 
                return Grammatic::joinMorfToBases($stems[2], KarGram::garmVowel($stems[10], 'mašta'));
            case 177: // 138. III инфинитив, абессив 
                return Grammatic::joinMorfToBases($stems[2], KarGram::garmVowel($stems[10], 'matta'));
                
            case 178: // 139. актив, 1-е причастие 
                return Grammatic::joinMorfToBases(KarGram::replaceSingVowel($stems[2], 'e', 'i'), KarGram::garmVowel($stems[10], 'ja'));
            case 179: // 140. актив, 2-е причастие 
                return self::partic2active($stems[5], $lang_id);
            case 282: // 141. актив, 2-е причастие  (сокращенная форма); перфект (форма основного глагола)
                return self::perfectForm($stems[5], $stems[10], $lang_id);
            case 180: // 142. пассив, 1-е причастие 
                return !$def ? Grammatic::joinMorfToBases($stems[7], KarGram::garmVowel($stems[10], 'ava')) : '';
            case 181: // 143. пассив, 2-е причастие 
                return !$def ? Grammatic::joinMorfToBases($stems[7], $U) : '';
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
        if (!$stem) {
            return '';
        }
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
        if (!$stem) {
            return '';
        }
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

    public static function condImp3SingPolByStem($stem, $lemma, $harmony, $dialect_id) {
//dd("$stem, $lemma");
        if (preg_match("/^(.+)i$/u",$stem, $regs)) {
            if (preg_match("/(ua|iä)$/u",$lemma)) {
                return $regs[1]. KarGram::garmVowel($harmony,'a'). 'is’';
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
    public static function inf2Ines($stem, $harmony) {
        $stem_for_search = Grammatic::toSearchForm($stem);
        $last_let = mb_substr($stem_for_search, -1, 1);
        $before_last_let = mb_substr($stem_for_search, -2, 1);
        
        if (KarGram::isVowel($before_last_let) && KarGram::isVowel($last_let)) {
            return $stem. (KarGram::isBackVowels($stem) ? 's’s’a': 'ssä');
        } elseif (KarGram::isConsonant($before_last_let) && preg_match("/^(.+)[aä]$/u", $stem, $regs)) {
            return $regs[1]. 'ešš'. KarGram::garmVowel($harmony,'a');
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
    public static function perfectForm($stem, $harmony, $lang_id) {
        if (!$stem) {
            return '';
        }
        $stem_for_search = Grammatic::toSearchForm($stem);
        $last_let = mb_substr($stem_for_search, -1, 1);
        $before_last_let = mb_substr($stem_for_search, -2, 1);
        
        if (KarGram::isConsonant($before_last_let) && KarGram::isVowel($last_let)) {
            return $stem. 'n';
        } elseif (KarGram::isVowel($before_last_let) && KarGram::isVowel($last_let)) {
            return $stem. KarGram::garmVowel($harmony, 'nun');
        } elseif (in_array($last_let, ['n', 'l', 'r', 's', 'š'])) {
            return $stem. $last_let. KarGram::garmVowel($harmony, 'un');
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
        if (!$stem) {
            return '';
        }
        $stem_for_search = Grammatic::toSearchForm($stem);
        $last_let = mb_substr($stem_for_search, -1, 1);
        $before_last_let = mb_substr($stem_for_search, -2, 1);
        
        if (KarGram::isConsonant($before_last_let) && KarGram::isVowel($last_let)) {
            return $stem. 'nnun';
        } elseif (KarGram::isVowel($before_last_let) && KarGram::isVowel($last_let)) {
            return $stem. 'nun';
        } elseif (in_array($last_let, ['n', 'l', 'r', 's', 'š'])) {
            return $stem. $last_let. 'un';
        }
    }
    
    public static function auxForm($gramset_id, $lang_id, $dialect_id, $negative=null) {
        if ($lang_id != 4) {
            return '';
        }

        if (in_array($gramset_id,[86])) { // Perf1Sg
            $aux = 'olen';
        } elseif (in_array($gramset_id,[87])) { // Perf2Sg
            $aux =  'olet';
        } elseif (in_array($gramset_id,[88, 91])) { // Perf3Sg
            $aux =  'on';
        } elseif (in_array($gramset_id,[89])) { // Perf1Pl
            $aux =  'olemma';
        } elseif (in_array($gramset_id,[90])) { // Perf2Pl
            $aux = 'oletta';
        } elseif (in_array($gramset_id,[92,93,94,95,96,97])) { // PerfNeg
            $aux = 'ole';
            
        } elseif (in_array($gramset_id,[98])) { // Plus1Sg
            $aux = 'olin';
        } elseif (in_array($gramset_id,[99])) { // Plus2Sg
            $aux = 'olit';
        } elseif (in_array($gramset_id,[100])) { // Perf3Sg
            $aux = 'oli';
        } elseif (in_array($gramset_id,[101])) { // Plus1Pl
            $aux = 'olima';
        } elseif (in_array($gramset_id,[102])) { // Plus2Pl
            $aux = 'olija';
        } elseif (in_array($gramset_id,[103])) { // Plus3Pl
            $aux = 'oldih';
        } elseif (in_array($gramset_id,[104,105,107,108,106])) { // PlusNeg without PlusSgNeg
            $aux = 'ollun';
        } elseif (in_array($gramset_id,[109])) { // PlusSgNeg
            $aux = 'oldu';
            
        } elseif (in_array($gramset_id,[135])) { // CondPlus1Sg
            $aux = 'olizin';
        } elseif (in_array($gramset_id,[125])) { // CondPlus2Sg
            $aux = 'olizit';
        } elseif (in_array($gramset_id,[136,139, 140,141,142,143,144,145])) { // CondPerf3Sg, CondPlus3Pl, CondPlusNeg
            $aux = 'olis’';
        } elseif (in_array($gramset_id,[137])) { // CondPlus1Pl
            $aux = 'olizima';
        } elseif (in_array($gramset_id,[138])) { // CondPlus2Pl
            $aux = 'olizija';
            
        } elseif (in_array($gramset_id,[158])) { // PotPerf1Sg
            $aux = 'lienen';
        } elseif (in_array($gramset_id,[159])) { // PotPerf2Sg
            $aux = 'lienet';
        } elseif (in_array($gramset_id,[160,163])) { // PotPerf3Sg
            $aux = 'lienöy';
        } elseif (in_array($gramset_id,[161])) { // PotPerf1Pl
            $aux = 'lienemmä';
        } elseif (in_array($gramset_id,[162])) { // PotPerf2Pl
            $aux = 'lienettä';
        } elseif (in_array($gramset_id,[164,165,166,167,168,169])) { // PotPerfNeg
            $aux = 'liene';
        }
        if (!isset($aux)) {
            return '';
        } elseif ($negative=='-') {
            return Grammatic::interLists(trim(Grammatic::negativeForm($gramset_id, $lang_id)), $aux);
        } 
        return $aux.' ';
        
/*        $lemma = 'olla';
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

        $aux_gramset = Gramset::where('gram_id_mood', $gramset->gram_id_mood)
                              ->where('gram_id_person', $gramset->gram_id_person)
                              ->where('gram_id_number', $aux_number)
                              ->where('gram_id_negation', $gramset->gram_id_negation)
                              ->where('gram_id_tense', $aux_tense)->first();
        if (!$aux_gramset) {
            return '';
        }
        $aux_wordform = $aux_lemma->wordforms()
                ->wherePivot('dialect_id', $dialect_id)
                ->wherePivot('gramset_id', $aux_gramset->id)->first();
        if (!$aux_wordform) {
            return '';
        }
        return $aux_wordform->wordform. ' ';*/
    }
    
    public static function indImperfConnegPl($stem7, $harmony) {
        if (!$stem7) { return ''; }
        return $stem7. KarGram::garmVowel($harmony,'u');
    }

    /**
     * 
     * @param String $stem
     * @param String $affix
     * @param Int $lang_id
     * @param Int $dialect_id
     */
    public static function potencialForm($stem, $affix, $lang_id, $dialect_id) {
        if (!$stem) {
            return '';
        }
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