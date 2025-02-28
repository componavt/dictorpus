<?php namespace App\Traits\Modify;

use App\Library\Grammatic;

use App\Models\Corpus\Sentence;

trait WordModify
{
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
     * Divides string on words
     * Suppose that the first and the last symbols of the string are the word chars, they are not special
     *
     * @param string $token  text without mark up
     * @param integer $word_count  initial word count
     *
     * @return array text with markup (split to words) and array of words
     */
    public static function splitWord($token, $word_count): array
    {        
        $str = '';
        $i = 0;
        $is_word = TRUE; // the first char enters in a word
        $words = [];
        $word = '';
        while ($i<mb_strlen($token)) {
            $char = mb_substr($token,$i,1);
            if ($char == '<') { // begin of a tag 
                list ($is_word, $str, $word, $words) = self::endWord($is_word, $str, $word, $words, $word_count);
                list ($i, $str) = self::tagOutWord($token, $i, $str);
                
                // the char is a delimeter or white space
            } elseif (mb_strpos(Sentence::word_delimeters(), $char)!==false || preg_match("/\s/",$char)
                       // if word is ending with a dash, the dash is putting out of the word
                      || $is_word && Sentence::dashIsOutOfWord($char, $token, $i) ) { 
                list ($is_word, $str, $word, $words) = self::endWord($is_word, $str, $word, $words, $word_count);
                $str .= $char;
            } else {                
                // if word is not started AND (the char is not dash or the next char is not special) THEN the new word started
                if (!$is_word && !Sentence::dashIsOutOfWord($char, $token, $i)) { 
                    $is_word = true;
                    $str .= '<w id="'.$word_count++.'">';
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
        $str .= $word;
        $words[$word_count-1] = $word;
//print "$i: $char| word: $word| str: $str\n";            
        return [$str, $words]; 
    }
    
    public function splitInSentence($word, $cyr_word='') {
        $text_obj = $this->text;
        $word_obj = $this;
        
        $sent_obj = Sentence::whereTextId($this->text_id)
                        ->whereSId($this->s_id)->first();
        if (!$sent_obj) { return; }
        
        $sentence = $sent_obj->text_xml;
//dd($sent_obj->text_xml);        
/*        list($sxe,$error_message) = Text::toXML($sent_obj->text_xml,'');
        if ($error_message) { return; }
dd($sxe->asXML());        */
        
        $next_word_count = self::nextWId($this->text_id);
        
        list ($str, $words) = self::splitWord($word, $next_word_count);
        
        $i = mb_strpos($sentence, '<w id="'.$this->w_id.'">');
        $j = mb_strpos($sentence, '</w>', $i+7);
        $new_sentence = mb_substr($sentence, 0, $i).'<w id="'.$this->w_id.'">'.$str.mb_substr($sentence, $j);
        $sent_obj->text_xml = $new_sentence;
        $sent_obj->save();
        
        $lang_id = $text_obj->lang_id;

        foreach ($words as $k=>$w) {
            $word_for_search = Grammatic::changeLetters($words[$k],$lang_id);
            if ($k>=$next_word_count) {
                $word_obj = self::create(['text_id' => $this->text_id, 'sentence_id' => $sent_obj->id, 's_id' => $sent_obj->s_id, 'w_id' => $k, 'word' => $word_for_search]);
            } else {
                $word_obj->word = $word_for_search;
                $word_obj->save();
            }
            $word_obj->setMeanings([], $lang_id);
            $text_obj->setWordforms([], $word_obj);        
        }
        
        if ($cyr_word) {
            $this->splitCyrWord($cyr_word, $next_word_count);
        }
    }
    
    public function splitCyrWord($cyr_word, $next_word_count) {
        $text_obj = $this->text;
        list ($str, $words) = self::splitWord($cyr_word, $next_word_count);
        
        $cyrtext_obj = $this->text->cyrtext;
        if (empty($cyrtext_obj)) {
            return;
        }
        $text = $cyrtext_obj->text_xml;
        $i = mb_strpos($text, '<w id="'.$this->w_id.'">');
        $j = mb_strpos($text, '</w>', $i+7);
        $new_text = mb_substr($text, 0, $i).'<w id="'.$this->w_id.'">'.$str.mb_substr($text, $j);
        $cyrtext_obj->text_xml = $new_text;
        $cyrtext_obj->save();
        
        
    }
}