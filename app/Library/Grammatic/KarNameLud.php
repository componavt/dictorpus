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
        return [1,  56, 3,  4, 277,  5, 6, 8,  9, 10,  11, 12, 13, 14, 15];
    }

    public static function gramsetListPl() {
        return [2, 57, 24, 22, 279, 59, 64, 23, 60, 61,  25, 62, 63, 65, 66, 281];
    }
        
    public static function wordformByStemsSg($stems, $gramset_id, $dialect_id) {
        return '';
    }

    public static function wordformByStemsPl($stems, $gramset_id, $dialect_id) {
        return '';
    }
    
    /** 
     * TODO!!! проверить для людиковского
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
    
    public static function templateFromWordforms($wordforms, $number) {
/*        if ($number==1 || $number==2) {
            return " (".join(', ',array_values($wordforms)).")";
        } else {
            return " (".$wordforms[3].", ".$wordforms[4]."; ".$wordforms[22].")";
        }*/
    }
}