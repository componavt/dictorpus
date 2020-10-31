<?php

namespace App\Http\Controllers\Library;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;
use App\Http\Requests;

use App\Library\Grammatic;
use App\Library\Import\ConceptParser;
use App\Library\Import\DictParser;
use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;

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
    
    /*
     * /import/dict_zaikov_verb_parser
     * Reads text file with dictionary entries
     * extracts lemmas and writes lemmas, meanings, word forms to DB
     * 
     * Line example:
     * a|bu {-vu / -bu, -buo, -buloi} s. – помощь, поддержка; подспорье
     * 
     * !!!! ----- изменена Grammatic::nameNumFromNumberField -----    sing->sg -----   !!!!!
     */
    public function dictZaikovVerbParser(Request $request) {
        $lang_id = 4;
        $filename = 'import/dict_zaikov_verb.txt';
        $dialect_id=46; // new written proper karelian

        $file_content = Storage::disk('local')->get($filename);
        $file_lines = preg_split ("/\r?\n/",$file_content);
print "<pre>";        
        $count = 0;
        foreach ($file_lines as $line) {
            $count++;
            if (!$line || mb_strlen($line)<2) {
                continue;
            }
            $entry = DictParser::parseEntryZaikovVerb($line, $lang_id, $dialect_id);
//dd($entry);            
            if (DictParser::checkEntry($entry, $line, $count)) {
//dd($entry);      
                DictParser::saveEntry($entry, $lang_id, $dialect_id);
            }
        }
    }
    
    public function conceptParser(Request $request) {
        $fname = (int)$request->input('fname');
        if (!$fname) {$fname = 'concept_dict_b'; }
        $filename = 'import/'.$fname.'.txt';
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

    public function conceptParserCheck(Request $request) {
        $fname = (int)$request->input('fname');
        if (!$fname) {$fname = 'concept_dict_b'; }
        $filename = 'import/'.$fname.'.txt';
        $file_content = Storage::disk('local')->get($filename);
        $file_lines = preg_split ("/\r?\n/",$file_content);
        list($categories, $blocks) = ConceptParser::readBlocks($file_lines);
        ConceptParser::checkConcepts($blocks);
    }

    public function phoneticsToLemmas() {
        $infname = 'import/concept_dict_b.txt';
        $outfname = 'export/concept_words.txt';
        
        $file_content = Storage::disk('local')->get($infname);
        $file_lines = preg_split ("/\r?\n/",$file_content);
        
print "<pre>";   
//dd($file_lines);
        $words = ConceptParser::readWords($file_lines);
//dd($words);  
        Storage::disk('public')->put($outfname, "");
        foreach ($words as $word) {
            if (preg_match("/[΄ńηŕĺśźć]/ui", $word)) {
                Storage::disk('public')->append($outfname, $word. " => ". Grammatic::toRightForm($word));            
            }
        }
print "Найдено ".sizeof($words).' слов.';
    }
    
    // import/extract_livvic_verbs
    public function extractVerbs() {
        $infname = 'import/livvic_dict.txt';
        $outfname = 'export/livvic_verbs.txt';
        
        $file_content = Storage::disk('local')->get($infname);
        $file_lines = preg_split ("/\r?\n/",$file_content);

        Storage::disk('public')->put($outfname, "");

        foreach ($file_lines as $line) {
            if (preg_match("/^[^\.]+\([^\),;]+[,;][^\),;]+\)\s+v\.\s+/", $line)) {
                Storage::disk('public')->append($outfname, $line);            
            }
        }
        print "done.";
    }
    
    // import/extract_livvic_compound_words
    public function extractCompoundWords() {
        $infname = 'import/livvic_dict.txt';
        $outfname = 'export/livvic_compound_words.txt';
        
        $file_content = Storage::disk('local')->get($infname);
        $file_lines = preg_split ("/\r?\n/",$file_content);

        Storage::disk('public')->put($outfname, "");

        foreach ($file_lines as $line) {
            if (preg_match("/\|\|/", $line)) {
                Storage::disk('public')->append($outfname, $line);            
            }
        }
        print "done.";
    }
    
    // import/change_stem_for_compound_words
    public function changeStemForCompoundWords() {
        $infname = 'import/livvic_compound_words.txt';
        $dialect_id = 44;
        $lang_id=5;
        
        $file_content = Storage::disk('local')->get($infname);
        $file_lines = preg_split ("/\r?\n/",$file_content);

        foreach ($file_lines as $line) {
            if (preg_match("/^([^\s\(]+)/", $line, $regs)) {
                $word = preg_replace("/\|/", '', $regs[1]);
                $lemmas = Lemma::where('lemma', 'like', $word)
                               ->whereLangId($lang_id);
                if ($lemmas->count()) {
                    if (preg_match("/^(.+[^\|])\|([^\|]+)$/", $regs[1], $regs1)) {
//preg_replace('/\|\|/','ǁ',$regs[1]);
                        $stem = $regs1[1];
                        $affix = $regs1[2];
                    } else {
                        $stem=$regs[1];
                        $affix='';
                    }
                    foreach ($lemmas->get() as $lemma) {
                        $lemma->storeReverseLemma($stem, $affix);
                        if (in_array($lemma->pos_id, PartOfSpeech::getNameIDs())) {
                            $lemma->reloadWordforms($dialect_id);
                            $lemma->updateWordformTotal();        
                        }
                        print "<p><a href=\"/dict/lemma/".$lemma->id."\">$regs[1]</a> = $stem + $affix</p>";  
                    }
                } 
            }
        }
        print "done.";
    }
}
