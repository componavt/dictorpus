<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use DB;
use URL;
use LaravelLocalization;
use \Venturecraft\Revisionable\Revision;

use App\Library\Grammatic;

use App\Models\User;
use App\Models\Corpus\Text;
use App\Models\Corpus\Word;

use App\Models\Dict\Label;
use App\Models\Dict\PartOfSpeech;


class Lemma extends Model
{
    protected $fillable = ['lemma','lang_id','pos_id', 'lemma_for_search'];
    
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
    
    // Belongs To Relations
    use \App\Traits\Relations\BelongsTo\Lang;
    use \App\Traits\Relations\BelongsTo\ReverseLemma;
    use \App\Traits\Relations\BelongsTo\POS;
    
    // Belongs To Many Relations
    use \App\Traits\Relations\BelongsToMany\Dialects;
    use \App\Traits\Relations\BelongsToMany\Labels;
    use \App\Traits\Relations\BelongsToMany\LemmaVariants;
    use \App\Traits\Relations\BelongsToMany\Wordforms;
   
    // Has Many Relations
    use \App\Traits\Relations\HasMany\Meanings;
    use \App\Traits\Relations\HasMany\Bases;
    
    // Scopes
//    use \App\Models\Scopes\ID;
//    use \App\Models\Scopes\LangID;
//    use \App\Models\Scopes\Wordform;
    
    /**
     * @return Array of bases
     */
    public function getBases($dialect_id=null) {
        $bases=[];
        for ($i=0; $i<9; $i++) {
//dd($this->bases);            
            $bases[$i] = $this->getBase($i, $dialect_id, $bases);
        }
        return $bases;
    }
    
    /**
     * @return String
     */
    public function getBase($base_n, $dialect_id=null, $bases=null) {
//print         
        $base = $this->getBaseFromDB($base_n, $dialect_id);

        if ($base) {
            return $base;
        }
        
        if ($dialect_id) { 
            $base = Grammatic::getStemFromStems($bases, $base_n, $this->lang_id,  $this->pos_id, $dialect_id, $this->lemma);
            if (!$base) {
                $is_reflexive = $this->features && $this->features->reflexive ? true : false;
                $base = Grammatic::getStemFromWordform($this, $base_n, $this->lang_id,  $this->pos_id, $dialect_id, $is_reflexive);
//dd($base_n,$base);                
            }
            if (!$base) {
                $base = $this->getBaseFromDB($base_n);
            }
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

    public function wordformsByGramsetDialect($gramset_id, $dialect_id){
        return $this->wordforms()->orderBy('wordform')
                    ->wherePivot('gramset_id',$gramset_id)
                    ->wherePivot('dialect_id',$dialect_id)
                    ->get();
    }
        
    /**
     *  Gets wordforms for given gramset and dialect
     * 
     * @param int $gramset_id
     * @param int $dialect_id
     * 
     * @return String or NULL
     */
    public function wordform($gramset_id,$dialect_id, $with_search_link=NULL){
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
                    $lang_id = $this->lang_id;
                    $word_count = Word::where('word', 'like',$wordform->wordform_for_search)
                                ->whereIn('text_id', function ($query) use ($lang_id) {
                                    $query->select('id')->from('texts')
                                          ->where('lang_id', $lang_id);
                                })
                                ->count();
                    if ($word_count) {
                        $w = '<a href="'.LaravelLocalization::localizeURL('/corpus/text/?search_lang='.$lang_id
                           . '&search_word='.$wordform->wordform_for_search). '" title="'.$word_count.'">'.$w.'</a>';
                    }
                }
                $wordform_arr[]=$w;
            }
            return join(', ',$wordform_arr);
        }        
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
            foreach ($lemma->dialects->unique() as $dialect) {
                $dialects[] = $dialect->name;
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
        $reverse = '';
        for ($i = mb_strlen($str); $i>=0; $i--) {
            $reverse .= mb_substr($str, $i, 1);
        }
        return $reverse;
    }   
    
    public function remove() {
        $this-> wordforms()->detach();
        $this-> labels()->detach();
        $this-> phraseLemmas()->detach();
        
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
            $meaning -> remove();
        }

        $bases = $this->bases;

        foreach ($bases as $base) {
            $base -> delete();
        }

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
                      ->select('dialect_id')
                      ->where('lemma_id',$this->id)
                      ->orderBy('sequence_number')
                      ->groupBy('dialect_id')->get();
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
/*        $gramset_ids = DB::table('lemma_wordform')
                      ->select('gramset_id')
                      ->where('lemma_id',$this->id)
                      ->groupBy('gramset_id')->get();*/
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

        $lemma->storeAddition($wordforms, $stem, $affix, $gramset_wordforms, $data, $data['dialect_id'], $stems);      
        return $lemma;
    }
    
    public static function store($lemma, $pos_id, $lang_id) {
//dd($lemma);        
        if (!$pos_id) {
            $pos_id = NULL;
        }
        $lemma = Lemma::create(['lemma'=>$lemma,'lang_id'=>$lang_id,'pos_id'=>$pos_id]);
        $lemma->lemma_for_search = Grammatic::toSearchForm($lemma->lemma);
        $lemma->save();
        return $lemma;
    }
    
    public function storeAddition($wordforms, $stem, $affix, $gramset_wordforms, 
                                  $features, $dialect_id, $stems) {
//dd($features);        
        $this->updateBases($stems, $dialect_id); 
        LemmaFeature::store($this->id, $features);
        $this->storeReverseLemma($stem, $affix);

        if (isset($features['variants'])) {
            $this->storeVariants($features['variants']);
        }
        
        $this->storeWordformsFromSet($gramset_wordforms, $dialect_id); 
        $this->createDictionaryWordforms($wordforms, 
                isset($features['number']) ? $features['number'] : NULL, 
                $dialect_id);
    }
    
    public function updateLemma($data) {
//dd($data);        
        list($new_lemma, $wordforms_list, $stem, $affix, $gramset_wordforms, $stems) 
                = Grammatic::parseLemmaField($data);
//dd($wordforms_list);        
//dd($new_lemma, $stem, $affix, $stems);        
        $this->lemma = $new_lemma;
        $this->lemma_for_search = Grammatic::toSearchForm($new_lemma);
        $this->lang_id = (int)$data['lang_id'];
        $this->pos_id = (int)$data['pos_id'] ? (int)$data['pos_id'] : NULL;
        $this->updated_at = date('Y-m-d H:i:s');
        $this->save();
        
        $this->storeAddition($wordforms_list, $stem, $affix, $gramset_wordforms, $data, $data['dialect_id'], $stems);           
        
        if (isset($data['phrase'])) {
            $this->storePhrase($data['phrase']);
        }
    }

    public function modify() { 
        $this->lemma_for_search = Grammatic::toSearchForm($this->lemma);
        $this->updated_at = date('Y-m-d H:i:s');
        $this->save();        
    }
    
    public function updateBases($stems, $dialect_id) {     
        if (!$dialect_id) {
            return;
        }
        if ($stems) {
            LemmaBase::updateStemsFromSet($this->id, $stems, $dialect_id);
        } else {
            LemmaBase::updateStemsFromDB($this, $dialect_id);
        }
        
    }

    /**
     * Removes all neutral links (relevance=1) from meaning_text
     * and adds new links for all meanings
     *
     * @return NULL
     */
    public function updateTextLinks()
    {        
        foreach ($this->meanings as $meaning_obj) {
            // this meaning has not links with texts yet, add them
            if (!$meaning_obj->texts()->count()) {
                $meaning_obj->addTextLinks();
            } else {
                $meaning_obj->updateTextLinks();
            }
        }
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
                              ->groupBy('sentence_id')
                              ->groupBy('w_id')
                              ->orderBy('text_id')
                              ->orderBy('sentence_id')
                              ->orderBy('w_id');
//dd($sentence_builder->count());                              
//print "<pre>";                              
        foreach ($sentence_builder->get() as $s) {
//print_r($s);            
            $sentence_builder2 = DB::table('meaning_text')
                              ->where('text_id',$s->text_id)
                              ->where('sentence_id',$s->sentence_id)
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
                                              $s->sentence_id, 
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
        return Dialect::where('lang_id', $this->lang_id)->orderBy('sequence_number')->first();
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
        if ($lemmas) {
            $this->variants()->attach($lemmas);
        }
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
        
        ReverseLemma::create([
            'id' => $this->id,
            'reverse_lemma' => $reverse_lemma,
            'lang_id' => $this->lang_id,
            'stem' => $stem,
            'affix' => $affix]);
    }
    
    public function addWordformGramsetDialect($wordform_id, $gramset_id, $dialect_id, $affix) {
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
                ['gramset_id'=>$gramset_id, 'dialect_id'=>$dialect_id, 'affix' => $affix]);                                            
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
        $stem = $this->reverseLemma->stem;
        if (!$stem) {
            return NULL;
        }
        if (preg_match("/^".$stem."(.*)$/u", $wordform, $regs)) {
            return $regs[1];
        }
       return '#';
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
//var_dump("word:$word, gramset:$gramset_id, text:$text_id, w_id:$w_id");        
        $wordform = Wordform::findOrCreate($word);
//dd($wordform->id);    
        $wordform->updateTextWordformLinks($text_id, $w_id, $gramset_id);
//dd();        
        if (!sizeof($dialects)) {
            $dialects[0] = NULL;
        }
        foreach ($dialects as $dialect_id) {
            $this->addWordformGramsetDialect($wordform->id, $gramset_id, $dialect_id,  $this->affixForWordform($wordform->wordform));
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
/*if ($trim_word == 'fateroidme')   {
    dd($trim_word);
} */    
        $wordform_obj = Wordform::findOrCreate($trim_word);
        $wordform_obj->wordform_for_search = Grammatic::toSearchForm($trim_word);
        $wordform_obj->save();

//        $this->wordforms()->detach($wordform_obj->id, ['gramset_id'=>NULL, 'dialect_id'=>NULL]);    
        DB::connection('mysql')->table('lemma_wordform')->whereLemmaId($this->id)
                ->whereWordformId($wordform_obj->id)->whereNull('dialect_id')
                ->whereNull('gramset_id')->delete();
        
        if ($this->isExistWordforms($gramset_id, $dialect_id, $wordform_obj->id)) {
            return;
        }
        if ($gramset_id) {
            $affix = $this->affixForWordform($wordform_obj->wordform);
        } else {
            $affix = NULL;
        }
        $this->wordforms()->attach($wordform_obj->id, 
                                   ['gramset_id'=>$gramset_id, 'dialect_id'=>$dialect_id, 'affix'=>$affix]);    
//print "<p>". $wordform_obj->wordform ." | $gramset_id | $dialect_id</p>";
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
        if ($url_args['search_wordform'] || $url_args['search_gramset']) {
            $lemmas = $lemmas->join('lemma_wordform', 'lemmas.id', '=', 'lemma_wordform.lemma_id');
            $lemmas = self::searchByWordform($lemmas, $url_args['search_wordform']);
            $lemmas = self::searchByGramset($lemmas, $url_args['search_gramset']);
        }    
        $lemmas = self::searchByLemma($lemmas, $url_args['search_lemma']);
        $lemmas = self::searchByLang($lemmas, $url_args['search_lang']);
        $lemmas = self::searchByPOS($lemmas, $url_args['search_pos']);
        $lemmas = self::searchByID($lemmas, $url_args['search_id']);
        $lemmas = self::searchByMeaning($lemmas, $url_args['search_meaning']);
        $lemmas = self::searchByLabel($lemmas, $url_args['search_label']);

        $lemmas = $lemmas
                //->groupBy('lemmas.id') // отключено, неправильно показывает общее число записей
                         ->with(['meanings'=> function ($query) {
                                    $query->orderBy('meaning_n');
                                }]);
//dd($lemmas->toSql());                                
        return $lemmas;
    }
    
    public static function searchByWordform($lemmas, $wordform) {
        if (!$wordform) {
            return $lemmas;
        }
        return $lemmas->whereIn('wordform_id',function($query) use ($wordform){
                            $query->select('id')
                            ->from('wordforms')
                            ->where('wordform_for_search','like', Grammatic::toSearchForm($wordform));
                        });
    }
    
    public static function searchByGramset($lemmas, $gramset) {
        if (!$gramset) {
            return $lemmas;
        }
        return $lemmas->where('gramset_id',$gramset);
    }
    
    public static function searchByLemma($lemmas, $lemma) {
        if (!$lemma) {
            return $lemmas;
        }
//        return $lemmas->where('lemma','like',$lemma);
//var_dump (Grammatic::toSearchForm($lemma), $lemma);
        return $lemmas->where(function ($query) use ($lemma) {
                            $query -> where('lemma_for_search', 'like', Grammatic::toSearchForm($lemma))
                                   -> orWhere('lemma_for_search', 'like', $lemma);
//                                   -> where('lemma_for_search', '');
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
                        ->whereIn('id',function($query) use ($meaning){
                            $query->select('meaning_id')
                            ->from('meaning_texts')
                            ->where('meaning_text','like', $meaning);
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
    
    public function storeWordformsFromSet($wordforms, $dialect_id) {
        if (!$wordforms || !sizeof($wordforms)) {
            return;
        }
        
        if (!$dialect_id) {
            $dialect_id = $this->firstDialect();
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
    
    public function getMultilangMeaningTexts() {
        $meanings = [];
        foreach ($this->meanings as $meaning_obj) {
             $meanings[] = $meaning_obj->getMultilangMeaningTextsStringLocale();
        }
        return $meanings;
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
            $lines[] = $this->lemma."\t".$wordform->wordform."\t".join(';',$features);
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
        return  $this->reverseLemma && $this->reverseLemma->affix 
                ? $this->reverseLemma->stem.'|'.$this->reverseLemma->affix 
                : $this->lemma;
        

    }
    
    public function getStemAffixByStems() {
        if (!$this->isChangeable()) {
            return [$this->lemma, null];
        }

        $max_stem=$this->lemma; 
        $stems = [];
        foreach ($this->getDialectIds() as $dialect_id) {
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
    public function generateWordforms($dialect_id) {
        $name_num = ($this->features && $this->features->number) ? Grammatic::nameNumFromNumberField($this->features->number) : null; 
        $is_reflexive = ($this->features && $this->features->reflexive) ? 1 : null;

        $stems = $this->getBases($dialect_id);
//dd($stems);        
//dd($name_num);     
        return Grammatic::wordformsByStems($this->lang_id, $this->pos_id, $dialect_id, $name_num, $stems, $is_reflexive);
    }

    public static function urlArgs($request) {
        $url_args = [
                    'limit_num'       => (int)$request->input('limit_num'),
                    'page'            => (int)$request->input('page'),
                    'search_gramset'  => (int)$request->input('search_gramset'),
                    'search_id'       => (int)$request->input('search_id'),
                    'search_label'     => (int)$request->input('search_label'),
                    'search_lang'     => (int)$request->input('search_lang'),
                    'search_lemma'    => $request->input('search_lemma'),
                    'search_meaning'  => $request->input('search_meaning'),
                    'search_pos'      => (int)$request->input('search_pos'),
                    'search_relation' => (int)$request->input('search_relation'),
                    'search_wordform' => $request->input('search_wordform'),
                ];
        
        if (!$url_args['page']) {
            $url_args['page'] = 1;
        }
        
        if (!$url_args['search_id']) {
            $url_args['search_id'] = NULL;
        }
        
        if ($url_args['limit_num']<=0) {
            $url_args['limit_num'] = 10;
        } elseif ($url_args['limit_num']>1000) {
            $url_args['limit_num'] = 1000;
        }   
              
        return $url_args;
    }
    
    public static function selectFromMeaningText() {
        return Lemma::join('meanings','lemmas.id','=','meanings.lemma_id')
                    ->join('meaning_text','meanings.id','=','meaning_text.meaning_id')
                    //->whereNotNull('pos_id')        
                    ->where('relevance', '>', 0);
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
