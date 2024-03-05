<?php

namespace App\Library\Experiments;

use Illuminate\Database\Eloquent\Model;
//use DB;

use App\Models\Dict\Lemma;

class Ludgen extends Model
{
//    protected $table='dialect_dmarker';
    const lang_id = 6;
    const dialect_id = 42;
    
    public static function getNames() {
        return [
            62004 => 'halgo',
            13424 => 'kirikkö',
            38637 => 'lindu',
            50254 => 'kydy',
            46747 => 'počči',
            21396 => 'd’ogi',
            29962 => 'akke',
            66666 => 'ukke',
            50308 => 'emände',
            47713 => 'nuotte',
            47289 => 'leibe',
            14295 => 'mua',
            37909 => 'diä',
            67102 => 'dänöi',
            69938 => 'dänyöi',
            69883 => 'tiäi',
            3540 => 'pedäi',
            69940 => 'astii',
            37234 => 'puu',
            62160 => 'hiili',
            30743 => 'hiiri',
            21425 => 'pieni',
            37342 => 'tuohi',
            36683 => 'lumi',
            13463 => 'lapsi',
            62360 => 'yksi',
            28722 => 'vezi',
            48744 => 'parži',
            70199 => 'alaine',
            49174 => 'petkel',
            46942 => 'paimen',
            28825 => 'tytär',
            70254 => 'härkin',
            18330 => 'pereh',
            40672 => 'barbaz',
            70262 => 'mätäz',
            47157 => 'kirvez',
            46865 => 'verez',
            46862 => 'veres',
            28730 => 'kaglus',
            40633 => 'kynäbrys',
            70267 => 'vahnuz',
            66796 => 'hyvyz',
            70269 => 'lapsut',
            51763 => 'lyhyd',
            70271 => 'd’algatoi',
            70272 => 'iänetöi',
            70274 => 'huondekselline'            
        ];
    }
    
    public static function getVerbs() {
        return [
            3461 => 'kaččoda',
            42494 => 'kuččuda',
            50380 => 'kyzydä',
            41301 => 'eččida',
            45142 => 'itkeda',
            22172 => 'andada',
            14596 => 'ottada',
            14594 => 'elädä',
            29444 => 'd’uoda',
            62863 => 'viedä',
            70904 => 'haravoita',
            29594 => 'tulda',
            3525 => 'mändä',
            67094 => 'purda',
            43235 => 'magata',
            22260 => 'pagišta',
            44615 => 'pestä',
            41869 => 'rubeta',
            62330 => 'suvaita',  
            41336 => 'suada'
        ];
    }   
    
    public static function getLemmas($what) {
        if ($what == 'verbs') {
            return [array_keys(Ludgen::getVerbs()), 11];
        } else {
            return [array_keys(Ludgen::getNames()), 5];
        }        
    }

    public static function groupedLemmas($words, $gramsets) {
        $dialect_id = Ludgen::dialect_id;
        
        foreach ($words as $id => $w) {
            $lemma = Lemma::find($id);
            $lemmas[$id]['lemma'] = $lemma->lemma;
            $lemmas[$id]['stem'] = $lemma->reverseLemma->stem;
            $lemmas[$id]['count'] = $lemma->wordforms()->wherePivot('dialect_id',$dialect_id)->count();
            
            foreach ($gramsets as $category_name => $category_gramsets) {
                foreach ($category_gramsets as $gramset_id => $gramset_name) {
                    foreach ($lemma->wordformsByGramsetDialect($gramset_id, $dialect_id) as $wordform) {
                        $lemmas[$id]['wordforms'][$gramset_id][] = self::analysWordform($wordform->wordform, $lemmas[$id]['stem']);
                    }
                }
            }
        }
        return $lemmas;
    }
    
    public static function analysWordform($wordform, $stem) {
        $prefix = $affix ='';
        if (preg_match("/^(.+\s+)(\S+)$/", $wordform, $regs)) {
            $prefix = $regs[1];
            $wordform = $regs[2];
        }
        if (preg_match("/^".$stem."(.*)$/u", $wordform, $regs)) {
            $affix = $regs[1];
        }
        return [$prefix, $affix];
    }
    
    public static function getAffixes($lem_ids, $gramsets, $what) {
//dd($gramsets);
        $affixes = $lemmas = [];
        foreach ($lem_ids as $lemma_id) {
            $lemmas[] = Lemma::find($lemma_id);
        }
        foreach (array_values($gramsets) as $category_gramsets) {
            foreach (array_keys($category_gramsets) as $gramset_id) {      
                if ($what == 'verbs') {
                    $affixes[$gramset_id]['вспом. глаголы'] = self::getPrefixes($lemmas, $gramset_id);                    
                }
                $affixes[$gramset_id]['окончания'] = self:: maxFlexion($lemmas, $gramset_id);
            }
        } 
//dd($affixes);        
        return $affixes;
    }
    
    // ищем максимальное совпадение окончаний
    public static function maxFlexion($lemmas, $gramset_id) {
        $dialect_id = Ludgen::dialect_id;
        $flexions = $wordforms = [];
        foreach ($lemmas as $lemma) {
            $ws = $lemma->wordformsByGramsetDialect($gramset_id, $dialect_id);
            foreach ($ws as $i => $w) {
                $wordforms[$i][] = $w->wordform;
            }
        }
//if ($gramset_id==3) { dd($wordforms); }
        foreach ($wordforms as $i => $ws) {
            $f = $ws[0];
            for ($j=1; $j<sizeof($ws); $j++) {
               while (strlen($f) && !preg_match("/".$f."$/", $ws[$j])) {
                   $f = substr($f, 1);
               } 
            }
            $flexions[$i] = $f;
        }
//if ($gramset_id==3) { dd($flexions); }
        return array_filter($flexions);
    }
    
    public static function getPrefixes($lemmas, $gramset_id) {
        $dialect_id = Ludgen::dialect_id;
        $prefixes = [];
        foreach ($lemmas as $lemma) {
            foreach ($lemma->wordformsByGramsetDialect($gramset_id, $dialect_id) as $wordform) {
                $words = preg_split("/\s+/", $wordform->wordform);
                if (sizeof($words) == 1) {
                    continue;
                }
                array_pop($words); // удаляем последнее слово
                $prefixes[] = join(' ', $words);
            }
        }
        return array_unique($prefixes);
    }
    
    public static function getBases1($lemmas) {
        $dialect_id = Ludgen::dialect_id;
        $gramset_id = 3; // генитив, ед.ч.
        $bases=[];
//dd($lemmas);        
        foreach ($lemmas as $lemma_id) {
            $tmp = [];
            $lemma = Lemma::find($lemma_id);
if (!$lemma) {
    dd($lemma_id);
}            
            foreach ($lemma->wordformsByGramsetDialect($gramset_id, $dialect_id) as $wordform) {
                if (preg_match("/^(.+)n$/", $wordform->wordform, $regs)) {
                    $tmp[] = $regs[1];
                } else {
                    dd('У генитива нет окончания -n');
                }
            }
            $bases[$lemma->id] = [$lemma->lemma, join(', ', $tmp)];
        }
        return $bases;
    }
}

