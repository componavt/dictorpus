<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

use App\Models\Corpus\Corpus;
use App\Models\Dict\Dialect;
use App\Models\Corpus\Event;
use App\Models\Corpus\Informant;
use App\Models\Dict\Lang;
use App\Models\Corpus\Source;
use App\Models\Corpus\Transtext;

class Text extends Model
{
    protected $fillable = ['corpus_id','lang_id','source_id','event_id','title','text','text_xml'];
    
//    protected $delimeters = [',', '.', '!', '?', ':', '-', '\'', '"', '[', ']', '(', ')', '{', '}', '«', '»', '='];

    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    
    public static function boot()
    {
        parent::boot();
    }
/*    
    public function getDelimeters()
    {
        return $this->delimeters;
    }    
*/
    // Text __belongs_to__ Lang
    public function lang()
    {
        return $this->belongsTo(Lang::class);
    }    
    
    // Text __belongs_to__ Corpus
    public function corpus()
    {
        return $this->belongsTo(Corpus::class);
    }    
    
    // Text __belongs_to__ Event
    public function event()
    {
        return $this->belongsTo(Event::class);
    }    
    
    // Text __belongs_to__ Source
    public function source()
    {
        return $this->belongsTo(Source::class);
    }    
    
    // Text __belongs_to__ Transtext
    public function transtext()
    {
        return $this->belongsTo(Transtext::class);
    }    
    
    // Text __has_many__ Dialects
    public function dialects(){
        $builder = $this->belongsToMany(Dialect::class);
        return $builder;
    }
    
    // Text __has_many__ Genres
    public function genres(){
        $builder = $this->belongsToMany(Genre::class);
        return $builder;
    }
    
    /**
     * Gets IDs of recorders for record's form field
     * 
     * @return Array
     */
    public function recorderValue():Array{
        $recorder_value = [];
        if ($this->event && $this->event->recorders) {
            foreach ($this->event->recorders as $recorder) {
                $recorder_value[] = $recorder->id;
            }
        }
        return $recorder_value;
    }
    
    /**
     * Gets IDs of dialects for dialect's form field
     * 
     * @return Array
     */
    public function dialectValue():Array{
        $value = [];
        if ($this->dialects) {
            foreach ($this->dialects as $dialect) {
                $value[] = $dialect->id;
            }
        }
        return $value;
    }
    
    /**
     * Gets IDs of genres for genre's form field
     * 
     * @return Array
     */
    public function genreValue():Array{
        $value = [];
        if ($this->genres) {
            foreach ($this->genres as $genre) {
                $value[] = $genre->id;
            }
        }
        return $value;
    }
    
    /**
     * Gets a markup text with sentences
     * 
     * @param $text String
     * @return String
     */
    public static function markupText($text): String{
        $out = '';
        $sen_count = 1;
        $word_count = 1;
        $one_text = new Text;
        $delimeters = [',', '.', '!', '?', ':', '-', '\'', '"', '[', ']', '(', ')', '{', '}', '«', '»', '='];
/*  division on paragraphs and then on sentences       
        if (preg_match_all("/(.+?)(\r?\n){2,}/is",$text,$desc_out)) {
            foreach ($desc_out[0] as $ab) {
                $ab = nl2br($ab);
                $out_ab = '';
                if (preg_match_all("/(.+?)(\.|\?|!|:){1,}(\s|<br(| \/)>|<\/p>|<\/div>|$)/is",$ab,$desc_out)) {
                    foreach ($desc_out[0] as $sentence) {
                       $out_ab .= "\t<s id=\"".$count++.'">'.trim($sentence)."</s>\n";
                    }
                } 
                $out .= "<p>\n".$out_ab."</p>\n";
            }
        } 
*/        
        // division only on sentences
        $text = nl2br($text);
        if (preg_match_all("/(.+?)(\.|\?|!|:|\.»|\?»|!»|\.\"|\?\"|!\"){1,}(\s|(<br(| \/)>\s*){1,}|$)/is",
                           $text, $desc_out)) {
//dd($desc_out);
            for ($k=0; $k<sizeof($desc_out[1]); $k++) {
                $sentence = trim($desc_out[1][$k]);
                
                // <br> in in the beginning of the string is moved before the sentence
                if (preg_match("/^(<br(| \/)>)(.+)$/is",$sentence,$regs)) {
                    $out .= $regs[1]."\n";
                    $sentence = trim($regs[3]);
                }
                
                // division on tokens
//                $tokens = preg_split('/\s+/',$sentence);
//                $words = [];
                
//                foreach ($tokens as $token) {
//$token = '"<br />line,';                   
                    $str = '';
                    $i = 0;
                    $is_word = false;
                    $token = $sentence;
                    while ($i<strlen($token)) {
                        $char = substr($token,$i,1);
//                        $token_tale = substr($token,$i+1);
                        if (in_array($char, $delimeters) || preg_match("/\s/",$char)) {
                            if ($is_word) {
                                $str .= '</w>';
                                $is_word = false;
                            }
                            $str .= $char;
                        } elseif ($char == '<') { // && strpos($token,'>',$i+1)
                            if ($is_word) {
                                $str .= '</w>';
                                $is_word = false;
                            }
                            $j = strpos($token,'>',$i+1);
                            $str .= substr($token,$i,$j-$i+1);
                            $i = $j;
                        } else {
                            if (!$is_word) {
                                $str .= '<w id="'.$word_count++.'">';
                                $is_word = true;
                            }
                            $str .= $char;
                        }
                        $i++;
                    }
                    if ($is_word) {
                        $str .= '</w>';
                    }
//print "<p>$str</p>";                    
//                    $words[] = $str;
                    
//                }                
//                $out .= "<s id=\"".$sen_count++.'">'.join(' ',$words).$desc_out[2][$k]."</s>\n";
 
                $out .= "<s id=\"".$sen_count++.'">'.$str.$desc_out[2][$k]."</s>\n";
//                $out .= "<s id=\"".$sen_count++.'">'.$sentence.$desc_out[2][$k]."</s>\n";
                $div = trim($desc_out[3][$k]);
                if ($div) {
                    $out .= trim($div)."\n";
                }
            }
        } 
        
        return trim($out);
    }
    
    /**
     * Sets text_xml as a markup text with sentences
     */
    public function markup(){
        $this->text_xml = self::markupText($this->text);
    }
}
