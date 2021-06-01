<?php

namespace App\Library\Experiments;

use DB;
//use Storage;
use App\Library\Str;

use App\Models\Dict\Wordform;

class PatternSearch
{
    public static function diff($str1, $str2) {
        $i=0;
        $is_diff=false;
        while ($i<mb_strlen($str1) && !$is_diff) {
            if (mb_substr($str1, $i, 1) != mb_substr($str2, $i, 1)) {
                $is_diff=true;
            } else {
                $i++;
            }
        }
        return [mb_substr($str1, $i), mb_substr($str2, $i)];
    }
    
    public static function letterGroups($lang_id, $dialect_id, $str, $deep, $dubles) {
/*        if ($deep<1) {
            return false;
        }*/
        $gen_count = 0;
        foreach (array_merge(range('a','z'),['ä','ö','ü','č','š','ž','’']) as $letter) {
            $ending = $letter.$str;
            $end_reverse = Str::reverse($ending);
            $groups = Wordform::where('wordform', 'like', '%'.$ending)
                         ->where('wordform', 'not like', '% %')
                         ->where('gramset_id','>',0)
                         ->whereNotIn('gramset_id', array_values($dubles))
                         ->join('lemma_wordform', 'lemma_wordform.wordform_id', '=', 'wordforms.id')
                         ->join('lemmas', 'lemma_wordform.lemma_id', '=', 'lemmas.id')
                         ->whereDialectId($dialect_id)
                         ->whereIn('lemma_id', function ($q2) use ($lang_id) {
                            $q2->select('id')->from('lemmas')
                               ->whereLangId($lang_id);
                         })->selectRaw("gramset_id, pos_id, count(*) as count")
                         ->groupBy('gramset_id', 'pos_id')->get();
            if (sizeof($groups)==0)  {
                continue;
            } elseif (sizeof($groups) > 1 && $deep>1) {
                $sub_count=self::letterGroups($lang_id, $dialect_id, $ending, $deep-1, $dubles);
                $gen_count += $sub_count;
                if ($sub_count>0) {
                   continue;
                }
            }                         
            foreach ($groups as $group) {
                DB::statement("INSERT INTO pattern_search 
                   (dialect_id, parent_end, ending, end_reverse, pos_id, gramset_id, count) 
                   VALUES ('$dialect_id', '$str', '$ending', '$end_reverse', '".$group['pos_id']."', '".$group['gramset_id']."', '".$group['count']."')");
                $gen_count +=$group['count'];
            }  
        }
        return $gen_count;
    }
    
    public static function getGramsets($dialect_id, $ending) {
        return DB::table('pattern_search')
                      ->whereDialectId($dialect_id)
                      ->where('ending', 'like', $ending)
                      ->orderBy('count', 'DESC')->get();
    }
}
