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
                    ?  self::illSgBase($stems[0],$stems[1],$stems[3]) : null;
            case 4: 
                return isset($stems[1]) && isset($stems[5]) && $stems[5] ?  self::genPlBase($stems[1],$stems[5]) : null;
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
        return [1,  3,  4, 277,  5,    8,  9, 10, 278, 12,  6, 14, 15];
    }

    public static function gramsetListPl($lang_id) {
        if ($lang_id==5) {
            return KarNameOlo::gramsetListPl();
        }
        return [2, 24, 22, 279, 59,     23, 60, 61, 280, 62, 64, 65, 66, 281];
    }

    public static function getListForAutoComplete($lang_id) {
        return array_merge(self::gramsetListSg($lang_id), self::gramsetListPl($lang_id));
    }
        
    public static function stemsFromTemplate($template, $name_num) {      
        $base_shab = "([^\s\(\|]+)";
        $base_suff_shab = "([^\s\(\|]*)";
//        $okon1_shab = "(-?[^\-\,\;\)]+)";
        $okon3_shab = "(-?[^\-\,\;\)]+?\/?-?[^\-\,\;\)]*)";
//        $lemma_okon1_shab = "/^".$base_shab."\|?".$base_suff_shab."\s*\(".$okon1_shab;
        $lemma_okon3_shab = "/^".$base_shab."\|?".$base_suff_shab."\s*\(".$okon3_shab;

        // only plural
        if ($name_num == 'pl' && preg_match($lemma_okon3_shab.",\s*".$okon3_shab."\)/", $template, $regs)) {
//            $name_num = 'pl';
            list($stems, $base, $base_suff) =  self::stemsPlFromTemplate($regs);
        // only single
        } elseif ($name_num == 'sg' && preg_match($lemma_okon3_shab.",\s*".$okon3_shab."\)/", $template, $regs)) {
            list($stems, $base, $base_suff) =  self::stemsSgFromTemplate($regs);
        // others
        } elseif (preg_match($lemma_okon3_shab.",\s*".$okon3_shab.";\s*".$okon3_shab."\)/", $template, $regs)) {
            $name_num = '';
            list($stems, $base, $base_suff) = self::stemsOthersFromTemplate($regs);
        } else {
            return Grammatic::getAffixFromtemplate($template, $name_num);
        }
//dd($base);        
        return [$stems, $name_num, $base, $base_suff];
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
     * @return array [stems=[0=>nom_sg, 1=>base_gen_sg, 2=>base_ill_sg, 3=>part_sg, 4=>base_gen_pl, 5=>base_part_pl], $base, $base_suff]
     *               [[0=>'paikku', 1=>'paika', 2=>'paikka', 3=>'paikkua', 4=>'paikoi', 5=>'paikkoi'], 'paik', 'ku']
     */
    public static function stemsOthersFromTemplate($regs) {
//dd($regs);    
        $out = [[$regs[0]], $regs[0], null];
        $base = $regs[1];
        $base_suff = $regs[2];
        $gen_sg_suff = $regs[3];
        $par_sg_suff = $regs[4];
        $par_pl_suff = $regs[5];

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
                  3 => preg_replace("/^\-/",$base,$par_sg_suff),
                  4 => '',
                  5 => $stem5];
        $stems[2] = self::illSgBase($stems[0],$stems[1],$stems[3]); // single illative base
        $stems[4] = self::genPlBase($stems[1],$stems[5]); // plural partitive base
//dd('stems:',$stems);        
        return [$stems, $base, $base_suff];
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
        $out = [[$regs[0]], $regs[0], null];
        $base = $regs[1];
        $base_suff = $regs[2];
        $gen_sg_suff = $regs[3];
        $par_sg_suff = $regs[4];

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
        return [$stems, $base, $base_suff];
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
        $out = [[$regs[0]], $regs[0], null];
        $base = $regs[1];
        $base_suff = $regs[2];
        $gen_pl_suff = $regs[3];
        $par_pl_suff = $regs[4];
//if  (!$gen_pl_suff || !$par_pl_suff) {dd($regs);}       

/*        if (!preg_match("/^(.*?)e??n$/u", $gen_pl_suff, $regs_gen)) {
            return $out;
        }*/
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
        return [$stems, $base, $base_suff];
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
        $V = KarGram::vowelSet();
        if (preg_match('/[dt][uy]$/u', $stem3)){ // А
            if (preg_match('/['.$V.']([uy])zi$/u', $stem0, $regs_u)) {
                $stem1=preg_replace('/vve$/u', $regs_u[1].'de', $stem1); // A.1
            } elseif (preg_match('/[uy]([oö])zi$/u', $stem0, $regs_o)) {
                $stem1=preg_replace('/vve$/u', $regs_o[1].'de', $stem1); // A.2                
            } elseif (preg_match('/zi$/', $stem0)) { // А.3
                if (preg_match('/^(.*['.$V.'])ve$/u', $stem1, $regs1) // А.3.а
                        || preg_match('/^(.*r)re$/u', $stem1, $regs1) // А.3.б
                        || preg_match('/^(.*n)ne$/u', $stem1, $regs1) // А.3.в
                        || preg_match('/^(.+)je$/u', $stem1, $regs1) // А.3.г
                        || preg_match('/^(.*ä)i$/u', $stem1, $regs1)) { // А.3.е
                    $stem1 = $regs1[1].'de';
                } elseif (preg_match('/^(.+)ie$/u', $stem1, $regs1)) { // А.3.д
                    $stem1 = $regs1[1].'ede';
                }
            }
            return $stem1;
        } elseif (preg_match('/^(.*)['.$V.']{2}$/u', $stem3, $regs3)) {
            $stem3 = $regs3[1];
            if (preg_match('/[aä]i$/', $stem1)) {
                $stem3 .= 'e';
            } elseif (preg_match('/(['.$V.'])$/u', $stem1, $regs1)) {
                $stem3 .= $regs1[1];
            }
            return $stem3;
        }
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
    public static function genPlBase($stem1, $stem5) {
        $V = KarGram::vowelSet();
        $C = KarGram::consSet();
        $out = [];
        $stems5 = preg_split("/\//",$stem5);
        foreach ($stems5 as $stem5) {
            if (preg_match('/kki$|tti$|ppi$|čči$|šši$|ssi$|[gdb]i$/u', $stem5)){ // А
                $out[] = preg_replace('/['.$V.']$/u','i',$stem1);
            } elseif (preg_match('/l[oö]i$|['.$C.']i$/u', $stem5)){ // Б
                $out[] = $stem5;
            } else { // В
                if (preg_match('/^(.+['.$C.'])['.$V.']$/u', $stem1, $regs1)) { // 1
                    $stem1 = $regs1[1].KarGram::garmVowel($stem1, 'o').'i';
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
                return $name_num == 'pl' ? $stems[0] : ($name_num != 'sg' && $stems[1] ? $stems[1].'t' : '');
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
            case 14: // комитатив, ед.ч. 
                return $stems[1] ? $stems[1].'nke' : '';
        }
        if ($lang_id==5) {
            return KarNameOlo::wordformByStemsSg($stems, $gramset_id, $dialect_id);
        } else {
            return self::wordformByStemsSgProp($stems, $gramset_id, $dialect_id);
        }
        
    }
    
    public static function wordformByStemsSgProp($stems, $gramset_id, $dialect_id) {
        $stem1_i = preg_match("/i$/u", $stems[1]);
        
        switch ($gramset_id) {
            case 277: // эссив, ед.ч. 
                return $stems[2] ? $stems[2]. 'n'. KarGram::garmVowel($stems[2],'a') : '';
            case 5: // транслатив, ед.ч. 
                return $stems[1] ? $stems[1] . ($stem1_i ? 'ksi' : 'kši') : '';
            case 8: // инессив, ед.ч. 
                return $stems[1] ? $stems[1] . ($stem1_i ? 'ss' : 'šš'). KarGram::garmVowel($stems[1],'a') : '';
            case 9: // элатив, ед.ч. 
                return $stems[1] ? $stems[1] . ($stem1_i ? 'st' : 'št'). KarGram::garmVowel($stems[1],'a') : '';
            case 278: // адессив-аллатив, ед.ч. 
                return $stems[1] ? $stems[1] . 'll'. KarGram::garmVowel($stems[1],'a') : '';
            case 12: // аблатив, ед.ч. 
                return $stems[1] ? $stems[1] . 'ld'. KarGram::garmVowel($stems[1],'a') : '';
            case 6: // абессив, ед.ч. 
                return $stems[1] ? $stems[1] . 'tt'. KarGram::garmVowel($stems[1],'a') : '';
            case 15: // пролатив, ед.ч. 
                return $stems[1] ? $stems[1].'čči' : '';
        }
    }

    public static function wordformByStemsPl($stems, $gramset_id, $dialect_id) {
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
     * @param string $lemma_str
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
        return [];
    }
    
}