<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\Meaning;
use App\Models\Dict\Wordform;

class Word extends Model
{
    public $timestamps = false;
    
    protected $fillable = ['text_id', 'sentence_id', 'w_id', 'word'];
    
    /** Word belongs_to Text
     * 
     * @return Relationship, Query Builder
     */
    public function text()
    {
        return $this->belongsTo(Text::class);
    } 
    
    // Word __has_many__ Meanings
    public function meanings(){
        $builder = $this->belongsToMany(Meaning::class,'meaning_text')
                 -> withPivot('relevance');
        return $builder;
    }
    
    public function getLemmas() {
        $w_id = $this->w_id;
        $text_id = $this->text_id;
        $lemmas = Lemma::whereIn('id', function ($query) use ($text_id, $w_id) {
            $query->select('lemma_id')->from('meanings')
                  ->whereIn('id', function ($query) use ($text_id, $w_id) {
                        $query->select('meaning_id')->from('meaning_text')
//WSD?                                
//                              ->where('relevance', '>', 0)
                              ->where('text_id',$text_id)
                              ->where('w_id',$w_id);
                    });
            });
//dd($lemmas->pluck('id'));            
//dd($lemmas->toSql(). "  $word_id"); 
//select * from `lemmas` where `id` in (select `lemma_id` from `meanings` where `id` in (select `meaning_id` from `meaning_text` where `word_id` = 1210143))  
        return $lemmas -> get();
    }

    public function remove() {
        $this->meanings()->detach();
        $this->delete();            
    }

    public static function getByTextWid($text_id,$w_id) {
        return self::where('text_id',$text_id) -> where('w_id',$w_id)->first();
    }

    public static function removeByTextWid($text_id,$w_id) {
        $word_obj = self::getByTextWid($text_id,$w_id);
        if ($word_obj) {
            $word_obj->remove();   
        }
    }
    
    /**
     * Is exists back vowels in the word
     * @param String $word
     * @return Boolean
     */
    public static function isBackVowels($word) {
        if (preg_match("/[aou]/u", $word)) { 
            return true;
        }
        return false;
    }
    
    /**
     * Changes obsolete letters to modern
     * If a parameter lang_id is given, then does the check need such a replacement
     * 
     * @param String $word
     * @param Int $lang_id
     * @return String
     */
    public static function changeLetters($word,$lang_id=null) {
        if (!$lang_id || $lang_id && !Lang::isLetterChangeable($lang_id)) {
            return $word;
        }

        $word = str_replace('ü','y',$word);
        $word = str_replace('Ü','Y',$word);
        
        if (self::isBackVowels($word)) { 
            $word = str_replace('w','u',$word);
            $word = str_replace('W','U',$word);            
        } else {
            $word = str_replace('w','y',$word);
            $word = str_replace('W','Y',$word);            
        }
        return $word;
    }
    
    /**
     * search the nearest left neighbour in the same sentence
     * @return Word
     */
    public function leftNeighbor() {
        if ($this->w_id == 1) { return; }
        $word = self::where('text_id',$this->text_id)
                ->where('sentence_id',$this->sentence_id)
                ->where('w_id','<',$this->w_id)
                ->orderBy('w_id','desc')
                //->toSql();
                ->first();
//dd($word.'|'.$this->text_id.'|'.$this->sentence_id.'|'.$this->w_id);        
        return $word;
    }
    
    public static function search(Array $url_args) {
        $words = self::orderBy('word');        
        $words = self::searchByLang($texts, $url_args['search_lang']);
//        $words = self::searchByWord($texts, $url_args['search_word']);
        
        if ($url_args['search_word']) {
            $words = $words->where('l_word','like',mb_strtolower($url_args['search_word'], 'UTF-8'));
        } 

        return $words;
    }

    public static function searchByLang($texts, $lang_id) {
        if (!$lang_id) {
            return $words;
        }
        return $words->whereIn('text_id', function() use ($lang_id){
                                $query = select('id')->from('texts')
                                       -> where('lang_id',$lang_id);
                        });
                
    }
    
    /**
     * 
     * @param String $word
     * @param Array $langs
     * @return Collection of Words
     */
    public static function searchByWordInTexts($word, $langs) {
        $word_coll = self::where('word','like',$word)
                ->whereIn('text_id',function($query) use ($langs){
                        $query->select('id')
                        ->from(with(new Text)->getTable())
                        ->whereIn('lang_id',$langs);
                    })->get();
        if (sizeof($word_coll)) { 
            return $word_coll; 
        }                           
    }
    
    /**
     * move to the left word in the sentence and compare with the given words
     * if all words are founded in the text return array of words, else return NULL
     * 
     * @param Array $words - array of strings
     * @return Array [word_id => word_string]
     */
    public function searchForWordform($words) {
        $word_founded=[$this->w_id => $this->word];
        $curr_word = $this;
        $sent_id = $this->sentence_id;
        $i=sizeof($words)-2;
        while ($i>=0) {
            $curr_word = $curr_word->leftNeighbor();
            if (!$curr_word || $curr_word->sentence_id != $sent_id) { 
                return;                            
            }
            $word_founded[$curr_word->w_id] = $curr_word->word;
            if ($curr_word->word != $words[$i]) {
                return;
            }
            $i--;
        }
        ksort($word_founded);
        return $word_founded;
    }
    
    /*
     * нужно на дереве xml найти узел с последним w_id, добавить к нему содержимое остальных узлов и остальные узлы удалить.
     * удалить записи в meaning_text для удаленных слов, если у meaning_text.lemma_id нет таких словоформ, то удалить и эти связи
     * обновить таблицу words: изменить последнее слово и удалить остальные
     */
    public function mergeWords($words_to_merge) {
        $word_ids = array_keys($words_to_merge);
        $last_id = array_pop($word_ids);
        
        list($sxe,$error_message) = Text::toXML($this->text->text_xml,$this->text_id);        
        if (!$sxe || $error_message) {return $error_message; }
//dd($sxe);        
        $last_node = $sxe->xpath("//w[@id='".$last_id."']");
        if (!$this->checkWord((string)$last_node[0])) { return; }

        foreach ($word_ids as $word_id) {
            $node = $sxe->xpath("//w[@id='".$word_id."']");
            if ($node) {
                $last_node[0][0] = (string)$node[0].' '.(string)$last_node[0];
                unset($node[0][0]);
            }
            self::removeByTextWid($this->text_id,$word_id);
        }
        $this->word = $last_node[0]->__toString();
        $this->save();
        $this->text->updateXML($sxe->asXML());
    }
    
    /**
     * check if Word equils to word in the text
     * if $word_string is NULL or empty string delete this Word
     * if the words are different update Word
     * 
     * @param String $word_string
     * @return boolean = true if $word_string = Word->word
     */
    public function checkWord($word_string) {
        if (!$word_string) { 
            $this->remove();
            return;             
        }
        
        if ($word_string == $this->word) {
            return true;
        }
        
        $this->word = $word_string;
        $this->save();
    }
    
    public static function toCONLL($text_id, $w_id) {
        $word = self::getByTextWid($text_id, $w_id);
//dd($word);        

        if (!$word || !$word->word) {
            return NULL;
        }
        
        $lemmas = $word->getLemmas();
//if ($text_id==1 && $w_id==73) {   
//    dd($lemmas);
//}
//dd($lemmas);        
        if (!$lemmas || !$lemmas->count()) {
            return [$word->word."\tUNKN\t_\t_\t_\t_\t_\t_\t_"];
        }
        $lines = [];
        foreach ($lemmas as $lemma) {
            $line = $word->word."\t".$lemma->lemma."\t";
            if ($lemma->pos && !in_array($lemma->pos->code, ['PHRASE','PRE'])) { // фразеологизм, предикатив
                $line .= $lemma->pos->code;
            } else {
                $line .= 'UNKN';
            }
            $lines[] = $line."\t_\t".$lemma->featsToCONLL($word->word)."\t_\t_\t_\t_";
        }
        return $lines;
    }
    
    public function isLinkedWithLemmaByLang($lang_id) {
        $word = $this->word;
        $word_t = addcslashes($word,"'%");
        $word_t_l = mb_strtolower($word_t);
        $lemmas = Lemma::where('lang_id',$lang_id)
                //where('lemma', 'like', $word)
                ->whereRaw("lemma like '$word_t' or lemma like '$word_t_l'");
//whereRaw("lemma_id in (SELECT id from lemmas where lang_id=".$this->lang_id  ." and (lemma like '$word_t' or lemma like '$word_t_l' or id in $lemma_q))")
//dd($lemmas);        
        if ($lemmas->count()) {
            return true;
        }
         //return false;
       
        $wordforms = Wordform::whereRaw("(wordform like '$word_t' or wordform like '$word_t_l')")
                //where('wordform', 'like', $word)
                   ->whereIn('id',function($query) use ($lang_id) {
                       $query ->select('wordform_id')->from('lemma_wordform')
                              ->whereIn('lemma_id',function($q) use ($lang_id) {
                                    $q ->select('id')->from('lemmas')
                                           ->where('lang_id',$lang_id);
                                });
                   });
        if ($wordforms->count()) {
            return true;
        }
    }
}
