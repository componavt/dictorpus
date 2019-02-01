<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use DB;

use App\Models\Corpus\Text;
use App\Models\Corpus\Word;

class Wordform extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['wordform'];

    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }

    /** Gets gramset by lemma, dialect (if presented) and wordform.
     * 
     * @param int $lemma_id
     * @param int $dialect_id
     * @return Gramset
     */
    
    // Wordforms __has_many__ Lemma
    public function lemmas(){
        $builder = $this->belongsToMany(Lemma::class,'lemma_wordform');
//        $builder = $builder ->groupBy('lemma_id');
        $builder = $builder -> orderBy('lemma');
        return $builder;
    }

    // Wordforms __has_many__ Texts
    public function texts(){
        $builder = $this->belongsToMany(Text::class,'text_wordform');
        return $builder;
    }

    public function lemmaDialectGramset($lemma_id, $dialect_id=NULL)
    {
        $builder = $this->belongsToMany(Gramset::class, 'lemma_wordform')
             ->wherePivot('lemma_id', $lemma_id);
        if ($dialect_id) {
            $builder = $builder->wherePivot('dialect_id', $dialect_id);
        }
        return $builder;
    } 
    
    public function lemmaGramsetDialect($lemma_id, $gramset_id=NULL)
    {
        return $this->belongsToMany(Dialect::class, 'lemma_wordform')
             ->wherePivot('lemma_id', $lemma_id)
             ->wherePivot('gramset_id', $gramset_id)->first();
    }
    
    public function getMainPart() {
        mb_internal_encoding("UTF-8");
        $wordform = trim($this -> wordform);
/*        if (preg_match_all("/\b([^\b]+)\b/", $wordform, $words, PREG_PATTERN_ORDER)) {
            $wordform = array_pop($words[1]); // the last element
        }
*/        
        $words = preg_split("/[\s\u{c2a0}]+/",$wordform); // &nbsp; https://stackoverflow.com/a/42424643/1173350
        if (sizeof($words)>1) {
            $wordform = array_pop($words); // the last element
        } 
 
        return $wordform;
    }

    /**
     * Store wordform in nominative for nouns (NOUN), adjectives(ADJ)
     * and infinitive for verbs (VERB)
     * 
     * @param Lemma $lemma - object of lemma
     * @return NULL
     */
    public static function storeInitialWordforms($lemma) {
//dd($lemma);
        $pos_code = $lemma->pos->code;
//dd($pos_code); 
        $dialects = array_keys(Dialect::getList($lemma->lang_id));
//dd($dialects);        
        $gramset_id = '';
        
        if ($pos_code == 'NOUN' || $pos_code == 'ADJ') {
            $gramset_id = 1; // nominative
        } elseif ($pos_code == 'VERB') {
            $gramset_id = 170; //infinitive I
        }
//dd($gramset_id);        
        if ($gramset_id) {
            $wordform_obj = self::firstOrCreate(['wordform'=>$lemma->lemma]);
//dd($wordform_obj);            
            foreach ($dialects as $dialect_id) {
//dd($dialect_id);                
                $lemma-> wordforms()->attach($wordform_obj->id, ['gramset_id'=>$gramset_id, 'dialect_id'=>$dialect_id]);                
            }
        }
    }
    
    /**
     * Removes all neutral links (relevance=1) from meaning_text
     * and adds new links
     *
     * @return NULL
     */
    public function updateTextLinks($lemma)
    {        
        $lang_id = $lemma->lang_id;
        $word = addcslashes($this->wordform,"'");
        foreach ($lemma->meanings as $meaning) {
            $query = "select text_id, sentence_id, w_id, words.id as word_id from words where"
               . " text_id in (select id from texts where lang_id = ".$lang_id
                                . ") and word like '".$word."'";
//dd($query);        
            $words = DB::select($query); 
            $meaning->updateTextLinks($words);
        }
    }
    
    /**
     * return all language ids of all lemmas, linked with this wordform
     * 
     * @return Array - array of unique lang_ids
     */
    public function langsArr() {
        $langs = [];
        $lemmas = $this->lemmas;
        if (!sizeof($lemmas)) { return; }
        
        foreach ($lemmas as $lemma) {
            $langs[] = $lemma->lang_id;
        }
        
        return array_unique($langs);
    }

    /**
     * remove extra spaces at the beginning and end of the wordform 
     * @return boolean true if wordform was trimmed
     */
    public function trimWord() {
        $trim_word = trim($this->wordform);
        if ($trim_word != $this->wordform) {
            $this->wordform = $trim_word;
            $this->save();                    
            return true;
        }        
    }
    /**
     * called for the wordforms which contains white spaces (many-word wordforms)
     * search for words in the text following in the same sequence as in the wordform
     * and merge these words
     * 
     */
    public function checkWordformWithSpaces($text_output=0) {
        $words = preg_split("/\s+/",$this->wordform);
        if (sizeof($words)<2) { return; }

        // search the last word in the text with the languages of the wordform
        $word_coll = Word::searchByWordInTexts($words[sizeof($words)-1], $this->langsArr());
        if (!$word_coll) { return; }        
        
        print $text_output ? "<br><span style='color:red'>BINGO!</span>: ".sizeof($word_coll) : '';
        foreach ($word_coll as $last_word) {
            $words_founded = $last_word->searchForWordform($words);
            if (!$words_founded) { continue; }
            
            print $text_output ? "<br><span style='color:red'>FOUNDED: </span>".
                  $last_word->text_id.' | '.$last_word->sentence_id.' | '.join(',',array_keys($words_founded)) : '';
            $error_message = $last_word->mergeWords($words_founded);
            if ($error_message) {
                dd($error_message);
            }
        }
    }    
    
}
