<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

use DB;
use LaravelLocalization;
use User;

use \App\Library\Grammatic;
use App\Library\Str;

use App\Models\Dict\Gramset;
use App\Models\Dict\Lemma;
use App\Models\Dict\LemmaWordform;
use App\Models\Dict\Meaning;
use App\Models\Dict\Wordform;

class Word extends Model
{
    public $timestamps = false;
    
    protected $fillable = ['text_id', 'sentence_id', 's_id', 'w_id', 'word', 'word_number'];
    
    // Belongs To Relations
    use \App\Traits\Relations\BelongsTo\Sentence;
    use \App\Traits\Relations\BelongsTo\Text;
    
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

    /**
     * remove <s>
     * @param boolean $highlight - if true, highlight the word
     * @return string
     */
    public function getClearSentence($highlight=false) {
        $sentence_id = $this->sentence->s_id;
        $sentence = str_replace("\n", '', $this->sentence->text_xml);
        if (!preg_match("/^\<s id=\"".$sentence_id."\"\>(.+)\<\/s\>\s*$/im", $sentence, $regs)) {
            return $sentence;
        }
        if ($highlight) {
            $regs[1] = str_replace("<w id=\"".$this->w_id."\"", "<w id=\"".$this->w_id."\" style=\"background: #fefea6;\"", $regs[1]);
        }
        return $regs[1];
    }
    
    /**
     * предшествующий знак препинания
     */
    public function getPrevSign() {
        $sentence = str_replace("\n", '', $this->sentence->text_xml);
        if (preg_match("/<\/w>\s*(\S+)\s*<w id=\"".$this->w_id."\"/", $sentence, $regs)) {
            return $regs[1];
        }
    }

    public function getPrevWord() {
        $prev_word = self::whereSentenceId($this->sentence_id)
                         ->whereWordNumber($this->word_number - 1)->first();
        if ($prev_word) {
            return $prev_word->word;
        }
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
     * search the nearest left neighbour in the same sentence
     * @return Word
     */
    public function leftNeighbor() {
        if ($this->w_id == 1) { return; }
        $word = self::where('text_id',$this->text_id)
                ->where('s_id',$this->s_id)
                ->where('w_id','<',$this->w_id)
                ->latest('w_id')
                //->toSql();
                ->first();
//dd($word.'|'.$this->text_id.'|'.$this->s_id.'|'.$this->w_id);        
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
        return $words->whereIn('text_id', function($query) use ($lang_id){
                                $query -> select('id')->from('texts')
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
     * if all words are found in the text return array of words, else return NULL
     * 
     * @param Array $words - array of strings
     * @return Array [word_id => word_string]
     */
    public function searchForWordform($words) {
        $word_found=[$this->w_id => $this->word];
        $curr_word = $this;
        $sent_id = $this->s_id;
        $i=sizeof($words)-2;
        while ($i>=0) {
            $curr_word = $curr_word->leftNeighbor();
            if (!$curr_word || $curr_word->s_id != $sent_id) { 
                return;                            
            }
            $word_found[$curr_word->w_id] = $curr_word->word;
            if ($curr_word->word != $words[$i]) {
                return;
            }
            $i--;
        }
        ksort($word_found);
        return $word_found;
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
    
    public static function toCONLL($text_id, $w_id, $word) {
        $word_obj = self::getByTextWid($text_id, $w_id);
//dd($word_obj);        

        if (!$word_obj) {
            return NULL;
        }
        
        $lemmas = $word_obj->getLemmas();
//if ($text_id==1 && $w_id==73) {   
//    dd($lemmas);
//}
//dd($lemmas);        
        if (!$lemmas || !$lemmas->count()) {
            return [$word."\tUNKN\t_\t_\t_\t_\t_\t_\t_"];
        }
        $lines = [];
        foreach ($lemmas as $lemma) {
            $line = $word."\t".$lemma->lemma."\t";
            if ($lemma->pos && !in_array($lemma->pos->code, ['PHRASE','PRE'])) { // фразеологизм, предикатив
                $line .= $lemma->pos->code;
            } else {
                $line .= 'UNKN';
            }
            $lines[] = $line."\t_\t".$lemma->featsToCONLL($word)."\t_\t_\t_\t_";
        }
        return $lines;
    }
    
    /**
     * 
     * 
     * @param Int $text_id
     * @param Int $w_id
     * @param String $word
     * @return string
     */
    public static function uniqueLemmaWords($text_id, $w_id, $word) {
        $word_obj = self::getByTextWid($text_id, $w_id);
//dd($word_obj);        

        if (!$word_obj) {
            return $word;
        }
        
        $lemmas = $word_obj->getLemmas();
//if ($text_id==1 && $w_id==73) {   
//    dd($lemmas);
//}
//dd($lemmas);        
        if (!$lemmas || !$lemmas->count()) {
            return $word;
        }
        $lemma_words = [];
        foreach ($lemmas as $lemma) {
            $lemma_words[] = $lemma->lemma;
        }
        if (!sizeof($lemma_words)) {
            return $word;
        }
        $lemma_words = array_unique($lemma_words);
        return join('|',$lemma_words);
    }
    
    public function isLinkedWithLemma() {
//        return $this->meanings()->count();
        return Word::where('word', 'like', $this->word)
                   ->whereIn('id', function ($q) {
                       $q->select('word_id')->from('meaning_text');
                   })->count();
/*        return Word::where('word', 'like', $this->word)
                   ->join('meaning_text', 'meaning_text.word_id', '=', 'words.id')
                   ->count();*/
    }
    
    public function isLinkedWithLemmaByLang($lang_id, $dialect_id=null) {
        $word = $this->word;
        $word_t = addcslashes($word,"'%");
        $word_t_l = mb_strtolower($word_t);
        $lemmas = Lemma::where('lang_id',$lang_id)
                ->whereRaw("lemma_for_search like '$word_t' or lemma_for_search like '$word_t_l'");
        if ($lemmas->count()) {
            return $lemmas->count();
        }
       
        $wordforms = Wordform:://whereRaw("(wordform_for_search like '$word_t' or wordform_for_search like '$word_t_l')")->
                    whereIn('id',function($query) use ($lang_id, $word_t, $word_t_l) {
                       $query ->select('wordform_id')->from('lemma_wordform')
                              ->whereRaw("(wordform_for_search like '$word_t' or wordform_for_search like '$word_t_l')")
                              ->whereIn('lemma_id',function($q) use ($lang_id) {
                                    $q ->select('id')->from('lemmas')
                                           ->where('lang_id',$lang_id);
                                });
                   });
        if ($wordforms->count()) {
            return $wordforms->count();
        }
    }
    
    public function hasImportantExamples() {
        if ($this->meanings()->whereRelevance(10)->count()>0) {
            return true;
        }
        $text_id = $this->id;
        
        $fragments_count = SentenceFragment::where('sentence_id', $this->sentence_id)
                                ->where('w_id', $this->w_id)->count();
        if ($fragments_count) {
            return true;
        }
        
        $translations_count = SentenceTranslation::where('sentence_id', $this->sentence_id)
                                ->where('w_id', $this->w_id)->count();
        if ($translations_count) {
            return true;
        }
    }
    
    /**
     * The number of words in texts with lang_id
     */
    public static function countByLang($lang_id=null) {
        $examples = self::whereIn('text_id', function ($q) use ($lang_id) {
                $q->select('id')->from('texts')->where('lang_id',$lang_id);
            });
        
        return $examples->count();
    }
    
    /**
     * The number of words linked with lemmas
     */
    public static function countMarked($lang_id=null) {
        $examples = self::whereIn('id', function ($query) {
                        $query->select('word_id')->from('meaning_text');
        });
        if ($lang_id) {
            $examples -> whereIn('text_id', function ($q) use ($lang_id) {
                $q->select('id')->from('texts')->where('lang_id',$lang_id);
            });
        }
        return $examples->count();
    }
    
    /**
     * The number of words without links to lemmas
     * select count(*) from words where checked=0 and id not in (select word_id from meaning_text) and text_id in (select id from texts where lang_id=1);
     * 
     */
    public static function countUnmarked($lang_id=null) {
        $examples = self::whereChecked(0)->whereNotIn('id', function ($query) {
                        $query->select('word_id')->from('meaning_text');
            });
        if ($lang_id) {
            $examples -> whereIn('text_id', function ($q) use ($lang_id) {
                $q->select('id')->from('texts')->where('lang_id',$lang_id);
            });
        }
        return $examples->count();
    }
    
    /**
     * set links between a word (of some text) and a meaning
     * 
     * @param Array $checked_relevances [meaning1_id => [word, relevance1], meaning2_id => [word, relevance2], ... ]
     * @param INT $lang_id
     * $retutn INT - the number of links with meanings
     */
    public function setMeanings($checked_relevances, $lang_id=NULL) {
        if (!$lang_id) {
            $lang_id = Text::getLangIDbyID($this->text_id);
        }
        $has_checked = false;
        foreach (array_values($checked_relevances) as $r) {
            if ($r>1) {
                $has_checked = true;
            }
        }
        foreach (self::getMeaningsByWord($this->word, $lang_id) as $meaning) {
            $meaning_id = $meaning->id;
            $relevance = $checked_relevances[$meaning_id] ?? ($has_checked ? 0 : 1);
            $this->addMeaning($meaning_id, $this->text_id, $this->s_id, $this->w_id, $relevance);
        }
    }

    /**
     * Search wordforms and lemmas matched with $word and
     * get meanings (objects) of these lemmas
     * @param String $word  in lower case
     * @param Int $lang_id
     * @return Collection
     */
    public static function getMeaningsByWord($word, $lang_id) {
//        $wordform_q = "(SELECT id from wordforms where wordform_for_search like '$word')";
//        $lemma_q = "(SELECT lemma_id FROM lemma_wordform WHERE wordform_id in $wordform_q)";
        $lemma_q = "(SELECT lemma_id FROM lemma_wordform WHERE wordform_for_search like '$word')";
        $meanings = Meaning::whereRaw("lemma_id in (SELECT id from lemmas where lang_id=".$lang_id
                            ." and (lemma_for_search like '$word' or id in $lemma_q))")
                  ->get();    
        return $meanings;
    }
    
    public function addMeaning($meaning_id, $text_id, $s_id, $w_id, $relevance) {
        if ($this->meanings()->wherePivot('s_id',$s_id)->wherePivot('meaning_id',$meaning_id)
                 ->wherePivot('text_id',$text_id)->wherePivot('w_id',$w_id)->count()) {
            return;
        }
        $this->meanings()->attach($meaning_id,
                ['s_id'=>$s_id,
                 'text_id'=>$text_id,
                 'w_id'=>$w_id,
                 'relevance'=>$relevance]);        
    }
    
    public static function createWordBlock($text_id, $w_id) {
        if (!$text_id || !$w_id) { return null; }
        
        $text = Text::find($text_id);
        if (!$text) { return null; }
        
        $meaning_checked = $text->meanings()->wherePivot('w_id',$w_id)->wherePivot('relevance','>',1)->first();
        $meaning_unchecked = $text->meanings()->wherePivot('w_id',$w_id)->wherePivot('relevance',1)->get();
        if (!$meaning_checked && !sizeof($meaning_unchecked)) { return null; }
        
        $word_obj = Word::whereTextId($text_id)->whereWId($w_id)->first();
        $s_id = $word_obj->s_id;
        if (!$s_id) {return null;} 
        
        $locale = LaravelLocalization::getCurrentLocale();
        $url = '/corpus/text/'.$text_id.'/edit/example/'.$s_id.'_'.$w_id;
        
        $str = '<div>';
        if ($meaning_checked) {
            $str .= '<p><a href="'.LaravelLocalization::localizeURL('dict/lemma/'.$meaning_checked->lemma_id)
                 .'">'.$meaning_checked->lemma->lemma.'<span> '.$meaning_checked->lemma->pos->code.' ('
                 .$meaning_checked->getMultilangMeaningTextsString($locale)
                 .')</span></a></p>';
        } else {
            foreach ($meaning_unchecked as $meaning) {
                $str .= '<p><a href="'.LaravelLocalization::localizeURL('dict/lemma/'.$meaning->lemma_id)
                     .'">'.$meaning->lemma->lemma.'<span> '.$meaning->lemma->pos->code.' ('
                     .$meaning->getMultilangMeaningTextsString($locale)
                     .')</span></a>';
                if (User::checkAccess('corpus.edit')) {                
                    $str .= '<span class="fa fa-plus choose-meaning" data-add="'
                         .$meaning->id.'_'.$text_id.'_'.$s_id.'_'.$w_id.'" title="'
                         .\Lang::trans('corpus.mark_right_meaning').'" onClick="addWordMeaning(this)"></span></p>';
                }
            }
        }
        $str .= '</div>';

        $str .= Word::createGramsetBlock($text_id, $w_id);

        if (User::checkAccess('corpus.edit')) { // icons 'pensil' and 'sync'
            $str.='<p class="text-example-edit">';
            if (!$word_obj->hasImportantExamples()) {
                $str.='<i class="fa fa-sync-alt fa-lg update-word-block" title="'.'" onclick="updateWordBlock('.$text_id.','.$w_id.')"></i>';
            }
            $str.='<a href="'.LaravelLocalization::localizeURL($url)
                 .'" class="glyphicon glyphicon-pencil"></a></p>';
        }
        return $str;
    }
    
    public function createLemmaBlock($text_id, $w_id) {
        $s_id = $this->s_id;
        if (!$s_id) {return null;} 
        
        $lemma_b = Lemma::whereIn('id', function ($q) use ($text_id, $w_id) {
            $q->select('lemma_id')->from('meanings')
              ->whereIn('id', function ($q2) use ($text_id, $w_id) {
                  $q2->select('meaning_id')->from('meaning_text')
                     ->whereTextId($text_id)->whereWId($w_id)
                     ->where('relevance','>',0);
                });                    
            })->orderBy('lemma');
        if (!$lemma_b->count()) {return null;} 
        $lemmas = $lemma_b->get();
        
        return self::lemmaBlock($this->word, $w_id, $lemmas, $text_id);
    }
    
    public static function lemmaBlock($word, $w_id, $lemmas, $text_id=null, $wordform_ids=[]) {
        $str = '<div><h3>'.$word.'</h3>';
        
        for ($i=0; $i<sizeof($lemmas); $i++) {
            $lemma_id = $lemmas[$i]->id;
            $str .= '<div class="lemma_b">'.(sizeof($lemmas)>1 ? ($i+1).'. ' : '')
                  . '<a href="'.LaravelLocalization::localizeURL('dict/lemma/'.$lemma_id)
                  . '">'.$lemmas[$i]->lemma.'</a><br>'
                  . '<span> '.$lemmas[$i]->pos->name.'</span> <i>'
                  . $lemmas[$i]->featsToString().'</i>'
            
                  . self::meaningsBlock($lemma_id, $w_id, $text_id)
                  . self::gramsetsBlock($lemma_id, $w_id, $text_id, $wordform_ids)
                  . '</div>';
        }
        $str .= '</div>';
        return $str;
    }
    
    public static function meaningsBlock($lemma_id, $w_id, $text_id=null) {
        $locale = LaravelLocalization::getCurrentLocale();        
        $str = '<div class="meanings_b">';
        foreach (self::meaningsForLemmaBlock($lemma_id, $w_id, $text_id) as $meaning) {
            $str .= '<p>'.$meaning->getMultilangMeaningTextsString($locale).'</p>';
        }
        return $str. '</div>';
    }
    
    public static function meaningsForLemmaBlock($lemma_id, $w_id, $text_id=null) {
        $meanings = Meaning::whereLemmaId($lemma_id);
        if ($text_id) {
            $meanings->whereIn('id', function ($q) use ($text_id, $w_id) {
                  $q->select('meaning_id')->from('meaning_text')
                     ->whereTextId($text_id)->whereWId($w_id)
                     ->where('relevance','>',0);
            });
        }
        return $meanings->orderBy('meaning_n')->get();
    }
    
    public static function gramsetsBlock($lemma_id, $w_id, $text_id=null, $wordform_ids=[]) {
        $gramsets = $text_id ? self::textGramsetsForlemmaBlock($lemma_id, $w_id, $text_id)
                             : (sizeof($wordform_ids) ? self::wordformGramsetsForlemmaBlock($lemma_id, $wordform_ids) : null);
        if (!$gramsets) {
            return null;
        }
        $str = '<div class="gramsets_b">';                
        foreach ($gramsets as $gramset) {
            $str .= '<p>- '.$gramset->gramsetString().'</p>';
        }
        return $str. '</div>';                
    }
    
    public static function textGramsetsForlemmaBlock($lemma_id, $w_id, $text_id=null) {             
        return Gramset::whereIn('id', function ($q) use ($text_id, $w_id, $lemma_id) {
            $q->select('gramset_id')->from('text_wordform')
              ->whereTextId($text_id)->whereWId($w_id)
              ->where('relevance','>',0)
              ->whereIn('wordform_id', function ($q2) use ($lemma_id) {
                  $q2->select('wordform_id')->from('lemma_wordform')
                     ->whereLemmaId($lemma_id);
              });
        })->get();
    }
    
    public static function wordformGramsetsForlemmaBlock($lemma_id, $wordform_ids=[]) {             
        return Gramset::whereIn('id', function ($q) use ($wordform_ids, $lemma_id) {
            $q->select('gramset_id')->from('lemma_wordform')
              ->whereIn('wordform_id', $wordform_ids)
              ->whereLemmaId($lemma_id);
        })->get();
    }
    
    public static function createGramsetBlock($text_id, $w_id) {
        $text = Text::find($text_id);
        if ($text) {
            $wordform = $text->wordforms()->wherePivot('w_id',$w_id)->wherePivot('relevance',2)->first();
            if ($wordform) {
                return '<p class="word-gramset">'.Gramset::getStringByID($wordform->pivot->gramset_id).'</p>';
            } elseif (User::checkAccess('corpus.edit')) { 
                $wordforms = $text->wordforms()->wherePivot('w_id',$w_id)->wherePivot('relevance',1)->get();
                if (!sizeof($wordforms)) { return null; }
                
                $str = '<div id="gramsets_'.$w_id.'" class="word-gramset-not-checked">';
                foreach ($wordforms as $wordform) {
                    $gramset_id = $wordform->pivot->gramset_id;
                    $str .= '<p>'.Gramset::getStringByID($gramset_id)
                         . '<span data-add="'.$text_id."_".$w_id."_".$wordform->id."_".$gramset_id
                         . '" class="fa fa-plus choose-gramset" title="'.\Lang::trans('corpus.mark_right_gramset').' ('
                         . $wordform->wordform.')" onClick="addWordGramset(this)"></span>'
                         . '</p>';
                }
                $str .= '</div>';
                return $str;
            }
        }
    }
    
    /**
     * Sets links meaning - text - sentence AND text-wordform
     */
    public function updateMeaningAndWordformText($reset=false){

        $checked_meaning_words = $reset ? [] : $this->checkedMeaningRelevances();
        DB::statement("DELETE FROM meaning_text WHERE text_id=".$this->text_id ." and w_id=".$this->w_id);
        $this->setMeanings($checked_meaning_words, $this->text->lang_id);

        $checked_wordform_words = $reset ? [] : $this->checkedWordformRelevances();
        DB::statement("DELETE FROM text_wordform WHERE text_id=".$this->text_id ." and w_id=".$this->w_id);
        $this->text->setWordforms($checked_wordform_words, $this);
    }
    
    /**
     * Sets links meaning - text - sentence AND text-wordform
     */
    public function updateWordformText($reset=false){

        $checked_wordform_words = $reset ? [] : $this->checkedWordformRelevances();
        DB::statement("DELETE FROM text_wordform WHERE text_id=".$this->text_id ." and w_id=".$this->w_id);
        $this->text->setWordforms($checked_wordform_words, $this);
    }
    
    // get old checked links meaning-text
    public function checkedMeaningRelevances() {
        $relevances = [];
        $meanings = $this->meanings()->wherePivot('relevance','<>',1)->get();
     
        foreach ($meanings as $meaning) {
            $relevances[$meaning->id] = $meaning->pivot->relevance;
        }
        return $relevances;
    }
    
    // get old checked links text-wordform
    public function checkedWordformRelevances() {
        $relevances = [];
        $wordforms = DB::table('text_wordform')->whereTextId($this->text_id)                
                       ->whereWId($this->w_id)
                       ->where('relevance','<>',1)->get();
        foreach ($wordforms as $wordform) {
            $relevances[$wordform->wordform_id.'_'.$wordform->gramset_id]
                       = $wordform->relevance;
        }
        return $relevances;
    }    
    
    public static function nextWId($text_id) {
        $last_word = self::whereTextId($text_id)
                               ->latest('w_id')->first();
        return 1+ $last_word->w_id ?? 0;
        
    }
    
    public static function tagOutWord($token, $i, $str) {
        $j = mb_strpos($token,'>',$i+1);
        $str .= mb_substr($token,$i,$j-$i+1); // other chars of the tag are transferred to str
        return [$j, $str];
    }

    public static function endWord($is_word, $str, $word, $words, $word_count) {
        if ($is_word) {
            $str .= $word.'</w>';
            $is_word = false;
            $words[$word_count-1] = $word;
            $word = '';
        }
        return [$is_word, $str, $word, $words];
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
     * Divides string on words
     * Suppose that the first and the last symbols of the string are the word chars, they are not special
     *
     * @param string $token  text without mark up
     * @param integer $word_count  initial word count
     *
     * @return array text with markup (split to words) and next word count
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
    
    public static function urlArgs($request) {
        $url_args = Str::urlArgs($request/*, 100*/) + [
                    'search_dialect'  => (int)$request->input('search_dialect'),
                    'search_lang'     => (int)$request->input('search_lang'),
                    'search_word'     => $request->input('search_word'),
                    'search_linked'   => (int)$request->input('search_linked'),
                ];
        
        return $url_args;
    }
    
    public function moveCharOut($char) {
        $text_xml = preg_replace("/\n/", "", $this->sentence->text_xml);
        
        if (preg_match("/^".$char."(.+)$/", $this->word, $w_parts) &&
            preg_match("/^(.+)(\<w id=\"".$this->w_id."\">)".$char."(.+)$/u", $text_xml, $s_parts)) {
            $new_text = $s_parts[1].$char.$s_parts[2].$s_parts[3];
//dd($text_xml, $this->w_id, $regs, $new_text);  
print "<p>sentence_id=".$this->sentence_id.", word_id=".$this->id."<br>\n".$new_text."</p><br>\n";   
            $this->sentence->text_xml = $new_text;
            $this->sentence->save();
            
            $this->word = $w_parts[1];
            $this->save();
            
            $this->sentence->numerateWords();            
//exit(0);            
        }
    }
    
    public static function addBlockToWord($word, $lang_id) {
//dd((string)$word);       
        $w_id = (int)$word->attributes()->id;
        $word_for_search = Grammatic::changeLetters((string)$word,$lang_id);

        $wordforms = LemmaWordform::where('wordform_for_search', 'like', $word_for_search)           
                                  ->whereLangId($lang_id);
        $lemmas = Lemma::where('lemma_for_search', 'like', $word_for_search)           
                                  ->whereLangId($lang_id);
        
        $word->addAttribute('word',$word_for_search);            
        
        if (!$wordforms->count() && !$lemmas->count()) {
            $word->addAttribute('class','no-wordforms');
        } else {
            $word->addAttribute('class','word-linked');            
            $word=Sentence::addLemmasBlock($word,$w_id);
        }
        return $word;
    }
    
}
