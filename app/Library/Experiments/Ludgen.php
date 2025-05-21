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
        $lemmas = [
            14295 => 'mua',
            37909 => 'diä',
            
            62004 => 'halgo',
            13424 => 'kirikkö',
            
            38637 => 'lindu',
            50254 => 'kydy',
            37234 => 'puu',
            
            47289 => 'leibe',
            50308 => 'emände',
            29962 => 'akke',
            47713 => 'nuotte',
            70274 => 'huondekselline',            
            70199 => 'alaine',
            
            21396 => 'd’ogi',
            37342 => 'tuohi',
            69940 => 'astii',
            62160 => 'hiili',
            36683 => 'lumi',
            21425 => 'pieni',
            30743 => 'hiiri',
            62360 => 'yksi',
            13463 => 'lapsi',
            28722 => 'vezi',
            48744 => 'parži',
            46747 => 'počči',
            
            70271 => 'd’algatoi',
            70272 => 'iänetöi',
            67102 => 'dänöi',
            69938 => 'dänyöi',
            69883 => 'tiäi',
            3540 => 'pedäi',

            51763 => 'lyhyd',
            18330 => 'pereh',            
            49174 => 'petkel',
            46942 => 'paimen',
            70254 => 'härkin',
            28825 => 'tytär',
            70269 => 'lapsut',
            40672 => 'barbaz',
            70262 => 'mätäz',
            47157 => 'kirvez',
            46865 => 'verez',
            46862 => 'veres',
            28730 => 'kaglus',
            40633 => 'kynäbrys',
            70267 => 'vahnuz',
            66796 => 'hyvyz',
        ];
/*        $reverse_lemmas = [];
        foreach ($lemmas as $lemma_id=>$lemma) {
            $reverse_lemmas[$lemma_id] = mb_strrev($lemma);
        }
        asort($reverse_lemmas);
        foreach ($reverse_lemmas as $lemma_id=>$lemma) {
            $reverse_lemmas[$lemma_id] = $lemmas[$lemma_id];
        }
dd($reverse_lemmas);  */
        return $lemmas;
    }
    
    public static function getVerbs() {
        return [
            3461 => 'kaččoda',
            42494 => 'kuččuda',
            50380 => 'kyzydä',
            41301 => 'eččida',
            22172 => 'andada',
            14596 => 'ottada',
            14594 => 'elädä',
            45142 => 'itkei',
            43596 => 'särbäi',
            29444 => 'd’uoda',
            62863 => 'viedä',
            41336 => 'suada',
            29594 => 'tulda',
            3525 => 'mändä',
            67094 => 'purda',
            22260 => 'pagišta',
            44615 => 'pestä',
            43235 => 'magata',
            41869 => 'rubeta',
            70904 => 'haravoita',
            62330 => 'suvaita',  
        ];
    }   
    
    public static function getMainGramsets($what) {
        if ($what == 'verbs') {
            return [26=>'1л. ед.ч. през. инд.', 28=>'3л. ед.ч. през. инд.', 32=>'1 л. ед. ч. имп. инд.', 34=>'3 л. ед. ч. имп. инд.',
                    179=>'перфект', 31=>'3 л. мн. ч. през. инд.', 37=>'3 л. мн. ч. имп. инд.',];            
        } 
        return [1=>'номинатив ед.ч.', 3=>'генетив ед.ч.', 4=>'партитив ед.ч.', 10=>'иллатив ед.ч.',
                2=>'номинатив мн.ч.', 24=>'генетив мн.ч.',22=>'партитив мн.ч.', 61=>'иллатив мн.ч.',];
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
       
        foreach ($words as $id) {
            $lemma = Lemma::find($id);
            if (!$lemma) {
                dd('Нет леммы с id='.$id);
            }
            $lemmas[$id]['lemma'] = $lemma->lemma;
            if (empty($lemma->reverseLemma)) {
                $lemma->reloadStemAffixByWordforms();
            }
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
    
    public static function getWordforms($words, $gramsets) {
        $dialect_id = Ludgen::dialect_id;
       
        foreach ($words as $id) {
            $lemma = Lemma::find($id);
            
            foreach ($gramsets as $gramset_id) {
                foreach ($lemma->wordformsByGramsetDialect($gramset_id, $dialect_id) as $wordform) {
                    $wordforms[$id][$gramset_id][] = self::analysWordform($wordform->wordform, $lemma->reverseLemma->stem);
                }
            }
        }
        return $wordforms;
    }
    
    public static function dictForms($words) {
        $out = [];
       
        foreach ($words as $id) {
            $lemma = Lemma::find($id);
            $out[$id] = $lemma->dictForm();
        }
        return $out;
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
        return [$prefix, $stem, $affix];
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
    
    public static function getBases($lemmas) {
        $dialect_id = Ludgen::dialect_id;
        $bases=[];
        foreach ($lemmas as $lemma_id) {
            $lemma = Lemma::find($lemma_id);
            for ($i=0; $i<8; $i++) {
                $bases[$lemma_id][$i] = $lemma->getBase($i, $dialect_id, $bases);
            }
//dd($bases);            
            if ($lemma->reverseLemma) {
                $bases[$lemma_id][10] = $lemma->harmony();
            }
/*            
            $bases[$lemma_id][0] = $lemma->lemma;
            
            $bases[$lemma_id][1] = Grammatic::getStemFromWordform($this, $base_n, $this->lang_id,  $this->pos_id, $dialect_id, $is_reflexive);

                    Ludgen::getBase1($lemma);
            $bases[$lemma_id][2] = Ludgen::getBase2($lemma);
            $bases[$lemma_id][3] = Ludgen::getBase3($lemma);
            $bases[$lemma_id][4] = Ludgen::getBase4($lemma);
            $bases[$lemma_id][5] = Ludgen::getBase5($lemma);
            $bases[$lemma_id][6] = '';
            $bases[$lemma_id][10] = KarGram::isBackVowels($lemma->lemma);*/
        }
        return $bases;
    }
    
    public static function getBase1($lemma) {
        $dialect_id = Ludgen::dialect_id;
        $gramset_id = 3; // генитив, ед.ч.

        $bases = [];
        foreach ($lemma->wordformsByGramsetDialect($gramset_id, $dialect_id) as $wordform) {
            if (preg_match("/^(.+)n$/", $wordform->wordform, $regs)) {
                $bases[] = $regs[1];
            } else {
                dd('У генитива нет окончания -n');
            }
        }
        return join(', ', $bases);
    }
    
    public static function getBase2($lemma) {
        $dialect_id = Ludgen::dialect_id;
        $gramset_id = 10; // иллатив, ед.ч.

        $bases = [];
        foreach ($lemma->wordformsByGramsetDialect($gramset_id, $dialect_id) as $wordform) {
            if (preg_match("/^(.+)h$/", $wordform->wordform, $regs)) {
                $bases[] = $regs[1];
            } else {
                dd('У иллатива нет окончания -h');
            }
        }
        return join(', ', $bases);
    }
    
    public static function getBase3($lemma) {
        $dialect_id = Ludgen::dialect_id;
        $gramset_id = 4; // партитив, ед.ч.

        $bases = [];
        foreach ($lemma->wordformsByGramsetDialect($gramset_id, $dialect_id) as $wordform) {
            $bases[] = $wordform->wordform;
        }
        return join(', ', $bases);
    }
    
    public static function getBase4($lemma) {
        $dialect_id = Ludgen::dialect_id;
        $gramset_id = 24; // генитив, мн.ч.

        $bases = [];
        foreach ($lemma->wordformsByGramsetDialect($gramset_id, $dialect_id) as $wordform) {
            if (preg_match("/^(.+)den$/", $wordform->wordform, $regs)) {
                $bases[] = $regs[1];
            } else {
                dd('У генитива '.$wordform->wordform.' мн.ч. нет окончания -den');
            }
        }
        return join('/', $bases);
    }
    
    public static function getBase5($lemma) {
        $dialect_id = Ludgen::dialect_id;
        $gramset_id = 22; // партитив, мн.ч.

        $bases = [];
        foreach ($lemma->wordformsByGramsetDialect($gramset_id, $dialect_id) as $wordform) {
            if (preg_match("/^(.+)d$/", $wordform->wordform, $regs)) {
                $bases[] = $regs[1];
            } else {
                dd('У партитива мн.ч. нет окончания -d');
            }
        }
        return join('/', $bases);
    }
}

