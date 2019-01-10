<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use DB;
use URL;
use LaravelLocalization;
use \Venturecraft\Revisionable\Revision;

use App\Models\User;
use App\Models\Corpus\Text;
use App\Models\Corpus\Word;
//use App\Models\Dict\Meaning;

class Lemma extends Model
{
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
    
    protected $fillable = ['lemma','lang_id','pos_id'];
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
    
    // Lemma __belongs_to__ Lang
    public function lang()
    {
        return $this->belongsTo(Lang::class);
    }    
    
    public function reverseLemma()
    {
        return $this->belongsTo(ReverseLemma::class,'id');
    }    
    
    // Lemma __belongs_to__ PartOfSpeech
    // $pos_name = PartOfSpeech::find(9)->name_ru;
    public function pos()
    {
        return $this->belongsTo(PartOfSpeech::class);
    }
    
    // Lemma __has_many__ Meanings
    public function meanings()
    {
        return $this->hasMany(Meaning::class);
//        return $this->hasMany('App\Models\Dict\Meaning'); // is working too
    }
    
    // Lemma has many MeaningTexts through Meanings
    public function meaningTexts()
    {
        return $this->hasManyThrough(MeaningText::class, Meaning::class, 'lemma_id', 'meaning_id');
//        return $this->hasManyThrough('App\Models\Dict\MeaningText', 'App\Models\Dict\Meaning');
    }
/*    public function meaning_texts($ids = [])
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

    /**
     *  Lemma __has_many__ Wordforms
     * 
     * @return Builder
     */
    public function wordforms(){
        $builder = $this->belongsToMany('App\Models\Dict\Wordform','lemma_wordform');
//        $builder->getQuery()->getQuery()->distinct = TRUE;
        $builder = $builder->withPivot('gramset_id','dialect_id');
//        $builder = $builder->join('gramsets', 'gramsets.id', '=', 'lemma_wordform.gramset_id');
        return $builder;//->get();
    }
    
    /**
     *  Gets wordforms for given gramset and dialect
     * 
     * @param int $gramset_id
     * @param int $dialect_id
     * 
     * @return String or NULL
     */
    public function wordform($gramset_id,$dialect_id){
        if (!$gramset_id) {
            $gramset_id=NULL;
        }
        if (!$dialect_id) {
            $dialect_id=NULL;
        }
        $wordform_coll = $this->wordforms()
                         ->wherePivot('gramset_id',$gramset_id)
                         ->wherePivot('dialect_id',$dialect_id)
                         ->get();
        if (!$wordform_coll) {
            return NULL;
        } else {
            $wordform_arr = [];
            foreach($wordform_coll as $wordform) {
                $wordform_arr[]=$wordform->wordform;
            }
            return join(', ',$wordform_arr);
        }        
    }
    
    /**
     * Gets Builder of dialects, that has any wordforms of this lemma
     * 
     * @return Builder
     */
    public function dialects()
    {
        $locale = LaravelLocalization::getCurrentLocale();
        return $this->belongsToMany(Dialect::class, 'lemma_wordform')
//                    ->withPivot('gramset_id','wordform_id')
                    ->orderBy('name_'.$locale);
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
        $locale = LaravelLocalization::getCurrentLocale();  
        $interpretation = [];
        foreach ($this->meanings as $meaning_obj) {
            $interpretation[] = $meaning_obj->getMultilangMeaningTextsStringLocale();
        }
        
        if (!sizeof($interpretation)) {
            return NULL;
        }
        return join('; ',$interpretation);
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
            $list[] = '<a href="'.LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id).'">'.$lemma->lemma.'</a> ('.$lemma->pos->name.')';
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
        //remove all records from table lemma_wordform
        $this-> wordforms()->detach();
        $this-> phraseLemmas()->detach();
        if ($this->reverseLemma) {
            $this->reverseLemma->delete();
        }
        
        $meanings = $this->meanings;

        foreach ($meanings as $meaning) {
            DB::table('meaning_relation')
              ->where('meaning2_id',$meaning->id)->delete();

            DB::table('meaning_translation')
              ->where('meaning2_id',$meaning->id)->delete();

            DB::table('meaning_text')
              ->where('meaning_id',$meaning->id)->delete();

            $meaning_texts = $meaning->meaningTexts;
            foreach ($meaning_texts as $meaning_text) {
                $meaning_text -> delete();
            }
            $meaning -> delete();
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
     * Gets a collection of wordforms with gramsets and sorted by sequence_number of gramsets
     * @return Collection of Wordform Objects
     * 
     * ФУНКЦИИЯ НЕ ИСПОЛЬЗУЕТСЯ НИГДЕ?
     */
    public function wordformsWithGramsets(){
        $dialects = existDialects();
        $wordforms = $this->wordforms()->get();

        foreach ($wordforms as $wordform) {
            $gramset = $wordform->lemmaDialectGramset($this->id,NULL)->first(); // А МОЖЕТ МАССИВ?
            if ($gramset) {
                $wordform->gramset_id = $gramset->id;
                $wordform->gramsetString = $gramset->gramsetString();
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
        $gramsets = Gramset::getList($this->pos_id,$this->lang_id);
        if ($dialect_id) {
            $dialects = [$dialect_id => Dialect::getNameByID($dialect_id)];
        } else {
            $dialects = [NULL=>''] + Dialect::getList($this->lang_id);
        }
        
        $wordforms = NULL;
        foreach (array_keys($gramsets) as $gramset_id) {
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
                $wordforms[$gramset_id][$dialect_id] = $wordform;
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
        $inflexion = '';
        $stem = $this->lemma;
//print "\n".join("\n ",$this->uniqueWordforms())."\n";

        foreach ($this->uniqueWordforms() as $wordform) {
            while (!preg_match("/^".$stem."/", $wordform)) {
                $inflexion = mb_substr($stem, -1, 1). $inflexion;
                $stem = mb_substr($stem, 0, mb_strlen($stem)-1);
//print "\n$wordform, $stem";                
            }
        }
        return [$stem, $inflexion];
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
    
    public function createDictionaryWordforms($wordforms,$plural=NULL) {        
//dd($request->wordforms);        
        if (!isset($wordforms)) { return; }
        
        $wordform_list=preg_split("/\s*[,;\s]\s*/",$wordforms);
        if (!$wordform_list || sizeof($wordform_list)<2) { return; }
        
        $wordform_list[3] = $this->lemma;
        
        $gramsets = Gramset::dictionaryGramsets($this->pos_id, $plural);
        if ($gramsets == NULL) { return; }
        
        $dialect = Dialect::where('lang_id', $this->lang_id)->orderBy('sequence_number')->first();
        if (!$dialect) { return; }
        
        foreach ($gramsets as $key=>$gramset_id) {
            if (isset($wordform_list[$key])) {
                $this -> addWordforms($wordform_list[$key], $gramset_id, $dialect->id);
            }
        }
    }
    
    public static function parseLemmaField($lemma, $wordforms='') {
        $inflexion = NULL;
        $parsing = preg_match("/^([^\s\(]+)\s*\(([^\,\;]+)\,\s*([^\,\;]+)([\;\,]\s*([^\,\;]+))?\)/", $lemma, $regs);
        
        if ($parsing) {
            $lemma = $regs[1];
        }

        $lemma = str_replace('||','',$lemma);
        if (preg_match("/^(.+)\|(.*)$/",$lemma,$rregs)){
            $stem = $rregs[1];
            $inflexion = $rregs[2];
            $lemma = $stem.$inflexion;
        } else {
            $stem = $lemma;
        }
        if (!$parsing) {
//var_dump([$parsing, $lemma, $wordforms, $stem, $inflexion]);
            return [$lemma, $wordforms, $stem, $inflexion];
        }

        $regs[2] = str_replace('-', $stem, $regs[2]);
        $regs[3] = str_replace('-', $stem, $regs[3]);
        if (isset($regs[5])) {
            $regs[5] = str_replace('-', $stem, $regs[5]);
        }
//dd($regs);
//exit(0);        

        $wordforms = $regs[2].', '.$regs[3];
        if (isset($regs[5])) {
            $wordforms .= '; '.$regs[5];
        }
        
        return [$lemma,$wordforms, $stem, $inflexion];
    }
    
    public function storePhrase($lemmas) {
        $this->phraseLemmas()->detach();
        if ($lemmas) {
            $this->phraseLemmas()->attach($lemmas);
        }
    }
    
    public function storeReverseLemma($stem=NULL, $inflexion=NULL) {
        $reverse_lemma = ReverseLemma::find($this->id);
//dd($stem, $inflexion);
        if ($reverse_lemma) {
            $reverse = $this->reverse();
            if (!$stem && !$inflexion) {
                list($stem, $inflexion) = $this->extractStem();
            }

            $reverse_lemma->reverse_lemma = $reverse;
            $reverse_lemma->lang_id = $this->lang_id;
            $reverse_lemma->stem = $stem;
            $reverse_lemma->inflexion = $inflexion;
            
            $reverse_lemma -> save();
        } else {
            $this->createReverseLemma($stem, $inflexion);
        }
        
    }
    
    public function createReverseLemma($stem=NULL, $inflexion=NULL) {
        $reverse_lemma = $this->reverse();
//print "<p>".$reverse_lemma.', '.$this->id; 
        if (!$stem && !$inflexion) {
            list($stem, $inflexion) = $this->extractStem();
        }
        
        ReverseLemma::create([
            'id' => $this->id,
            'reverse_lemma' => $reverse_lemma,
            'lang_id' => $this->lang_id,
            'stem' => $stem,
            'inflexion' => $inflexion]);
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
        $wordform = Wordform::firstOrCreate(['wordform'=>$word]);
        if ($gramset_id) {
            $wordform->texts()->attach($text_id,['w_id'=>$w_id, 'gramset_id'=>$gramset_id]);
        }
        
        if (!sizeof($dialects)) {
            $dialects[0] = NULL;
        }
        foreach ($dialects as $dialect_id) {
            $query = "DELETE FROM lemma_wordform WHERE lemma_id=".$this->id
                . " and wordform_id=".$wordform->id." and gramset_id";
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
            $this-> wordforms()->attach($wordform->id, 
                    ['gramset_id'=>$gramset_id, 'dialect_id'=>$dialect_id]);                                    
        }
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
        $trim_word = trim($word);
        if (!$trim_word) { return;}
        
        $wordform_obj = Wordform::firstOrCreate(['wordform'=>$trim_word]);
        if ($this->isExistWordforms($gramset_id, $dialect_id, $wordform_obj->id)) {
            return;
        }
        $this-> wordforms()->attach($wordform_obj->id, ['gramset_id'=>$gramset_id, 'dialect_id'=>$dialect_id]);        
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

        $lemmas = $lemmas
                //->groupBy('lemmas.id') // отключено, неправильно показывает общее число записей
                         ->with(['meanings'=> function ($query) {
                                    $query->orderBy('meaning_n');
                                }]);
        return $lemmas;
    }
    
    public static function searchByWordform($lemmas, $wordform) {
        if (!$wordform) {
            return $lemmas;
        }
        return $lemmas->whereIn('wordform_id',function($query) use ($wordform){
                            $query->select('id')
                            ->from('wordforms')
                            ->where('wordform','like', $wordform);
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
        return $lemmas->where('lemma','like',$lemma);
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
                
                $dialect_id = (isset($dialects[$gramset_id]) && (int)$dialects[$gramset_id])
                        ? (int)$dialects[$gramset_id] : NULL;
                $this->addWordforms($wordform_texts, $gramset_id, $dialect_id);
            }
        }
//exit(0); 
    }

    /**
     * Stores relations with array of wordform (without gramsets изначально) and create Wordform if is not exists
     * 
     * @param array $wordforms array of wordforms with pairs "id of gramset - wordform"
     * @param Lemma $lemma_id object of lemma
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
    
    function getMultilangMeaningTexts() {
        $meanings = [];
        foreach ($this->meanings as $meaning_obj) {
             $meanings[] = $meaning_obj->getMultilangMeaningTextsStringLocale();
        }
        return $meanings;
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
