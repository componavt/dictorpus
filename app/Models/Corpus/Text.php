<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use DB;
use LaravelLocalization;

use App\Models\Corpus\Corpus;
use App\Models\Corpus\Event;
use App\Models\Corpus\Informant;
use App\Models\Corpus\Source;
use App\Models\Corpus\Transtext;

use App\Models\Dict\Dialect;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\Meaning;
use App\Models\Dict\Wordform;

class Text extends Model
{
    protected $fillable = ['corpus_id','lang_id','source_id','event_id','title','text','text_xml'];
    
//    protected $delimeters = [',', '.', '!', '?', ':', '\'', '"', '[', ']', '(', ')', '{', '}', '«', '»', '=']; // '-', 

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
    
    // Text __has_many__ Meanings
    public function meanings(){
        $builder = $this->belongsToMany(Meaning::class)
                 -> withPivot('relevance');
        return $builder;
    }
    
    // Text __has_many__ Words
    public function words(){
        return $this->hasMany(Word::class);
//        return = $this->belongsToMany(Word::class,'meaning_text');
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
        $delimeters = [',', '.', '!', '?', '"', '[', ']', '(', ')', '{', '}', '«', '»', '=', '–', '”', ':']; // - and ' - part of word
        
        $end1 = ['.','?','!','…'];
        $end2 = ['.»','?»','!»','."','?"','!"','.”','?”','!”'];
        $text = trim($text);
        $pseudo_end = false;
        if (!in_array(substr($text,-1,1),$end1) && !in_array(substr($text,-1,2),$end2)) {
            $text .= '.';
            $pseudo_end = true;            
        }
        
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
//dd($text);        
        if (preg_match_all("/(.+?)(\.|\?|!|\.»|\?»|!»|\.\"|\?\"|!\"|\.”|\?”|!”|…){1,}(\s|(<br(| \/)>\s*){1,}|$)/is", // :|
                           $text, $desc_out)) {
//dd($desc_out);
            for ($k=0; $k<sizeof($desc_out[1]); $k++) {
                $sentence = trim($desc_out[1][$k]);
                
                // <br> in in the beginning of the string is moved before the sentence
                if (preg_match("/^(<br(| \/)>)(.+)$/is",$sentence,$regs)) {
                    $out .= $regs[1]."\n";
                    $sentence = trim($regs[3]);
                }
                if ($k == sizeof($desc_out[1])-1 && $pseudo_end) {
                    $desc_out[2][$k] = '';
                }
                
                // division on tokens
                $str = '';
                $i = 0;
                $is_word = false;
                $token = $sentence;
                while ($i<strlen($token)) {
                    $char = substr($token,$i,1);
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
 
                $out .= "<s id=\"".$sen_count++.'">'.$str.$desc_out[2][$k]."</s>\n";
//                $out .= "<s id=\"".$sen_count++.'">'.$sentence.$desc_out[2][$k]."</s>\n";
                $div = trim($desc_out[3][$k]);
                if ($div) {
                    $out .= trim($div)."\n";
                }
            }
        }
        
        if ($pseudo_end) {
            
        }
        
        return trim($out);
    }
    
    /**
     * Sets text_xml as a markup text with sentences
     */
    public function markup(){
        $this->text_xml = self::markupText($this->text);        
        $error_message = $this->updateMeaningText();
        if ($error_message) {
            return $error_message;
        }        
    }
    
    /**
     * Sets links meaning - text - sentence
     */
    public function updateMeaningText(){
        list($sxe,$error_message) = self::toXML($this->text_xml,$this->id);
/*        libxml_use_internal_errors(true);
        $sxe = simplexml_load_string('<?xml version="1.0" encoding="utf-8" standalone="yes" ?>'.
                                     '<text>'.$this->text_xml.'</text>');
        $error_text = '';
        if (!$sxe) {
            $error_text = "XML loading error\n". '('.$this->id.')';
            foreach(libxml_get_errors() as $error) {
                $error_text .= "\t". $error->message. '('.$this->id.')';
            }
            return $error_text;
        } */
        
        if ($error_message) {
            return $error_message;
        }

        $checked_words = [];
        $meanings = $this->meanings()->wherePivot('relevance','<>',1)
                         ->join('words','words.id','=','meaning_text.word_id')
                         ->get();
/*
        $meanings = DB::table('meaning_text')
                            ->join('words','words.id','=','meaning_text.word_id')
                            ->where('relevance','<>',1)
                            ->where('words.text_id',$text_id)
                            ->get();
 */
        foreach ($meanings as $meaning) {
            $checked_words[$meaning->w_id] =
                    [$meaning->word, $meaning->relevance];
        }
//dd($checked_words);        
        $this->words()->delete();
        $this->meanings()->detach();        
        
        foreach ($sxe->children()->s as $sentence) {
//            if ($sentence->getName() == 's') {
                $s_id = $sentence->attributes()->id;
                //print "<P>".$s_id .'.';
                foreach ($sentence->children()->w as $word) {
                    $w_id = $word->attributes()->id;
                    $w_id = (int)$w_id;
//dd($w_id);                    
                    $word_obj = Word::create(['text_id' => $this->id,
                                              'w_id' => $w_id,
                                              'word' => $word
                                            ]);
                    $meanings = [];
                    $lemmas = Lemma::select('id')->where('lang_id',$this->lang_id)
                            ->whereRaw("lemma like ?",[addcslashes(strtolower($word),"'%")]);
                    if ($lemmas->count()) {
                        foreach ($lemmas->get() as $lemma) {
                            foreach ($lemma->meanings as $meaning) {
                                $meanings[$meaning->id] = 1;
                            }
                        }
                    }
                    
                    $wordforms = Wordform::select('id')
                            ->whereRaw("wordform like ?",[addcslashes(strtolower($word),"'%")]);
                    if ($wordforms->count()) {
                        foreach ($wordforms->get() as $wordform) {
                            foreach ($wordform->lemmas as $lemma) {
                                if ($lemma->lang_id == $this->lang_id) {
                                    foreach ($lemma->meanings as $meaning) {
                                        $meanings[$meaning->id] = 1;
                                    }
                                }
                            }
                        }
                    }
                    
                    foreach (array_keys($meanings) as $meaning_id) {
                        $relevance = 1;
                        if (isset($checked_words[$w_id][0])) {
                            if ($checked_words[$w_id][0] == $word) {
                                $relevance = $checked_words[$w_id][1];
                            }
                        }
                        $this->meanings()->attach($meaning_id,
                                ['sentence_id'=>$s_id, 
                                 'word_id'=>$word_obj->id, 
                                 'w_id'=>$w_id, 
                                 'relevance'=>$relevance]);
                    }
                            
/*                    if ($lemmas->count() || $wordforms->count()) {
                        print ' ('.$w_id.')'. $word;
                    }*/
                }
                //print '</p>';
//            }
        }
//exit(0);
    }
    
    /**
     * Load xml from string, create SimpleXMLelement
     * 
     * @param $text_xml - markup text
     * @param id - identifier of object Text or Transtext
     * 
     * return Array [SimpleXMLElement object, error text if exists]
     */
    public static function toXML($text_xml, $id){
        libxml_use_internal_errors(true);
        $sxe = simplexml_load_string('<?xml version="1.0" encoding="utf-8" standalone="yes" ?>'.
                                     '<text>'.$text_xml.'</text>');
        $error_text = '';
        if (!$sxe) {
            $error_text = "XML loading error\n". '('.$id.')';
            foreach(libxml_get_errors() as $error) {
                $error_text .= "\t". $error->message. '('.$id.')';
            }
        }
        return [$sxe,$error_text];
    }
    
    /**
     * Gets markup text with links from words to related lemmas
     
     * @param $markup_text String
     * @return String markup text 
     **/
    public function setLemmaLink($markup_text){
        $text_id = (int)$this->id;
        list($sxe,$error_message) = self::toXML($markup_text,'');
        if ($error_message) {
            return $markup_text;
        }        
        $words = $sxe->xpath('//w');
        foreach ($words as $word) {
            $word_id = (int)$word->attributes()->id;
            $meanings = $this->meanings()->wherePivot('text_id',$text_id)
                             ->wherePivot('w_id',$word_id)
                             ->wherePivot('relevance','>',0);
            if ($meanings->count()) {                
                $link_block = $word->addChild('div');
                $link_block->addAttribute('class','links-to-lemmas');
                $link_block->addAttribute('id','links_'.$word_id);

                $has_checked_meaning = false;
                foreach ($meanings->get() as $meaning) {
                    $lemma = $meaning->lemma;

                    if ($meaning->pivot->relevance >1) {
                        $has_checked_meaning = true;
                    }

                    $link = $link_block->addChild('a',$lemma->lemma);
                    $link->addAttribute('href',LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id));
                    
                    $locale = LaravelLocalization::getCurrentLocale();
                    $meaning_text = $link->addChild('span',' ('.$meaning->getMultilangMeaningTextsString($locale).')');                    
                }
                
                $class = 'lemma-linked';
                if ($has_checked_meaning) {
                    $class .= ' has-checked';
                }
                $word->addAttribute('class',$class);
                
            }            
/*            $lemmas = Lemma::whereIn('id',function($query) use ($text_id, $word_id){
                                $query->select('lemma_id')
                                      ->from('meanings')
                                      ->whereIn('id',function($q) use ($text_id, $word_id){
                                            $q->select('meaning_id')
                                              ->from('meaning_text')
                                              ->where('text_id',$text_id)      
                                              ->where('word_id',$word_id)
                                              ->where('relevance','>',0);
                                              });
                                   })->orderBy('lemma'); 
            if ($lemmas->count()) {
                $word->addAttribute('class','lemma-linked');
                
                $link_block = $word->addChild('div');
                $link_block->addAttribute('class','links-to-lemmas');
                $link_block->addAttribute('id','links_'.$word_id);

                foreach ($lemmas->get() as $lemma) {
                    $link = $link_block->addChild('a',$lemma->lemma);
                    $link->addAttribute('href',LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id));
                    $meaning->getMultilangMeaningTextsString()
                }
            } */
            
        }
        
        return $sxe->asXML();
    }
    
}
