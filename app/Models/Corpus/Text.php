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
use App\Models\Corpus\Word;

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
     * Gets IDs of informants for informant's form field
     *
     * @return Array
     */
    public function informantValue():Array{
        $informant_value = [];
        if ($this->event && $this->event->informants) {
            foreach ($this->event->informants as $informant) {
                $informant_value[] = $informant->id;
            }
        }
        return $informant_value;
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
     * @param $text String text without mark up
     * @return String text with markup (split to sentences and words)
     */
    public static function markupText($text): String
    {
        $out = '';
        $sen_count = 1;
        $word_count = 1;

        $end1 = ['.','?','!','…'];
        $end2 = ['.»','?»','!»','."','?"','!"','.”','?”','!”'];
        $text = trim($text);
        $pseudo_end = false;
        if (!in_array(mb_substr($text,-1,1),$end1) && !in_array(mb_substr($text,-1,2),$end2)) {
            $text .= '.';
            $pseudo_end = true;
        }

        $text = nl2br($text);
        if (preg_match_all("/(.+?)(\.|\?|!|\.»|\?»|!»|\.\"|\?\"|!\"|\.”|\?”|!”|…{1,})(\s|(<br(| \/)>\s*){1,}|$)/is", // :|
                           $text, $desc_out)) {
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

                // division on words
                list($str,$word_count) = Text::markupSentence($sentence,$word_count);

                $out .= "<s id=\"".$sen_count++.'">'.$str.$desc_out[2][$k]."</s>\n";
                $div = trim($desc_out[3][$k]);
                if ($div) {
                    $out .= trim($div)."\n";
                }
            }
        }

        return trim($out);
    }

    /**
     * Divides sentence on words
     *
     * @param $sentence String text without mark up
     * @param $word_count Integer initial word count
     *
     * @return Array text with markup (split to words) and next word count
     */
    public static function markupSentence($sentence,$word_count): Array
    {
        $delimeters = ',.!?"[](){}«»=”:'; // - and ' - part of word
        // different types of dashes and hyphens: '-', '‒', '–', '—', '―' 
        // if dashes inside words, then they are part of words,
        // if dashes surrounded by spaces, then dashes are not parts of words.
        $dashes = '-‒–—―';
        
        $str = '';
        $i = 0;
        $is_word = false; // word tag <w> is not opened
        $token = $sentence;
        while ($i<mb_strlen($token)) {
            $char = mb_substr($token,$i,1);
            if (mb_strpos($delimeters, $char)!==false || preg_match("/\s/",$char)) {
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
                $j = mb_strpos($token,'>',$i+1);
                $str .= mb_substr($token,$i,$j-$i+1);
                $i = $j;
            } else {
                $next_char = ($i+1 < mb_strlen($token)) ? mb_substr($token,$i+1,1) : '';
                $next_char_is_special = (!$next_char || mb_strpos($delimeters, $next_char) || preg_match("/\s/",$next_char) || mb_strpos($dashes,$next_char));
//                if (!$is_word && !preg_match("/^-\s/",mb_substr($token,$i,2))) {
                if (!$is_word && !(mb_strpos($dashes,$char)!==false && $next_char_is_special)) { // && $next_char_is_special
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
        return [$str,$word_count];
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
/*$word = "Ojat’";
// select * from wordforms where wordform like 'Ojat’';
                    $wordforms = Wordform::select('id')
                            ->whereRaw("wordform like ?",[addcslashes(strtolower($word),"'%")])->get();
dd($wordforms);                    
*/
        list($sxe,$error_message) = self::toXML($this->text_xml,$this->id);

        if ($error_message) {
            return $error_message;
        }

        // saving old checked links
        $checked_words = [];
//dd($this->meanings);        
        $meanings = DB::table("meaning_text")
                  ->where('relevance','<>',1)
                  ->where('text_id',$this->id)
                //$this->meanings()->wherePivot('relevance','<>',1)
                         //->join('words','words.id','=','meaning_text.word_id')
                  ->get();
//dd($meanings);
        foreach ($meanings as $meaning) {
            $word = Word::where('text_id',$this->id)
                        ->where('w_id',$meaning->w_id)->first();
            $checked_words[$meaning->w_id][$meaning->meaning_id] =
                    [$word->word, $meaning->relevance];
        }
//dd($checked_words);
//print '<p>'.$this->id;     
//print '<p>'.$this->words()->count();
//        $this->words()->delete();
        DB::statement("DELETE FROM words WHERE text_id=".(int)$this->id);
//        $this->meanings()->detach();
        DB::statement("DELETE FROM meaning_text WHERE text_id=".(int)$this->id);
//print '<p>'.$this->words()->count();
//exit(0);
        foreach ($sxe->children()->s as $sentence) {
//            if ($sentence->getName() == 's') {
                $s_id = $sentence->attributes()->id;
                //print "<P>".$s_id .'.';
                foreach ($sentence->children()->w as $word) {
                    $w_id = $word->attributes()->id;
                    $w_id = (int)$w_id;
//dd($w_id);
                    $word_obj = Word::create(['text_id' => $this->id,
                                              'sentence_id' => $s_id,
                                              'w_id' => $w_id,
                                              'word' => $word
                                            ]);
                    $word_t = addcslashes(strtolower($word),"'%");
                    $meanings = [];
                    $lemmas = Lemma::select('id')->where('lang_id',$this->lang_id)
                            ->whereRaw("lemma like ?",[$word_t]);
                    if ($lemmas->count()) {
                        foreach ($lemmas->get() as $lemma) {
                            foreach ($lemma->meanings as $meaning) {
                                $meanings[$meaning->id] = 1;
                            }
                        }
                    }

                    $wordforms = Wordform::select('id')
                            ->whereRaw("wordform like ?",[$word_t]);
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
                        if (isset($checked_words[$w_id][$meaning_id][0])) {
                            if ($checked_words[$w_id][$meaning_id][0] == $word 
                                    || $checked_words[$w_id][$meaning_id][0] == $word.',') {
                                $relevance = $checked_words[$w_id][$meaning_id][1];
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
                } elseif ($meanings->count() > 1) {
                    $class .= ' polysemy';                
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

    public function sentences($word=''){
        $sentences = [];
        
        list($sxe,$error_message) = Text::toXML($this->text_xml,$this->id);
        if ($error_message) {
            return $sentences;
        }

        $sentence_builder = Word::select('sentence_id','w_id')
                              ->where('text_id',$this->id)
                              ->orderBy('sentence_id');
        if ($word) {
            $sentence_builder = $sentence_builder ->where('word','like',$word);
        }                                
//dd($sentence_builder->toSql());        
        foreach ($sentence_builder->get() as $sentence) {
            $sentences[$sentence->sentence_id]['w_id'][]=$sentence->w_id;
        }
        
        foreach ($sentences as $sentence_id => $sentence) {
            $s = $sxe->xpath('//s[@id="'.$sentence_id.'"]');
            if (isset($s[0])) {
                $sentences[$sentence_id]['s']= $s[0]->asXML();
            }
        }
        
        return $sentences;
    }
    
    /**
     * find sentence in text, parse xml
     * 
     * @param int $text_id
     * @param int $sentence_id - number of sentence in the text
     * @param int $w_id - number of word in the text
     * @param INT OR Array $relevance - if function is called for one meaning, type is INT, else ARRAY
     * @return array f.e. ['s' => <sentence in xml format>, 
                           's_id' => <number of sentence in the text>,
                           'text' => <text object>, 
                           'trans_s' => <transtext sentence in xml format>,
                           'w_id' => <number of word in the text>, 
                            'relevance' => <relevance>]
     */
    public static function extractSentence($text_id, $sentence_id, $w_id, $relevance='') {
            $text = Text::find($text_id);
            if (!$text) {
//print "<p>text error</p>";
                return NULL;
            }
            list($sxe,$error_message) = Text::toXML($text->text_xml,$text->id);
            if ($error_message) {
//print "<p>$error_message</p>";                
                return NULL;
            }
            $s = $sxe->xpath('//s[@id="'.$sentence_id.'"]');
            if (isset($s[0])) {
                $transtext = Transtext::find($text->transtext_id);
                $trans_s = '';
                if ($transtext) {
                    list($trans_sxe,$trans_error) = Text::toXML($transtext->text_xml,'trans: '.$transtext->id);
                    if (!$trans_error) {
                        $trans_sent = $trans_sxe->xpath('//s[@id="'.$sentence_id.'"]');
                        if (isset($trans_sent[0])) {
                            $trans_s = $trans_sent[0]->asXML();
                        }
                    }                    
                }
                $sentence = ['s' => $s[0]->asXML(), 
                                's_id' => $sentence_id,
                                'text' => $text, 
                                'trans_s' => $trans_s,
                                'w_id' => $w_id, 
                                'relevance' => $relevance]; 
                return $sentence;
            } else {
                dd("!s: meaning_id=".$this->id.' and text_id='.$text_id.' and sentence_id='.$sentence_id.' and w_id='.$w_id);                    
            }
    }
}
