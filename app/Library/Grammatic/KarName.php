<?php

namespace App\Library\Grammatic;

use App\Library\Grammatic\KarGram;

use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;

class KarName
{
    public static function getListForAutoComplete() {
        return [1,  3,  4, 277,  5,  8,  9, 10, 278, 12, 6,  14, 15, 
                2, 24, 22, 279, 59, 23, 60, 61, 280, 62, 64, 65, 66, 281];
    }
    
    public static function wordformByStems($stems, $gramset_id, $lang_id, $dialect_id) {
        $stem1_i = preg_match("/i$/u", $stems[1]);
        $stem5_oi = preg_match("/[oö]i$/u", $stems[5]);
        
        switch ($gramset_id) {
            case 1: // номинатив, ед.ч. 
                return $stems[0];
            case 3: // генитив, ед.ч. 
                return $stems[1].'n';
            case 4: // партитив, ед.ч. 
                return $stems[3];
            case 277: // эссив, ед.ч. 
                return $stems[2]. 'n'. KarGram::garmVowel($stems[2],'a');
            case 5: // транслатив, ед.ч. 
                return $stems[1] . ($stem1_i ? 'ksi' : 'kši');
            case 8: // инессив, ед.ч. 
                return $stems[1] . ($stem1_i ? 'ss' : 'šš'). KarGram::garmVowel($stems[1],'a');
            case 9: // элатив, ед.ч. 
                return $stems[1] . ($stem1_i ? 'st' : 'št'). KarGram::garmVowel($stems[1],'a');
            case 10: // иллатив, ед.ч. 
                return $stems[2].'h';
            case 278: // адессив-аллатив, ед.ч. 
                return $stems[1] . 'll'. KarGram::garmVowel($stems[1],'a');
            case 12: // аблатив, ед.ч. 
                return $stems[1] . 'ld'. KarGram::garmVowel($stems[1],'a');
            case 6: // абессив, ед.ч. 
                return $stems[1] . 'tt'. KarGram::garmVowel($stems[1],'a');
            case 14: // комитатив, ед.ч. 
                return $stems[1].'nke';
            case 15: // пролатив, ед.ч. 
                return $stems[1].'čči';
                                
            case 2: // номинатив, мн.ч. 
                return $stems[1]. 't';
            case 24: // генитив, мн.ч. 
                return $stems[4]. 'n';
            case 22: // партитив, мн.ч. 
                return $stems[5] . ($stem5_oi ? 'd'.KarGram::garmVowel($stems[5],'a') : 'e' );
            case 279: // эссив, мн.ч.
                return $stems[5] . 'n'. KarGram::garmVowel($stems[5],'a');
            case 59: // транслатив, мн.ч. 
                return $stems[4].'ksi';
            case 23: // инессив, мн.ч.
                return $stems[4] . 'ss'. KarGram::garmVowel($stems[5],'a');
            case 60: // элатив, мн.ч.
                return $stems[4] . 'st'. KarGram::garmVowel($stems[5],'a');
            case 61: // иллатив, мн.ч. 
                return $stems[5].'h';
            case 280: // адессив-аллатив, мн.ч.
                return $stems[4] . 'll'. KarGram::garmVowel($stems[5],'a');
            case 62: // аблатив, мн.ч.
                return $stems[4] . 'ld'. KarGram::garmVowel($stems[5],'a');
            case 64: // абессив, мн.ч.
                return $stems[4] . 'tt'. KarGram::garmVowel($stems[5],'a');
            case 65: // комитатив, мн.ч. 
                return $stems[4].'nke';
            case 66: // пролатив, мн.ч. 
                return $stems[4].'čči';
            case 281: // инструктив, мн.ч. 
                return $stems[4].'n';
        }
        return '';
    }

}