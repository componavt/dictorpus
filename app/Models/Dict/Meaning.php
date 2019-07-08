<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use DB;
use LaravelLocalization;

use App\Models\Corpus\Text;
use App\Models\Corpus\Transtext;

use App\Models\Dict\Lang;
use App\Models\Dict\Relation;

use App\Models\Corpus\Word;

class Meaning extends Model
{
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = ['lemma_id','meaning_n'];

    /**
     * Meaning __belongs_to__ Lemma
     *
     * @return Illuminate\Database\Eloquent\Relations\Relation
     */
    public function lemma()
    {
        return $this->belongsTo(Lemma::class);
    }

    /**
     * Meaning __has_many__ MeaningTexts
     *
     * @return Illuminate\Database\Eloquent\Relations\Relation
     */
    public function meaningTexts()
    {
        return $this->hasMany(MeaningText::class);
    }

    /**
     * Meaning __has_many__ Meaning
     *
     * @return Illuminate\Database\Eloquent\Relations\Relation
     */
    public function meaningRelations(){
/*        return $this->belongsToMany(Meaning::class,'meaning_relation','meaning1_id','meaning2_id')
                    ->withPivot('relation_id');     */

        return $this->belongsToMany(Relation::class,'meaning_relation','meaning1_id','relation_id')
                    ->withPivot('meaning2_id');
    }

    /**
     * Meaning __has_many__ Lang
     *
     * @return Illuminate\Database\Eloquent\Relations\Relation
     */
    public function translations(){
/*        return $this->belongsToMany(Meaning::class,'meaning_relation','meaning1_id','meaning2_id')
                    ->withPivot('relation_id');     */

        return $this->belongsToMany(Lang::class,'meaning_translation','meaning1_id','lang_id')
                    ->withPivot('meaning2_id');
    }

    /**
     * Meaning __has_many__ Texts
     *
     * @return Illuminate\Database\Eloquent\Relations\Relation
     */
    public function texts(){
        return $this->belongsToMany(Text::class,'meaning_text')
                ->withPivot('w_id')
                ->withPivot('relevance');
    }
    
    /** Gets list of meanings for lemma $lemma_id,
     * if $lang_id is empty, gets null
     * 
     * @param $lemma_id - lemma ID
     * @return Array
     */
    public static function getList($lemma_id=NULL)
    {     
        $lemma = Lemma::find($lemma_id);
        if (!$lemma) {
            return;
        }
//        $locale = LaravelLocalization::getCurrentLocale();
        
        $meanings = $lemma->meanings;
        
        $list = array();
        foreach ($meanings as $row) {
            $list[$row->id] = $row->getMultilangMeaningTextsStringLocale();
        }
        
        return $list;         
    }
    
    /**
     * Gets total number of sentences for examples in Lemma show page
     *
     * @param $for_edit Boolean: true - for edition, output all sentences, 
     *                           false - for view, output all positive examples (relevance>0)
     * @return array
     */
    public function countSentences($for_edit=false)
    {    
        $sentence_builder = DB::table('meaning_text')
                              ->where('meaning_id',$this->id);
        if (!$for_edit) {
            $sentence_builder = $sentence_builder->where('relevance','>',0);
        }
        return $sentence_builder->count();
    }
    
    /**
     * Gets sentences for examples in Lemma show page
     *
     * @param $for_edit Boolean: true - for edition, output all sentences, 
     *                           false - for view, output all positive examples (relevance>0)
     * @return array
     */
    public function sentences($for_edit=false, $limit=''){
        $sentences = [];
        $sentence_builder = DB::table('meaning_text')
                              ->where('meaning_id',$this->id)
                              ->orderBy('relevance','desc')
                              ->orderBy('text_id')
                              ->orderBy('sentence_id')
                              ->orderBy('word_id');
        if (!$for_edit) {
            $sentence_builder = $sentence_builder->where('relevance','>',0);
        }
        
        if ($limit) {
            $sentence_builder = $sentence_builder->take($limit);
        }
//print "<p>". $sentence_builder->count()."</p>";       
        
        foreach ($sentence_builder->get() as $sentence) {
            $sentence = Text::extractSentence($sentence->text_id, 
                                              $sentence->sentence_id, 
                                              $sentence->w_id, 
                                              $sentence->relevance);
            if ($sentence) {
                $sentences[] = $sentence;
            }
        }
        
        return $sentences;
    }
    
    public function remove() {
        DB::table('meaning_relation')
          ->where('meaning1_id',$this->id)->delete();
        DB::table('meaning_relation')
          ->where('meaning2_id',$this->id)->delete();
        
        DB::table('meaning_translation')
          ->where('meaning1_id',$this->id)->delete();
        DB::table('meaning_translation')
          ->where('meaning2_id',$this->id)->delete();

        DB::table('meaning_text')
          ->where('meaning_id',$this->id)->delete();

        foreach ($this->meaningTexts as $meaning_text) {
            $meaning_text -> delete();
        }
        $this->delete();
    }

    /**
     * Gets all meaning texts and returns string:
     * 
     * <meaning_n>. <lang1_code>: <meaning_on_lang1>; <lang2_code>: <meaning_on_lang2>; ...
     * OR
     *              <lang1_code>: <meaning_on_lang1>; <lang2_code>: <meaning_on_lang2>; ...
     *              if lemma has one meaning 
     * 
     * @param $code String - language code
     * @return String
     */
    public function getMultilangMeaningTextsString($code='') :String
    {
        $mean_langs = [];
        $meaning_texts = $this->meaningTexts()->get();
       if ($code) {
            $lang = Lang::where('code',$code)->first();
            if ($lang) {
                $meaning_texts_by_code = $this->meaningTexts()->where('lang_id',$lang->id);
                if ($meaning_texts_by_code->count() > 0) {
                    $meaning_texts = $meaning_texts_by_code->get();
                }
            }
        }
        foreach ($meaning_texts as $meaning_text_obj) {
            $meaning_text = $meaning_text_obj->meaning_text;
            if ($meaning_text) {
                if ($meaning_text_obj->lang->code != $code) {
                    $meaning_text = $meaning_text_obj->lang->code .': '. $meaning_text;
                }
                $mean_langs[] = $meaning_text;  
            } 
        }
        
        $out = join(', ',$mean_langs);

        if ($this->lemma->meanings()->count()>1) {
            $out = $this->meaning_n. ') '.$out;
        }
        return $out;
    }

    public function getMultilangMeaningTextsStringLocale() :String
    {
        return $this->getMultilangMeaningTextsString(LaravelLocalization::getCurrentLocale());
    }
    
    /**
     * Gets all meaning texts and returns string:
     * 
     * <lemma> (<meaning_n>. <lang1_code>: <meaning_on_lang1>; <lang2_code>: <meaning_on_lang2>; ...)
     * OR
     * <lemma> (<lang1_code>: <meaning_on_lang1>; <lang2_code>: <meaning_on_lang2>; ...)
     *              if lemma has one meaning 
     * 
     * @return String
     */
    public function getLemmaMultilangMeaningTextsString() :String
    {
        return $this->lemma->lemma . ' ('. $this->getMultilangMeaningTextsString() . ')';
    }
    
    /**
     * Gets an array of meaning texts for ALL languages and sorted by lang_id
     *
     * @return Array
     */
    public function meaningTextsWithAllLangs()
    {
        $own_lang_id = $this->lemma->lang_id;

        $langs = Lang::getListWithPriority($own_lang_id);
        $meaning_texts = array();

        foreach ($langs as $lang_id => $lang_text) {
                $meaning_text_obj = $this->meaningTexts()->where('lang_id', $lang_id)->first();
                if (!$meaning_text_obj) {
                    $meaning_text_obj = new MeaningText;
                    $meaning_text_obj -> meaning_text = NULL;
                }
                $meaning_text_obj -> lang_name = $lang_text;
                $meaning_texts[$lang_id] = $meaning_text_obj;
        }

        return $meaning_texts;
    }
    
    /**
     * Gets relations missing in this meaning
     * 
     * @return Array [1=>'synonyms'...]
     */
    public function missingRelationsList() :Array
    {
        $relations = [];
        
        foreach (Relation::getList() as $relation_id=>$relation_text) {
            if ($this->meaningRelations()->wherePivot('relation_id',$relation_id)->count() == 0) {
                $relations[$relation_id] = $relation_text;
            }
        }
        return $relations;
    }

    /**
     * Stores array of new meanings for the lemma
     *
     * @return NULL
     */
    public static function storeLemmaMeanings($meanings, $lemma_id){
        if (!$meanings || !is_array($meanings)) {
            return;
        }
        foreach ($meanings as $meaning) {
            $meaning_texts = $meaning['meaning_text'];
            foreach ($meaning_texts as $lang=>$meaning_text) {
                if (!$meaning_text) { // а если все толкования пусты, сотрутся они из базы?
                    unset($meaning_texts[$lang]);
                }
            }

            if (sizeof($meaning_texts)){
                $meaning_obj = self::firstOrCreate(['lemma_id' => $lemma_id, 'meaning_n' => (int)$meaning['meaning_n']]);
                self::updateLemmaMeaningTexts($meaning_texts, $meaning_obj->id);
            }
        }
    }

    /**
     * Updates array of meanings and remove meanings without meaning texts
     *
     * @return NULL
     */
    public static function updateLemmaMeanings($meanings){
        if (!$meanings || !is_array($meanings)) {
            return;
        }
        foreach ($meanings as $meaning_id => $meaning) {
            $meaning_obj = self::find($meaning_id);

            self::updateLemmaMeaningTexts($meaning['meaning_text'], $meaning_id);
            
            $meaning_obj->updateMeaningRelations(isset($meaning['relation']) ? $meaning['relation'] : []);

            $meaning_obj->updateMeaningTranslations(isset($meaning['translation']) ? $meaning['translation'] : []);

            // is meaning has any meaning texts or any relations
            if ($meaning_obj->meaningTexts()->count() || $meaning_obj->meaningRelations()->count()) { 
                $meaning_obj -> meaning_n = $meaning['meaning_n'];
                $meaning_obj -> save();

            } else {
                $meaning_obj->texts()->detach();
                $meaning_obj -> delete();
            }
        }
    }

    public static function updateLemmaMeaningTexts($meanings, $meaning_id){
        foreach ($meanings as $lang=>$meaning_text) {
            if ($meaning_text) {
                $meaning_text_obj = MeaningText::firstOrCreate(['meaning_id' => $meaning_id, 'lang_id' => $lang]);
                $meaning_text_obj -> meaning_text = $meaning_text;
                $meaning_text_obj -> save();
            } else {
                // delete if meaning_text exists in DB but it's empty in form
                $meaning_text_obj = MeaningText::where('meaning_id',$meaning_id)->where('lang_id',$lang)->first();
                if ($meaning_text_obj) {
                    $meaning_text_obj -> delete();
                }
            }
        }
    }
    /**
     * Updates array of meaning relations 
     *
     * @return NULL
     */
    public function updateMeaningRelations($relations)
    {
        // removes all relations to this meaning
        DB::table('meaning_relation')
          ->where('meaning2_id',$this->id)->delete();
        // removes all relations from this meaning
        $this->meaningRelations()->detach();
        
        if (!is_array($relations)) {
            return;
        }
        foreach ($relations as $relation_id=>$rel_means) {
            foreach ($rel_means as $rel_mean_id) {
                $this->meaningRelations()
                     ->attach($relation_id,['meaning2_id'=>$rel_mean_id]);
                
                // reverse relation
                $mean2_obj = self::find($rel_mean_id);
                $relation_obj = Relation::find($relation_id);
                $mean2_rels = $mean2_obj->meaningRelations();
//                if (!$mean2_rels->wherePivot('relation_id',$relation_obj->reverse_relation_id)
  //                              ->wherePivot('meaning2_id',$this->id)->count()) {
                 $mean2_rels->attach($relation_obj->reverse_relation_id,
                                        ['meaning2_id'=>$this->id]);
    //            }
            }
        }
    }
    
    /**
     * Updates array of meaning relations 
     *
     * @return NULL
     */
    public function updateMeaningTranslations($translations)
    {
        // removes all translations to this meaning
        DB::table('meaning_translation')
          ->where('meaning2_id',$this->id)->delete();
        // removes all translations from this meaning
        $this->translations()->detach();
        
        if (!is_array($translations)) {
            return;
        }
        foreach ($translations as $lang_id=>$trans_means) {
            foreach ($trans_means as $trans_mean_id) {
                $this->translations()
                     ->attach($lang_id,['meaning2_id'=>$trans_mean_id]);
                
                // reverse translation
                $mean2_obj = self::find($trans_mean_id);
                $mean2_transls = $mean2_obj->translations();
                $mean2_transls->attach($this->lemma->lang_id,
                                       ['meaning2_id'=>$this->id]);
            }
        }
    }
    
    public function chooseRelevance($text_id, $w_id, $old_relevance=1) {
        if ($old_relevance == 0 ||
        // if some another meaning has positive evaluation with this sentence, 
        // it means that this meaning is not suitable for this example
            DB::table('meaning_text')->where('meaning_id','<>',$this->id)
              ->where('text_id',$text_id)->where('w_id',$w_id)
              ->where('relevance','>',1)->count()>0) {
            return 0;
        } 
/*if ($text_id==1548 && $w_id==7) {
dd($relevance);
} */       
        return $old_relevance;
    }

    public function addTextLink($text_id, $sentence_id, $word_id, $w_id, $old_relevance) {
        $relevance = $this->chooseRelevance($word_id, $w_id, $old_relevance);
        $this->texts()->attach($text_id,
                                ['sentence_id'=>$sentence_id, 
                                 'word_id'=>$word_id, 
                                 'w_id'=>$w_id, 
                                 'relevance'=>$relevance]);        
    }
    
    /**
     * Add records to meaning_text for new meanings
     *
     * @return NULL
     */
    public function addTextLinks() {
        $words = $this->getWordsByWordforms();
        if (!$words) {
            return;
        }
        
        foreach ($words as $word) {
            $this->addTextLink($word->text_id, $word->sentence_id, $word->word_id, $word->w_id, 1);        
        }
    }
    
    /**
     * (Check comment and remove it:  "Removes all neutral links (relevance=1) from meaning_text").
     * 
     * Updates records in the table meaning_text, 
     * which binds the tables meaning and text.
     * Search in texts a lemma and all wordforms of the lemma.
     *
     * TODO: remove duplicates of wordorms from the SQL request.
     * 
     * SQL: select text_id, sentence_id, w_id, words.id as word_id from words, texts where words.text_id = texts.id and texts.lang_id = 5 and (word like 'olla' OR word like 'olen' OR word like 'on' OR word like 'ollah' OR word like 'olla' OR word like 'en ole') LIMIT 1;
     * SQL: select text_id, sentence_id, w_id, words.id as word_id from words where text_id in (select id from texts where lang_id = 5) and (word like 'olla' OR word like 'olen' OR word like 'on' OR word like 'ollah' OR word like 'olla' OR word like 'en ole') LIMIT 1;
     * 
     * @return NULL
     */
    public function getWordsByWordforms()
    {        
        $lemma_obj=$this->lemma;
        $lang_id = $lemma_obj->lang_id;
        $strs = ["word like '".$lemma_obj->lemma_for_search."'"];
        
//dd($lemma_obj->wordforms);        
        foreach ($lemma_obj->wordforms as $wordform_obj) {
            $wordform_obj->trimWord(); // remove extra spaces at the beginning and end of the wordform 
            //$wordform_obj->checkWordformWithSpaces(0); // too heave request, we are waiting new server :(((
            $strs[] = "word like '".$wordform_obj->wordform_for_search."'";
        }
        $cond = join(' OR ',array_unique($strs));
//dd($cond);        

        $query = "select text_id, sentence_id, w_id, words.id as word_id from words where"
               . " text_id in (select id from texts where lang_id = ".$lang_id
               . ") and (".$cond.")"; 
//dd($query);        
        $words = DB::select($query); 
        return $words;
    }
    
    /**
     * Saves relevances <> 1 into array 
     * 
     * @return Array
     */
    public function getRelevances() {
        $relevances = [];
        foreach ($this->texts as $text) {
            if ($text->pivot->relevance != 1) {
                $relevances[$text->id][$text->pivot->w_id] = $text->pivot->relevance;
            }
        }
        return $relevances;
    }

    /**
     * Updates records in the table meaning_text, 
     * which binds the tables meaning and text.
     * Search in texts a lemma and all wordforms of the lemma.
     *
     * @return NULL
     */
     public function updateTextLinks() {
        $words = $this->getWordsByWordforms();
//dd($words); 
        $old_relevances = $this->getRelevances();
//dd($old_relevances);        
        $this->texts()->detach();
        
        if (!$words) {
            return;
        }
        foreach ($words as $word) {
            $relevance = isset($old_relevances[$word->text_id][$word->w_id]) ? $old_relevances[$word->text_id][$word->w_id] : 1;
            $this->addTextLink($word->text_id, $word->sentence_id, $word->word_id, $word->w_id, $relevance);
        }
    }
    
    /**
     *
     * @return NULL
     */
     public function updateSomeTextLinks($words) {
        if (!$words) {
            return;
        }
//dd($words);        
        foreach ($words as $word) {
            $link = $this->texts()->wherePivot('text_id', $word->text_id)->wherePivot('w_id', $word->w_id);
            if (!$link) {
                $this->addTextLink($word->text_id, $word->sentence_id, $word->word_id, $word->w_id, 1);
            }
        }
    }
    
    public static function countTranslations(){
        return DB::table('meaning_translation')->count();
    }
    
    public static function countRelations(){
        return DB::table('meaning_relation')->count();
    }
        
}
