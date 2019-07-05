<?php

namespace App\Http\Controllers\Library;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;
use App\Http\Requests;


use App\Library\Import\DictParser;

class ImportController extends Controller
{
    public function __construct(Request $request)
    {
        // permission= dict.edit, redirect failed users to /dict/lemma/, authorized actions list:
        $this->middleware('auth:admin,/');
    }
    
    /*
     * Reads text file with dictionary entries
     * extracts lemmas and writes lemmas, meanings, word forms to DB
     * 
     * Line example:
     * a|bu {-vu / -bu, -buo, -buloi} s. – помощь, поддержка; подспорье
     */
    public function dictParser() {
        $filename = 'import/dict_tver2.txt';
        $dialect_id=47; // new written tver karelian
//        $filename = 'import/line.txt';

        $file_content = Storage::disk('local')->get($filename);
        $file_lines = preg_split ("/\r\n/",$file_content);
print "<pre>";        
        $count = 1;
        foreach ($file_lines as $line) {
            if (!$line) {
                continue;
            }
            $entry = DictParser::parseEntry($line, $dialect_id);
            DictParser::checkEntry($entry, $line, $count);
//var_dump($entry);            
            $count++;
        }
    }
    
}
