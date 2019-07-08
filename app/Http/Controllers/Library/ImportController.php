<?php

namespace App\Http\Controllers\Library;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;
use App\Http\Requests;

use App\Library\Import\DictParser;
use App\Models\Dict\Lemma;

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
        $filename = 'import/dict_tver3_b.txt';
//        $filename = 'import/line.txt';
        $lang_id = 4;
        $dialect_id=47; // new written tver karelian
        $label_id = 1;

        $file_content = Storage::disk('local')->get($filename);
        $file_lines = preg_split ("/\r\n/",$file_content);
print "<pre>";        
        $count = 0;
        foreach ($file_lines as $line) {
            $count++;
            if (!$line || mb_strlen($line)<2) {
                continue;
            }
            $entry = DictParser::parseEntry($line, $dialect_id);
            if (DictParser::checkEntry($entry, $line, $count)) {
var_dump($entry);            
//dd($entry);            
                DictParser::saveEntry($entry, $lang_id, $dialect_id, $label_id);
            }
        }
    }
    
}
