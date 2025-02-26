<?php namespace App\Traits\Modify;

use DB;

use App\Models\Dict\MeaningText;

use App\Models\Corpus\Place;
use App\Models\Dict\Relation;

trait MeaningModify
{
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
            $meaning_obj = self::storeLemmaMeaning($lemma_id, (int)$meaning['meaning_n'], $meaning['meaning_text']);
            if (!$meaning_obj) {
                continue;
            }
            $meaning_obj->updateMeaningRelations($meaning['relation'] ?? []);
            $meaning_obj->updateMeaningTranslations($meaning['translation'] ?? []);
            $meaning_obj->updateConcepts($meaning['concepts'] ?? []);            
            $meaning_obj->updatePlaces($meaning['places'] ?? []);
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

            $meaning_obj->updateConcepts($meaning['concepts'] ?? []);
            
            $meaning_obj->updateMeaningTranslations($meaning['translation'] ?? []);

            $meaning_obj->updatePlaces($meaning['places'] ?? []);
            
            // is meaning has any meaning texts or any relations
            if ($meaning_obj->meaningTexts()->count() || $meaning_obj->meaningRelations()->count() || $meaning_obj->translations()->count()
                     || $meaning_obj->concepts()->count() || $meaning_obj->places()->count() || $meaning_obj->examples()->count()) { 
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
                $mean2_obj = self::find($rel_mean_id);
                $this->addMeaningRelation($rel_mean_id, $relation_id);
            }
        }
    }
    
    public function addMeaningRelation($mean2_id, $relation_id, $reverse_relation_id=null) {
        $this->meaningRelations()
             ->attach($relation_id,['meaning2_id'=>$mean2_id]);

        // reverse relation
        if (!$reverse_relation_id) {
            $relation_obj = Relation::find($relation_id);
            $reverse_relation_id = $relation_obj->reverse_relation_id;
        }
        self::find($mean2_id)->meaningRelations()
                  ->attach($reverse_relation_id, ['meaning2_id'=>$this->id]);
//            }        
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
        $this->addConceptTranslations();
    }

    /**
     * Check all meanings linked with concept
     * and link by translation with unlinked meanings
     */
    public function addConceptTranslations() {
        $this->concepts;
        if ($this->concepts()->count()) {
            $lang_id = $this->lemma->lang_id;
            foreach ($this->concepts as $concept) {
                $other_meanings = $concept->meanings()->where('id', '<>', $this->id)
                        ->whereNotIn('lemma_id', function ($q) use ($lang_id) {
                            $q->select('id')->from('lemmas')->whereLangId($lang_id);
                        })->get();
                foreach ($other_meanings as $meaning) {
                    if (!$this->translations()->where('meaning2_id', $meaning->id)->count()) {
                        $this->translations()->attach($meaning->lemma->lang_id,['meaning2_id'=>$meaning->id]);                                            
                    }
                    if (!$meaning->translations()->where('meaning2_id', $this->id)->count()) {
                        $meaning->translations()->attach($lang_id,['meaning2_id'=>$this->id]); 
                    }
                }
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
        $this->concepts()->sync($concepts);
/*        $this->concepts()->detach();
        if (!is_array($concepts) || !sizeof($concepts)) {
            return;
        }
//dd($concepts);        
        $this->concepts()->attach($concepts);*/
/*        
        foreach ($this->concepts as $concept) {
            $other_meanings = $concept->meanings()->where('meaning_id', '<>', $this->id)->get();
            foreach ($other_meanings as $meaning) {
                if ($this->meaningRelations()->wherePivot('relation_id',Relation::SynonymId)
                         ->wherePivot('meaning2_id', $meaning->id)->count() == 0) {
                    $this->addMeaningRelation($meaning, Relation::SynonymId, Relation::SynonymId);
                }
            }
        } */
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
        $this->dialects()->detach();
        if (!is_array($places) || !sizeof($places)) {
            return;
        }
//dd($concepts);        
        $this->places()->attach($places);
        $dialects = [];
        foreach ($places as $place_id) {
            $place=Place::find($place_id);
            foreach ($place->dialects as $dialect) {
                $dialects[]=$dialect->id;
            }
        }
        $dialects = array_unique($dialects);
        $this->dialects()->attach($dialects);
    }
    
    public function addTextLink($text_id, $s_id, $word_id, $w_id, $old_relevance) {
        $relevance = $this->checkRelevance($text_id, $w_id, $old_relevance);
        $this->texts()->attach($text_id,
                                ['s_id'=>$s_id, 
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
            $this->addTextLink($word->text_id, $word->s_id, $word->word_id, $word->w_id, 1);        
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
            $this->addTextLink($word->text_id, $word->s_id, $word->word_id, $word->w_id, 
                    $this->checkRelevance($word->text_id, $word->w_id, $old_relevances[$word->text_id][$word->w_id] ?? 1));
        }
    }
    
    public function reloadExamples() {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '1024M');

        $words = $this->lemma->getWordsForMeanings();
        if ($words) {
            if (!$this->texts()->count()) {
                $this->addTextLinks($words);
            } else {
                $this->updateTextLinks($words);
            }
        } else {
            $this->texts()->detach();
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
        foreach ($words as $word) {
            $link = $this->texts()->wherePivot('text_id', $word->text_id)->wherePivot('w_id', $word->w_id);
            if (!$link) {
                $this->addTextLink($word->text_id, $word->s_id, 
                        $word->word_id, $word->w_id, 
                        self::getDefaultRelevance($word->text_id, $word->w_id));
            }
        }
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