<?php

namespace App\Library\Experiments;

use DB;
use Storage;

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
}
