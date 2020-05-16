<?php

namespace App\Library;

class Str
{
    public static function highlightTail($word, $tail, $b_div='<b>', $e_div='</b>') {
        if (preg_match("/^(.*)".$tail."$/", $word, $regs)) {
            return $regs[1].$b_div.$tail.$e_div;
        }
        return $word;
    }
    
    public static function arrayToString($arr, $b_div='<b>', $e_div='</b>') {
        $out = '';
        $count = 1;
        foreach ($arr as $p=>$c) { 
            $out .= $b_div."$p".$e_div.": $c";
            if ($count<sizeof($arr)) {
                $out .= ", ";
            }
            $count++;
        }   
        return $out;
    }

    /** Takes data from search form (part of speech, language) and 
     * returns string for url such_as 
     * pos_id=$pos_id&lang_id=$lang_id
     * IF value is empty, the pair 'argument-value' is ignored
     * 
     * @param Array $url_args - array of pairs 'argument-value', f.e. ['pos_id'=>11, lang_id=>1]
     * @return String f.e. 'pos_id=11&lang_id=1'
     */
    public static function searchValuesByURL(Array $url_args=NULL) : String
    {
        $url = '';
        if (isset($url_args) && sizeof($url_args)) {
            $tmp=[];
            foreach ($url_args as $a=>$v) {
                if (is_array($v)) {
                    foreach ($v as $k=>$value) {
//                        $tmp[] = $a."[".$k."]=".$value;
                        $tmp[] = $a."%5B%5D=".$value;
                    }
                }
                elseif ($v!='' && !($a=='page' && $v==1) && !($a=='limit_num' && $v==10)) {
                    $tmp[] = "$a=$v";
                }
            }
           if (sizeof ($tmp)) {
                $url .= "?".implode('&',$tmp);
            }
        }
        
        return $url;
    }
    
    
    public static function urlArgs($request) {
        $url_args = [
                    'limit_num' => (int)$request->input('limit_num'),
                    'page'      => (int)$request->input('page'),
                ];
//dd($url_args);        
        if (!$url_args['page']) {
            $url_args['page'] = 1;
        }
        
        if ($url_args['limit_num']<=0) {
            $url_args['limit_num'] = 10;
        } elseif ($url_args['limit_num']>1000) {
            $url_args['limit_num'] = 1000;
        }   
//var_dump($url_args); 
//print '<br>';
              
        return $url_args;
    }
    
    public static function reverse($str){
        $reverse = '';
        for ($i = mb_strlen($str); $i>=0; $i--) {
            $reverse .= mb_substr($str, $i, 1);
        }
        return $reverse;
    }   
}
