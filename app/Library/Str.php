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
}
