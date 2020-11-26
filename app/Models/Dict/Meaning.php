<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use DB;
use LaravelLocalization;

use App\Models\Corpus\Place;
use App\Models\Corpus\Text;
//use App\Models\Corpus\Transtext;

use App\Models\Dict\Lang;
use App\Models\Dict\Relation;

//use App\Models\Corpus\Word;

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

    // Belongs To Relations
    use \App\Traits\Relations\BelongsTo\Lemma;

    // Belongs To Many Relations
    use \App\Traits\Relations\BelongsToMany\Concepts;
    use \App\Traits\Relations\BelongsToMany\Dialects;
    use \App\Traits\Relations\BelongsToMany\Labels;
    use \App\Traits\Relations\BelongsToMany\MeaningRelations;
    use \App\Traits\Relations\BelongsToMany\Places;
    use \App\Traits\Relations\BelongsToMany\Translations;
    
    public function texts(){
        return $this->belongsToMany(Text::class,'meaning_text')
                ->withPivot('w_id')
                ->withPivot('relevance');
    }
    
    // Has Many Relations
    use \App\Traits\Relations\HasMany\MeaningTexts;


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

/*        DB::table('concept_meaning')
          ->where('meaning_id',$this->id)->delete();*/

        $this->concepts()->detach();
        $this->labels()->detach();
        $this->dialects()->detach();
        $this->places()->detach();

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
     * @param $lang_code String - language code
     * @return String
     */
    public function getMultilangMeaningTextsString($lang_code='') :String
    {
        $mean_langs = [];
        $meaning_texts = $this->meaningTexts()->get();
        if ($lang_code) {
            $lang = Lang::where('code',$lang_code)->first();
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
                if ($meaning_text_obj->lang->code != $lang_code) {
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
    
    public function storeDialects($dialects) {
        $this->dialects()->detach();
        if ($dialects && sizeof($dialects)) {
            $this->dialects()->attach($dialects);
        }
    }
    
    /**
     * Stores array of new meanings for the lemma
     * 
     * @param Array $meanings [<count>=>["meaning_n" => "1", "meaning_text" => [<lang1_id> => <meaning_text11>,  <lang2_id> => <meaning_text12>, ...]],
                               <count>+1=>["meaning_n" => "2", "meaning_text" => [<lang1_id> => <meaning_text21>, <lang2_id> => <meaning_text22>, ...],
     *                         ... ]
     *                         count=0, if it is a new lemma
     *
     * @return NULL
     */
    public static function storeLemmaMeanings($meanings, $lemma_id){
//dd($meanings);        
        if (!$meanings || !is_array($meanings)) {
            return;
        }
        foreach ($meanings as $meaning) {
            self::storeLemmaMeaning($lemma_id, (int)$meaning['meaning_n'], $meaning['meaning_text']);
        }
    }

    /**
     * 
     * @param int $lemma_id
     * @param int $meaning_n
     * @param array $meaning_texts [<lang1_id> => <meaning_text1>,  <lang2_id> => <meaning_text2>, ...]]
     * @return Meaning - object
     */
    public static function storeLemmaMeaning($lemma_id, $meaning_n, $meaning_texts){
        foreach ($meaning_texts as $lang=>$meaning_text) {
            if (!$meaning_text) { // а если все толкования пусты, сотрутся они из базы?
                unset($meaning_texts[$lang]);
            }
        }

        if (sizeof($meaning_texts)){
            $meaning_obj = self::firstOrCreate(['lemma_id' => $lemma_id, 'meaning_n' => $meaning_n]);
            self::updateLemmaMeaningTexts($meaning_texts, $meaning_obj->id);
            return $meaning_obj;
        }
        return null;
    }

    /**
     * Updates array of meanings and remove meanings without meaning texts
     *
     * @return NULL
     */
    public static function updateLemmaMeanings($meanings){
//dd($meanings);        
        if (!$meanings || !is_array($meanings)) {
            return;
        }
        foreach ($meanings as $meaning_id => $meaning) {
//print "<p>".$meaning['meaning_n'];  
//dd($meaning);
            $meaning_obj = self::find($meaning_id);

            self::updateLemmaMeaningTexts($meaning['meaning_text'], $meaning_id);
            
            $meaning_obj->updateMeaningRelations($meaning['relation'] ?? []);

            $meaning_obj->updateMeaningTranslations($meaning['translation'] ?? []);

            $meaning_obj->updateConcepts($meaning['concepts'] ?? []);
            
            $meaning_obj->updatePlaces($meaning['places'] ?? []);
            
            // is meaning has any meaning texts or any relations
            if ($meaning_obj->meaningTexts()->count() || $meaning_obj->meaningRelations()->count()) { 
                $meaning_obj -> meaning_n = $meaning['meaning_n'];
                $meaning_obj -> save();

            } else {
                $meaning_obj->remove();
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
    
    /**
     * Updates array of meaning concepts 
     *
     * @return NULL
     */
    public function updateConcepts($concepts)
    {
        // removes all concepts from this meaning
        $this->concepts()->detach();
        if (!is_array($concepts) || !sizeof($concepts)) {
            return;
        }
//dd($concepts);        
        $this->concepts()->attach($concepts);
    }
    
    /**
     * Updates array of meaning concepts 
     *
     * @return NULL
     */
    public function updatePlaces($places)
    {
        // removes all concepts from this meaning
        $this->places()->detach();
        if (!is_array($places) || !sizeof($places)) {
            return;
        }
//dd($concepts);        
        $this->places()->attach($places);
        
        foreach ($places as $place_id) {
            $place=Place::find($place_id);
            foreach ($place->dialects as $dialect) {
                $this->dialects()->attach($dialect->id);
            }
        }
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
    public function checkRelevance($text_id, $w_id, $old_relevance=1) {
        if ($old_relevance == 0 ||
        // if some another meaning has positive evaluation with this sentence, 
        // it means that this meaning is not suitable for this example
            DB::table('meaning_text')->where('meaning_id','<>',$this->id)
              ->whereTextId($text_id)->whereWId($w_id)
              ->where('relevance','>',1)->count()>0) {
            return 0;
        } 
/*if ($text_id==1548 && $w_id==7) {
dd($relevance);
} */       
        return $old_relevance;
    }

    public function addTextLink($text_id, $sentence_id, $word_id, $w_id, $old_relevance) {
        $relevance = $this->checkRelevance($text_id, $w_id, $old_relevance);
        $this->texts()->attach($text_id,
                                ['sentence_id'=>$sentence_id, 
                                 'word_id'=>$word_id, 
                                 'w_id'=>$w_id, 
                                 'relevance'=>$relevance]);        
    }
    
    /**
     * Add records to meaning_text for new meanings
     *
     * @param Collection $words - collection of Word objects
     * @return NULL
     */
    public function addTextLinks($words) {
        foreach ($words as $word) {
            $this->addTextLink($word->text_id, $word->sentence_id, $word->word_id, $word->w_id, 1);        
        }
    }
    
    /**
     * Updates records in the table meaning_text, 
     * which binds the tables meaning and text.
     * Search in texts a lemma and all wordforms of the lemma.
     *
     * @return NULL
     */
     public function updateTextLinks($words) {
        $old_relevances = $this->getRelevances();
        $this->texts()->detach();
        
        foreach ($words as $word) {
            $this->addTextLink($word->text_id, $word->sentence_id, $word->word_id, $word->w_id, 
                    $this->checkRelevance($word->text_id, $word->w_id, $old_relevances[$word->text_id][$word->w_id] ?? 1));
        }
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
     *
     * @return NULL
     */
     public function updateSomeTextLinks($words) {
        if (!$words) {
            return;
        }
        foreach ($words as $word) {
            $link = $this->texts()->wherePivot('text_id', $word->text_id)->wherePivot('w_id', $word->w_id);
            if (!$link) {
                $this->addTextLink($word->text_id, $word->sentence_id, 
                        $word->word_id, $word->w_id, 
                        self::getDefaultRelevance($word->text_id, $word->w_id));
            }
        }
    }
    
    public static function countTranslations(){
        return DB::table('meaning_translation')->count();
    }
    
    public static function countRelations(){
        return DB::table('meaning_relation')->count();
    }
        
    /**
     * 
     * @param array $phonetic_dialects [<phonetic1>=>[<dialect1_id>=>[<place1_id>, ...], ...], ...]
     */
    public function updateDialects($phonetic_dialects) {
        foreach ($phonetic_dialects as $phonetic => $dialects) {
            foreach ($dialects as $dialect_id => $places) {
                $this->addDialect($dialect_id);
                foreach ($places as $place_id) {
                    $this->addPlace($place_id);
                }
            }
        }
    }

    public function addConcept($concept_id) {
        if (!$this->concepts()->where('concept_id', $concept_id)->first()) {
            $this->concepts()->attach($concept_id);
        }
    }
    
    public function addLabel($label_id) {
        if (!$this->labels()->where('label_id', $label_id)->first()) {
            $this->labels()->attach($label_id);
        }
    }
    
    public function addDialect($dialect_id) {
        if (!$this->dialects()->where('dialect_id', $dialect_id)->first()) {
            $this->dialects()->attach($dialect_id);
        }
    }
    
    public function addPlace($place_id) {
        if (!$this->places()->where('place_id', $place_id)->first()) {
            $this->places()->attach($place_id);
        }
    }
    
}
