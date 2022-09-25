<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use DB;
use LaravelLocalization;
use \Venturecraft\Revisionable\Revision;
use Arrays;

use App\Library\Grammatic;
use App\Library\Grammatic\KarGram;
use App\Library\Str;

use App\Models\User;
use App\Models\Corpus\Text;
use App\Models\Corpus\Word;

use App\Models\Dict\Audio;
//use App\Models\Dict\Label;
use App\Models\Dict\PartOfSpeech;


class Lemma extends Model
{
    protected $fillable = ['lemma','lang_id','pos_id', 'lemma_for_search', 'wordform_total'];
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.
    protected $revisionFormattedFields = array(
//        'title'  => 'string:<strong>%s</strong>',
//        'public' => 'boolean:No|Yes',
//        'modified_at' => 'datetime:d/m/Y g:i A',
//        'deleted_at' => 'isEmpty:Active|Deleted'
        'updated_at' => 'datetime:m/d/Y g:i A'
    );
    protected $revisionFormattedFieldNames = array(
//        'title' => 'Title',
//        'small_name' => 'Nickname',
//        'deleted_at' => 'Deleted At'
    );
    
    /**
    * Атрибуты, которые должны быть преобразованы к датам.
    *
    * @var array
    */
    protected $dates = [
        'created_at',
        'updated_at',
    ];    
    public static function boot()
    {
        parent::boot();
    }
    
    // Belongs To Methods
    use \App\Traits\Methods\toSqlFull;
    use \App\Traits\Methods\search\lemmasByDialects;
    
    // Belongs To Relations
    use \App\Traits\Relations\BelongsTo\Lang;
    use \App\Traits\Relations\BelongsTo\ReverseLemma;
    use \App\Traits\Relations\BelongsTo\POS;
    
    // Belongs To Many Relations
//    use \App\Traits\Relations\BelongsToMany\Dialects;
    use \App\Traits\Relations\BelongsToMany\Labels;
    use \App\Traits\Relations\BelongsToMany\LemmaVariants;
//    use \App\Traits\Relations\BelongsToMany\Places;
    use \App\Traits\Relations\BelongsToMany\WordformDialects;
    use \App\Traits\Relations\BelongsToMany\Wordforms;
   
    // Has Many Relations
    use \App\Traits\Relations\HasMany\Meanings;
    use \App\Traits\Relations\HasMany\Bases;
    use \App\Traits\Relations\HasMany\Phonetics;
    
    // Scopes
//    use \App\Models\Scopes\ID;
//    use \App\Models\Scopes\LangID;
//    use \App\Models\Scopes\Wordform;
    
    /**
     * Gets gramsets, that has any wordforms of this lemma
     * 
     * @return Builder
     */
    public function gramsets()
    {
        return $this->belongsToMany(Gramset::class, 'lemma_wordform')
                    ->orderBy('sequence_number');
    } 
    
    // Lemma __has one__ LemmaFeature
    public function features()
    {
        return $this->hasOne(LemmaFeature::class,'id','id');
    }    
    
    public function phraseLemmas(){
        $builder = $this->belongsToMany(Lemma::class,'lemma_phrase','phrase_id');
        return $builder;
    }

    public function phrases(){
        $builder = $this->belongsToMany(Lemma::class,'lemma_phrase','lemma_id','phrase_id');
        return $builder;
    }

    public function audios(){
        return $this->belongsToMany(Audio::class);
    }    
    
    public function dialectIds(){
        $ids=[];
        $lemma_id= $this->id;
        $dialects = DB::table('dialect_meaning')
                      ->whereIn('meaning_id', function ($query) use ($lemma_id) {
                          $query->select('id')->from('meanings')
                                ->whereLemmaId($lemma_id);
                      })->get();
        foreach ($dialects as $dialect) {
            $ids[]=$dialect->dialect_id;
        }
        return $ids;
    }

    public function labelStatus($label_id) {
        return $this->labels()->wherePivot('label_id', $label_id)->first()->pivot->status;
    }

    public function meaningsWithBestExamples() {
        return $this->meanings()->whereIn('id', function ($q) {
                    $q->select('meaning_id')->from('meaning_text')
                      ->whereRelevance(10);
                })->get();
    }

    public static function getLemmaById($id) {
        $obj = self::find($id);
        if (!$obj) {
            return FALSE;
        }
        return $obj->lemma;
    }
    
    public static function getPOSidById($id) {
        $obj = self::find($id);
        if (!$obj) {
            return FALSE;
        }
        return $obj->pos_id;
    }
    
    /**
     * @return Array of bases
     */
    public function getBases($dialect_id=null) {
        $bases=[];
        for ($i=0; $i<9; $i++) {
            $bases[$i] = $this->getBase($i, $dialect_id, $bases);
        }
        if ($this->lang_id != 1 && $this->reverseLemma) {
            $bases[10] = $this->harmony();
        }
        if (!$bases[2]) {
            $bases[2] = $this->getBase(2, $dialect_id, $bases);            
        }
        // for olo base 4 is formed after base5
        if (!$bases[4]) {
            $bases[4] = $this->getBase(4, $dialect_id, $bases);            
        }
//dd($bases);        
        return $bases;
    }
    
    public function harmony(){
        if ($this->lang_id == 1) {
            return null;
        }
        return KarGram::isBackVowels($this->reverseLemma->stem. $this->reverseLemma->affix);
    }
    
    /**
     * @return String
     */
    public function getBase($base_n, $dialect_id=null, $bases=null) {        
        $base = $this->getBaseFromDB($base_n, $dialect_id);

        if ($base) {
            return $base;
        }
        if ($dialect_id) { 
            $base = Grammatic::getStemFromStems($bases, $base_n, $this->lang_id,  $this->pos_id, $dialect_id, $this->lemma);
//dd($base);
            if (!$base) {
                $is_reflexive = $this->features && $this->features->reflexive ? true : false;
                $base = Grammatic::getStemFromWordform($this, $base_n, $this->lang_id,  $this->pos_id, $dialect_id, $is_reflexive);
//dd($base_n,$base);                
            }
        } 
        if (!$base) {
            $base = $this->getBaseFromDB($base_n);
        }

        return $base;
    }
    
    public function getBaseFromDB($base_n, $dialect_id=null) {
        $base_obj = $this->bases()->where('base_n',$base_n);
        if ($dialect_id) {
            $base_obj = $base_obj->where('dialect_id',$dialect_id);
        }
        $base_obj = $base_obj->first();

        if ($base_obj) {
            return $base_obj->base;
        }
        return null;
    }
    
/*     // Lemma has many MeaningTexts through Meanings
    public function meaningTexts()
    {
        return $this->hasManyThrough(MeaningText::class, Meaning::class, 'lemma_id', 'meaning_id');
//        return $this->hasManyThrough('App\Models\Dict\MeaningText', 'App\Models\Dict\Meaning');
    }
   public function meaning_texts($ids = [])
    {
        return MeaningText::whereHas('meanings', function($q) use($ids) { 
                                $q->whereIn('id', $ids);                             
                           })->get(); 
        
    }    
/*    public function meaning_texts()
    {
        $lemma_id = $this->id;
        $builder = MeaningText::whereIn('meaning_id', function($query) use($lemma_id) { 
                                $query->select('id')->from('meanings')
                                      ->where('lemma_id',$lemma_id);
                           });
//dd($builder->toSQL());                           
        return $builder->get(); 
        
    } */   

    public function countWordformsByDialect($dialect_id){
        return $this->wordforms()
                    ->wherePivot('dialect_id',$dialect_id)
                    ->count();
    }
        
    public function wordformsByDialect($dialect_id){
        return $this->wordforms()->orderBy('wordform')
                    ->wherePivot('dialect_id',$dialect_id)
                    ->get();
    }
        
    public function wordformsByGramsetDialect($gramset_id, $dialect_id){
        return $this->wordforms()->orderBy('wordform')
                    ->wherePivot('gramset_id',$gramset_id)
                    ->wherePivot('dialect_id',$dialect_id)
                    ->get();
    }
    
    /**
     * Wordforms array for one dialect.
     * Output in a table:
     * 
     * |           | единственное | множественное |
     * | номинатив |              |               |
     * 
     * or
     * 
     * |               | положительные | отрицательные |
     * | Индикатив, презенс
     * | 1 л., ед. ч.  |               |               |
     * 
     * @param int $dialect_id -- ID of dialect
     */
    public function wordformsForTable(int $dialect_id) {
        $lang_id = Dialect::getLangIDByID($dialect_id);
        $numbers = Gram::getByCategory(2);
        $wordforms = [];
        if ($this->pos->isName()) {
            $cases = Gram::getByCategory(1);
            foreach ($cases as $case) {
                foreach ($numbers as $number) {
                    $gramset = Gramset::gramsetsLangPOS($lang_id, $this->pos_id)
                                      ->whereGramIdCase($case->id)
                                      ->whereGramIdNumber($number->id)
                                      ->first();
                    if (!$gramset) { 
                        continue;                         
                    }
                    $wordforms[$case->name][$number->id]
                            =$this->wordform($gramset->id, $dialect_id);
                }
            }
        } elseif ($this->pos->isVerb()) {
  //          $gramsets = Gramset::getGroupedList($this->pos_id, $lang_id);
//dd($gramsets);            
            $negations = Gram::getByCategory(6);
            foreach (Gram::getByCategory(5) as $mood) {
                foreach (Gram::getByCategory(3) as $tense) {
                    foreach ($numbers as $number) {
                        foreach (Gram::getByCategory(4) as $person) {
                            foreach ($negations as $negation) {
                                $gramset = Gramset::gramsetsLangPOS($lang_id, $this->pos_id)
                                                  ->whereGramIdMood($mood->id)
                                                  ->whereGramIdTense($tense->id)
                                                  ->whereGramIdPerson($person->id)
                                                  ->whereGramIdNumber($number->id)
                                                  ->whereGramIdNegation($negation->id)
                                                  ->first();
                                if (!$gramset) { 
                                    continue;                         
                                }
                                $wordforms[$mood->name. ', ' .$tense->name][$person->short_name. ', '. $number->short_name][$negation->id]
                                        =$this->wordform($gramset->id, $dialect_id);
                            }
                        }
                    }                
                }
            }
            $infinite_category_id = 26;
            $gramsets = Gramset::gramsetsLangPOS($lang_id, $this->pos_id)
                    ->where('gramset_category_id', $infinite_category_id)->get();
            foreach ($gramsets as $gramset) {
                $wordforms[GramsetCategory::getNameById($infinite_category_id)][$gramset->inCategoryString()] 
                        = $this->wordform($gramset->id, $dialect_id);
            }
        }
//dd($wordforms);        
        return $wordforms;
    }

    /**
     *  Gets wordforms for given gramset, dialects and search string (wordform or pattern, e.g. '%čin')
     * 
     * @param int $gramset_id
     * @param int $dialect_id
     * 
     * @return String or NULL
     */
    public function wordformsForSearch($gramsets, $dialects, $wordforms){
        if (!$wordforms[1] && !$gramsets[1]) {
            return null;
        }
        $out = [];
        
        foreach ($wordforms as $i => $wordform_str) {
            if (!$wordform_str && !$gramsets[$i]) {
                continue;
            }
            $wordform_for_search = Grammatic::toSearchByPattern($wordform_str, $this->lang_id);
            
            $wordform_coll = $this->wordforms()->orderBy('wordform');
            if ($wordform_for_search) {
                $wordform_coll->where('lemma_wordform.wordform_for_search','rlike', $wordform_for_search);
            }
            if ($gramsets[$i]) {
                $wordform_coll->where('gramset_id', $gramsets[$i]);
            }
            if (isset($dialects[0]) && $dialects[0]) {
                $wordform_coll->whereIn('dialect_id', $dialects);
            }
//            $query = str_replace(array('?'), array('\'%s\''), $wordform_coll->toSql());
//            $query = vsprintf($query, $wordform_coll->getBindings());     
//dd($query);            
            $wordform_coll = $wordform_coll->get();
            foreach($wordform_coll as $wordform) {
                $out[]=$wordform->wordform;
            }
        }        
        return join(', ',array_unique($out));
    }
    
    /**
     *  Gets wordforms for given gramset and dialect
     * 
     * @param int $gramset_id
     * @param int $dialect_id
     * 
     * @return String or NULL
     */
    public function wordform($gramset_id, $dialect_id, $with_search_link=NULL){
        if (!$gramset_id) {
            $gramset_id=NULL;
        }
        if (!$dialect_id) {
            $dialect_id=NULL;
        }
        $wordform_coll = $this->wordformsByGramsetDialect($gramset_id, $dialect_id);
        
        if (!$wordform_coll) {
            return NULL;
        } else {
            $wordform_arr = [];
            foreach($wordform_coll as $wordform) {
                $w = $wordform->wordform;
                if ($with_search_link) { 
                    $w = $this->wordformWithLink($w, $wordform);
                }
                $wordform_arr[]=$w;
            }
            return join(', ',$wordform_arr);
        }        
    }

    public function wordformWithLink($w, $wordform){
        $lang_id = $this->lang_id;
        $word_count =  $wordform->texts()->whereLangId($lang_id)
                        ->wherePivot('gramset_id', $wordform->pivot->gramset_id)->wherePivot('relevance','>',0)->count();
        if (!$word_count) {
            return $w;
        }
        return '<a href="'.LaravelLocalization::localizeURL('/corpus/text/?search_lang='.$lang_id
               . '&search_word='.Grammatic::changeLetters($wordform->wordform, $lang_id))
               . '" title="'.$word_count.'">'.$w.'</a>';        
    }
    
    public function getWordformsByWord($word) {
//dd($word);        
//        return $this->wordforms()->where('wordform','like',$word)->orderByRaw('wordform collate utf8_unicode_ci')->get();
        return $this->wordforms()->whereRaw("wordform like '".addslashes($word)."' collate utf8_unicode_ci")->get();
    }
    
    public function getGramsetsByWord($word) {
        $wordforms = $this->getWordformsByWord($word);
//dd($wordforms);        
        $gramsets = [];
        foreach ($wordforms as $wordform) {
            if ($wordform->pivot->gramset_id) {
                $gramsets[] = $wordform->pivot->gramset_id;
            }
        }
        return array_unique($gramsets);
    }
    
    public function getStemAffix() {
        if (!$this->reverseLemma) {
            $this->createReverseLemma();
        }
        if (!$this->reverseLemma) {
            dd('It is not possible to create reverse lemma for '.$this->id);
        }
        return [$this->reverseLemma->stem, $this->reverseLemma->affix];        
    }

    public function getWordformsCONLL($word) {
        $gramsets = $this->getGramsetsByWord($word);
        $features = [];
        foreach ($gramsets as $gramset_id) {
            $gramset = Gramset::find($gramset_id);
            if (!$gramset) {
                continue;
            }
            $gramset_feats = $gramset->toCONLL();
            if ($gramset_feats) {
                $features[] = $gramset_feats;
            }
        }
        return $features;
    }
    
    public function featsToCONLL($word) {
        $lemma_feats = [];
        if ($this->features) {
            $lemma_feats = $this->features->toCONLL();
        }
//dd($lemma_feats); 
        $wordform_feats = $this->getWordformsCONLL($word);
        if (!sizeof($wordform_feats)) {
            if (!sizeof($lemma_feats)) {
                return "_";
            } else {
                return join('|',$lemma_feats);
            }
        }
//dd($wordform_feats);
        $features = [];
        foreach ($wordform_feats as $feats) {
            $f = array_unique(array_merge($lemma_feats, $feats));
            if (sizeof($f)) {
                $features[] = join('|',$f);
            }
        }
        return join('#',$features);
    }
    
    public function featsToString() {
        $features = [];
        if ($this->features) {
            foreach (array_values($this->features->filledFeatures()) as $field) {
               if (is_array($field)) {
                   $values = trans('dict.'.$field['title'].'s');
                   if (isset($values[$field['value']])) {
                       $value = $values[$field['value']];
                       if ($field['title'] == 'degree') {
                           $value .= ' '. trans('dict.'.$field['title']);
                       }
                       $features[] = $value;
                   }
               } else {
                   $features[] = trans('dict.'.$field);
               }
            }
        }  
        if (!sizeof($features)) {
            return null;
        }
        return '('.join(', ', $features).')';
    }

    public function phraseListWithLink(){
        if (!$this->phrases()) {
            return NULL;
        }
//dd($this->phrases);        
        $list=[];
        foreach ($this->phrases as $lemma) {
            $list[] = '<a href="'.LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id).'">'.$lemma->lemma.'</a>';
        }    
        return join('; ',$list);
    }
    
    public function phraseLemmasListWithLink(){
        if ($this->pos_id != PartOfSpeech::getPhraseID() || !$this->phraseLemmas()) {
            return NULL;
        }
//dd($this);        
        $list=[];
        foreach ($this->phraseLemmas as $lemma) {
            $list[] = '<a href="'.LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id).'">'.$lemma->lemma.'</a>';
        }    
        return join('; ',$list);
    }
    
    public function phraseMeaning(){
        $interpretation = [];
        foreach ($this->meanings as $meaning_obj) {
            $interpretation[] = $meaning_obj->getMultilangMeaningTextsStringLocale();
        }
        
        if (!sizeof($interpretation)) {
            return NULL;
        }
        return join('; ',$interpretation);
    }
    
    public function variantsWithLink(){
        $list=[];
        foreach ($this->variants as $lemma) {
            $dialects = [];
            foreach ($lemma->wordformDialects->unique() as $dialect) {
                $dialects[] = $dialect->name;
            } 
            foreach ($lemma->meanings as $meaning) {
                foreach($meaning->dialects as $dialect) {
                    if (!in_array($dialect->name, $dialects)) {
                        $dialects[] = $dialect->name;
                    }
                }
            }
            $l = '<a href="'.LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id).'">'.$lemma->lemma.'</a>';
            if (sizeof($dialects)) {
                $l .= ' ('.join(', ',$dialects).')';
            }
            $list[] =  $l;
        }    
        return join('; ',$list);
    }
    
    public function omonymsListWithLink(){
        $lemmas = self::where('lemma',$this->lemma)
                ->where('lang_id',$this->lang_id)
                ->where('id','<>',$this->id)->get();
        if (!sizeof($lemmas)) {
            return NULL;
        }
        $list=[];
        foreach ($lemmas as $lemma) {
            $list[] = '<a href="'.LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id).'">'.$lemma->lemma.'</a> ('.($lemma->pos? $lemma->pos->name: '').')';
        }    
        return join('; ',$list);
    }
    
    public function reverse(){
        $str = $this->lemma;
        return Str::reverse($str);
/*        $reverse = '';
        for ($i = mb_strlen($str); $i>=0; $i--) {
            $reverse .= mb_substr($str, $i, 1);
        }
        return $reverse;*/
    }   
    
    public function remove() {
        $this-> wordforms()->detach();
        $this-> labels()->detach();
        $this-> phraseLemmas()->detach();
        
        // связи с другими леммами - фонетическими вариантами
        foreach ($this->variants as $lemma) {
            $lemma->variants()->detach($this->id);
        }
        $this->variants()->detach();
        
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
    
    /**
     * Gets array of unique dialects, that has any wordforms of this lemma
     * 
     * @param $lemma_id
     * @return Array [NULL=>'', 2=>'средневепсский говор',...]
     */
    public function existDialects()
    {
        $dialect_ids = DB::table('lemma_wordform')
                      ->leftJoin('dialects','dialects.id','=','lemma_wordform.dialect_id')
                      ->where('lemma_id',$this->id)
                      ->orderBy('sequence_number')
                      ->groupBy('dialect_id')->get(['dialect_id']);
        $dialects = [];
        
        foreach ($dialect_ids as $dialect) {
            if (!$dialect->dialect_id) {
                $dialects[$dialect->dialect_id] = '';
            } else {
                $dialects[$dialect->dialect_id] = Dialect::find($dialect->dialect_id)->name;
            }
        }
 //       asort($dialects);
        return $dialects;
    } 
    
    /**
     * Gets array of unique grammatical sets, that has any wordforms of this lemma
     * 
     * @return Array [NULL=>'', 
     *                  26=>'индикатив, презенс, 1 л., ед. ч., положительная форма',...]
     */
    public function existGramsets()
    {
        $gramsets = [];
        if ($this->wordforms()->wherePivot('gramset_id',NULL)->count()) {
            $gramsets[NULL] ='';
        }
        $gramset_coll=$this->gramsets()
                           ->groupBy('id')
                           ->get();
        foreach ($gramset_coll as $gramset) {
            $gramsets[$gramset->id] = $gramset->gramsetString();
        }
        return $gramsets;
    } 
    
    /**
     * Gets array of unique grammatical sets, that has any wordforms of this lemma
     * 
     * @return Array [NULL=>'', 
     *                  26=>'индикатив, презенс, 1 л., ед. ч., положительная форма',...]
     */
    public function existGramsetsGrouped()
    {
        $gramsets = [];
        if ($this->wordforms()->wherePivot('gramset_id',NULL)->count()) {
            $gramsets[NULL][NULL] ='';
        }
        $gramset_coll=$this->gramsets()
//                           ->groupBy('id')
                           ->get();
       
        foreach (Gramset::getGroupedList($this->pos_id, $this->lang_id) as $category_name => $category_gramsets) {
            foreach ($category_gramsets as $gramset_id => $gramset_name) {
                if ($gramset_coll->contains($gramset_id)) {
                    $gramsets[$category_name][$gramset_id] = $gramset_name;
                }
            }
        }
        return $gramsets;
    } 
    
    /**
     * Gets a collection of wordforms with gramsets and sorted by sequence_number of gramsets
     * @return Collection of Wordform Objects
     * 
     */
    public function wordformsWithGramsets($dialect_id=NULL){
//        $dialects = Dialect::existDialects();
        $wordforms = $this->wordforms()->get();
dd($wordforms);
        foreach ($wordforms as $wordform) {
            $gramset = $wordform->lemmaDialectGramset($this->id,$dialect_id)->first(); // А МОЖЕТ МАССИВ?
            
            if ($gramset) {
                $wordform->gramset = $gramset;
//                $wordform->gramset_id = $gramset->id;
//                $wordform->gramsetString = $gramset->gramsetString();
                $wordform->sequence_number = $gramset->sequence_number;
            }
        }      
        $wordforms=$wordforms->sortBy('sequence_number');
//print "<pre>";        
//dd($wordforms);
//print "</pre>";        
        return $wordforms;
    }
    
    /**
     * Gets a collection of wordforms without gramsets and sorted by id
     * @return Wordform Object
     */
    public function wordformsWithoutGramsets(){
        $wordforms = $this->wordforms()->wherePivot('gramset_id',NULL)->get();
        return $wordforms;
    }
    
    /**
     * Gets a collection of wordforms for ALL gramsets and sorted by sequence_number of gramsets
     * 
     * @param $dialect_id
     * @return Collection of Wordform Objects
     */
    public function wordformsWithAllGramsets($dialect_id=NULL){
        $gramsets = Gramset::getGroupedList($this->pos_id, $this->lang_id);
        if ($dialect_id) {
            $dialects = [$dialect_id => Dialect::getNameByID($dialect_id)];
        } else {
            $dialects = [NULL=>''] + Dialect::getList($this->lang_id);
        }
        
        $wordforms = NULL;
//dd($gramsets);            
        foreach ($gramsets as $category_name => $category_gramsets) {
            foreach (array_keys($category_gramsets) as $gramset_id) {
        //                         ->withPivot('dialect_id',NULL)
                foreach (array_keys($dialects) as $dialect_id) {
                    if (!(int)$dialect_id) {
                        $dialect_id = NULL;
                    }
    //dd($dialect_id);        
                    $wordform = $this->wordforms()
                                     ->wherePivot('gramset_id',$gramset_id)
                                     ->wherePivot('dialect_id', $dialect_id)
                                     //->first();
                                     ->get();
                    $wordforms[$category_name][$gramset_id][$dialect_id] = $wordform;
                }
            }
        }
//dd($wordforms);        
        return $wordforms;
    }
    
    // Lemma has any Gramsets
    public function hasGramsets($wordform_id='', $dialect_id=''){
        $builder = $this->belongsToMany(Gramset::class, 'lemma_wordform');
        if ($wordform_id!=='') {
            $builder = $builder -> wherePivot('wordform_id',$wordform_id);
        }
        if ($dialect_id!=='') {
            $builder = $builder -> wherePivot('dialect_id',$dialect_id);
        }
        return $builder;
    }
    
    /**
     * Gets meaning_n for next meaning created
     * 
     * @return int
     */
    public function getNewMeaningN(){
        $builder = DB::table('meanings')->select(DB::raw('max(meaning_n) as max_meaning_n'))->where('lemma_id',$this->id)->first();
        if ($builder) {
            $max_meaning_n = $builder->max_meaning_n;
        } else {
            $max_meaning_n = 0;
        }
        return 1+ $max_meaning_n;
    }
    
    public function uniqueWordforms() {
        $wordforms = [];
        foreach ($this->wordforms as $wordform) {
            $wordforms[] = $wordform->getMainPart();
        }
        return array_unique($wordforms);
    }
    
    public function extractStem() {
        $affix = '';
        $stem = $this->lemma;
/*if ($this->id == 42093) {
   dd($stem, $affix);
} */       
//print "\n".join("\n ",$this->uniqueWordforms())."\n";

        foreach ($this->uniqueWordforms() as $wordform) {
            while (!preg_match("/^".$stem."/", $wordform)) {
                $affix = mb_substr($stem, -1, 1). $affix;
                $stem = mb_substr($stem, 0, mb_strlen($stem)-1);
//print "\n$wordform, $stem";                
            }
        }
        return [$stem, $affix];
    }
    
    /**
     * @return Boolean is true, if this lemma can have wordforms, 
     * i.e the part of speech in this language has grammatical sets
     */
    public function isChangeable(){
        $lang = $this->lang_id;
        $pos = $this->pos_id;
        return (boolean)DB::table('gramset_pos')->where('lang_id',$lang)
                ->where('pos_id',$pos)->count();
    }
    
    public static function countByLang($lang_id) {
        return self::where('lang_id', $lang_id)->count();
    }

    /**
     * Gets count of sentence-examples
     * 
     * @return int
     */
    public function countExamples(){
        $lemma_id = $this->id;
        $texts = DB::table('meaning_text')
                     ->whereIn('meaning_id',function($q) use ($lemma_id){
                         $q->select('id')->from('meanings')
                           ->where('lemma_id',$lemma_id);
                     })->groupBy('text_id','w_id')->get();
//dd($texts->toSql());                     
        return sizeof($texts);
    }
    
    /**
     * Gets count of the checked sentence-examples
     * (relevance >1)
     * 
     * @return int
     */
    public function countCheckedExamples(){
        $lemma_id = $this->id;
        $texts = DB::table('meaning_text')
                    ->where('relevance','>',1)
                    ->whereIn('meaning_id',function($q) use ($lemma_id){
                         $q->select('id')->from('meanings')
                           ->where('lemma_id',$lemma_id);
                    })->groupBy('text_id','w_id')->get();
//dd($texts->toSql());                     
        return sizeof($texts);
    }
    
    /**
     * Gets count of the checked sentence-examples
     * (relevance >1)
     * 
     * @return int
     */
    public function countUncheckedExamples(){
        $lemma_id = $this->id;
        $texts = DB::table('meaning_text')
                    ->where('relevance',1)
                    ->whereIn('meaning_id',function($q) use ($lemma_id){
                         $q->select('id')->from('meanings')
                           ->where('lemma_id',$lemma_id);
                    })->groupBy('text_id','w_id')->get();
//dd($texts->toSql());                     
        return sizeof($texts);
    }
    
    public static function storeLemma($data) {        
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
        
        $this->storeWordformsFromSet($gramset_wordforms, $dialect_id); 
        $this->createDictionaryWordforms($wordforms, 
                isset($features['number']) ? $features['number'] : NULL, 
                $dialect_id);
        $this->updateTextWordformLinks();
    }
    
    public function updateLemma($data) {
//dd($data);        
        list($new_lemma, $wordforms_list, $stem, $affix, $gramset_wordforms, $stems) 
                = Grammatic::parseLemmaField($data);
//dd($wordforms_list);        
//dd($new_lemma, $stem, $affix, $stems);  
        $lang_id = (int)$data['lang_id'];
        $this->lemma = $new_lemma;
//        $this->lemma_for_search = Grammatic::toSearchForm($new_lemma);
        $this->lemma_for_search = Grammatic::changeLetters($new_lemma, $lang_id);
        $this->lang_id = $lang_id;
        $this->pos_id = (int)$data['pos_id'] ? (int)$data['pos_id'] : NULL;
        $this->updated_at = date('Y-m-d H:i:s');
        $this->save();
        
        $this->storeAddition($wordforms_list, $stem, $affix, $gramset_wordforms, $data, $data['wordform_dialect_id'], $stems);           
        
        $this->storePhrase(isset($data['phrase']) ? $data['phrase'] : null);
    }

    public function modify() { 
//        $this->lemma_for_search = Grammatic::toSearchForm($this->lemma);
        $this->lemma_for_search = Grammatic::changeLetters($this->lemma, $this->lang_id);
        $this->updated_at = date('Y-m-d H:i:s');
        $this->save();        
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
    
    /**
     * Search in texts a lemma and all wordforms of the lemma for creating meaning-text links
     * 
     * SQL: select text_id, s_id, w_id, words.id as word_id from words, texts where words.text_id = texts.id and texts.lang_id = 5 and (word like 'olla' OR word like 'olen' OR word like 'on' OR word like 'ollah' OR word like 'olla' OR word like 'en ole') LIMIT 1;
     * SQL: select text_id, s_id, w_id, words.id as word_id from words where text_id in (select id from texts where lang_id = 5) and (word like 'olla' OR word like 'olen' OR word like 'on' OR word like 'ollah' OR word like 'olla' OR word like 'en ole') LIMIT 1;
     * 
     * @return Collection
     */
    public function getWordsForMeanings()
    {        
        $lang_id = $this->lang_id;
        $strs = ["word like '".$this->lemma_for_search."'"];
/*        
        foreach ($this->wordforms as $wordform_obj) {
            $wordform_obj->trimWord(); // remove extra spaces at the beginning and end of the wordform 
            //$wordform_obj->checkWordformWithSpaces(0); // too heave request, we are waiting new server :(((
            $strs[] = "word like '".$wordform_obj->wordform_for_search."'";
        }
*/        
        $wordforms = LemmaWordform::whereLemmaId($this->id)
                                  ->get(['wordform_for_search']);
        foreach ($wordforms as $lw) {
            $strs[] = "word like '".$lw->wordform_for_search."'";
        }
        
        if (!sizeof($strs)) {
            return null;
        }
        $cond = join(' OR ',array_unique($strs));

        $query = "select text_id, s_id, w_id, words.id as word_id from words where"
               . " text_id in (select id from texts where lang_id = ".$lang_id
               . ") and (".$cond.")"; 
        $words = DB::select($query); 
        return $words;
    }
    
    /**
     * Gets sentences for Lemma text examples edition 
     *
     * @return array
     */
    public function sentences(){
        $sentences = [];
        $lemma_id = $this->id;
        
        $sentence_builder = DB::table('meaning_text')
                              ->whereIn('meaning_id',function($q) use($lemma_id){
                                    $q->select('id')->from('meanings')
                                       ->where('lemma_id',$lemma_id);
                              })
                              ->groupBy('text_id')
                              ->groupBy('s_id')
                              ->groupBy('w_id')
                              ->orderBy('text_id')
                              ->orderBy('s_id')
                              ->orderBy('w_id');
//dd($sentence_builder->count());                              
//print "<pre>";                              
        foreach ($sentence_builder->get() as $s) {
//print_r($s);            
            $sentence_builder2 = DB::table('meaning_text')
                              ->where('text_id',$s->text_id)
                              ->where('s_id',$s->s_id)
                              ->where('w_id',$s->w_id)
                              ->whereIn('meaning_id',function($q) use($lemma_id){
                                    $q->select('id')->from('meanings')
                                       ->where('lemma_id',$lemma_id);
                              });
            $relevance=[];
            foreach ($sentence_builder2->get() as $s2) {
                $relevance[$s2->meaning_id] = $s2->relevance;
            }
            $sentence = Text::extractSentence($s->text_id, 
                                              $s->s_id, 
                                              $s->w_id, 
                                              $relevance);
            if ($sentence) {
                $sentences[] = $sentence;
            }
        }
        return $sentences;
    }
    
    public static function lastCreated($limit='') {
        $lemmas = self::latest();
        if ($limit) {
            $lemmas = $lemmas->take($limit);
        }
        $lemmas = $lemmas->get();
        foreach ($lemmas as $lemma) {
            $revision = Revision::where('revisionable_type','like','%Lemma')
                                ->where('key','created_at')
                                ->where('revisionable_id',$lemma->id)
                                ->latest()->first();
            if ($revision) {
                $lemma->user = User::getNameByID($revision->user_id);
            }
        }
        return $lemmas;
    }
    
    public static function lastUpdated($limit='',$is_grouped=0) {
        $revisions = Revision::where('revisionable_type','like','%Lemma')
                            ->where('key','updated_at')
                            ->groupBy('revisionable_id')
                            ->latest()->take($limit)->get();
        $lemmas = [];
        foreach ($revisions as $revision) {
            $lemma = Lemma::find($revision->revisionable_id);
            if ($lemma) {
                $lemma->user = User::getNameByID($revision->user_id);
                if ($is_grouped) {
                    $updated_date = $lemma->updated_at->formatLocalized(trans('main.date_format'));            
                    $lemmas[$updated_date][] = $lemma;
                } else {
                    $lemmas[] = $lemma;
                }
            }
        }
        
        return $lemmas;
    }
    
    public function allHistory() {
        $all_history = $this->revisionHistory->filter(function ($item) {
                            return $item['key'] != 'updated_at'
                                 && !($item['key'] == 'reflexive' && $item['old_value'] == null && $item['new_value'] == 0);
                        });
        foreach ($all_history as $history) {
            $history->what_created = trans('history.lemma_accusative');
        }
        foreach ($this->meanings as $meaning) {
            foreach ($meaning->revisionHistory as $history) {
                $history->what_created = trans('history.meaning_accusative', ['num'=>$meaning->meaning_n]);
            }
            $all_history = $all_history -> merge($meaning->revisionHistory);
            foreach($meaning->meaningTexts as $meaning_text) {
               foreach ($meaning_text->revisionHistory as $history) {
                   $lang = $meaning_text->lang->name;
                   $fieldName = $history->fieldName();
                   $history->field_name = trans('history.'.$fieldName.'_accusative'). ' '
                           . trans('history.meaning_genetiv',['num'=>$meaning->meaning_n])
                           . " ($lang)";
               }
               $all_history = $all_history -> merge($meaning_text->revisionHistory);
            }
        }
         
        $all_history = $all_history->sortByDesc('id')
                      ->groupBy(function ($item, $key) {
                            return (string)$item['updated_at'];
                        });
//dd($all_history);                        
        return $all_history;
    }
    
    public function firstDialect() {
        $dialect_id = Lang::mainDialectByID($this->lang_id);
        return Dialect::find($dialect_id);
//        return Dialect::where('lang_id', $this->lang_id)->orderBy('sequence_number')->first();
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
    
    public function storePhrase($lemmas) {
        $this->phraseLemmas()->detach();
        if ($lemmas) {
            $this->phraseLemmas()->attach($lemmas);
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
    
    /**
     * 
     * @param String $wordform
     * @return string
     */
    public function affixForWordform($wordform) {
/*                $wordform_comp = preg_split("/\s/", $wordform->wordform); we don't take analytic forms
                $last_comp = array_pop($wordform_comp);
                if (preg_match("/^".$stem."(.*)$/u", $last_comp, $regs)) { */
        if (preg_match("/\s/", $wordform)) {
            return NULL;
        }
        if (!$this->reverseLemma) {
            return NULL;
        }
        $stem = $this->reverseLemma->stem;
        if (!$stem) {
            return NULL;
        }
        $stem = preg_replace('/\|\|/','',$stem);
        if (preg_match("/^".$stem."(.*)$/u", $wordform, $regs)) {
            return $regs[1] ?? '';
        }
       return '#';
    }
    
    public function meaningIdsToList() {
        $out = [];
        foreach ($this->meanings as $meaning) {
            $out[] = $meaning->id;
        }
        return join(',',$out);
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
    
    public function isExistWordforms($gramset_id, $dialect_id, $wordform_id) {
        $exist_wordforms = $this-> wordforms()
                                 ->wherePivot('gramset_id',$gramset_id)
                                 ->wherePivot('dialect_id',$dialect_id)
                                 ->wherePivot('wordform_id',$wordform_id);
        return $exist_wordforms->count();        
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
                            ['gramset_id'=>$gramset_id, 'dialect_id'=>$dialect_id, 'affix'=>$affix, 
                             'wordform_for_search'=>Grammatic::changeLetters($wordform_obj->wordform, $this->lang_id)]);    
/*
        $query = "DELETE FROM lemma_wordform WHERE lemma_id=".$this->id
            . " and wordform_id=".$wordform_id." and gramset_id";
//print "<p>$query";            
        if (!$gramset_id) {
            $gramset_id=NULL;
            $query .= " is NULL";
        } else {
            $query .= "=".(int)$gramset_id;
        }
        $query .= " and dialect_id";
        if (!$dialect_id) {
            $query .= " is NULL";
        } else {
            $query .= "=".(int)$dialect_id;
        }
        DB::statement($query);
        $this-> wordforms()->attach($wordform_id, 
                ['gramset_id'=>$gramset_id, 'dialect_id'=>$dialect_id, 'affix' => $affix, 
                             'wordform_for_search'=>Grammatic::changeLetters($trim_word, $this->lang_id)]);   */                                         
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

    public static function search(Array $url_args) {
        $lemmas = self::orderBy('lemma');
//        if ($url_args['search_wordform'] || $url_args['search_gramset']) {
  //          $lemmas = $lemmas->join('lemma_wordform', 'lemmas.id', '=', 'lemma_wordform.lemma_id');
//            $lemmas = self::searchByWordform($lemmas, $url_args['search_wordform'], $url_args['search_lang']);
            $lemmas = self::searchByGramset($lemmas, $url_args['search_gramset']);
    //    }    
        $lemmas = self::searchByLemma($lemmas, $url_args['search_lemma']); // in trait
        $lemmas = self::searchByLang($lemmas, $url_args['search_lang']);
        $lemmas = self::searchByPOS($lemmas, $url_args['search_pos']);
        $lemmas = self::searchByID($lemmas, $url_args['search_id']);
        $lemmas = self::searchByMeaning($lemmas, $url_args['search_meaning']);
        $lemmas = self::searchByLabel($lemmas, $url_args['search_label']);
        $lemmas = self::searchByConcept($lemmas, $url_args['search_concept']);
        $lemmas = self::searchByConceptCategory($lemmas, $url_args['search_concept_category']);
        $lemmas = self::searchByDialects($lemmas, $url_args['search_dialects']);
        $lemmas = self::searchWithAudios($lemmas, $url_args['with_audios']);
        $lemmas = self::searchWithExamples($lemmas, $url_args['with_examples']);

        $lemmas = $lemmas
                //->groupBy('lemmas.id') // отключено, неправильно показывает общее число записей
                         ->with(['meanings'=> function ($query) {
                                    $query->orderBy('meaning_n');
                                }]);
//dd($lemmas->toSql());                                
        return $lemmas;
    }
    
    public static function searchByLemma($lemmas, $lemma) {
        if (!$lemma) {
            return $lemmas;
        }
        
        return $lemmas->where(function ($query) use ($lemma) {
                        self::searchLemmas($query, $lemma);
                       });
    }    

    public static function searchLemmas($query, $lemma) {
        $lemma = preg_replace("/\|/", '', $lemma);
        return $query -> where('lemma_for_search', 'like', Grammatic::toSearchForm($lemma))
                       -> orWhere('lemma_for_search', 'like', $lemma)
                       -> orWhereIn('id', function ($q) use ($lemma) {
                            $q->select('lemma_id')->from('phonetics')
                              ->where('phonetic', 'like', $lemma);
                        });
    }        
    
    /**
     * 
     * @param array $url_args
     * @return type
     */
    public static function searchByWordformGrams(Array $url_args) {
        $lemmas = self::orderBy('lemma');
        $lemmas = self::searchByLang($lemmas, $url_args['search_lang']);
        $lemmas = self::searchByPOS($lemmas, $url_args['search_pos']);

        $lemmas = self::searchByWordforms($lemmas, $url_args['search_wordforms'], 
                $url_args['search_gramsets'], 
                $url_args['search_lang'], 
                $url_args['search_dialects']);

        $lemmas = $lemmas->with(['meanings'=> function ($query) {
                                    $query->orderBy('meaning_n');
                                }]);
//        $query = str_replace(array('?'), array('\'%s\''), $lemmas->toSql());
//        $query = vsprintf($query, $lemmas->getBindings());     
//dd($query);                                
        return $lemmas;
    }
    
    public static function searchByWordforms($lemmas, $wordforms, $gramsets, $lang_id, $dialects) {
        if (!sizeof($wordforms)) {
            return $lemmas;
        }

        foreach ($wordforms as $i => $wordform) {
            $wordform_for_search = Grammatic::toSearchByPattern($wordform, $lang_id);
            $gramset_id = $gramsets[$i] ?? null;
            if ($wordform_for_search || $gramset_id) {
            $lemmas = $lemmas->whereIn('id',function($q) use ($wordform_for_search, $gramset_id, $dialects){
                            $q->select('lemma_id')->from('lemma_wordform');
                            if ($wordform_for_search) {
                                $q->where('wordform_for_search','rlike', $wordform_for_search);
                            }
                            if ($gramset_id) {
                                $q->where('gramset_id', $gramset_id);
                            }
                            if (isset($dialects[0]) && $dialects[0]) {
                                $q->whereIn('dialect_id', $dialects);
                            }
                        });
            }
        }
        return $lemmas;                            
    }
    
    public static function searchByWordform($lemmas, $wordform, $lang_id) {
        if (!$wordform) {
            return $lemmas;
        }
/*        return $lemmas->whereIn('wordform_id',function($query) use ($wordform){
                            $query->select('id')
                            ->from('wordforms')
                            ->where('wordform_for_search','like', Grammatic::toSearchForm($wordform));
                        });*/
        $wordform_for_search = Grammatic::changeLetters($wordform, $lang_id);
        return $lemmas->whereIn('id',function($q) use ($wordform_for_search){
                            $q->select('lemma_id')->from('lemma_wordform')
//                              ->whereIn('wordform_id',function($query) use ($wordform_for_search){
  //                                  $query->select('id')
    //                                ->from('wordforms')
                                    ->where('wordform_for_search','like', $wordform_for_search);
//                                });
                            });
    }
    
    public static function searchByGramset($lemmas, $gramset) {
        if (!$gramset) {
            return $lemmas;
        }
//        return $lemmas->where('gramset_id',$gramset);
        return $lemmas->whereIn('id',function($q) use ($gramset){
                            $q->select('lemma_id')->from('lemma_wordform')
                              ->where('gramset_id',$gramset);
                            });
    }
    
    public static function searchByLang($lemmas, $lang) {
        if (!$lang) {
            return $lemmas;
        }
        return $lemmas->where('lang_id',$lang);
    }
    
    public static function searchByPOS($lemmas, $pos) {
        if (!$pos) {
            return $lemmas;
        }
        return $lemmas->where('pos_id',$pos);
    }
    
    public static function searchByID($lemmas, $id) {
        if (!$id) {
            return $lemmas;
        }
        return $lemmas->where('id',$id);
    }
    
    public static function searchByMeaning($lemmas, $meaning) {
        if (!$meaning) {
            return $lemmas;
        }
        return $lemmas->whereIn('id',function($query) use ($meaning){
                    $query->select('lemma_id')
                        ->from('meanings')
                        ->whereIn('id',function($q) use ($meaning){
                            $q->select('meaning_id')
                            ->from('meaning_texts')
                            ->where('meaning_text','like', $meaning);
                        });
                    });
    }
    
    public static function searchWithAudios($lemmas, $with_audios) {
        if (!$with_audios) {
            return $lemmas;
        }
        return $lemmas->whereIn('id',function($query){
                    $query->select('lemma_id')
                        ->from('audio_lemma');
                    });
    }
    
    public static function searchWithExamples($lemmas, $with_meanings) {
        if (!$with_meanings) {
            return $lemmas;
        }
        return $lemmas->whereIn('id',function($query){
                    $query->select('lemma_id')
                        ->from('meanings')
                        ->whereIn('id',function($q){
                            $q->select('meaning_id')->from('meaning_text')
                              ->where('relevance', '>', 0);
                        });
                    });
    }
    
    public static function searchByConcept($lemmas, $concept_id) {
        if (!$concept_id) {
            return $lemmas;
        }
        return $lemmas->whereIn('id',function($query) use ($concept_id){
                    $query->select('lemma_id')
                        ->from('meanings')
                        ->whereIn('id',function($query) use ($concept_id){
                            $query->select('meaning_id')
                            ->from('concept_meaning')
                            ->where('concept_id', $concept_id);
                        });
                    });
    }
    
    public static function searchByConceptCategory($lemmas, $concept_category_id) {
        if (!$concept_category_id) {
            return $lemmas;
        }
        return $lemmas->whereIn('id',function($query) use ($concept_category_id){
                    $query->select('lemma_id')
                        ->from('meanings')
                        ->whereIn('id',function($q1) use ($concept_category_id){
                            $q1->select('meaning_id')
                            ->from('concept_meaning')
                            ->whereIn('concept_id', function($q2) use ($concept_category_id) {
                                $q2->select('id')
                                ->from('concepts')
                                ->where('concept_category_id', $concept_category_id);
                            });
                        });
                    });
    }
    
    public static function searchByLabel($lemmas, $label_id) {
        if (!$label_id) {
            return $lemmas;
        }
        return $lemmas->whereIn('id', function ($query) use ($label_id){
                            $query->select('lemma_id')->from('label_lemma')
                                  ->where('label_id', $label_id);
        });
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
    
    public function meaningsWithLabel($label_id) {
        return $this->meanings()->whereIn('id', function ($q) use ($label_id) {
            $q->select('meaning_id')->from('label_meaning')
              ->whereLabelId($label_id);
        })->get();
    }
    
    public function getMultilangMeaningTexts() {
        $meanings = [];
        foreach ($this->meanings as $meaning_obj) {
             $meanings[] = $meaning_obj->getMultilangMeaningTextsStringLocale();
        }
        return $meanings;
    }
    
    public function getLangMeaningTexts($lang_code) {
        $meanings = [];
        foreach ($this->meanings as $meaning_obj) {
             $meanings[] = $meaning_obj->getMultilangMeaningTextsString($lang_code);
        }
        return $meanings;
    }
    
    public function getFrequencyInCorpus() {
        $lemma_id = $this->id;
        return Word::whereIn('id', function ($q) use ($lemma_id) {
                        $q->select('word_id')->from('meaning_text')
                          ->where('relevance', '>', 0)
                          ->whereIn('meaning_id', function ($q2) use ($lemma_id) {
                              $q2->select('id')->from('meanings')
                                 ->whereLemmaId($lemma_id);
                          });
                    })->count();
    }

    public function getWordformsForTest($dialect_id) {
        $wordforms = [];
        $lang_id = $this->lang_id;
        $pos_id = $this->pos_id;
        $gramsets = Grammatic::getListForAutoComplete($lang_id, $pos_id);
//dd($gramsets);        
        foreach ($gramsets as $gramset_id) {
            $wordform_coll = $this->wordformsByGramsetDialect($gramset_id, $dialect_id);
            if (sizeof($wordform_coll)>1) {
                $tmp = [];
                foreach ($wordform_coll as $w) {
                    $tmp[] = $w->wordform;
                }
                $wordforms[$gramset_id] = join (', ', $tmp);                    
            } else {
                $wordforms[$gramset_id] = isset($wordform_coll[0]) ? $wordform_coll[0]->wordform : ''; 
            }
        }
//var_dump($wordforms);        
        return $wordforms;
    }

    public function toUniMorph($dialect_id) {
        $pos_id = $this->pos_id;
        
        if (!in_array($pos_id, PartOfSpeech::getNameIDs()) && $pos_id != PartOfSpeech::getVerbID()) {
            return false;
        } 
        $pos = PartOfSpeech::find($pos_id);
        if (!$pos) { return false; }
        
        $pos_code = $pos->unimorph;
        if ($pos_code == 'V' && $this->features && $this->features->reflexive) {
            $pos_code .= ';REFL';
        }
//dd($this->wordforms);              
        $wordforms = $this->wordforms()->wherePivot('dialect_id',$dialect_id)->get();//wordformsWithGramsets();
//dd($dialect_id, $wordforms);
        if (!$wordforms) { return false; }
        $lines = [];
        foreach ($wordforms as $wordform) {
            $gramset=$wordform->gramsetPivot();
            if (!$gramset) { continue; }
            $features = $gramset->toUniMorph($pos_code);
            if (!$features) { continue; }
            $lines[] = $this->lemma."\t".$wordform->wordform."\t".$features;
        }
        return join("\n", $lines);
    }
    
    public function compoundToUniMorph() {
        if ($this->features && $this->features->comptype_id) {
            $comptype = $this->features->comptype_id;
        } else {
            $comptype = '';
        }
        $lemmas = $this->phraseLemmas;
        if (!$lemmas) { return false; }
        $tmp = [];
        foreach ($lemmas as $lemma) {
            $tmp[] = $lemma->lemma;
        }
        return $this->lemma. "\t$comptype\t". join(";", $tmp);
    }
    
    public function stemAffixForm() {
        if (!$this->reverseLemma || !$this->reverseLemma->stem) {
            return $this->lemma;
        }
        return  $this->reverseLemma->stem. 
                ($this->reverseLemma->affix ? '|'.$this->reverseLemma->affix : '');        
    }
    
    public function dictForm() {
        $out = $this->stemAffixForm();
//dd($out);        
        $dialect_id = $this->lang->mainDialect();
        if (!$this->reverseLemma || !$this->reverseLemma->stem || !in_array($this->lang_id, [1,4,5]) || !$dialect_id) { // not veps and livvi
            return $out;
        }
        $max_stem = preg_replace("/\|\|/", '', $this->reverseLemma->stem);
        $gramsets = Gramset::dictionaryGramsets($this->pos_id, $this->features->number ?? NULL, $this->lang_id);
        if (!$gramsets || !is_array($gramsets) || sizeof($gramsets)<2) { return $out; }
        
        array_pop($gramsets);       
//dd($gramsets);        
        $wordforms = [];
        foreach ($gramsets as $gramset_id) {
            $wforms = $this->wordformsByGramsetDialect($gramset_id, $dialect_id);
//print "<p>w:".$w[0]->wordform."</p>";            
            if (!$wforms || !isset($wforms[0])) {
                return $out;
            }
            $tmp = [];
            foreach ($wforms as $w) {
                if (!preg_match("/^".$max_stem."(.*)$/u", $w->wordform, $regs)) {
                    return $out;
                } else {
                    $tmp[] = isset($regs[1]) ? '-'.$regs[1] : '';
                }                
            }
            $wordforms[$gramset_id] = join('/',$tmp);
        }
        return $out. Grammatic::templateFromWordforms($wordforms, $this->lang_id, $this->pos_id, $this->features->number ?? NULL); //" (".join(', ',$wordforms).")";        
    }
    
    public function getStemAffixByStems() {
        if (!$this->isChangeable()) {
            return [$this->lemma, null];
        }

        $max_stem=$this->lemma; 
        $stems = [];
        foreach ($this->getWordformDialectIds() as $dialect_id) {
            $stems_for_max = $stems = $this->getBases($dialect_id);
            $this->updateBases($stems, $dialect_id);

            if ($this->lang_id==1 && $this->pos_id == PartOfSpeech::getVerbID()) {
            }
                $stems_for_max = array_slice($stems, 0, 5);
            list($max_stem) = Grammatic::maxStem(array_merge([$max_stem], $stems_for_max));
        }
        if (preg_match("/^".$max_stem."(.*)/u", $this->lemma, $regs)) {
            $affix = $regs[1];
        } else {
            $affix = false;
        }    
        return [$max_stem, $affix];
    }
    
    public function getStemAffixByWordforms() {
        if (!$this->isChangeable()) {
            return [$this->lemma, null];
        }

        $wordforms =[$this->lemma];
        foreach ($this->wordforms()->whereNotNull('gramset_id')->get() as $wordform) {
            if (!preg_match("/\s/", $wordform->wordform)) {
                $wordforms[]=$wordform->wordform;
            }
        }
//dd((array)$wordforms);            
        list($max_stem) = Grammatic::maxStem($wordforms);
//dd($max_stem);            
        if (preg_match("/^".$max_stem."(.*)/u", $this->lemma, $regs)) {
            $affix = $regs[1];
        } else {
            $affix = false;
        }    
        return [$max_stem, $affix];
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

    /*
     * Important parameter for wordform generation
     * number for nominals OR impersonal for verbs
     * 
     */
    public function getNameNum() {
        if ($this->pos->isVerb()) {
            return ($this->features && $this->features->impersonal) ? 1 : null; 
        } else {
            return ($this->features && $this->features->number) ? Grammatic::nameNumFromNumberField($this->features->number) : null; 
        }
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
    
    public static function urlArgs($request) {
        $url_args = Str::urlArgs($request) + [
                    'search_affix'    => $request->input('search_affix'),
                    'search_concept_category'  => $request->input('search_concept_category'),
                    'search_concept'  => (int)$request->input('search_concept'),
//                    'search_dialect'  => (array)$request->input('search_dialect'),
                    'search_dialect'  => (int)$request->input('search_dialect'),
                    'search_dialects' => (array)$request->input('search_dialects'),
                    'search_gramset'  => (int)$request->input('search_gramset'),
                    'search_gramsets' => (array)$request->input('search_gramsets'),
                    'search_id'       => (int)$request->input('search_id'),
                    'search_label'    => (int)$request->input('search_label'),
                    'search_lang'     => (int)$request->input('search_lang'),
                    'search_lemma'    => $request->input('search_lemma'),
                    'search_meaning'  => $request->input('search_meaning'),
                    'search_pos'      => (int)$request->input('search_pos'),
                    'search_relation' => (int)$request->input('search_relation'),
                    'search_wordform' => $request->input('search_wordform'),
                    'search_wordforms'=> (array)$request->input('search_wordforms'),
                    'with_audios'     => (int)$request->input('with_audios'),
                    'with_examples'   => (int)$request->input('with_examples')
                ];
        
        if (!$url_args['search_id']) {
            $url_args['search_id'] = NULL;
        }
        
        foreach ($url_args['search_wordforms'] as $i => $wordform) {
            if (!$wordform && !$url_args['search_gramsets'][$i]) {
                unset($url_args['search_wordforms'][$i]);
                unset($url_args['search_gramsets'][$i]);
            }
        }
        if (!isset($url_args['search_wordforms'][1])) {
            $url_args['search_wordforms'][1] = null;
        }
        
        if (!isset($url_args['search_gramsets'][1])) {
            $url_args['search_gramsets'][1] = null;
        }
        
        ksort($url_args['search_wordforms']);

        return $url_args;
    }
    
    public static function selectFromMeaningText($search_dialect=null) {
        $builder = Lemma::join('meanings','lemmas.id','=','meanings.lemma_id')
                    ->join('meaning_text','meanings.id','=','meaning_text.meaning_id')
                    ->where('relevance', '>', 0);
        if ($search_dialect) {
            $builder->whereIn('text_id', function ($q) use ($search_dialect) {
                $q->select('text_id')->from('dialect_text')
                  ->whereDialectId($search_dialect);
            });
        }
        return $builder;
    }
    
    /**
     * 
     * @param array $phonetic_dialects [<phonetic1>=>[<dialect1_id>=>[<place1_id>, ...], ...], ...]
     */
    public function updatePhonetics($phonetic_dialects) {
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
    
    /**
     * builder for extraction of lemmas having w_count wordforms
     * 
     * @param int $lang_id
     * @param int $pos_id
     * @param int $w_count
     * @return type
     */
    public static function lemmasWithWordformsByCount($lang_id, $pos_id, $w_count) {
        return $lemmas = Lemma::where('lang_id', $lang_id)
                           ->where('pos_id', $pos_id)
                           ->whereIn('id', function ($query) use ($w_count) {
                               $query->select('lemma_id')->from('lemma_wordform')
                                     ->groupBy('lemma_id')
                                     ->havingRaw('count(*) = ?', [$w_count]);
                           });        
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
    
    public static function findOrCreate($lemma, $pos_id, $lang_id, $interpretation) {
        $lemmas = self::where('lemma', 'like', $lemma)
                         ->wherePosID($pos_id)
                         ->get();
        if (!$lemmas || sizeof($lemmas)==0) {
            $lemma_obj = self::store($lemma, $pos_id, $lang_id);
        } elseif (sizeof($lemmas) >1) {
// выбрать по значению или создать новую            
        } else {
// проверить значение            
        }
    }
    
    public function getMeaningTexts() {
        $meaning_texts = [];
        $meanings = $this->meanings;
        $langs_for_meaning = Lang::getListWithPriority($this->lang_id);
        
        foreach ($meanings as $meaning) {
            foreach ($langs_for_meaning as $lang_id => $lang_text) {
                $meaning_text_obj = MeaningText::where('lang_id',$lang_id)->where('meaning_id',$meaning->id)->first();
                if ($meaning_text_obj) {
                    $meaning_texts[$meaning->id][$lang_text] = $meaning_text_obj->meaning_text;
                }
            }
        }
        return $meaning_texts;
    }

    public function getMeaningRelations() {
        $meaning_relations = [];
        $meanings = $this->meanings;
        $relations = Relation::getList();
        
        foreach ($meanings as $meaning) {
            $relation_meanings = $meaning->meaningRelations;
            if ($relation_meanings) {
                foreach ($relation_meanings as $relation_meaning) {
                    $meaning2_id = $relation_meaning->pivot->meaning2_id;
                    $relation_id = $relation_meaning->pivot->relation_id;
                    $relation_text = $relations[$relation_id];
                    $relation_meaning_obj = Meaning::find($meaning2_id);
                    $relation_lemma_obj = $relation_meaning_obj->lemma;
                    $relation_lemma = $relation_lemma_obj->lemma;
                    $meaning_relations[$meaning->id][$relation_text][$relation_lemma_obj->id]  
                            = ['lemma' => $relation_lemma,
                               'meaning' => $relation_meaning_obj->getMultilangMeaningTextsString()];
                }
            }
        }
        return $meaning_relations;
    }

    public function getMeaningTranslations() {
        $translation_values = [];
        $meanings = $this->meanings;
        $langs_for_meaning = Lang::getListWithPriority($this->lang_id);
        
        foreach ($meanings as $meaning) {
            foreach ($langs_for_meaning as $l_id => $lang_text) {
                $meaning_translations = $meaning->translations()->wherePivot('lang_id',$l_id)->get();
                if ($meaning_translations) {
                    foreach ($meaning_translations as $meaning_translation) {
                        $meaning2_id = $meaning_translation->pivot->meaning2_id; 
                        $meaning2_obj = Meaning::find($meaning2_id);
                        $translation_lemma_obj = $meaning2_obj->lemma;
                        $translation_lemma = $translation_lemma_obj->lemma;
                        $translation_values[$meaning->id][$lang_text][$translation_lemma_obj->id] 
                            = ['lemma' => $translation_lemma,
                               'meaning' => $meaning2_obj->getMultilangMeaningTextsString()];
                    }
                }
            }
        }   
        return $translation_values;
    }

    /*    
    public static function totalCount(){
        return self::count();
    }*/
     
   /**
     * Gets Delete link created in a view
     * Generates a CSRF token and put it inside a custom data-delete attribute
     * @param bool $is_button Is this button or link?
     */
/*    public function buttonDelete($is_button=true)
    {
//        $format = '<a href="%s" data-toggle="tooltip" data-delete="%s" title="%s" class="btn btn-default"><i class="fa fa-trash-o"></i></a>';
        $format = '<a href="%s" data-toggle="tooltip" data-delete="%s" title="%s"';
        if ($is_button) {
            $format .= ' class="btn btn-xs btn-danger"';
        }
        $format .= '>%s</a>';
        $link = URL::route('lemma.destroy', ['id' => $this->id]);
        $token = csrf_token();
        $title = \Lang::get('messages.delete');
        return sprintf($format, $link, $token, $title, $title);
    }
 * 
 */
}
