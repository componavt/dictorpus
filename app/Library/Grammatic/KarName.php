<?php

namespace App\Library\Grammatic;

use App\Library\Grammatic;
use App\Library\Grammatic\KarGram;
use App\Library\Grammatic\KarNameOlo;

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
    
    public static function getStemFromStems($stems, $stem_n, $dialect_id) {
        switch ($stem_n) {
            case 2: 
                return isset($stems[0]) && isset($stems[1]) && $stems[1] && isset($stems[3]) && $stems[3] 
                    ?  self::illSgBase($stems[0],$stems[1],$stems[3]) : '';
            case 4: 
                return isset($stems[1]) && isset($stems[5]) && isset($stems[10]) ?  self::genPlBase($stems[1], $stems[5], $stems[10]) : '';
            default: 
                return null;
        }
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
                return self::getStem4FromWordform($lemma, $dialect_id);
            case 5: // illative pl (tver) OR partitive pl (olo)
                return self::getStem5FromWordform($lemma, $dialect_id);
        }
    }
    
    public static function getStem4FromWordform($lemma, $dialect_id) {
        $genPls = preg_split('/,/', $lemma->wordform(24, $dialect_id));
        $stems4=[];
        foreach ($genPls as $g) {
            if ($lemma->lang_id == 4) {
                if (preg_match("/^(.+)n$/", trim($g), $regs)) { 
                    $stems4[] = $regs[1];
                }            
            } else {
                if (preg_match("/^(.+?)e??n$/u", trim($g), $regs)) {
                    $stems4[] = $regs[1];
                }
            }
        }
        return join('/', $stems4);
    }
    
    public static function getStem5FromWordform($lemma, $dialect_id) {
        if ($lemma->lang_id == 4) { // illative pl (tver)
            if (preg_match("/^(.+)h$/", $lemma->wordform(61, $dialect_id), $regs)) { 
                return $regs[1];
            }            
        } else { // partitive pl
            $partPls = preg_split('/,/', $lemma->wordform(22, $dialect_id));
            $stems5=[];
            foreach ($partPls as $p) {
                $stems5[] = preg_replace('/ii$/', 'i', trim($p));            
            }
            return join('/', $stems5);
        }
        return '';
    }
    
    public static function gramsetListSg($lang_id) {
        if ($lang_id==5) {
            return KarNameOlo::gramsetListSg();
        }
        return [1,  56, 3,  4, 277,  5,    8,  9, 10, 278, 12,  6, 14, 15];
    }

    public static function gramsetListPl($lang_id) {
        if ($lang_id==5) {
            return KarNameOlo::gramsetListPl();
        }
        return [2, 57, 24, 22, 279, 59,     23, 60, 61, 280, 62, 64, 65, 66, 281];
    }

    public static function getListForAutoComplete($lang_id) {
        return array_merge(self::gramsetListSg($lang_id), self::gramsetListPl($lang_id));
    }
        
    public static function stemsFromTemplate($template, $lang_id, $pos_id, $name_num) {      
        $base_shab = "([^\s\(\]\|]+)";
        $base_suff_shab = "([^\s\(\]\|]*)";
//        $okon1_shab = "(-?[^\-\,\;\)]+)";
        $okon3_shab = "(-?[^\-\,\;\)]+?\/?-?[^\-\,\;\)]*)";
//        $lemma_okon1_shab = "/^".$base_shab."\|?".$base_suff_shab."\s*\(".$okon1_shab;
        $lemma_okon3_shab = "/^".$base_shab."\|?".$base_suff_shab."\s*\(".$okon3_shab;
        
        // mini template
        if (preg_match("/^".$base_shab."\|?".$base_suff_shab."\s*\[([^\]]*)\]/", $template, $regs)) {
            return self::stemsFromMiniTemplate($lang_id, $pos_id, $regs);
        // only plural
        } elseif ($name_num == 'pl' && preg_match($lemma_okon3_shab.",\s*".$okon3_shab."\)/", $template, $regs)) {
//            $name_num = 'pl';
            return  self::stemsPlFromTemplate($regs);
        // only single
        } elseif ($name_num == 'sg' && preg_match($lemma_okon3_shab.",\s*".$okon3_shab."\)/", $template, $regs)) {
            return  self::stemsSgFromTemplate($regs);
        // others
        } elseif (preg_match($lemma_okon3_shab.",\s*".$okon3_shab.";\s*".$okon3_shab."\)/", $template, $regs)) {
            $name_num = '';
            return self::stemsOthersFromTemplate($regs);
        } else {
            return Grammatic::getAffixFromtemplate($template, $name_num);
        }
    }

    /**
     * mua []
     * nuor|i [e, ]
     * lyhy|t [ö, t]
     * ve|si [je/te, t]
     * 
     * @param type $regs = [0=>template, 1=>base, 2=>base-suff, 3=>list_of_pseudostems]
     */
    public static function stemsFromMiniTemplate($lang_id, $pos_id, $regs) {
//dd("!!!!");        
        $base = preg_replace('/ǁ/','',$regs[1]);
        $stem0 = $base.$regs[2];
        $out = [[$stem0], null, $regs[0], null];
        $ps_list = preg_split("/\s*,\s*/", $regs[3]);
        $harmony = KarGram::isBackVowels($regs[1].$regs[2]); // harmony
        $stem0_syll=KarGram::countSyllable($regs[1].$regs[2]);
        
        list ($stem1, $stem6, $ps1) = self::stems1And6FromMiniTemplate($base, $stem0, $ps_list);
        if (!$stem6) {
            return $out;
        }
        $stem2 = self::stem2FromMiniTemplate($base, $stem1, $stem6, isset($ps1[1]) ? $ps1[1]: null);
        $stem3 = self::stem3FromMiniTemplate($stem6, $lang_id, $harmony);
        $stem5 = self::stem5FromMiniTemplate($stem0, $stem1, $stem6, $lang_id, $pos_id, $harmony, $stem0_syll);
        $stem4 = self::stem4FromMiniTemplate($stem1, $stem5, $stem6, $harmony);
        
        $stems = [0 => $stem0,
                  1 => $stem1, // single genetive base 
                  2 => $stem2, // single illative base
                  3 => $stem3,
                  4 => $stem4,
                  5 => $stem5,
                  6 => $stem6,
                  10 => $harmony
            ];
        return [$stems, null, $regs[1], $regs[2]];
    }
    
    public static function stems1And6FromMiniTemplate($base, $stem0, $ps_list) {
        if (!sizeof($ps_list)) { // mua []
            $stem1 = $stem6 = $stem0;
            $ps1=null;
        } else {
            $V = "[".KarGram::vowelSet()."]";
            $ps1 = preg_split("/\s*\/\s*/", $ps_list[0]);
            $stem1 = $base.$ps1[0];
//dd($ps_list);            
            if (isset($ps_list[1])) {
                $stem6 = $base.$ps_list[1];
            } elseif(preg_match("/^(.+)".$V."$/", $stem0, $regs0) 
                    && preg_match("/(".$V.")$/", $stem1, $regs1)) {
                $stem6 = $regs0[1]. $regs1[1];
            } else {
                $stem6 = null;
            }
        }
        return [$stem1, $stem6, $ps1];
    }
    
    public static function stem2FromMiniTemplate($base, $stem1, $stem6, $ps1_2) {
        $V = "[".KarGram::vowelSet()."]";
        if (preg_match("/".$V."$/", $stem6)) {
            return $stem6;
        } elseif ($ps1_2) {
            return $base.$ps1_2;
        } else {
            return $stem1;
        }
        
    }
    
    // partitive sg
    public static function stem3FromMiniTemplate($stem6, $lang_id, $harmony) {
        $C = "[".KarGram::consSet()."]";
        $V = "[".KarGram::vowelSet()."]";
//        if (preg_match("/^(.+?".$C."’?)(".$V.")$/", $stem6, $regs)) {
//dd($stem6, $lang_id, $harmony);        
        if (preg_match("/^(.+".$C.")(".$V.")$/u", $stem6, $regs)) {
//dd($regs);            
            if (in_array($regs[2], ['a', 'o'])) {
                return $regs[1].'u'.$regs[2];
            } elseif (in_array($regs[2], ['y', 'ö'])) {
                return $regs[1].'yö';
            } elseif ($regs[2]== 'e') {
                return $regs[1].'ie';
            } elseif ($regs[2] =='u') {
                return $regs[1].'uo';
            } elseif ($regs[2]=='ä' && $lang_id==5) { // livvic
                return $regs[1].'iä';
            } elseif ($regs[2]=='ä') { // proper
                return $regs[1].'yä';
            } elseif ($regs[2] =='i' && $lang_id==5) { // livvic
                return $regs[1].'ii';
            } elseif ($regs[2] =='i') { // proper
                return $regs[1].'ie';
            }
        } elseif (preg_match("/".$V.$V."$/u", $stem6)) {
            if ($lang_id == 5) {
                return $stem6.KarGram::garmVowel($harmony,'du');
            } else {
                return $stem6.KarGram::garmVowel($harmony,'ta');                
            }
        } else { // ending by consonant
            if ($lang_id == 4) { // proper
                return $stem6.KarGram::garmVowel($harmony,'ta');                
            } elseif (preg_match("/[lnr]$/u", $stem6)) {
                return $stem6.KarGram::garmVowel($harmony,'du');
            } elseif (preg_match("/[hst]$/u", $stem6)) {
                return $stem6.KarGram::garmVowel($harmony,'tu');
            }
        }
    }
    
    /**
     * А. Если о.6 заканч. на
     * 1) с.к.: C[oö] и в с.ф. 3 слога, то о.6 + i;
     * 2) C[iuyoö] ИЛИ Vi, то о.6 + loi~löi;
     * 3) Ce, то e > i;
     * 4) Ca, то 
     *  4.1) a > i если 
     *      4.1.1) в с.ф. 2 слога и в о.6 в первом слоге есть гласные u, o (в том числе в составе VV: uu, uo, ui, ou, oi) 
     *  ИЛИ 4.1.2) в с.ф. более 2-х слогов и о.6 заканч. на mpa/mba, ma или является прилагательным с о.6. на va.  
     * 4.2) a > oi, если 
     *      4.2.1) в с.ф. 2 слога и в о.6 в первом слоге есть гласные a, e, i (в том числе в составе VV: ai, au, ua, ea, ii, ie, iu, ie, ee, eu); 
     *  ИЛИ 4.2.2) в с.ф. более 2-х слогов и о.2 не заканч. на mpa / mba, ma и не является прилагательным с о.6. на va.
     * 5) Cä, то 
     *  5.1) ä > i если 
     *      5.1.1) в с.ф. 2 слога; 
     *  ИЛИ 5.1.2) в с.ф. более 2-х слогов и о.6 заканч. на [mvžsjpb]ä.
     *  5.2) ä > öi если в с.ф. более 2-х слогов и о.6 заканч. на [dtgkčhlnr]ä.
     * 6) VV (кроме Vi), то: с.к.: – первый V → + i; ливв.: о.6 + loi~löi.
     * 
     * Б. Если о.6 заканч. на C, то о.6 > о.1 → если о.1 заканч. на
     * 1) Ce и с.ф. заканч. на [sšzž]i, то o.5.=с.ф ;
     * 2) Ce и с.ф. не заканч. на [sšzž]i, то e > i;
     * 3) C[aä], то a, ä > i;
     * 4) VV, то
     * с.к.: 4.1) uo > ui, 4.2) yö > yi, 4.3) ie > ei; 
     * ливв.: 4.1) если о.1 заканч. на uo, yö, то – o, ö → + zi;
     *        4.2) иначе о.6 + loi~löi.
     * 
     * @param string $stem0
     * @param string $stem1
     * @param string $stem6
     * @param int $lang_id
     * @param boolean $harmony
     * @return string
     */
    public static function stem5FromMiniTemplate($stem0, $stem1, $stem6, $lang_id, $pos_id, $harmony, $stem0_syll) {
        $C = "[".KarGram::consSet()."]";
        $V = "[".KarGram::vowelSet()."]";
        if ($lang_id == 4 && preg_match("/".$C."’?[oö]$/u", $stem6) && $stem0_syll==3) { // А.1
            return $stem6.'i';
        } elseif (preg_match("/".$C."[iuyoö]$/u", $stem6)
                || preg_match("/".$V."i$/u", $stem6)) { // А.2
            return $stem6.KarGram::garmVowel($harmony,'loi');
        } elseif (preg_match("/^(.+".$C.")e$/u", $stem6, $regs)) { // А.3
            return $regs[1].'i';
        } elseif (preg_match("/^(.+".$C.")a$/u", $stem6, $regs)) { // А.4
            if ($stem0_syll==2 && preg_match("/^".$C."?".$V."?[uo]/u", $stem6) // А.4.1.1
                    || $stem0_syll>2 && (preg_match("/m[pb]?a$/u", $stem6) || $pos_id==1 && preg_match("/va$/u", $stem6))) { // А.4.1.2
                return $regs[1].'i';                
            } elseif ($stem0_syll==2 && preg_match("/^".$C."?".$V."?[aei]/u", $stem6) // А.4.2.1
                    || $stem0_syll>2 && !preg_match("/m[pb]?a$/u", $stem6) && !($pos_id==1 && preg_match("/va$/u", $stem6))) { // А.4.2.2
                return $regs[1].'oi';                
            }
        } elseif (preg_match("/^(.+".$C.")ä$/u", $stem6, $regs)) { // А.5
            if ($stem0_syll==2 // А.5.1.1
                    || $stem0_syll>2 && preg_match("/[mvžsjpb]ä$/u", $stem6)) { // А.5.1.2
                return $regs[1].'i';                
            } elseif ($stem0_syll>2 && preg_match("/[dtgkčhlnr]ä$/u", $stem6)) { // А.5.2
                return $regs[1].'öi';                
            }
        } elseif (preg_match("/^(.+)".$V."(".$V.")$/u", $stem6, $regs)) { // А.6
            if ($lang_id == 4) {
                return $regs[1].$regs[2].'i';
            } else {
                return $stem6.KarGram::garmVowel($harmony,'loi');
            }
        } elseif (preg_match("/".$C."$/u", $stem6)) { // Б
//            if (preg_match("/^(.+".$C.")e$/u", $stem1, $regs)) {
            if (preg_match("/^(.+)e$/u", $stem1, $regs) && preg_match("/[sšzž]i$/u", $stem0)) { // Б.1
                    return $stem0;
            } elseif (preg_match("/^(.+".$C.")e$/u", $stem1, $regs) && !preg_match("/[sšzž]i$/u", $stem0)) { // Б.2
                return $regs[1].'i';
            } elseif (preg_match("/^(.+".$C.")[aä]$/u", $stem1, $regs)) { // Б.3
                    return $regs[1].'i';
            } elseif (preg_match("/^(.+?)(".$V.")(".$V.")$/u", $stem1, $regs)) { // Б.4
                if ($lang_id == 4) {
                    switch ($regs[2].$regs[3]) { // Б.4.1
                        case 'uo': return $regs[1].'ui';
                        case 'yö': return $regs[1].'yi';
                        case 'ie': return $regs[1].'ei';
                        default : return $stem1;
                    }
                } else {
                    if (in_array($regs[2].$regs[3], ['uo', 'yö'])) { // Б.4.1
                        return $regs[1].$regs[2].'zi';
                    } else {
                        return $stem6.KarGram::garmVowel($harmony,'loi');
                    }
                }
            }
        }
    }
        
    /**
     * А. Если o.5 заканч. на С[oö]i и о.6 на C[aä] и кол-во слогов в о.5 и о.6 одинаковое, то o.5 > о.1 → если о.1 заканч. на
     * 1) Сa, Cä, то a > oi, ä > öi;
     * 2) ua, то ua > avoi
     * 3) iä, то iä > ävöi.
     * 
     * Б. Если o.5 заканч. на l[oö]i и кол-во слогов в o.5 больше чем в о.6, то =o.5.
     * 
     * В. Если o.5 заканч. на Vi и о.6 заканч. на VV, то =o.5.
     * 
     * Г. Если o.5 заканч. на Ci 
     * 1) и перед конечным i есть čč, šš, ss, k, t, p, g, d, b, то o.5 > о.1 → если о.1 заканч. на
     *   1.1) СV, то V> i; 
     *   1.2) ie>ei, ua>ai, iä>äi
     * 2) иначе =o.5
     * 
     * @param string $stem1
     * @param string $stem5
     * @param string $stem6
     * @param boolean $harmony
     * @return string
     */
    public static function stem4FromMiniTemplate($stem1, $stem5, $stem6, $harmony) {
        $C = "[".KarGram::consSet()."]";
        $V = "[".KarGram::vowelSet()."]";
//dd($stem6, preg_match("/".$V.$V."$/u", $stem6));        
        if (preg_match("/".$C."[oö]i$/u", $stem5) && preg_match("/".$C."[aä]$/u", $stem6)
                && KarGram::countSyllable($stem5)==KarGram::countSyllable($stem6)) { // А
            if (preg_match("/^(.+".$C.")[aä]$/u", $stem1, $regs)) { // А.1
                return $regs[1].KarGram::garmVowel($harmony,'oi');
            } elseif (preg_match("/^(.+)ua$/u", $stem1, $regs)) { // А.2
                return $regs[1].'avoi';
            } elseif (preg_match("/^(.+)iä$/u", $stem1, $regs)) { // А.3
                return $regs[1].'ävöi';
            }
        } elseif (preg_match("/l[oö]i$/u", $stem5) && KarGram::countSyllable($stem5)>KarGram::countSyllable($stem6) // Б
                || preg_match("/".$V."i$/u", $stem5) && preg_match("/".$V.$V."$/u", $stem1)) { // В
//dd($stem1, $stem5, $stem6);        
            return $stem5;
        } elseif (preg_match("/".$C."i$/u", $stem5)) { // Г
            if (preg_match("/čči$|šši$|ssi$|[ktpgdb]i$/u", $stem5)) { // Г.1
                if (preg_match("/^(.+".$C.")".$V."$/u", $stem1, $regs)) {
                    return $regs[1].'i';
                } elseif (preg_match("/^(.+)ie$/u", $stem1, $regs)) {
                    return $regs[1].'ei';
                } elseif (preg_match("/^(.+)ua$/u", $stem1, $regs)) {
                    return $regs[1].'ai';
                } elseif (preg_match("/^(.+)iä$/u", $stem1, $regs)) {
                    return $regs[1].'äi';
                } else {
                    return $stem1;
                }
            } else {
                return $stem5;
            }
        }
        
    }
    
    /**
     * paik|ku (-an, -kua; -koi)
     * pajo||jouk|ko (-on, -kuo; -koloi)
     * puol|i (-en, -du; -ii)
     * päp|pi (-in, -pii; -pilöi)
     * piä (-n, -dy; -löi)
     * pačkeh (-en, -tu; -ii)
     * pada (puan, padua; padoi)
     * pagan||rengi (-n, -i; -löi)
     * po|udu (-vvan, -udua; -udii)
     * 
     * @param array $regs [0=>template, 1=>base, 2=>nom-sg-suff, 3=>gen-sg-suff, 4=>par-sg-suff, 5=>par-pl-suff]
     *                    [0=>"paik|ku (-an, -kua; -koi)", 1=>"paik", 2=>"ku", 3=>"-an", 4=>"-kua", 5=>"-koi"]
     * @return array [0=>nom_sg, 1=>base_gen_sg, 2=>base_ill_sg, 3=>part_sg, 4=>base_gen_pl, 5=>base_part_pl], $base, $base_suff]
     *               [[0=>'paikku', 1=>'paika', 2=>'paikka', 3=>'paikkua', 4=>'paikoi', 5=>'paikkoi']
     */
    public static function stemsOthersFromTemplate($regs) {
//dd($regs);    
        $base = preg_replace('/ǁ/','',$regs[1]);
        $gen_sg_suff = $regs[3];
        $par_sg_suff = $regs[4];
        $par_pl_suff = $regs[5];
        
        $out = [[$base.$regs[2]], null, $regs[0], null];

        $stem1 = self::parseGenSg(preg_replace("/\-/", $base, $gen_sg_suff));
        if (!$stem1) {
            return $out;
        }
        
        $stem5 = self::partPlBase($base, $par_pl_suff);
        if (!$stem5) {
            return $out;
        }

        $stems = [0 => $base.$regs[2],
                  1 => $stem1, // single genetive base 
                  2 => '',
                  3 => preg_replace("/\-/",$base,$par_sg_suff),
                  4 => '',
                  5 => $stem5,
                  10 => KarGram::isBackVowels($regs[1].$regs[2]) // harmony
            ];
//dd($stems);        
        $stems[2] = self::illSgBase($stems[0], $stems[1], $stems[3]); // single illative base
        $stems[4] = self::genPlBase($stems[1], $stems[5], $stems[10]); // plural partitive base
//dd('stems:',$stems);        
        return [$stems, null, $regs[1], $regs[2]];
    }
    
    /**
     * Viändö|i (-in, -idy)
     * 
     * @param array $regs [0=>template, 1=>base, 2=>nom-sg-suff, 3=>gen-sg-suff, 4=>par-sg-suff]
     *                    [0=>"Viändö|i (-in, -idy)", 1=>"Viändö", 2=>"i", 3=>"-in", 4=>"-idy"]
     * @return array [stems=[0=>nom_sg, 1=>base_gen_sg, 2=>base_ill_sg, 3=>part_sg, 4=>base_gen_pl, 5=>base_part_pl], $base, $base_suff]
     *               [[0=>'Viändöi', 1=>'Viändöi', 2=>'Viändöi', 3=>'Viändöidy', 4=>'', 5=>''], 'Viändö', 'i']
     */
    public static function stemsSgFromTemplate($regs) {
//dd($regs);    
        $base = preg_replace('/ǁ/','',$regs[1]);
        $gen_sg_suff = $regs[3];
        $par_sg_suff = $regs[4];

        $out = [[$base.$regs[2]], null, $regs[0], null];
        
        $stem1 = self::parseGenSg(preg_replace("/\-/", $base, $gen_sg_suff));
        if (!$stem1) {
            return $out;
        }
        
        $stems = [0 => $base.$regs[2],
                  1 => $stem1, // single genetive base 
                  2 => '',
                  3 => preg_replace("/^\-/",$base,$par_sg_suff),
                  4 => '',
                  5 => ''];
        $stems[2] = self::illSgBase($stems[0],$stems[1],$stems[3]); // single illative base
//dd('stems:',$stems);        
        return [$stems, 'sg', $regs[1], $regs[2]];
    }
    
    /**
     * pordah|at (-ien, -ii)
     * 
     * @param array $regs [0=>template, 1=>base, 2=>nom-pl-suff, 3=>gen-pl-suff, 4=>par-pl-suff]
     *                    [0=>"pordah|at (-ien, -ii)", 1=>"pordah", 2=>"at", 3=>"-ien", 4=>"-ii"]
     * @return array [stems=[0=>nom_pl, 1=>base_gen_sg, 2=>base_ill_sg, 3=>part_sg, 4=>base_gen_pl, 5=>base_part_pl], $base, $base_suff]
     *               [[0=>'pordahat', 1=>'', 2=>'', 3=>'', 4=>'pordahi', 5=>'pordahi'], 'pordah', 'at']
     */
    public static function stemsPlFromTemplate($regs) {
        $base = preg_replace('/ǁ/','',$regs[1]);
        $gen_pl_suff = $regs[3];
        $par_pl_suff = $regs[4];
        
        $out = [[$base.$regs[2]], null, $regs[0], null];
        
        $stem4 = self::parseGenPl($base, $gen_pl_suff);
        if (!$stem4) {
            return $out;
        }
        
        $stem5 = self::partPlBase($base, $par_pl_suff);
        if (!$stem5) {
            return $out;
        }
        
        $stems = [0 => $base.$regs[2],
                  1 => '',
                  2 => '',
                  3 => '',
                  4 => $stem4,  // plural genetive base 
                  5 => $stem5];
        return [$stems, 'pl', $regs[1], $regs[2]];
    }
       
    /**
     * п.о. 1
     * 
     * @param type $wordform
     * @return string
     */
    public static function parseGenSg($wordform) {
        if (!$wordform) {
            return '';
        }
        $out = [];
        $V = "[".KarGram::vowelSet()."]";
        $words = preg_split("/\//",$wordform);
        foreach ($words as $word) {
            if (preg_match("/^(.+".$V.")n$/u", $word, $regs)) {
                $out[] = $regs[1];
            } else {
                return '';
            }
        }
        return join('/',$out);
    }
    
    /**
     * @param type $wordform
     * @return string
     */
    public static function parseGenPl($base, $gen_pl_suff) {
        $out = [];
        $V = "[".KarGram::vowelSet()."]";
        $stems = preg_split("/\/\s*/",preg_replace("/\-/", $base, $gen_pl_suff));
        foreach ($stems as $stem) {
            $stem = trim($stem);
            if (preg_match("/^(.+?i)e?n$/u", $stem, $regs)) {
                $out[] = $regs[1];
            } else {
                return '';
            }
        }
        return join('/',$out);
    }
    
    public static function partPlBase($base, $par_pl_suff) {
        $out = [];
        $stems = preg_split("/\/\s*/",preg_replace("/\-/", $base, $par_pl_suff));
        foreach ($stems as $stem) {
            $stem = trim($stem);
            if (!preg_match("/i$/", $stem)) {
                return '';
            }
            $out[] = preg_replace("/ii$/", 'i', $stem);
        }
        return join('/',$out);
    }
    
    /** 
     * А. если $stem3 заканчивается на [dt][uy], то =$stem1, при этом возможны замены:
     *    1) если $stem0 заканчивается на V[uy]zi, а $stem1 на vve,
     *       то vve > [uy]de
     *    2) если $stem0 заканчивается на [uy][oö]zi, а $stem1 на vve,
     *       то vve > [oö]de
     *    3) если $stem0 заканчивается на zi, a $stem1 заканчивается на
     *       а) Vve, то ve > de 
     *       б) rre, то rre > rde
     *       в) nne, то nne > nde
     *       г) je, то je > de
     *       д) ie, то ie > ede
     *       е) äi, то äi > äde
     * Б. если в $stem3 конечные VV, то =$stem3-VV
     *    1) если $stem1 заканчивается на [aä]i, то +e
     *    2) в остальных случаях + конечный V из $stem1
     * 
     * @param string $stem0
     * @param string $stem1
     * @param string $stem3
     * @return string
     */
    public static function illSgBase($stem0, $stem1, $stem3) {
        $V = '['.KarGram::vowelSet().']';
    //dd($stem0, $stem1, $stem3);        
        $out = [];
        $stems1 = preg_split("/\/\s*/", $stem1);
        $stems3 = preg_split("/\/\s*/", $stem3);
        if (sizeof($stems3)==2 && sizeof($stems1)==1) {
            $stems1[1]=$stems1[0];
        } elseif (sizeof($stems3)==1 && sizeof($stems1)==2) {
            $stems3[1]=$stems3[0];
        }
        for ($i=0; $i<sizeof($stems1); $i++) {
            $stem1 = $stems1[$i];
            $stem3 = $stems3[$i];
            if (preg_match('/[dt][uy]$/u', $stem3)){ // А
                if (preg_match('/'.$V.'([uy])zi$/u', $stem0, $regs_u)) {
                    $stem1=preg_replace('/vve$/u', $regs_u[1].'de', $stem1); // A.1
                } elseif (preg_match('/[uy]([oö])zi$/u', $stem0, $regs_o)) {
                    $stem1=preg_replace('/vve$/u', $regs_o[1].'de', $stem1); // A.2                
                } elseif (preg_match('/zi$/', $stem0)) { // А.3
                    if (preg_match('/^(.*'.$V.')ve$/u', $stem1, $regs1) // А.3.а
                            || preg_match('/^(.*r)re$/u', $stem1, $regs1) // А.3.б
                            || preg_match('/^(.*n)ne$/u', $stem1, $regs1) // А.3.в
                            || preg_match('/^(.+)je$/u', $stem1, $regs1) // А.3.г
                            || preg_match('/^(.*ä)i$/u', $stem1, $regs1)) { // А.3.е
                        $stem1 = $regs1[1].'de';
                    } elseif (preg_match('/^(.+)ie$/u', $stem1, $regs1)) { // А.3.д
                        $stem1 = $regs1[1].'ede';
                    }
                }
                $out[] = $stem1;
            } elseif (preg_match('/^(.*)'.$V.$V.'$/u', $stem3, $regs3)) {
                $stem3 = $regs3[1];
                if (preg_match('/[aä]i$/', $stem1)) {
                    $stem3 .= 'e';
                } elseif (preg_match('/('.$V.')$/u', $stem1, $regs1)) {
                    $stem3 .= $regs1[1];
                }
                $out[] = $stem3;
            }
        }
        return join('/',$out);
    }
    
    /**
     * A. если $stem5 заканчивается на kki, tti, ppi, čči, šši, ssi, gi, di, bi,
     *    то = $stem1 – конечный V + i
     * Б. если $stem5 заканчивается на loi, löi или Ci, то = $stem5
     * 
     * В. в остальных случаях = $stem1 со след. изменениями:
     *    1) если $stem1 заканчивается на СV, то – конечный V + oi / öi
     *    2) если $stem1 заканчивается на ua, iä, то – ua, iä → + avoi / ävöi
     * 
     * @param string $stem1
     * @param string $stem5
     * @return string
     */
    public static function genPlBase($stem1, $stem5, $harmony) {
        if (!$stem5) {
            return '';
        }
        $V = '['.KarGram::vowelSet().']';
        $C = '['.KarGram::consSet().']';
        $out = [];
        $stems5 = preg_split("/\//",$stem5);
        foreach ($stems5 as $stem5) {
            if (preg_match('/k’?k’?i$|t’?t’?i$|p’?p’?i$|č’?č’?i$|š’?š’?i$|s’?s’?i$|[gdb]’?i$/u', $stem5)){ // А
                $out[] = preg_replace('/'.$V.'$/u','i',$stem1);
            } elseif (preg_match('/l’?[oö]i$|'.$C.'i$/u', $stem5)){ // Б
                $out[] = $stem5;
            } else { // В
                if (preg_match('/^(.+'.$C.'’?)'.$V.'$/u', $stem1, $regs1)) { // 1
                    $stem1 = $regs1[1].KarGram::garmVowel($harmony, 'o').'i';
                } else { 
                    $stem1=preg_replace('/ua$/u', 'avoi', $stem1); // 2
                    $stem1=preg_replace('/iä$/u', 'ävöi', $stem1); // 2                
                }
                $out[] = $stem1;            
            }
        }
        return join('/',$out);
    }
    
    public static function wordformByStems($stems, $gramset_id, $lang_id, $dialect_id, $name_num=null) {
        switch ($gramset_id) {
            case 2: // номинатив, мн.ч. 
                return $name_num == 'pl' ? $stems[0] : ($name_num != 'sg' && $stems[1] ? KarNameOlo::addEndToMultiBase($stems[1], 't') : '');
            case 56: // аккузатив, ед.ч. 
                return $stems[0].($stems[1] ? ', '.$stems[1].'n' : '');
            case 57: // аккузатив, мн.ч. 
                return $name_num == 'pl' ? $stems[0] : ($name_num != 'sg' && $stems[1] ? KarNameOlo::addEndToMultiBase($stems[1], 't') : '');
        }
        
        if ($name_num !='pl' && in_array($gramset_id, self::gramsetListSg($lang_id))) {
            return self::wordformByStemsSg($stems, $gramset_id, $lang_id, $dialect_id);
        }
        
        if ($name_num !='sg' && in_array($gramset_id, self::gramsetListPl($lang_id))) {
            if ($lang_id==5) {
                return KarNameOlo::wordformByStemsPl($stems, $gramset_id, $dialect_id);
            } else {        
                return self::wordformByStemsPl($stems, $gramset_id, $dialect_id);
            }
        }
        return '';
    }
    
    public static function wordformByStemsSg($stems, $gramset_id, $lang_id, $dialect_id) {
        switch ($gramset_id) {
            case 1: // номинатив, ед.ч. 
                return $stems[0];
            case 3: // генитив, ед.ч. 
                return $stems[1] ? $stems[1].'n' : '';
            case 4: // партитив, ед.ч. 
                return $stems[3] ? $stems[3] : '';
            case 10: // иллатив, ед.ч. 
                return $stems[2] ? $stems[2].'h' : '';
        }
        if ($lang_id==5) {
            return KarNameOlo::wordformByStemsSg($stems, $gramset_id, $dialect_id);
        } else {
            return self::wordformByStemsSgProp($stems, $gramset_id, $dialect_id);
        }
        
    }
    
    /**
     * $stems[10] - harmony
     * 
     * @param type $stems
     * @param type $gramset_id
     * @param type $dialect_id
     * @return type
     */
    public static function wordformByStemsSgProp($stems, $gramset_id, $dialect_id) {
        $a = KarGram::garmVowel($stems[10],'a');
        switch ($gramset_id) {
            case 277: // эссив, ед.ч. 
                return $stems[2] ? $stems[2]. 'n'. $a  : '';
            case 5: // транслатив, ед.ч. 
                return $stems[1] ? $stems[1]. KarGram::distrCons($stems[1], 'ksi') : '';
            case 8: // инессив, ед.ч. 
                return $stems[1] ? $stems[1]. KarGram::distrCons($stems[1], 'ss'). $a : '';
            case 9: // элатив, ед.ч. 
                return $stems[1] ? $stems[1]. KarGram::distrCons($stems[1], 'st'). $a : '';
            case 278: // адессив-аллатив, ед.ч. 
                return $stems[1] ? $stems[1]. 'll'. $a : '';
            case 12: // аблатив, ед.ч. 
                return $stems[1] ? $stems[1]. 'l'. ($dialect_id==47 ? 'd': 't'). $a : '';
            case 6: // абессив, ед.ч. 
                return $stems[1] ? $stems[1]. 'tt'. $a : '';
            case 14: // комитатив, ед.ч. 
                return $dialect_id==47 && $stems[1] ? $stems[1].'nke' : '';
            case 15: // пролатив, ед.ч. 
                return $dialect_id==47 && $stems[1] ? $stems[1].'čči' : '';
        }
    }

    public static function wordformByStemsPl($stems, $gramset_id, $dialect_id) {
        $a = KarGram::garmVowel($stems[10],'a');
        
        switch ($gramset_id) {
            case 24: // генитив, мн.ч. 
                return self::genPl($stems[4], $stems[5], isset($stems[6])? $stems[6]: null, $dialect_id);
            case 22: // партитив, мн.ч. 
                return self::partPl($stems[5], isset($stems[6])? $stems[6]: null, $stems[10], $dialect_id);
            case 279: // эссив, мн.ч.
                return $stems[5] ? $stems[5]. 'n'. $a : '';
            case 59: // транслатив, мн.ч. 
                return $stems[4] ? $stems[4].'ksi' : '';
            case 23: // инессив, мн.ч.
                return $stems[4] ? $stems[4]. 'ss'. $a : '';
            case 60: // элатив, мн.ч.
                return $stems[4] ? $stems[4]. 'st'. $a : '';
            case 61: // иллатив, мн.ч. 
                return $stems[5] ? $stems[5].'h' : '';
            case 280: // адессив-аллатив, мн.ч.
                return $stems[4] ? $stems[4]. 'll'. $a : '';
            case 62: // аблатив, мн.ч.
                return $stems[4] ? $stems[4]. 'l'. ($dialect_id==47 ? 'd': 't'). $a : '';
            case 64: // абессив, мн.ч.
                return $stems[4] ? $stems[4]. 'tt'. $a : '';
            case 65: // комитатив, мн.ч. 
                return self::comPl($stems[4], $stems[5], $dialect_id);
            case 66: // пролатив, мн.ч. 
                return $dialect_id==47 && $stems[4] ? $stems[4].'čči' : '';
            case 281: // инструктив, мн.ч. 
                return $stems[4] ? $stems[4].'n' : '';
        }
    }
    
    /**
     * For tver (dialect_id=47) = stem4+n
     * 
     * For norhern proper karelian:
     * Если o.5 заканч. на
     * 1) Ci, то o.5 + en;
     * 2) l[oö]i и кол-во слогов в o.5 больше чем в о.6, то loi~löi > jen;
     * 3) Vi и кол-во слогов в o.5 и о.6 одинаковое, а о.6 заканч. на
     *  – СV, то i > jen;
     *  – VV, то o.5 + jen.
     * 
     * @param string $stem4
     * @param string $stem5
     * @param string $stem6
     * @param int $dialect_id
     */
    public static function genPl($stem4, $stem5, $stem6, $dialect_id) {
        if ($dialect_id==47) {
            return $stem4 ? $stem4. 'n' : '';
        }
        
        if (!$stem5 || !$stem6) {
            return '';
        }
        $V = "[".KarGram::vowelSet()."]";
        $C = "[".KarGram::consSet()."]";
        if (preg_match("/".$C."i$/u", $stem5)) {
            return $stem5. 'en';            
        } elseif (preg_match("/^(.+)l[oö]i$/u", $stem5, $regs) 
                && KarGram::countSyllable($stem5) > KarGram::countSyllable($stem6)) {
            return $regs[1]. 'jen';            
        } elseif (preg_match("/^(.+".$V.")i$/u", $stem5, $regs) 
                && KarGram::countSyllable($stem5) == KarGram::countSyllable($stem6)) {
            if (preg_match("/".$C.$V."$/u", $stem6)) {
                return $regs[1]. 'jen';  
            } else {
                return $stem5. 'jen';
            }
        } else {return '!!!';}
    }

    /**
     * For tver (dialect_id=47) 
     * если о.5 заканчивается на oi/öi, то = о.5 + da/dä,
     * иначе = о.5 + e
     * 
     * For norhern proper karelian:
     * Eсли o.5 заканч. на
     * 1) Ci, то o.5 + e ;
     * 2) loi~löi и кол-во слогов в o.5 больше чем в о.6, то loi~löi > ja~jä;
     * 3) Vi и кол-во слогов в o.5 и о.6 одинаковое, а о.6 заканч. на
     * – СV, то i > ja~jä;
     * – VV, то o.5 + ta~tä.
     * 
     * @param string $stem5
     * @param string $stem6
     * @param int $dialect_id
     */
    public static function partPl($stem5, $stem6, $harmony, $dialect_id) {
        if (!$stem5) {
            return '';
        }
        if ($dialect_id==47) {
            if (preg_match("/[oö]i$/u", $stem5)) {
                return $stem5. KarGram::garmVowel($harmony,'da');
            } else {
                return $stem5. 'e';
            }
        }
        
        if (!$stem6) {
            return '';
        }
//dd(preg_match("/^(.+)l[oö]i$/u", $stem5, $regs), KarGram::countSyllable($stem5), KarGram::countSyllable($stem6));        
        $V = "[".KarGram::vowelSet()."]";
        $C = "[".KarGram::consSet()."]";
        if (preg_match("/".$C."i$/u", $stem5)) {
            return $stem5. 'e';            
        } elseif (preg_match("/^(.+)l[oö]i$/u", $stem5, $regs) 
                && KarGram::countSyllable($stem5) > KarGram::countSyllable($stem6)) {
            return $regs[1]. KarGram::garmVowel($harmony,'ja');            
        } elseif (preg_match("/^(.+".$V.")i$/u", $stem5, $regs) 
                && KarGram::countSyllable($stem5) == KarGram::countSyllable($stem6)) {
            if (preg_match("/".$C.$V."$/u", $stem6)) {
                return $regs[1]. KarGram::garmVowel($harmony,'ja');  
            } else {
                return $stem5. KarGram::garmVowel($harmony,'ta');
            }
        }
    }

    public static function comPl($stem4, $stem5, $dialect_id) {
        if ($dialect_id == 47) { // tver
            return $stem4 ? $stem4. 'nke' : '';
        }
        return $stem5 ? $stem5. 'neh' : '';        
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
     * @param string $lemma_str
     */
    public static function toRightTemplate($bases, $base_list, $lemma_str, $num) {
        if (!(sizeof($base_list)==3 || sizeof($base_list)==2 && $num=='sg' || sizeof($base_list)==1 && $num=='pl')) {
            return $lemma_str;
        }
        if (preg_match("/^([^\/\s]+)\s*[\/\:]\s*([^\s]+)$/u", $base_list[0], $regs)) {
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
            } elseif (preg_match("/^([^\/\s]+)\s*[\/\:]\s*([^\s]+)$/u", $base_list[2], $regs)) {
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
                return ['lda', 'ldä', 'lta', 'ltä'];
            case 6: // абессив, ед.ч. 
            case 64: // абессив, мн.ч.
                return ['tta', 'ttä'];
            case 14: // комитатив, ед.ч. 
            case 65: // комитатив, мн.ч. 
                return ['nke', 'neh', 'n’eh'];
            case 15: // пролатив, ед.ч. 
            case 66: // пролатив, мн.ч. 
                return ['čči'];
            case 2: // номинатив, мн.ч. 
                return ['t'];
            case 22: // партитив, мн.ч. 
                return ['e', 'da', 'dä', 'ja', 'jä', 'ta', 'tä'];
            case 59: // транслатив, мн.ч. 
                return ['ksi'];
            case 23: // инессив, мн.ч.
                return ['ssa', 'ssä'];
            case 60: // элатив, мн.ч.
                return ['sta', 'stä'];
        }
        return [];
    }
    
}