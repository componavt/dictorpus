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
class KarNameOlo
{
    public static function gramsetListSg() {
        return [1,  56, 3,  4, 277,  5, 6, 8,  9, 10,  11, 12, 13, 14, 15];
    }

    public static function gramsetListPl() {
        return [2, 57, 24, 22, 279, 59, 64, 23, 60, 61,  25, 62, 63, 65, 66, 281];
    }
        
    public static function wordformByStemsSg($stems, $gramset_id, $dialect_id) {
        switch ($gramset_id) {
            case 277: // эссив, ед.ч. 
                return $stems[1] ? $stems[1].'nn'. KarGram::garmVowel($stems[10],'u') : '';
            case 5: // транслатив, ед.ч. 
                return $stems[1] ? $stems[1].'kse' : '';
            case 6: // абессив, ед.ч. 
                return $stems[1] ? $stems[1].'tt'. KarGram::garmVowel($stems[10],'a'). 'h' : '';
            case 8: // инессив, ед.ч. 
                return $stems[1] ? $stems[1].'s' : '';
            case 9: // элатив, ед.ч. 
                return $stems[1] ? $stems[1].'s, '. $stems[1].'späi' : '';
            case 11: // адессив, ед.ч. 
                return $stems[1] ? $stems[1].'l' : '';
            case 12: // аблатив, ед.ч. 
                return $stems[1] ? $stems[1].'l, '. $stems[1].'lpäi' : '';
            case 13: // аллатив, ед.ч. 
                return $stems[1] ? $stems[1].'le' : '';
            case 14: // комитатив, ед.ч. 
                return $stems[1] ? $stems[1].'nke' : '';
            case 15: // пролатив, ед.ч. 
                return $stems[1] ? $stems[1].'či' : '';
        }
    }

    public static function wordformByStemsPl($stems, $gramset_id, $dialect_id) {
        switch ($gramset_id) {
            case 24: // генитив, мн.ч. 
                return self::genPl($stems[4], $stems[5]);
            case 22: // партитив, мн.ч. 
                return self::partPl($stems[5]);
            case 279: // эссив, мн.ч.
                return self::addEndToMultiBase($stems[4], 'nn'. KarGram::garmVowel($stems[10],'u'));
            case 59: // транслатив, мн.ч. 
                return self::addEndToMultiBase($stems[4], 'kse');
            case 64: // абессив, мн.ч. 
                return self::addEndToMultiBase($stems[4], 'tt'. KarGram::garmVowel($stems[10],'a'). 'h');
            case 23: // инессив, мн.ч.
                return self::addEndToMultiBase($stems[4], 's');
            case 60: // элатив, мн.ч.
                return self::addEndToMultiBase($stems[4], 's, späi');
            case 61: // иллатив, мн.ч. 
                return self::addEndToMultiBase($stems[5], 'h');
            case 25: // адессив, мн.ч.
                return self::addEndToMultiBase($stems[4], 'l');
            case 62: // аблатив, мн.ч.
                return self::addEndToMultiBase($stems[4], 'l, lpäi');
            case 63: // аллатив, мн.ч.
                return self::addEndToMultiBase($stems[4], 'le');
            case 65: // комитатив, мн.ч. 
                return self::comPl($stems[4]);
            case 66: // пролатив, мн.ч. 
                return self::addEndToMultiBase($stems[4], 'či');
            case 281: // инструктив, мн.ч. 
                return self::addEndToMultiBase($stems[4], 'n');
        }
    }
    
    public static function addEndToMultiBase($base, $end, $div='\/') {
        if (!$base) {
            return '';
        }
        $bases = preg_split('/'.$div.'/', $base);
        $ends = preg_split('/,/', $end);
        $forms = [];
        foreach($bases as $base) {
            foreach($ends as $end) {
                $forms[] = trim($base).trim($end);
            }
        }
        return join(', ', $forms);
    }
    
    public static function genPl($stem4, $stem5) {
        if (!$stem4 || !$stem5) {
            return '';
        }
        $stems4 = preg_split('/\//', $stem4);
        $stems5 = preg_split('/\//', $stem5);
        $forms = [];
        for ($i=0; $i<sizeof($stems4); $i++) {
            if (preg_match('/['.KarGram::consSet().']’?i$/u', $stems5[$i])) {
                $stems4[$i] .= 'e';
            }
            $forms[] = $stems4[$i].'n';
        }
        return join(', ', $forms);
    }

    // оставим только 2) если п.о.5 заканчивается на Сi, то п.о.5 + i
    public static function partPl($stem5) {
        if (!$stem5) {
            return '';
        }
        $stems5 = preg_split('/\//', $stem5);
        $forms = [];
        foreach ($stems5 as $stem5) {
            if (preg_match('/['.KarGram::consSet().']’?i$/u', $stem5)) {
                $stem5 .= 'i';
            }
            $forms[] = $stem5;
        }
        return join(', ', $forms);
    }

    public static function comPl($stem4) {
        if (!$stem4) {
            return '';
        }
        $stems4 = preg_split('/\//', $stem4);
        $forms = [];
        foreach ($stems4 as $stem4) {
            if (preg_match('/['.KarGram::consSet().']’?i$/u', $stem4)) {
                $forms[] = $stem4. 'enke, '.$stem4. 'nneh';
            } else {
                $forms[] = $stem4. 'nke, '.$stem4. 'nneh';
            }
        }
        return join(', ', $forms);
    }

    /** 
     * TODO!!! проверить для ливвиковского
     * 
     * @param type $gramset_id
     * @return type
     */
    public static function getAffixesForGramset($gramset_id) {
        switch ($gramset_id) {
            case 3: // генитив, ед.ч. 
            case 24: // генитив, мн.ч. 
            case 281: // инструктив, мн.ч. 
                return ['n'];
            case 277: // эссив, ед.ч. 
            case 279: // эссив, мн.ч.
                return ['nnu', 'nny'];
            case 5: // транслатив, ед.ч. 
            case 59: // транслатив, мн.ч. 
                return ['kse'];
            case 6: // абессив, ед.ч. 
            case 64: // абессив, ед.ч. 
                return ['ttah', 'ttäh'];
            case 8: // инессив, ед.ч. 
            case 23: // инессив, мн.ч.
                return ['s'];
            case 9: // элатив, ед.ч. 
            case 60: // элатив, мн.ч.
                return ['s', 'späi'];
            case 10: // иллатив, ед.ч. 
            case 61: // иллатив, мн.ч. 
                return ['h'];
            case 11: // адессив, ед.ч. 
            case 25: // адессив, мн.ч.
                return ['l'];
            case 12: // аблатив, ед.ч. 
            case 62: // аблатив, мн.ч.
                return ['l', 'lpäi'];
            case 63: // аллатив, мн.ч.
            case 13: // аллатив, ед.ч. 
                return ['le'];
            case 14: // комитатив, ед.ч. 
            case 65: // комитатив, мн.ч. 
                return ['nke', 'enke', 'nneh'];
            case 15: // пролатив, ед.ч. 
            case 66: // пролатив, мн.ч. 
                return ['či'];
            case 2: // номинатив, мн.ч. 
                return ['t'];
            case 22: // партитив, мн.ч. 
                return ['i'];
        }
        return [];
    }
    
}