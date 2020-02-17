<?php
//app/Helpers/Array.php
namespace App\Helpers;
 
class Arrays {
    public static function array_key_first(array $arr) {
        foreach($arr as $key => $unused) {
            return $key;
        }
        return NULL;
    }
}