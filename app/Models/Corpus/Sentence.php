<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

class Sentence extends Model
{
    protected $fillable = ['text_id', 's_id','text_xml'];

    use \App\Traits\Search\SentenceSearch;
    use \App\Traits\Select\SentenceWordBlock;
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.


    public static function boot()
    {
        parent::boot();
    }
    
    // Belongs To Relations
    use \App\Traits\Relations\BelongsTo\Text;
    
    // Has Many Relations
    use \App\Traits\Relations\HasMany\SentenceFragments;    // fragments()
    use \App\Traits\Relations\HasMany\SentenceTranslations; // translations()
    use \App\Traits\Relations\HasMany\Words;
    
    public static function getBySid($text_id, $s_id) {
        return self::whereTextId($text_id)->whereSId($s_id)->first();
    }

    // different types of dashes and hyphens: '-', '‒', '–', '—', '―' 
    // if dash '-' inside words, then they are part of words,
    // if dash surrounded by spaces, then dashes are not parts of words.
    //
    // - and ' are a part of word
    public static function word_delimeters() {
        return ',;.!?"[](){}«»=„”“”:%‒–—―¦/';
    }
    
    /**
     * The char is a dash AND the next char is a special symbol (a word delimeter)
     * 
     * @param string $char one symbol
     * @param string $token
     * @param int $i
     * @return boolean
     */
    public static function dashIsOutOfWord($char, $token, $i) {
        $dashes = '-'; //digit dash

        $next_char = ($i+1 < mb_strlen($token)) ? mb_substr($token,$i+1,1) : '';

        // if the next_char is and of the sentence OR a delimeter OR a white space OR a dash THEN the next char is special
        $next_char_is_special = (!$next_char // empty string
                   // a word delimeter
                || mb_strpos(self::word_delimeters(), $next_char)!==false 
                   // a white space
                || preg_match("/\s/",$next_char) 
                   // a dash
                || mb_strpos($dashes,$next_char)!==false 
                   // a begin of a tag
                || $next_char == '<');
        
        return mb_strpos($dashes,$char)!==false && $next_char_is_special;
    }

    public static function wordAddToSentence($is_word, $word, $str, $word_count) {
        if ($is_word) { // the previous char is part of a word, the word ends
            if (!preg_match("/([a-zA-ZА-Яа-яЁёÄÖÜČŠŽäöüčšž])/u",$word, $regs)) {
                $str .= $word;
            } else {
//dd($regs);
                $str .= '<w id="'.$word_count++.'">'.$word.'</w>';
            }
            $is_word = false;
        }
        return [$is_word, $str, $word_count]; 
    }
    
    /**
     * Divides sentence on words
     *
     * ./vendor/bin/phpunit tests/Models/Corpus/TextTest
     * 
     * @param string $text  text without mark up
     * @param integer $word_count  initial word count
     *
     * @return array text with markup (split to words) and next word count
     */
    public static function markup($text, $word_count): array
    {        
        $str = '';
        $i = 0;
        $is_word = false; // word tag <w> is not opened
        $word='';
        while ($i<mb_strlen($text)) {
            $char = mb_substr($text,$i,1);
            if ($char == '<') { // begin of a tag 
                list ($is_word, $str, $word_count) = self::wordAddToSentence($is_word, $word, $str, $word_count);
                list ($i, $str) = Word::tagOutWord($text, $i, $str);
                
                // the char is a delimeter or white space
            } elseif (mb_strpos(self::word_delimeters(), $char)!==false || preg_match("/\s/",$char)
                       // if word is ending with a dash, the dash is putting out of the word
                      || $is_word && self::dashIsOutOfWord($char, $text, $i) ) { 
                list ($is_word, $str, $word_count) = self::wordAddToSentence($is_word, $word, $str, $word_count);
                $str .= $char;
            } else {                
                // if word is not started AND (the char is not dash or the next char is not special) THEN the new word started
                if (!$is_word && !self::dashIsOutOfWord($char, $text, $i)) { 
                    $is_word = true;
                    $word='';
                }
                if ($is_word) {
                    $word .= $char;
                } else {
                    $str .= $char;            
                }
            }
//print "$i: $char| word: $word| is_word: $is_word| str: $str\n";            
            $i++;
        }
        list ($is_word, $str, $word_count) = self::wordAddToSentence($is_word, $word, $str, $word_count);
//print "$i: $char| word: $word| str: $str\n";            
        return [$str, $word_count]; 
    }
    
    public static function store($text_id, $s_id, $text_xml) {
        $sentence = self::firstOrCreate(['text_id' => $text_id, 's_id' => $s_id]);
        $sentence->text_xml = $text_xml;
        $sentence->save();
        return $sentence;
    }

    public function numerateWords() {
        $count=1;
//dd($this->text_xml);        
        list($sxe,$error_message) = Text::toXML($this->text_xml, 'sentence.id='.$this->id);
        if ($error_message) { dd($error_message); }
//dd($sxe->children()->s->w);        
        foreach ($sxe->children()->s->w as $w) {
            $w_id = (int)$w->attributes()->id;
            $word = Word::getByTextWid($this->text_id, $w_id); 
//dd($word);            
            $word->word_number = $count++;
            $word->save();
        }       
    }
    
    public function moveBrFromSentences() {
        $text=$this->text;
        $new_sentence = mb_ereg_replace("<s id=\"".$this->s_id."\"><br/>\r?\n?", "<s id=\"".$this->s_id."\">", $this->text_xml);
        $new_structure = mb_ereg_replace("<s id=\"".$this->s_id."\"", "<br/>\n<s id=\"".$this->s_id."\"", $text->text_structure);
        if ($this->text_xml != $new_sentence && $text->text_structure != $new_structure) {
            $this->text_xml = $new_sentence;
            $text->text_structure = $new_structure;
        } else {
dd($this->id, $text->id, $this->text_xml != $new_sentence, $text->text_structure != $new_structure);        
            
        }
//exit();        
//dd($this->text_xml, $text->text_structure);        
        $text->save();
        $this->save();
    }            
}
