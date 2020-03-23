<?php

namespace App\Http\Controllers\Library;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;
use App\Http\Requests;

use App\Library\Import\ConceptParser;
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
     * /import/dict_parser?search_lang=1
     * Reads text file with dictionary entries
     * extracts lemmas and writes lemmas, meanings, word forms to DB
     * 
     * Line example:
     * a|bu {-vu / -bu, -buo, -buloi} s. – помощь, поддержка; подспорье
     * 
     * !!!! ----- изменена Grammatic::nameNumFromNumberField -----    sing->sg -----   !!!!!
     */
    public function dictParser(Request $request) {
        $lang_id = (int)$request->input('search_lang');
        if ($lang_id == 1) {
            $filename = 'import/dict_veps.txt';
            $dialect_id=43; // new written veps
            $label_id = NULL;
        } else {
            $filename = 'import/dict_tver5.txt';
    //        $filename = 'import/line.txt';
            $lang_id = 4;
            $dialect_id=47; // new written tver karelian
            $label_id = 1; 
        }

        $file_content = Storage::disk('local')->get($filename);
        $file_lines = preg_split ("/\r?\n/",$file_content);
print "<pre>";        
        $count = 0;
        foreach ($file_lines as $line) {
            $count++;
            if (!$line || mb_strlen($line)<2) {
                continue;
            }
//$start_time = microtime(true); //начало измерения
            $entry = DictParser::parseEntry($line, $lang_id, $dialect_id);
//dd($entry);            
//$time_parsing = microtime(true);            
//print "<p><b>Time parsing ".$entry['lemmas'][0]." :".round($time_parsing-$start_time, 2).'</p>';
            if (DictParser::checkEntry($entry, $line, $count)) {
//var_dump($entry);            
//dd($entry);      
//$time_checking = microtime(true);            
//print "<p><b>Time checking ".$entry['lemmas'][0]." :".round($time_checking-$time_parsing,2).'</p>';
                DictParser::saveEntry($entry, $lang_id, $dialect_id, $label_id/*, $time_checking*/);
//$time_saving = microtime(true);            
//print "<p><b>Time saving ".$entry['lemmas'][0]." :".(float)($time_saving-$time_checking).'</p>';
            }
        }
    }
    
    public function conceptParser() {
        $filename = 'import/concept_dict_b.txt';
        $file_content = Storage::disk('local')->get($filename);
        $file_lines = preg_split ("/\r?\n/",$file_content);
print "<pre>";   
//dd($file_lines);
        list($categories, $blocks) = ConceptParser::readBlocks($file_lines);
//dd($categories);        
//dd($blocks);
        ConceptParser::saveCategories($categories); 
print "Категории сохранены.";        
        ConceptParser::processBlocks($blocks);
//dd($blocks['A11']);        
    }
}
