<?php namespace App\Traits;

use DB;
use Storage;
use LaravelLocalization;
use \Venturecraft\Revisionable\Revision;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;

use App\Library\Grammatic;
use App\Library\Str;

use App\Models\User;

use App\Models\Corpus\Event;
use App\Models\Corpus\Sentence;
use App\Models\Corpus\Source;
use App\Models\Corpus\Transtext;
use App\Models\Corpus\Word;

use App\Models\Dict\Gramset;
use App\Models\Dict\Meaning;
use App\Models\Dict\Wordform;

trait TextMarkup
{
    public static function preProcessText($text) {
        $end1 = ['.','?','!','…','|'];
        $end2 = ['.»','?»','!»','."','?"','!"','.”','?”','!”','.“'];
        $pseudo_end = false;
        if (!in_array(mb_substr($text,-1,1),$end1) && !in_array(mb_substr($text,-1,2),$end2)) {
            $text .= '.';
            $pseudo_end = true;
        }
        $text = str_replace("\r\n", "\n", nl2br($text));
        return [$text, $pseudo_end];
    }

    
    /**
     * Gets a markup text with sentences
     * 
     * ^ - to ignore end of sentence
     *
     * @param string $text  text without mark up
     * @param boolean $with_words  if it is true, sentences divided into words
     * @param boolean $by_sentences  if it is true, return only text structure and the array of sentences
     *                                        false, return full marked text
     * @return string text with markup (split to sentences and words) if $by_sentences=false
     *      OR [<markup text>, <sentences>] if $by_sentences=true
     */
    public static function markupText($text, $with_words=true, $by_sentences=false)
    {
        list($text, $pseudo_end) = self::preProcessText(trim($text));
        $text = convert_quotes($text);
        
        $text_xml = '';
        $sen_count = $word_count = 1;
        $sentences = [];
        $prev='';

        if (preg_match_all("/(.+?)(\||\.|\?|!|\.»|\?»|!»|\.\"|\?\"|!\"|\.”|\?”|!”|…{1,})(\s|(\<br \/\>\n)+?|$)/is", // :| //
                           $text, $desc_out)) {
            for ($k=0; $k<sizeof($desc_out[1]); $k++) {
                $sentence = $prev.trim($desc_out[1][$k]);
                
                if ($k == sizeof($desc_out[1])-1 && $pseudo_end || $desc_out[2][$k] == '|') {
                    $desc_out[2][$k] = '';
                }

                
                if ($k<sizeof($desc_out[1])-1 && preg_match("/^\s*\^/", $desc_out[1][$k+1])) {
                    $prev = $sentence.$desc_out[2][$k];
                    continue;
                }
                
                $prev = '';

                // <br> in in the beginning of the string is moved before the sentence
                while (preg_match("/^(<br(| \/)>)(.+)$/is",$sentence,$regs)) {
                    $text_xml .= $regs[1]."\n";
                    $sentence = trim($regs[3]);
                }
                // division on words
                list($str,$word_count) = Sentence::markup($sentence,$word_count);
//                $str = str_replace('¦', '', $str);
                $sentences[$sen_count] = "<s id=\"".$sen_count.'">'.$str.$desc_out[2][$k]."</s>\n";
                $text_xml .= $by_sentences ? "<s id=\"".$sen_count.'"/>'
                                                 : $sentences[$sen_count];
                $sen_count++;
                $div = trim($desc_out[3][$k]);
                $text_xml .= $div ? $div."\n" : '';
            }
        }
//dd($text_xml);
        return $by_sentences ? [trim($text_xml), $sentences] : trim($text_xml);
    }
    
    /**
     * Sets text_xml as a markup text with sentences
     */
    public function markup(){
        ini_set('max_execution_time', 7200);
        ini_set('memory_limit', '512M');
        list($this->text_structure, $sentences) = self::markupText($this->text, true, true);
//dd($this->text_structure, $sentences);        
        foreach ($sentences as $s_id => $text_xml) {
            $sentence = Sentence::store($this->id, $s_id, $text_xml);
            $error_message = $this->updateMeaningAndWordformText($sentence, $text_xml);
            if ($error_message) {
                print $error_message;
            }
        }
        DB::statement("DELETE FROM sentences WHERE s_id>$s_id and text_id=".(int)$this->id);
    }

    public function cyrToSentence($sentence, $words) {
        if (empty($sentence) || empty ($words)) {
            return $sentence;
        }
        foreach ($words as $i => $word) {
            $sentence = preg_replace("/(<w\s+id=\"".$i."\"\>[^<]+)(\<\/w\>)/", '${1}<sup>'.$word.'</sup>${2}', $sentence);
        }
        return $sentence;
    }
}