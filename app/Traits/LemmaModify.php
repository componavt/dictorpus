<?php namespace App\Traits;

use DB;

use App\Library\Grammatic;

use App\Models\Dict\Dialect;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\LemmaBase;
use App\Models\Dict\LemmaFeature;
use App\Models\Dict\LemmaWordform;
use App\Models\Dict\Phonetic;
use App\Models\Dict\ReverseLemma;
use App\Models\Dict\Wordform;

trait LemmaModify
{
    public function updateLemma($data) {
        list($new_lemma, $wordforms_list, $stem, $affix, $gramset_wordforms, $stems) 
                = Grammatic::parseLemmaField($data);
        $lang_id = (int)$data['lang_id'];            
        $this->lang_id = $lang_id;
        $this->lemma = $new_lemma;
        $this->lemma_for_search = Grammatic::changeLetters($new_lemma, $lang_id);
        $this->pos_id = (int)$data['pos_id'] ? (int)$data['pos_id'] : NULL;
        $this->updated_at = date('Y-m-d H:i:s');
        $this->save();
        
        $this->storeAddition($wordforms_list, $stem, $affix, $gramset_wordforms, $data, $data['wordform_dialect_id'], $stems);           
        
        $this->storePhrase(isset($data['phrase']) ? $data['phrase'] : null);
    }

    public function storeAddition($wordforms, $stem, $affix, $gramset_wordforms, 
                                  $features, $dialect_id, $stems) {
//dd($features);        
        LemmaFeature::store($this->id, $features);
        
        if (!$dialect_id) {
            $dialects = $this->dialectIds();
            $dialect_id = $dialects[0] ?? null;
        }
        $stems=$this->updateBases($stems, $dialect_id); 
        if ($this->features && !$this->features->without_gram && !$gramset_wordforms && $stems) {
            $gramset_wordforms = Grammatic::wordformsByStems($this->lang_id, $this->pos_id, null, 
                    Grammatic::nameNumFromNumberField($this->features->number ?? null), 
                    $stems, $this->features->reflexive ?? null);
        }
        $this->storeReverseLemma($stem, $affix);

        $this->storeVariants($features['variants'] ?? []);
        $this->updatePhonetics($features['phonetics'] ?? []);
        $this->storePhonetics($features['new_phonetics'] ?? []);
        
        $this->storeWordformsFromSet($gramset_wordforms, $dialect_id); 
        $this->createDictionaryWordforms($wordforms, 
                isset($features['number']) ? $features['number'] : NULL, 
                $dialect_id);
        $this->updateTextWordformLinks();
    }
    
    public function storePhrase($lemmas) {
        $this->phraseLemmas()->detach();
        if ($lemmas) {
            $this->phraseLemmas()->attach($lemmas);
        }
    }
    
    public function updateBases($stems=null, $dialect_id=null) {     
        if (!$dialect_id) {
            $dialect_id = Lang::mainDialectByID($this->lang_id);
        }
        if (!$dialect_id) {
            return;
        }        
        if ($stems) {
            LemmaBase::updateStemsFromSet($this->id, $stems, $dialect_id);
        } else {
            return LemmaBase::updateStemsFromDB($this, $dialect_id);
        }        
    }

    public function storeReverseLemma($stem=NULL, $affix=NULL) {
        $reverse_lemma = ReverseLemma::find($this->id);
//dd($stem, $affix);
//dd($reverse_lemma);
        if ($reverse_lemma) {
            $reverse = $this->reverse();
            if (!$stem && !$affix) {
                list($stem, $affix) = $this->extractStem();
            }

            $reverse_lemma->reverse_lemma = $reverse;
            $reverse_lemma->lang_id = $this->lang_id;
            $reverse_lemma->stem = $stem;
            $reverse_lemma->affix = $affix;
            
            $reverse_lemma -> save();
        } else {
            $this->createReverseLemma($stem, $affix);
        }        
    }
   
    public function storeVariants($lemmas) {
        $this->variants()->detach();
        if (!sizeof($lemmas)) {
            return;
        }
        $this->variants()->attach($lemmas);
        foreach ($this->variants as $lemma) {
            $back_link = $lemma->variants()->where('lemma2_id',$this->id)->first();
            if (!$back_link) {
                $lemma->variants()->attach($this->id);
            }
        }
    }
    
    public function updatePhonetics($phonetics) {
        foreach ($phonetics as $phonetic_id=>$phonetic_info) {
            $phonetic = $this->phonetics()->whereId($phonetic_id)->first();
            if (!$phonetic_info['phonetic']) {
                $phonetic->remove();
                continue;
            }
            $phonetic->phonetic = $phonetic_info['phonetic'];
            $phonetic->places()->sync($phonetic_info['places']); 
            $dialects = [];
            foreach ($phonetic->places as $place) {
                $dialects = array_merge($dialects, $place->dialects()->pluck('id')->toArray());
            }
            $phonetic->dialects()->sync(array_unique($dialects));
            $phonetic->save();
        }
    }
    
    public function storePhonetics($phonetics) {
        foreach ($phonetics as $phonetic_id=>$phonetic_info) {
            if (!$phonetic_info['phonetic']) {
                continue;
            }
            $phonetic = Phonetic::create(['lemma_id'=>$this->id,'phonetic'=>$phonetic_info['phonetic']]);
            $phonetic->places()->sync($phonetic_info['places']); 
            $dialects = [];
            foreach ($phonetic->places as $place) {
                $dialects = array_merge($dialects, $place->dialects()->pluck('id')->toArray());
            }
            $phonetic->dialects()->sync(array_unique($dialects));
            $phonetic->save();
        }
    }
    
    public function storeWordformsFromSet($wordforms, $dialect_id=null) {
        if (!$wordforms || !sizeof($wordforms)) {
            return;
        }
        
        if (!$dialect_id) {
            $dialect_id = Lang::mainDialectByID($this->lang_id);
        }
//dd($dialect_id, $wordforms);        
        foreach ($wordforms as $gramset_id => $wordform) {
            $wordform_exists = $this->wordforms()
                             ->wherePivot('gramset_id',$gramset_id)
                             ->wherePivot('dialect_id',$dialect_id)
                             ->get()->pluck('wordform')->toArray();
            foreach ((array)$wordform as $w) {
                if (!in_array($w, $wordform_exists)) {
                    $this->addWordforms($wordform, $gramset_id, $dialect_id);
                }
            }
        }
    }
    
    public function createDictionaryWordforms($wordforms, $number=NULL, $dialect_id=NULL) {        
//dd($request->wordforms);        
        if (!isset($wordforms)) { return; }
        
        $wordform_list=preg_split("/\s*[,;\s]\s*/",$wordforms);
        if (!$wordform_list || sizeof($wordform_list)<2) { return; }
        
        $wordform_list[3] = $this->lemma;
        
        $gramsets = Gramset::dictionaryGramsets($this->pos_id, $number, $this->lang_id);
        if ($gramsets == NULL) { return; }
        
        if ($dialect_id) {
            $dialect = Dialect::find($dialect_id);
        }
        if (!$dialect) {
            $dialect = $this->firstDialect();
        }
        if (!$dialect) { return; }
        
        foreach ($gramsets as $key=>$gramset_id) {
            if (isset($wordform_list[$key])) {
                $this -> addWordforms($wordform_list[$key], $gramset_id, $dialect->id);
            }
        }
    }
    
    /**
     * Update text-wordform links or creating new links for all word forms
     *
     * @return NULL
     */
    public function updateTextWordformLinks()
    {     
        $lang_id = $this->lang_id;
        foreach ($this->wordforms as $wordform_obj) {
            $words = $wordform_obj->getWordsForLinks($this->lang_id);
//dd($words);            
            if (!$wordform_obj->texts()->whereLangId($lang_id)
                    ->wherePivot('gramset_id',$wordform_obj->pivot->gramset_id)->count()) {
                $wordform_obj->addTextLinks($words, $this->lang_id);
            } else {
                $wordform_obj->updateTextLinks($words, $this->lang_id);
            }
        }        
    }
    
    public function createReverseLemma($stem=NULL, $affix=NULL) {
        $reverse_lemma = $this->reverse();
//print "<p>".$reverse_lemma.', '.$this->id; 
        if (!$stem && !$affix) {
            list($stem, $affix) = $this->extractStem();
        }
        
        $this->reverseLemma = ReverseLemma::create([
            'id' => $this->id,
            'reverse_lemma' => $reverse_lemma,
            'lang_id' => $this->lang_id,
            'stem' => $stem,
            'affix' => $affix]);         
    }
    
    public function addWordforms($words, $gramset_id, $dialect_id) {
        $trim_words = trim($words);
        if (!$trim_words) { return;}

        foreach (preg_split("/[\/,]/",$trim_words) as $word) {
            $this->addWordform($word, $gramset_id, $dialect_id);
        }
    }
    
    public function addWordform($word, $gramset_id, $dialect_id) {       
        $trim_word = Grammatic::toRightForm($word);
        if (!$trim_word) { return;}
        
        $wordform_obj = Wordform::findOrCreate($trim_word);
//TODO: лишнее поле, удалить        
        $wordform_obj->wordform_for_search = Grammatic::toSearchForm($trim_word);
        $wordform_obj->save();

        $affix = $gramset_id ? $this->affixForWordform($wordform_obj->wordform) : NULL;

        $this->addWordformGramsetDialect($wordform_obj, $gramset_id, $dialect_id, $affix);
    }
    
    public function addWordformGramsetDialect($wordform_obj, $gramset_id, $dialect_id, $affix) {
        DB::connection('mysql')->table('lemma_wordform')->whereLemmaId($this->id)
                ->whereWordformId($wordform_obj->id)->whereNull('dialect_id')
                ->whereNull('gramset_id')->delete();
        
        if ($this->isExistWordforms($gramset_id, $dialect_id, $wordform_obj->id)) {
            return;
        }
        $this->wordforms()->attach($wordform_obj->id, 
                            ['gramset_id'=>$gramset_id, 'dialect_id'=>$dialect_id, 'affix'=>$affix, 'lang_id'=>$this->lang_id,
                             'wordform_for_search'=>Grammatic::changeLetters($wordform_obj->wordform, $this->lang_id)]);    
    }

    /**
     * Add wordform found in the text with gramset_id and set of dialects
     * 
     * @param String $word 
     * @param Int $gramset_id
     * @param Array $dialects
     * @param Int $text_id
     * @param Int $w_id
     */
    public function addWordformFromText($word, $gramset_id, $dialects, $text_id, $w_id) {
        if (!$word || !$this->pos || !$this->pos->isChangeable()) {
            return;
        }
        $wordform = Wordform::findOrCreate($word);
        $wordform->updateTextWordformLinks($text_id, $w_id, $gramset_id);
        
        $affix = $gramset_id ? $this->affixForWordform($wordform->wordform) : NULL;
        
        if (!sizeof($dialects)) {
            $dialects[0] = NULL;
        }
        foreach ($dialects as $dialect_id) {
            $this->addWordformGramsetDialect($wordform, $gramset_id, $dialect_id,  $affix);
        }
        $wordform->updateMeaningTextLinks($this);
    }
    
    /**
     * Update meaning-text links or creating new links for all meanings
     *
     * @return NULL
     */
    public function updateMeaningTextLinks($words=null)
    {     
        if (!$words) {
            $words = $this->getWordsForMeanings();
        }
        if (!$words) {
            return;
        }
        foreach ($this->meanings as $meaning_obj) {
            // this meaning has not links with texts yet, add them
            if (!$meaning_obj->texts()->count()) {
                $meaning_obj->addTextLinks($words);
            } else {
                $meaning_obj->updateTextLinks($words);
            }
        }
    }
    
    public function remove() {
        $this-> audios()->detach();
        $this-> wordforms()->detach();
        $this-> labels()->detach();
        $this-> phraseLemmas()->detach();
        
        // связи с другими леммами - фонетическими вариантами
        DB::statement("DELETE from lemma_variants WHERE lemma1_id=".$this->id." or lemma2_id=".$this->id);
/*        foreach ($this->variants as $lemma) {
            $lemma->variants()->detach($this->id);
        }
        $this->variants()->detach();*/
        
        if ($this->reverseLemma) {
            $this->reverseLemma->delete();
        }        
        if ($this->features) {
            $this->features->delete();
        }
        
        $meanings = $this->meanings;

        foreach ($meanings as $meaning) {
            $meaning->remove();
        }

        $bases = $this->bases;

        foreach ($bases as $base) {
            $base -> delete();
        }
        
        // произношения
        foreach ($this->phonetics as $phonetic) {
            $phonetic->dialects()->detach();
            $phonetic->places()->detach();
            $phonetic->delete();
        }        

//        $this-> dialects()->detach();
//        $this-> places()->detach();
        DB::statement("DELETE FROM dialect_lemma WHERE lemma_id=".$this->id);
        DB::statement("DELETE FROM lemma_place WHERE lemma_id=".$this->id);
        $this->delete();
    }
    
    public static function storeLemma($data) {  
//        $data['lemma'] = mb_ereg_replace("\/", "|", $data['lemma']);
        list($data['lemma'], $wordforms, $stem, $affix, $gramset_wordforms, $stems) 
                = Grammatic::parseLemmaField($data);
//dd($gramset_wordforms);        
        $lemma = self::store($data['lemma'], $data['pos_id'], $data['lang_id']);

        $lemma->storeAddition($wordforms, $stem, $affix, $gramset_wordforms, $data, $data['wordform_dialect_id'], $stems);      
        return $lemma;
    }
    
    public static function store($lemma, $pos_id, $lang_id) {
//dd($lemma);        
        if (!$pos_id) {
            $pos_id = NULL;
        }
        $lemma = Lemma::create(['lemma'=>$lemma,'lang_id'=>$lang_id,'pos_id'=>$pos_id]);
//        $lemma->lemma_for_search = Grammatic::toSearchForm($lemma->lemma);
        $lemma->lemma_for_search = Grammatic::changeLetters($lemma->lemma, $lemma->lang_id);
        $lemma->save();
        return $lemma;
    }
    
    public function modify() { 
//        $this->lemma_for_search = Grammatic::toSearchForm($this->lemma);
        $this->lemma_for_search = Grammatic::changeLetters($this->lemma, $this->lang_id);
        $this->updated_at = date('Y-m-d H:i:s');
        $this->save();        
    }
    
    public function updateTextLinks()
    {     
        // With Meanings
        $words = $this->getWordsForMeanings();
        if (!$words) {
            return;
        }
        $this->updateMeaningTextLinks($words);
        
        // With Wordforms;
        $this->updateTextWordformLinks();
    }

    /**
     * Stores relations with array of wordform (with gramsets) and create Wordform if is not exists
     * 
     * @param array $wordforms array of wordforms with pairs "id of gramset - wordform",
     *                         f.e. [<gramset_id1> => [<dialect_id1> => <wordform1>, ...], ..] ]
     * @param array $dialects array of dialects with pairs gramset - dialect
     *                         f.e. [<gramset_id1> => [<dialect_id1>, ...], ..] ]
     *                        is neccessary for changing dialect of wordform
     * 
     * @return NULL
     */
    public function storeWordformGramsets($wordforms, $dialects)
    {
        if(!$wordforms || !is_array($wordforms)) {
            return;
        }
        foreach($wordforms as $gramset_id=>$wordform_dialect) {
            $gramset_id = (!(int)$gramset_id) ? NULL : (int)$gramset_id; 
            foreach ($wordform_dialect as $old_dialect_id => $wordform_texts) {
                $old_dialect_id = (!(int)$old_dialect_id) ? NULL : (int)$old_dialect_id; 
                $this->deleteWordforms($gramset_id, $old_dialect_id);
                
                if (isset($dialects[$gramset_id]) && $dialects[$gramset_id]=='all' ) {
                    foreach (Dialect::getByLang($this->lang_id) as $dialect) {
                        $this->addWordforms($wordform_texts, $gramset_id, $dialect->id);
                    }
                } else {
                    $dialect_id = (isset($dialects[$gramset_id]) && (int)$dialects[$gramset_id])
                            ? (int)$dialects[$gramset_id] : NULL;
                    $this->addWordforms($wordform_texts, $gramset_id, $dialect_id);
                }
            }
        }
//exit(0); 
    }

    /**
     * Stores relations with array of wordform (without gramsets изначально) and create Wordform if is not exists
     * 
     * @param array $wordforms array of wordforms with pairs "id of gramset - wordform"
     * @param Int $dialect_id 
     * 
     * @return NULL
     */
    public function storeWordformsEmpty($wordforms, $dialect_id='')
    {
//exit(0);        
        if(!$wordforms || !is_array($wordforms)) {
            return;
        }
        $this->deleteWordformsEmptyGramsets();
        
        foreach($wordforms as $wordform_info) {
            $wordform_info['gramset'] = ((int)$wordform_info['gramset']) ? (int)$wordform_info['gramset'] : NULL; 
            if (!(int)$wordform_info['dialect']) {
                $wordform_info['dialect'] = ((int)$dialect_id) ? (int)$dialect_id : NULL; 
            }
            $this->addWordforms($wordform_info['wordform'], $wordform_info['gramset'], $wordform_info['dialect']);
        }
    }
    
    public function deleteWordforms($gramset_id, $dialect_id) {
        $this-> wordforms()
              ->wherePivot('gramset_id',$gramset_id)
              ->wherePivot('dialect_id',$dialect_id)
              ->detach();
    }
    
    public function deleteWordformsEmptyGramsets() {
        $this-> wordforms()
              ->wherePivot('gramset_id',NULL)
              ->detach();
    }

    public function updateWordformAffixes($for_all=false) {
        list($stem, $affix) = $this->getStemAffix();
        if (!$stem) { return false; }

        $wordforms = $this->wordforms()->where('wordform','NOT LIKE','% %');
        if (!$for_all) {
             $wordforms = $wordforms->whereNull('affix');
        }
         $wordforms = $wordforms->whereNotNull('gramset_id')->get();
//dd($wordforms);        
        foreach ($wordforms as $wordform) {
            $w_affix = $this->affixForWordform($wordform->wordform);
//print "<p>".$lemma->lemma. " = ". $wordform->wordform. " = $w_affix</p>";  
            $wordform->updateAffix($this->id, $wordform->pivot->gramset_id, $w_affix);
        }  
        return true;
    }

    public function generateWordforms($dialect_id, $update_bases=false, $without_remove=false) {
        $name_num = $this->getNameNum(); 
        $is_reflexive = ($this->features && $this->features->reflexive) ? 1 : null;

        $stems = $this->getBases($dialect_id);
//dd($stems);        
//dd($name_num);     
        if ($update_bases) {
            $this->updateBases($stems, $dialect_id);
        }
        
        if (!$without_remove) {
            $this->wordforms()->wherePivot('dialect_id',$dialect_id)->detach();
        }
        
        return Grammatic::wordformsByStems($this->lang_id, $this->pos_id, $dialect_id, $name_num, $stems, $is_reflexive);
    }

    public function generateWordform($gramset_id, $dialect_id, $update_bases=false) {
        $name_num = $this->getNameNum(); 
        $is_reflexive = ($this->features && $this->features->reflexive) ? 1 : null;
        $stems = $this->getBases($dialect_id);
//dd($stems);        
//dd($name_num);     
        if ($update_bases) {
            $this->updateBases($stems, $dialect_id);
        }
        
        return Grammatic::wordformByStems($this->lang_id, $this->pos_id, $dialect_id, $gramset_id, $stems, $name_num, $is_reflexive);
    }

    public function reloadWordforms($dialect_id, $with_updateText=false, $without_remove=false) {
        $gramset_wordforms = $this->generateWordforms($dialect_id, true, $without_remove); 
//dd($dialect_id, $gramset_wordforms);        
        if ($gramset_wordforms) {
            $this->storeWordformsFromSet($gramset_wordforms, $dialect_id); 
            if ($with_updateText) {
                $this->updateTextWordformLinks();//updateTextLinks();
            }
        }
//exit(0);        
    }
    
    /**
     * 
     * @param array $phonetic_dialects [<phonetic1>=>[<dialect1_id>=>[<place1_id>, ...], ...], ...]
     */
    public function updatePhoneticDialects($phonetic_dialects) {
/*if ($this->lemma=='pal’l’aine' && $this->lang_id==6) {
    dd($phonetic_dialects);
} */       
        if (sizeof($phonetic_dialects)==1 && $this->lemma==Arrays::array_key_first($phonetic_dialects) && !$this->phonetics()->count()) {
            return;
        }
        foreach ($phonetic_dialects as $phonetic => $dialects) {
            $phonetic_obj = $this->phonetics()->where('phonetic', $phonetic)->first();
            if (!$phonetic_obj) {            
                $phonetic_obj = Phonetic::firstOrCreate(['lemma_id' => $this->id, 'phonetic' => $phonetic]);
            }
            $phonetic_obj -> updateDialects($dialects);
        }
    }
    
    public function updateWordformTotal(){
        $this->wordform_total = LemmaWordform::whereLemmaId($this->id)->count();
        $this->save();                
    }

    public function createInitialWordforms() {
        $stems= $this->updateBases();
//dd($stems);            
        $dialects = $this->dialectIds();
//dd($lemma, $dialects);                
        $gramset_wordforms = Grammatic::wordformsByStems($this->lang_id, $this->pos_id, $dialects[0] ?? null, 
                Grammatic::nameNumFromNumberField($this->features->number ?? null), 
                $stems, $this->features->reflexive ?? null);
//dd($gramset_wordforms);         
        if ($gramset_wordforms) {
            $this->storeWordformsFromSet($gramset_wordforms, $dialects[0] ?? null); 
            $this->updateTextWordformLinks();
            $this->updated_at = date('Y-m-d H:i:s');
            $this->save();                    
        }
    }
    
    
}