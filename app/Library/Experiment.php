<?php

namespace App\Library;

use DB;

use \App\Charts\ExperimentValuation;

use App\Models\Dict\LemmaWordform;

class Experiment
{
    
    public static function searchPosGramsetsByUniqueWordforms($wordform_obj) {
        $i=1;
        $match_wordforms = NULL;
        $s_wordform = $wordform_obj->wordform;
        while ($i<mb_strlen($s_wordform) && !$match_wordforms) {
            $str = mb_substr($s_wordform,$i);
            $match_wordforms = DB::table('unique_wordforms')
                     ->where('wordform', 'not like', $s_wordform)
                     ->where('wordform', 'like', '%'.$str)->get();
            $i++;
        }
        $match_wordforms = collect($match_wordforms);        
print "<p>$s_wordform, $str, ".$wordform_obj->pos_id.", ".$wordform_obj->gramsets;   
        if ($match_wordforms) {
            list($search_pos,$pos_val)=Experiment::valuationPosGramsetsByUniqueWordforms(
                    $match_wordforms, 'pos_id', $wordform_obj->pos_id);
            if ($pos_val>0) {
                list($search_gramsets,$gram_val)=Experiment::valuationPosGramsetsByUniqueWordforms(
//                    $match_wordforms->where('pos_id',$search_pos), 'gramsets', $wordform_obj->gramsets);
                    $match_wordforms, 'gramsets', $wordform_obj->gramsets);
            } else {
                //$search_gramsets = null;
                $gram_val = 0;
            }
        }
        return [$pos_val, $gram_val];
    }
    
    public static function valuationPosGramsetsByUniqueWordforms($match_wordforms, $property, $right_value) {
        $counts = [];
        foreach ($match_wordforms as $match_wordform) {
if ($property=='pos_id') {            
print '<br>'.$match_wordform->wordform.', '.$match_wordform->pos_id.', '.$match_wordform->gramsets; 
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
    
    public static function searchPosGramsetsByUniqueWordformsResults($table_name) {
        $total_num = DB::table($table_name)->whereNotNull('pos_val')->count();
        
        $pos_val_coll = DB::table($table_name)//->select('gram_val')
                ->select(DB::raw("ROUND(pos_val,1) as val"), DB::raw("count(*) as count"))
                ->whereNotNull('pos_val')
                ->groupBy('val')
                ->get();
        $gram_val_coll = DB::table($table_name)//->select('gram_val')
                ->select(DB::raw("ROUND(gram_val,1) as val"), DB::raw("count(*) as count"))
                ->whereNotNull('gram_val')
                ->groupBy('val')
                ->get();
//dd($pos_val_coll);   
        $pos_val = $gram_val = [];
        $pos_val_proc = $gram_val_proc = ['0'=>0, '0.1-0.5'=>0, '1'=>0];
        $pos_val_sum=$gram_val_sum=0;
        foreach ($pos_val_coll as $row) {
            $pos_val[(string)$row->val] = $row->count;
            if ($row->val >0 && $row->val<1) {
                $pos_val_proc['0.1-0.5'] += $row->count;
            } else {
                $pos_val_proc[(string)$row->val] = $row->count;                
            }
            $pos_val_sum +=$row->count;
        }
        foreach ($pos_val_proc as $k => $v) {
             $pos_val_proc[$k] = round(100*$v/$pos_val_sum, 2);
        }
        
        foreach ($gram_val_coll as $row) {
            $gram_val[(string)$row->val] = $row->count;
            if ($row->val >0 && $row->val<1) {
                $gram_val_proc['0.1-0.5'] += $row->count;
            } else {
                $gram_val_proc[(string)$row->val] = $row->count;                
            }
            $gram_val_sum +=$row->count;
        }
        foreach ($gram_val_proc as $k => $v) {
             $gram_val_proc[$k] = round(100*$v/$gram_val_sum,2);
        }
        
        $chart = new ExperimentValuation;
        $chart->labels(array_keys($pos_val));                
        $chart->dataset('по частям речи', 'line', array_values($pos_val))
              ->fill(false)
              ->color('#663399')
              ->backgroundColor('#663399');
        $chart->dataset('по грамсетам', 'line', array_values($gram_val))
              ->fill(false)
              ->color('#00BFFF')
              ->backgroundColor('#00BFFF');
        
        return ['total_num'=>$total_num, 'pos_val'=>$pos_val, 'gram_val'=>$gram_val, 
                'chart'=>$chart, 'pos_val_proc'=>$pos_val_proc, 'gram_val_proc'=>$gram_val_proc];
    }
    
    public static function searchPosGramsetsByAffix($wordform_obj, $search_lang) {
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
    
    
}
