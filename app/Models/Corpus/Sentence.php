<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

use App\Library\Grammatic;
use App\Library\Str;

class Sentence extends Model
{
    protected $fillable = ['text_id', 's_id','text_xml'];

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
            if (!preg_match("/([a-zA-ZА-Яа-яЁё])/u",$word, $regs)) {
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
     * @param string $token  text without mark up
     * @param integer $word_count  initial word count
     *
     * @return array text with markup (split to words) and next word count
     */
    public static function markup($token, $word_count): array
    {        
        $str = '';
        $i = 0;
        $is_word = false; // word tag <w> is not opened
        $word='';
        while ($i<mb_strlen($token)) {
            $char = mb_substr($token,$i,1);
            if ($char == '<') { // begin of a tag 
                list ($is_word, $str, $word_count) = self::wordAddToSentence($is_word, $word, $str, $word_count);
                list ($i, $str) = Word::tagOutWord($token, $i, $str);
                
                // the char is a delimeter or white space
            } elseif (mb_strpos(self::word_delimeters(), $char)!==false || preg_match("/\s/",$char)
                       // if word is ending with a dash, the dash is putting out of the word
                      || $is_word && self::dashIsOutOfWord($char, $token, $i) ) { 
                list ($is_word, $str, $word_count) = self::wordAddToSentence($is_word, $word, $str, $word_count);
                $str .= $char;
            } else {                
                // if word is not started AND (the char is not dash or the next char is not special) THEN the new word started
                if (!$is_word && !self::dashIsOutOfWord($char, $token, $i)) { 
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
        return [$str,$word_count]; 
    }
    
    public static function store($text_id, $s_id, $text_xml) {
        $sentence = self::firstOrCreate(['text_id' => $text_id, 's_id' => $s_id]);
        $sentence->text_xml = $text_xml;
        $sentence->save();
    }

    public function numerateWords() {
        $count=1;
//dd($this->text_xml);        
        list($sxe,$error_message) = Text::toXML($this->text_xml, $this->s_id);
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
    
    public static function search(Array $url_args) {
        $texts = Text::orderBy('title');        
        if ($url_args['search_corpus']) {
            $texts = $texts->whereIn('corpus_id',$url_args['search_corpus']);
        } 
        $texts = Text::searchByDialects($texts, $url_args['search_dialect']);
        $texts = Text::searchByGenres($texts, $url_args['search_genre']);
        $texts = Text::searchByLang($texts, $url_args['search_lang']);
        $texts = Text::searchByYear($texts, $url_args['search_year_from'], $url_args['search_year_to']);
        
        $texts = self::searchByWords($texts, $url_args['search_word1'], $url_args['search_word2'], $url_args['search_distance_from'], $url_args['search_distance_to']);
        return $texts;
    }
    
    public static function searchByWords($texts, $word1, $word2, $distance_from, $distance_to) {
        if (!$word1) {
            return $texts;
        }
        return $texts->whereIn('id',function($query) use ($word1){
                        $query->select('text_id')
                        ->from('text_wordform')
                        ->where('relevance', '>', 0)
                        ->whereIn('wordform_id',function($query1) use ($word1){
                            $query1->select('wordform_id')
                            ->from('lemma_wordform')
                            ->whereIn('lemma_id',function($query2) use ($word1){
                                $query2->select('id')
                                ->from('lemmas')
                                ->where('lemma_for_search', 'like', Grammatic::toSearchForm($word1));
                            });
                        });
                    });
    }
    
    public static function urlArgs($request) {
        $url_args = Str::urlArgs($request) + [
                    'search_corpus'   => (array)$request->input('search_corpus'),
                    'search_dialect'  => (array)$request->input('search_dialect'),
                    'search_genre'    => (array)$request->input('search_genre'),
                    'search_lang'     => (array)$request->input('search_lang'),
                    'search_year_from'=> (int)$request->input('search_year_from'),
                    'search_year_to'  => (int)$request->input('search_year_to'),
            
                    'search_distance_from'  => (int)$request->input('search_distance_from'),
                    'search_distance_to'  => (int)$request->input('search_distance_to'),
                    'search_word1' => $request->input('search_word1'),
                    'search_word2' => $request->input('search_word1'),
//                    'search_lang'  => (array)$request->input('search_lang'),
                ];
        
        if (!$url_args['search_distance_from']) {
            $url_args['search_distance_from'] = 1;
        }
        if (!$url_args['search_distance_to']) {
            $url_args['search_distance_to'] = 1;
        }
        
        return $url_args;
    }    
}
