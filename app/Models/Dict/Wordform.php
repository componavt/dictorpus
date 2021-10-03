<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use DB;

use App\Models\Corpus\MeaningTextRel;
use App\Models\Corpus\Text;
use App\Models\Corpus\Word;

use App\Library\Grammatic;

class Wordform extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */                                 // TODO: лишнее поле удалить
    protected $fillable = ['wordform', 'wordform_for_search']; 

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
        $builder = $this->belongsToMany(Lemma::class,'lemma_wordform')
                        ->withPivot('gramset_id') 
                        ->withPivot('dialect_id') 
                        ->orderBy('lemma');
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
    
    public static function urlArgs($request) {
        $url_args = [
                    'limit_num'       => (int)$request->input('limit_num'),
                    'page'            => (int)$request->input('page'),
                    'search_affix'    => $request->input('search_affix'),
                    'search_dialect'  => (int)$request->input('search_dialect'),
                    'search_gramset'  => (int)$request->input('search_gramset'),
                    'search_lang'     => (int)$request->input('search_lang'),
                    'search_pos'      => (int)$request->input('search_pos'),
                    'search_wordform' => $request->input('search_wordform'),
                ];
        
        if (!$url_args['page']) {
            $url_args['page'] = 1;
        }
        
        if ($url_args['limit_num']<=0) {
            $url_args['limit_num'] = 10;
        } elseif ($url_args['limit_num']>1000) {
            $url_args['limit_num'] = 1000;
        }   
        
        return $url_args;
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

    public function gramsetPivot() {
        $gramset_id=$this->pivot->gramset_id;
        if (!$gramset_id) { return; }
        $gramset = Gramset::find($gramset_id);
        return $gramset;
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
        $affix = $lemma->reverseLemma->affix;
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
                $lemma->wordforms()->attach($wordform_obj->id, ['gramset_id'=>$gramset_id, 'dialect_id'=>$dialect_id, 'affix'=>$affix]);                
            }
        }
    }
    
    public function updateTextWordformLinks($text_id, $w_id, $gramset_id) {
        if (!$gramset_id) {
            return;
        }
        DB::statement('UPDATE text_wordform SET relevance=0'. // всем связям проставим отрицательные
                      ' WHERE text_id='.$text_id.
                      ' AND w_id='.$w_id);
        $wordform_link = $this->texts()->wherePivot('text_id',$text_id)->wherePivot('w_id',$w_id)->wherePivot('gramset_id',$gramset_id);
        if ($wordform_link->count()) {
            $wordform_link->update(['relevance'=>2]);
        } else {
            $wordform_link = $this->texts()->attach($text_id, ['w_id'=>$w_id, 'gramset_id'=>$gramset_id, 'relevance'=>2]);
        }
        MeaningTextRel::updateMeaningLinksAfterCheckExample($text_id, $w_id, $gramset_id);
}
    
    /**
     * Search in texts words matched with this wordform
     * 
     * SQL: select text_id, w_id, words.id as word_id from words, texts where words.text_id = texts.id and texts.lang_id = 5 and word like 'olen' LIMIT 1;
     * 
     * @return Collection
     */
    public function getWordsForLinks($lang_id) {
        if (!$lang_id) {
            return null;
        }
        $this->trimWord(); // remove extra spaces at the beginning and end of the wordform 
        $query = "select text_id, s_id, w_id, words.id as word_id from words where"
               . " text_id in (select id from texts where lang_id = ".$lang_id
               . ") and word like '".Grammatic::changeLetters($this->wordform,$lang_id)."'"; 
        $words = DB::select($query); 
        return $words;
    }

    /**
     * Ckeck if any checked meaning exists
     * Return 0, if it exists
     * 
     * @param int $text_id
     * @param int $w_id
     * @param int $old_relevance
     * @return int
     */
    public function checkRelevance($text_id, $w_id, $gramset_id, $old_relevance=1) {
        if ($old_relevance == 0 ||
        // if some another wordform has positive evaluation with this sentence, 
        // it means that this wordform is not suitable for this example
            DB::table('text_wordform')->where('wordform_id','<>',$this->id)
              ->whereTextId($text_id)->whereWId($w_id)->whereGramsetId($gramset_id)
              ->where('relevance', 2)->count()>0) {
            return 0;
        } 
/*if ($text_id==1548 && $w_id==7) {
dd($relevance);
} */       
        return $old_relevance;
    }

    public function addTextLink($text_id, $w_id, $gramset_id, $old_relevance) {
        $relevance = $this->checkRelevance($text_id, $w_id, $old_relevance);
        $this->texts()->attach($text_id,
                                ['gramset_id'=>$gramset_id, 
                                 'w_id'=>$w_id, 
                                 'relevance'=>$relevance]);        
    }
    
    /**
     * Add records to meaning_text for new meanings
     *
     * @param Collection $words - collection of Word objects
     * @return NULL
     */
    public function addTextLinks($words=null, $lang_id) {
        if (!$this->pivot->gramset_id) { return; }

        if (!$words) {
            $words = $this->getWordsForLinks($lang_id);            
        }
        if (!$words) { return; }
        
        foreach ($words as $word) {
            $this->addTextLink($word->text_id, $word->w_id, $this->pivot->gramset_id, 1);        
        }
    }
    
    /**
     * Updates records in the table meaning_text, 
     * which binds the tables meaning and text.
     * Search in texts a lemma and all wordforms of the lemma.
     *
     * @param Collection $words - collection of Word objects
     * @return NULL
     */
     public function updateTextLinks($words=null) {
        if (!$this->pivot->gramset_id) { return; }
        if (!$this->lemma) { return; }
        if (!$words) {
            $words = $this->getWordsForLinks($this->lemma->lang_id);            
        }

        $old_relevances = $this->getRelevances();
        $this->texts()->detach();
        if (!$words) { return; }
        
        foreach ($words as $word) {
            $this->addTextLink($word->text_id, $word->w_id, $this->pivot->gramset_id, 
                   $old_relevances[$word->text_id][$word->w_id] ?? 1);
        }
    }
    
    /**
     * Saves relevances <> 1 into array 
     * 
     * @return Array
     */
    public function getRelevances() {
        $relevances = [];
        $texts = $this->texts()->wherePivot('gramset_id', $this->pivot->gramset_id)->get();
        foreach ($texts as $text) {
            if ($text->pivot->relevance != 1) {
                $relevances[$text->id][$text->pivot->w_id] = $text->pivot->relevance;
            }
        }
        return $relevances;
    }

    /**
     * Removes all neutral links (relevance=1) from meaning_text
     * and adds new links
     *
     * @return NULL
     */
    public function updateMeaningTextLinks($lemma)
    {        
        $lang_id = $lemma->lang_id;
        $word = addcslashes(Grammatic::changeLetters($this->wordform,$lang_id),"'");
        $query = "select text_id, s_id, w_id, words.id as word_id from words where"
           . " text_id in (select id from texts where lang_id = ".$lang_id
                            . ") and word like '".$word."'";
//dd($query);        
        $words = DB::select($query); 
        foreach ($lemma->meanings as $meaning) {
            $meaning->updateSomeTextLinks($words);
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
    
    public function updateAffix($lemma_id, $gramset_id, $affix) {
//        DB::statement("UPDATE lemma_wordform SET affix='".str_replace("’","\’",$affix)."' WHERE wordform_id='". $this->id. "' AND lemma_id='$lemma_id' AND gramset_id='$gramset_id'");
        DB::table('lemma_wordform')->whereWordformId($this->id)->whereLemmaId($lemma_id)->whereGramsetId($gramset_id)
                ->update(["affix"=>$affix]);
/*        $lws = LemmaWordform::where('wordform_id', $this->id)
                            ->where('lemma_id', $lemma_id)
                            ->where('gramset_id', $gramset_id)->get();
        foreach ($lws as $wordform) {
            $wordform->affix = $affix;
            $wordform ->save();
        }*/
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
                  $last_word->text_id.' | '.$last_word->s_id.' | '.join(',',array_keys($words_founded)) : '';
            $error_message = $last_word->mergeWords($words_founded);
            if ($error_message) {
                dd($error_message);
            }
        }
    }    
    
    public static function search(Array $url_args) {
        $wordforms = self::orderBy('wordform');
        $wordforms = self::searchByWordform($wordforms, $url_args['search_wordform']);
        $wordforms = self::searchByAffix($wordforms, $url_args['search_affix']);

        if ($url_args['search_dialect'] || !$url_args['search_lang']) {
             $url_args['search_lang'] = Dialect::getLangIDByID($url_args['search_dialect']);
        }
//        if ($search_lang || $search_pos || $search_dialect) {
        $wordforms = $wordforms->join('lemma_wordform', 'wordforms.id', '=', 'lemma_wordform.wordform_id');
//        }
        $wordforms = self::searchByGramset($wordforms, $url_args['search_gramset']);
        $wordforms = self::searchByLang($wordforms, $url_args['search_lang']);
        $wordforms = self::searchByPOS($wordforms, $url_args['search_pos']);
        $wordforms = self::searchByDialect($wordforms, $url_args['search_dialect']);
//dd($wordforms->toSql());        
        return $wordforms;
    }
    
    public static function searchByWordform($wordforms, $wordform) {
        $wordform = Grammatic::toSearchForm($wordform);
        if (!$wordform) {
            return $wordforms;
        }
        return 
            $wordforms->where('lemma_wordform.wordform_for_search','like', $wordform);
    }
    
    public static function searchByAffix($wordforms, $affix) {
        if (!$affix) {
            return $wordforms;
        }
        return 
            $wordforms->where('affix','like', $affix);
    }
    
    public static function searchByDialect($wordforms, $dialect) {
        if (!$dialect) {
            return $wordforms;
        }
        return $wordforms->where('dialect_id',$dialect);
    }
    
    public static function searchByGramset($wordforms, $gramset) {
        if (!$gramset) {
            return $wordforms;
        }
        return $wordforms->where('gramset_id',$gramset);
    }
    
    public static function searchByLang($wordforms, $lang) {
        if (!$lang) {
            return $wordforms;
        }
        return $wordforms->whereIn('lemma_id',function($query) use ($lang){
                    $query->select('id')
                    ->from(with(new Lemma)->getTable())
                    ->where('lang_id', $lang);
                });
    }
    
    public static function searchByPOS($wordforms, $pos) {
        if (!$pos) {
            return $wordforms;
        }
        return $wordforms->whereIn('lemma_id',function($query) use ($pos){
                    $query->select('id')
                    ->from(with(new Lemma)->getTable())
                    ->where('pos_id',$pos);
                });
    }

    public static function countByLang($lang_id) {
        return LemmaWordform::selectWhereLang($lang_id)->count();
        
    }
    
    public static function findOrCreate($word) {
        $wordform = self::firstOrCreate(['wordform'=>$word]);
//TODO: лишнее поле, удалить        
        $wordform_for_search = Grammatic::toSearchForm($word);
        if ($wordform->wordform_for_search != $wordform_for_search) {
            $wordform->wordform_for_search = $wordform_for_search;
            $wordform->save();
        }        
        return $wordform;
    }
    
    /**
     * select count(*) from lemma_wordform where affix is NULL and gramset_id is not NULL and wordform_id in (select id from wordforms where wordform not like '% %') and lemma_id in (select id from lemmas where lang_id=1);

     * 
     * @param type $lang_id
     * @return type
     */
    public static function countWithoutAffixes($lang_id) {
        return LemmaWordform::selectWhereLang($lang_id)
                ->whereNull('affix')
                ->whereNotNull('gramset_id')
                ->whereIn('wordform_id',function($query){
                          $query->select('id')->from('wordforms')
                                ->where('wordform','NOT LIKE','% %');
                  })
                ->count();        
    }      
    
    public static function countWrongAffixes($lang_id) {
        return LemmaWordform::selectWhereLang($lang_id)
                ->whereAffix('#')
                ->count();        
    }      
}
