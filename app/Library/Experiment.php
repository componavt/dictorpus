<?php

namespace App\Library;

use DB;
use Storage;

use \App\Charts\ExperimentValuation;

use App\Models\Dict\LemmaWordform;

class Experiment
{
    public static function writePosGramset($table_name, $property_name, $search_lang, $wordform, $property_id) {
        if ($property_name=='pos_id' && $property_id == 14) {
            $property_id = 5;
        }
        $lemma_exists = DB::table($table_name)
                          ->whereLangId($search_lang)
                          ->where($property_name, $property_id)
                          ->where('wordform', 'like', $wordform)
                          ->count();
        if ($lemma_exists) {
            return 0;
        }
        DB::table($table_name)->insert([
            'lang_id' => $search_lang,
            'wordform' => $wordform,
            $property_name => $property_id
        ]);
        return 1;
    }

    public static function searchPosGramsetByWord($lang_id, $word, $property) {
        $i=1;
        $property_id = $property.'_id';
        $match_wordforms = NULL;
        $ending = $word;
        while ($i<mb_strlen($word) && !$match_wordforms) {
            $ending = mb_substr($word,$i);
            $match_wordforms = DB::table('search_'.$property)
                     ->select($property_id, DB::raw('count(*) as count'))
                     ->whereLangId($lang_id)
                     ->where('wordform', 'not like', $word)
                     ->where('wordform', 'like', '%'.$ending)
                     ->groupBy($property_id)
                     ->orderBy(DB::raw('count(*)'), 'DESC')
                     ->get();
            $i++;
        }
        if (!$match_wordforms) {
            return [NULL, NULL];
        }
//print "<br><b>$ending</b>";        
        $list = [];
        foreach ($match_wordforms as $m_wordform) {
            $list[$m_wordform->{$property_id}] = $m_wordform->count;
        }
        return [$ending, $list];
    }
    
    public static function searchPosGramsetByWordWithWList($lang_id, $word, $property) {
        $i=1;
        $property_id = $property.'_id';
        $match_wordforms = NULL;
        $ending = $word;
        while ($i<mb_strlen($word) && !$match_wordforms) {
            $ending = mb_substr($word,$i);
            $match_wordforms = DB::table('search_'.$property)
                     ->whereLangId($lang_id)
                     ->where('wordform', 'not like', $word)
                     ->where('wordform', 'like', '%'.$ending)->get();
            $i++;
        }
        if (!$match_wordforms) {
            return [NULL, NULL];
        }
//print "<br><b>$ending</b>";        
        $list = [];
        foreach ($match_wordforms as $m_wordform) {
//print "<br>".$m_wordform->wordform.", ".$m_wordform->{$property_id};            
            $list[$m_wordform->{$property_id}] = !isset($list[$m_wordform->{$property_id}])
                                           ? 1 : 1+$list[$m_wordform->{$property_id}];
        }
        arsort($list);
        return [$ending, $list];
    }
    
    public static function searchPosGramsetByEnding($lang_id, $word, $ending, $table_name, $field) {
        $match_wordforms = DB::table($table_name)
                 ->whereLangId($lang_id)
                 ->where('wordform', 'not like', $word)
                 ->where('wordform', 'like', '%'.$ending)->get();
        $list = [];
        foreach ($match_wordforms as $m_wordform) {
            $list[$m_wordform->{$field}] = !isset($list[$m_wordform->{$field}])
                                           ? 1 : 1+$list[$m_wordform->{$field}];
        }
        arsort($list);
        return $list;
    }
        
    public static function searchGramsetByAffix($word, $search_lang) {
        $i=1;
        $match_wordforms = NULL;
        $ending = $word;
        while ($i<mb_strlen($word) && (!$match_wordforms || $match_wordforms->count()==0)) {
            $ending = mb_substr($word,$i);
            $match_wordforms = LemmaWordform::join('lemmas', 'lemmas.id', '=', 'lemma_wordform.lemma_id')
                     ->join('wordforms', 'wordforms.id', '=', 'lemma_wordform.wordform_id')
                     ->where('wordform', 'not like', '% %') // without analytic forms
                     ->where('wordform_id', '<>', $word)
                     ->where('affix', 'like', $ending)
                     ->where('lang_id', $search_lang)
                     ->groupBy('wordform','gramset_id');
                     //->get();
            $i++;
        }
//dd($ending, $match_wordforms->get());            
        if (!$match_wordforms || $match_wordforms->count()==0) {
            return [NULL, NULL];
        }
print "<br><b>$ending</b>";        
        $list = [];
        foreach ($match_wordforms->get() as $m_wordform) {
print "<br>".$m_wordform->wordform.", ".$m_wordform->gramset_id;            
            $list[$m_wordform->gramset_id] = !isset($list[$m_wordform->gramset_id])
                                           ? 1 : 1+$list[$m_wordform->gramset_id];
        }
        arsort($list);
        return [$ending, $list];
    }
    
    
    public static function getEvalForOneValue($counts, $right_value) {
        reset($counts);
        $first_key = key($counts);
        $first_count = current($counts);
        if (!isset($counts[$right_value])) {
            $evaluation = 0;  
        } elseif ($first_count == $counts[$right_value]) {
            $evaluation = 1;
        } else {
            $evaluation = $counts[$right_value] / array_sum($counts);
        }
        return $evaluation;
    }
    
    public static function resultsSearch($lang_id, $table_name, $field='eval_end') {
        $total_num = DB::table($table_name)->whereLangId($lang_id)
                       ->whereNotNull($field)->count();
        
        list($eval1,$eval1_proc) = self::calculateEvalLists($lang_id, $table_name, $field);
        list($eval2,$eval2_proc) = self::calculateEvalLists($lang_id, $table_name, $field.'_gen');
        
        $chart = new ExperimentValuation;
        $chart->labels(array_keys($eval1));                
        $chart->dataset('по отдельности', 'line', array_values($eval1))
              ->fill(false)
              ->color('#663399')
              ->backgroundColor('#663399');
        $chart->dataset('по совокупности', 'line', array_values($eval2))
              ->fill(false)
              ->color('#00BFFF')
              ->backgroundColor('#00BFFF');
        
        return ['total_num'=>$total_num, 'eval1'=>$eval1, 'eval2'=>$eval2, 
                'chart'=>$chart, 'eval1_proc'=>$eval1_proc, 'eval2_proc'=>$eval2_proc];
    }
    
    public static function calculateEvalLists($lang_id, $table_name, $field) {
        $coll = DB::table($table_name)
                ->select(DB::raw("ROUND(".$field.",1) as eval"), DB::raw("count(*) as count"))
                ->whereLangId($lang_id)
                ->whereNotNull($field)
                ->groupBy('eval')
                ->orderBy('eval')
                ->get();
        
        $list = [];
        $list_proc = ['0'=>0, '0.1-0.5'=>0, '1'=>0];
        $sum=0;
        foreach ($coll as $row) {
            $list[(string)$row->eval] = $row->count;
            if ($row->eval >0 && $row->eval<1) {
                $list_proc['0.1-0.5'] += $row->count;
            } else {
                $list_proc[(string)$row->eval] = $row->count;                
            }
            $sum +=$row->count;
        }
        foreach ($list_proc as $k => $v) {
             $list_proc[$k] = round(100*$v/$sum, 2);
        }
        return [$list, $list_proc];
    }
    
    public static function searchGramsetsByAffix($wordform_obj, $search_lang) {
        $i=1;
        $match_wordforms = [];
        $s_wordform = $wordform_obj->wordform;
        while ($i<mb_strlen($s_wordform) && !sizeof($match_wordforms)) {
            $str = mb_substr($s_wordform,$i);
            $match_wordforms = LemmaWordform::join('lemmas', 'lemmas.id', '=', 'lemma_wordform.lemma_id')
                     ->join('wordforms', 'wordforms.id', '=', 'lemma_wordform.wordform_id')
                     ->where('wordform', 'not like', '% %') // without analytic forms
                     ->where('wordform_id', '<>', $wordform_obj->wordform_id)
                     ->where('affix', 'like', $str)
                     ->where('lang_id', $search_lang)
                     ->get();
            $i++;
        }
        
print "<p>$s_wordform, $str, ".$wordform_obj->pos_id.", ".$wordform_obj->gramset_id;   
        if (sizeof($match_wordforms)) {
            list($search_pos,$pos_val)=Experiment::valuationPosGramsetsByAffix(
                    $match_wordforms, 'pos_id', $wordform_obj->pos_id);
            if ($pos_val>0) {
                list($search_gramsets,$gram_val)=Experiment::valuationPosGramsetsByAffix(
                    $match_wordforms, 'gramset_id', $wordform_obj->gramset_id);
            } else {
                //$search_gramsets = null;
                $gram_val = 0;
            }
        }
        return [$pos_val, $gram_val];
    }
    
    public static function valuationPosGramsetsByAffix($match_wordforms, $property, $right_value) {
        $counts = [];
//dd($match_wordforms);        
        foreach ($match_wordforms as $match_wordform) {
if ($property=='pos_id') {            
print '<br>'.$match_wordform->affix.', '.$match_wordform->pos_id.', '.$match_wordform->gramset_id; 
}
            $counts[$match_wordform->{$property}] = !isset($counts[$match_wordform->{$property}])
                                                  ? 1 : 1+$counts[$match_wordform->{$property}];
        }
        arsort($counts);
        reset($counts);
        $first_key = key($counts);
        $first_count = current($counts);
//dd($first_count);        
        if (!isset($counts[$right_value])) {
            $valuation = 0;  
            $search_value = null;
        } elseif ($first_count == $counts[$right_value]) {
            $valuation = 1;
            $search_value = $right_value;
        } else {
            $valuation = $counts[$right_value] / array_sum($counts);
            $search_value = $right_value;
        }
print "<br><b>$property:</b> $first_key, <b>valuation:</b> $valuation";            
        return [$search_value, $valuation];
    }
    
    /**
     * !!!!TODO ДЛЯ грамсетов отобрать 10 самых частотных словоформ
     * ИЛИ упорядочить не по алфавиту, а 
     * select max(length(ending) as max from search_gramset;
     * 
     * @param type $table_name
     * @param type $field
     * @param type $names
     * @return type
     */
    public static function lenEndDistribution($lang_id, $table_name, $field, $names) {
        $name_coll = DB::table($table_name)
                ->select($field, DB::raw('count(*) as count'))
                ->whereLangId($lang_id)
                ->whereNotNull('ending')
                ->groupBy($field)
                ->orderBy('count', 'DESC')
                ->get();
        $max = DB::table($table_name)
                ->select(DB::raw('max(length(ending)) as max'))
                ->whereLangId($lang_id)
                ->whereNotNull('ending')
                ->first()->max;
//dd($max);        
        $min = DB::table($table_name)
                ->select(DB::raw('min(length(ending)) as min'))
                ->whereLangId($lang_id)
                ->whereNotNull('ending')
                ->first()->min;
        $list = [];
        foreach ($name_coll as $name) {
            $len_coll = DB::table($table_name)
                    ->select(DB::raw('length(ending) as len'), DB::raw('count(*) as count'))
                    ->whereLangId($lang_id)
                    ->whereNotNull('ending')
                    ->where($field, $name->{$field})
                    ->groupBy('len')
                    ->orderBy('len')
                    ->get();
            foreach ($len_coll as $l) {
                $list[$names[$name->{$field}]][$l->len] = $l->count;
            }
        }
        
        $len_list = range($min,$max);
        foreach ($list as $p_name => $p_info) {
            foreach ($len_list as $l) {
                if (!isset($p_info[$l])) {
                    $list[$p_name][$l] = '-';
                }
            }
            ksort($list[$p_name]);
        }
//dd($list);   
        $chart = self::lenEndDistributionChart($len_list, $list, $field);
        return ['p_list'=>$list, 'len_list'=>$len_list, 'chart' => $chart];
    }
    
    public static function lenEndDistributionChart($len_list, $p_list, $field) {        
        $chart = new ExperimentValuation;
        $chart->labels($len_list);   
        foreach ($p_list as $p_name => $p_info) {
            $chart->dataset($p_name, 'bar', array_values($p_info))
                  ->fill(false)
                  ->color('#663399')
                  ->backgroundColor('#663399');
        }
        return $chart;
    }
    
    public static function createShiftErrors($lang_id, $table_name, $field) {
        $shift_list = [];
        $name_coll = DB::table($table_name)
                ->select($field, DB::raw('count(*) as count'))
                ->whereLangId($lang_id)
                ->whereNotNull('ending')
                ->where('eval_end_gen',0)
                ->groupBy($field)
                ->orderBy('count', 'DESC')
                ->get();
//dd($name_coll);    
        foreach ($name_coll as $p) {
            $w_coll = DB::table($table_name)
                    ->select('wordform', 'ending')
                    ->whereLangId($lang_id)
                    ->whereNotNull('ending')
                    ->where('eval_end_gen',0)
                    ->where($field, $p->{$field})
                    ->get();
            foreach ($w_coll as $w) {        
                $list = self::searchPosGramsetByEnding($lang_id, $w->wordform, $w->ending, $table_name, $field);
                reset($list);
                $search_p = key($list);
                $shift_list[$p->{$field}][$search_p] = !isset($shift_list[$p->{$field}][$search_p])
                                                       ? 1 : 1+ $shift_list[$p->{$field}][$search_p];
            }
        }
        return $shift_list;        
    }
     public static function readShiftErrors($filename, $p_names) {
        $out = [];
        $file_content = Storage::disk('public')->get($filename);
        $file_lines = preg_split ("/\r?\n/",$file_content);
//dd($file_lines);        
        foreach ($file_lines as $line) {
            list($p1,$p2,$count) = preg_split ("/\t/",$line);
            $out[$p_names[$p1]][$p_names[$p2]] = $count;
        }
//dd($out);        
        return $out;
     }
     
     public static function readShiftErrorsForDot($lang_id, $filename, $table_name, $property_id, $min_limit, $p_names) {
        $node_list = $totals = $edge_list = [];
        $file_content = Storage::disk('public')->get($filename);
        $file_lines = preg_split ("/\r?\n/",$file_content);
//dd($file_lines);        
        foreach ($file_lines as $line) {
            list($p1,$p2,$count) = preg_split ("/\t/",$line);
            if (!isset( $nodes[$p1])) {
                $totals[$p1] = DB::table($table_name)
                          ->whereLangId($lang_id)
                          ->whereNotNull('ending')
                          ->where($property_id, $p1)
                          ->count();
            }
            if (!isset( $nodes[$p2])) {
                $totals[$p2] = DB::table($table_name)
                          ->whereLangId($lang_id)
                          ->whereNotNull('ending')
                          ->where($property_id, $p2)
                          ->count();
            }
            $w = 100*$count/$totals[$p1];
            if($w < 10) {
                $weight = round($w, 1); // 2.4% if < 10%
            } else {
                $weight = round($w);
            }
            if ($weight>$min_limit) {
                $edge_list[$p1][$p2] = $weight;
            }
        }
        
        foreach ($edge_list as $p1 =>$p_info) {
            $node_list[$p1] = $totals[$p1];
            
            foreach ($p_info as $p2 => $weight) {
                $node_list[$p2] = $totals[$p2];
            }
        }
//dd($node_list);        
//        print "<pre>";
//var_dump($edge_list);        
        arsort($node_list);
//dd($node_list);        
        
        foreach ($node_list as $node => $total) {
if (!isset($p_names[$node])) {
    print "unknown $node";
}            
            $node_list[$node] = preg_replace('/\s+/','\n',$p_names[$node]).'\n'.$total;
        }
//dd($out);        
        return [$node_list, $edge_list];
     }
}
