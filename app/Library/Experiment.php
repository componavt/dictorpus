<?php

namespace App\Library;

use DB;
use Storage;

use \App\Charts\ExperimentValuation;

use App\Models\Dict\Gramset;
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

    public static function evaluateSearchPosGramset($search_lang, $table_name, $property, $wordform) {
        $property_id = $property.'_id';
print "<p><b>".$wordform->wordform."</b>";   
        list($ending,$list) = self::searchPosGramsetByWord($search_lang, $wordform->wordform, $property);     
        if (!$list) {
            DB::statement("UPDATE $table_name SET ending=NULL"
                         .", eval_end=0, eval_end_gen=0, win_end=NULL"
                         ." where wordform like '".$wordform->wordform."' and lang_id=".$search_lang);

        } else {
print "<br>COUNTS: ";                
foreach ($list as $p=>$c) {
print "<b>$p</b>: $c, ";
}   
            $wordforms = DB::table($table_name)
                       ->whereLangId($search_lang)
                       ->where('wordform', 'like', $wordform->wordform)
                       ->get();
            $eval_ends = $winners = [];
            foreach ($wordforms as $w) { 
//                self::writeList($table_name.'_list', $w->id, $property_id, 'end', $list);
                list($eval_ends[$w->id], $winners[$w->id]) = self::getEvalForOneValue($list, $w->{$property_id});
print "<br>".$w->{$property_id}.": ". $eval_ends[$w->id];
            }
//dd($winners);
            $max = max($eval_ends);
            reset($list);
print "<br><b>max:</b> ".$max;  
            foreach ($eval_ends as $w_id=>$eval_end) {
                DB::statement("UPDATE $table_name SET ending='".$ending
                             ."', eval_end=$eval_end, eval_end_gen=$max"
                             .", win_end=".$winners[$w_id]." where id=".$w_id);
            }
        }        
    }
    
    public static function writeList($table_name_list, $search_id, $property_name, $type, $list) {
        foreach ($list as $p_id =>$count) {
            DB::table($table_name_list)->insert([
                'search_id' => $search_id,
                $property_name => $p_id,
                'count' => $count,
                'type' => $type
            ]);
        }
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
        if (!$ending) {
            return [];
        }
        $match_wordforms = DB::table($table_name)
                ->select($field, DB::raw('count(*) as count'))
                ->whereLangId($lang_id)
                ->where('wordform', 'not like', $word)
                ->where('wordform', 'like', '%'.$ending)
                ->groupBy($field)
                ->orderBy(DB::raw('count(*)'), 'DESC')
                ->get();
        $list = [];
        foreach ($match_wordforms as $m_wordform) {
            $list[$m_wordform->{$field}] = $m_wordform->count;
        }
        return $list;
    }
        
    public static function evaluateSearchGramsetByAffix($wordform, $search_lang) {
print "<p><b>".$wordform->wordform."</b>";   
        list($affix,$list) = self::searchGramsetByWordformAffix($wordform->wordform, $search_lang);     
        if (!$list) {
            DB::statement("UPDATE search_gramset SET affix=NULL,"
                         ." eval_aff=0, eval_aff_gen=0"
                         ." where wordform like '".$wordform->wordform."' and lang_id=".$search_lang);

        } else {
print "<br>COUNTS: ";                
foreach ($list as $p=>$c) { print "<b>$p</b>: $c, ";}   
            $wordforms = DB::table('search_gramset')
                       ->whereLangId($search_lang)
                       ->where('wordform', 'like', $wordform->wordform)
                       ->get();
            $eval_affs = $winners = [];
            foreach ($wordforms as $w) { 
                list ($eval_affs[$w->id], $winners[$w->id]) = self::getEvalForOneValue($list, $w->gramset_id);
print "<br>".$w->gramset_id.": ". $eval_affs[$w->id];
            }
            $max = max($eval_affs);
print "<br><b>max:</b> ".$max;  
            foreach ($eval_affs as $w_id=>$eval_aff) {
                DB::statement("UPDATE search_gramset SET affix='$affix',"
                             ." eval_aff=$eval_aff, eval_aff_gen=$max,"
                             ." win_aff=".$winners[$w_id]." where id=".$w_id);
            }
        }
    }

    /**
     * select gramset_id, count(*) as count from lemma_wordform, lemmas, wordforms where affix like 'ija' and lemma_wordform.lemma_id=lemmas.id and  lemma_wordform.wordform_id=wordforms.id and wordform  not like '% %' and wordform not like 'Aasija' and lang_id=4 group by gramset_id order by count desc;
     * select gramset_id, count(*) as count from lemma_wordform where affix like 'ija' and lemma_id in (select id from lemmas where lang_id=4) and wordform_id in (select id from wordforms where wordform  not like '% %' and wordform not like 'Aasija') group by gramset_id order by count;
     * 
     * @param type $word
     * @param type $search_lang
     * @return type
     */
    public static function searchGramsetByWordformAffix($word, $search_lang) {
        $i=1;
        $match_wordforms = NULL;
        $ending = $word;
        while ($i<mb_strlen($word) && (!$match_wordforms || $match_wordforms->count()==0)) {
            $ending = mb_substr($word,$i);
            $match_wordforms = LemmaWordform::where('affix', 'like', $ending)
                     ->join('lemmas', 'lemmas.id', '=', 'lemma_wordform.lemma_id')
                     ->where('lang_id', $search_lang)
                     ->join('wordforms', 'wordforms.id', '=', 'lemma_wordform.wordform_id')
                     ->where('wordform', 'not like', '% %') // without analytic forms
                     ->where('wordform', 'not like', $word)
                     ->select('gramset_id', DB::raw('count(*) as count'))
                     ->groupBy('gramset_id')
                     ->orderBy(DB::raw('count(*)'), 'DESC');
                     //->get();
            $i++;
        }
        if (!$match_wordforms || $match_wordforms->count()==0) {
            return [NULL, NULL];
        }
//print "<br><b>$ending</b>";        
        $list = [];
        foreach ($match_wordforms->get() as $m_wordform) {
            $list[$m_wordform->gramset_id] = $m_wordform->count;
        }
        return [$ending, $list];
    }
    
    /**
     * select wordform_id,gramset_id from lemma_wordform, lemmas, wordforms where lemma_wordform.lemma_id=lemmas.id and  lemma_wordform.wordform_id=wordforms.id and wordform  not like '% %' and wordform not like 'Aasija' and lang_id=4 and affix like 'ija';
     * 
     * @param type $word
     * @param type $search_lang
     * @return type
     */
    public static function searchGramsetByAffixWithWList($word, $search_lang) {
        $i=1;
        $match_wordforms = NULL;
        $ending = $word;
        while ($i<mb_strlen($word) && (!$match_wordforms || $match_wordforms->count()==0)) {
            $ending = mb_substr($word,$i);
            $match_wordforms = LemmaWordform::join('lemmas', 'lemmas.id', '=', 'lemma_wordform.lemma_id')
                     ->join('wordforms', 'wordforms.id', '=', 'lemma_wordform.wordform_id')
                     ->where('wordform', 'not like', '% %') // without analytic forms
                     ->where('wordform', 'not like', $word)
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
//print "<br><b>$ending</b>";        
        $list = [];
        foreach ($match_wordforms->get() as $m_wordform) {
//print "<br>".$m_wordform->wordform.", ".$m_wordform->gramset_id;            
            $list[$m_wordform->gramset_id] = !isset($list[$m_wordform->gramset_id])
                                           ? 1 : 1+$list[$m_wordform->gramset_id];
        }
        arsort($list);
        return [$ending, $list];
    }
    
    public static function searchGramsetByAffix($wordform, $search_lang) {
        $match_wordforms = LemmaWordform::where('affix', 'like', $wordform->affix)
                 ->join('lemmas', 'lemmas.id', '=', 'lemma_wordform.lemma_id')
                 ->where('lang_id', $search_lang)
                 ->join('wordforms', 'wordforms.id', '=', 'lemma_wordform.wordform_id')
                 ->where('wordform', 'not like', '% %') // without analytic forms
                 ->where('wordform', 'not like', $wordform->wordform)
                 ->select('gramset_id', DB::raw('count(*) as count'))
                 ->groupBy('gramset_id')
                 ->orderBy(DB::raw('count(*)'), 'DESC');
                 //->get();
        if ($match_wordforms->count()==0) {
            return [];
        }
        $list = [];
        foreach ($match_wordforms->get() as $m_wordform) {
            $list[$m_wordform->gramset_id] = $m_wordform->count;
        }
        return $list;
    }
    
    public static function getEvalForOneValue($counts, $right_value) {
        reset($counts);
        $first_key = key($counts);
        $first_count = current($counts);
        if (!isset($counts[$right_value])) {
            $evaluation = 0;  
            $winner = $first_key;
        } elseif ($first_count == $counts[$right_value]) {
            $evaluation = 1;
            $winner = $right_value;
        } else {
            $evaluation = $counts[$right_value] / array_sum($counts);
            $winner = $first_key;
        }
        return [$evaluation, $winner];
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
             $list_proc[$k] = $sum==0 ? 0 : round(100*$v/$sum, 2);
        }
        return [$list, $list_proc];
    }
/*    
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
            list($search_pos,$pos_val) = self::valuationPosGramsetsByAffix(
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
*/    
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
    
// select gramset_id from search_gramset_list where type='end' and search_id=1 order by count desc limit 1;    
// select gramset_id from search_gramset_list where type='end' group by order by count desc limit 1;    
// select gramset_id, win_end, count(*) from search_gramset where lang_id=4 and win_end is not null and eval_end<>1 group by gramset_id, win_end order by gramset_id, win_end;    
    public static function createShiftErrors($lang_id, $table_name, $field, $all=false) {
        $shift_list = [];
        $name_coll = self::selectErrors($lang_id, $table_name, $all)
                   ->select($field)
                   ->groupBy($field)
                   ->orderBy(DB::raw('count(*)'), 'DESC')
                   ->get();
//dd($name_coll);    
        foreach ($name_coll as $p) {
            $w_coll = self::selectErrors($lang_id, $table_name, $all)
                    ->select('win_end', DB::raw('count(*) as count'))
                    ->where($field, $p->{$field})
                    ->groupBy('win_end')
                    ->orderBy('count', 'DESC')
                    ->get();
            foreach ($w_coll as $w) {        
                $shift_list[$p->{$field}][$w->win_end] = $w->count;
            }
        }
        return $shift_list;        
    }

    public static function selectErrors($lang_id, $table_name, $all=false) {
        $builder = DB::table($table_name)
                   ->whereLangId($lang_id)
                   ->whereNotNull('win_end')
                   ->whereNotNull('ending');
        if($all) {
            $builder->where('eval_end', '<>', 1);
        } else {
            $builder->where('eval_end_gen',0);            
        }
        return $builder;
    }
    
    public static function readShiftErrors($filename, $p_names) {
        $out = [];
        $file_content = Storage::disk('public')->get($filename);
        $file_lines = preg_split ("/\r?\n/",$file_content);
//dd($file_lines);        
        foreach ($file_lines as $line) {
            if (!$line) {
                continue;
            }
            list($p1,$p2,$count) = preg_split ("/\t/",$line);
            $out[$p_names[$p1]][$p_names[$p2]] = $count;
        }
//dd($out);        
        return $out;
     }
     
     public static function readShiftErrorsForDot($lang_id, $filename, $table_name, $property_id, $min_limit, $p_names, $total_limit) {
        $node_list = $totals = $edge_list = [];
        $file_content = Storage::disk('public')->get($filename);
        $file_lines = preg_split ("/\r?\n/",$file_content);
//dd($file_lines);        
        foreach ($file_lines as $line) {
            if (!$line) {
                continue;
            }
            list($p1,$p2,$count) = preg_split ("/\t/",$line);
            if (!isset( $totals[$p1])) {
                $totals[(string)$p1] = DB::table($table_name)
                          ->whereLangId($lang_id)
                          ->whereNotNull('ending')
                          ->where($property_id, $p1)
                          ->count();
            }
            if (!isset( $totals[$p2])) {
                $totals[(string)$p2] = DB::table($table_name)
                          ->whereLangId($lang_id)
                          ->whereNotNull('ending')
                          ->where($property_id, $p2)
                          ->count();
            }
            if ($total_limit && $totals[$p1]<=$total_limit) {
                continue;
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
        arsort($node_list);
        
        foreach ($node_list as $node => $total) {
            if (!isset($p_names[$node])) {
                print "unknown $node";
            }            
            $p_names[$node] = preg_replace("/, positive form/", "", $p_names[$node]);
            $node_list[$node] = preg_replace('/\s+/','\n',$p_names[$node]).'\n\n'.$total;
        }
        return [$node_list, $edge_list];
     }

    public static function writeShiftErrorsToDot($filename, $node_list, $edge_list, $with_claster, $property) {
        $color_names = ['darkgreen', 'darkgoldenrod3', 'brown', 'aquamarine3', 'darkorange2', 'crimson', 'indigo', 'navyblue', 'mistyrose3', 'peru'];
        $limit_color = 10;
        $limit_dotted = 10;
        $double_line_limit = 1000;
        
        Storage::disk('public')->put($filename, "digraph G {\n");
//                    "edge[colorscheme=accent8]\n"); 
        $colors = [];
        if ($with_claster) {
            $colors = self::writeNodesWithSub($filename, $node_list, $colors, $color_names, $double_line_limit);
        } else {
            $colors = self::writeNodes($filename, $node_list, $colors, $color_names, $double_line_limit, $property);
        }
        Storage::disk('public')->append($filename, '');
        self::writeEdges($filename, $edge_list, $limit_color, $colors, $limit_dotted);
        Storage::disk('public')->append($filename, "}"); 
    }
    
    /**
     * 
     * @param type $filename
     * @param type $node_list
     * @param type $colors
     * @param type $color_names
     * @param type $duble_line_limit
     * @return type
     */
    public static function writeNodes($filename, $node_list, $colors, $color_names, $double_line_limit, $property='pos') {
        $count_color = 0;
        foreach ($node_list as $node=>$label) {
            $line = "$node\t[label=\"".$label.'"';
            if (preg_match("/(\d+)$/", $label, $regs)) {
                $total = $regs[1];
            } else {
                $total=0;
            }
            if ($total > $double_line_limit) {
                $line .=", peripheries=2";                    
            }
            if ($property=='gramset' && Gramset::isIdForName($node)) {
                $line .=", shape=box";                    
            }
            if ($count_color < sizeof($color_names)) {
                $colors[$node] = $color_names[$count_color++];
                $line .=", color=".$colors[$node];                    
            }
            $line .= '];';
            Storage::disk('public')->append($filename, $line);
        }
        return $colors;
    }

    public static function writeNodesWithSub($filename, $node_list, $colors, $color_names, $double_line_limit) {
        $clasters = [0=>"label = \"Name gramsets\";\nstyle=filled;\ncolor=lightgrey;\n", 
                     1=>"label = \"Verb gramsets\";\ncolor=blue;\n"];
        $node_list = self::groupGramsetNodeList($node_list);
        $count = 0;
        foreach ($node_list as $claster_id => $nodes) {
            if (isset($clasters[$claster_id])) {
                Storage::disk('public')->append($filename, "subgraph cluster".$claster_id." {\n".$clasters[$claster_id]);    
            }
            self::writeNodes($filename, $nodes, $colors, $color_names, $double_line_limit);
            if (isset($clasters[$claster_id])) {
                Storage::disk('public')->append($filename, "}\n");
            }
        }
    }
    
    public static function writeEdges($filename, $edge_list, $limit_color, $colors, $limit_dotted) {
        foreach ($edge_list as $p1 =>$p_info) {
            foreach ($p_info as $p2 => $weight) {
                $line = "$p1 -> $p2\t[label=\"$weight %\", weight=$weight";
                if ($weight>$limit_color && isset($colors[$p1]))  {
                    $line .=", color=".$colors[$p1];
                } elseif($weight<$limit_dotted) {
                    $line .=", style=dotted";
                }
                $line .="];";
                Storage::disk('public')->append($filename, $line);
            }
            Storage::disk('public')->append($filename, '');
        }
    }

    public static function totalFill($table_name, $lang_id) {
        return DB::table($table_name)
                       ->whereLangId($lang_id)
                       ->count();
     }
     
     public static function evaluationCompletedInProcents($table_name, $lang_id, $total_num, $field = 'eval_end') {
//        $total_num = self::totalFill($table_name, $lang_id); 
        if (!$total_num) {
            return 0;
        }
        $completed = DB::table($table_name)
                       ->whereLangId($lang_id)
                       ->whereNotNull($field)
                       ->count();
        return 100*$completed/$total_num; 
    }
    
    public static function groupGramsetNodeList($node_list) {
        $grouped_list = [0=>[], 1=>[], 'other'=>[]];
        foreach ($node_list as $gramset_id => $gramset_name) {
            if (Gramset::isIdForName($gramset_id)) {
                $grouped_list[0][$gramset_id] = $gramset_name;
            } elseif (Gramset::isIdForVerb($gramset_id)) {
                $grouped_list[1][$gramset_id] = $gramset_name;                
            } else {
                $grouped_list['other'][$gramset_id] = $gramset_name;                
            }
        }
        return $grouped_list;
    }
    
    public static function writeWinners($search_lang, $table_name, $property, $wordform, $type) {
        $property_id = $property.'_id';
        if ($type == 'affix') {
            $list = self::searchGramsetByAffix($wordform, $search_lang);
        } else {
            $list = self::searchPosGramsetByEnding($search_lang, $wordform->wordform, $wordform->ending, $table_name, $property_id);
        }
        if (!$list || !sizeof($list)) {
            DB::statement("UPDATE $table_name SET win_end=NULL"
                         ." where wordform like '".$wordform->wordform."' and lang_id=".$search_lang);
            return;
        } else {
            $wordforms = DB::table($table_name)
                       ->whereLangId($search_lang)
                       ->where('wordform', 'like', $wordform->wordform)
                       ->get();
            $winners = [];
            foreach ($wordforms as $w) { 
                list($tmp, $winners[$w->id]) = self::getEvalForOneValue($list, $w->{$property_id});
            }
            foreach ($winners as $w_id=>$winner) {
                DB::statement("UPDATE $table_name SET win_".$type."=".$winner." where id=".$w_id);
            }
        }        
    }
}
