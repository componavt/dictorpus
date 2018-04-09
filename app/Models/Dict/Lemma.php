<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use DB;
use URL;
use LaravelLocalization;

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
    
    protected $fillable = ['lemma','lang_id','pos_id','reflexive'];
    
    public static function boot()
    {
        parent::boot();
    }
    
    // Lemma __belongs_to__ Lang
    public function lang()
    {
        return $this->belongsTo(Lang::class);
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
    public function meaning_texts()
    {
        return $this->hasManyThrough('App\Models\Dict\Meaning', 'App\Models\Dict\MeaningText');
    }

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
     *  Gets wordform for given gramset and dialect
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
                                 ->first();
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
            $meaning_obj->updateTextLinks();
        }
/*        
        $lang_id = $lemma_obj->lang_id;
        $lemma_t = addcslashes($lemma_obj->lemma,"'");
        $query = "select * from words, texts "
                          . "where words.text_id = texts.id and texts.lang_id = ".$lang_id
                            . " and (word like '".$lemma_t."' OR word in "
                                . "(select wordform from wordforms, lemma_wordform "
                                 . "where wordforms.id = lemma_wordform.wordform_id "
                                   . "and lemma_id = ".$lemma_obj->id." "
                                   . "and wordform like '".$lemma_t."'))";
        $words = DB::select($query);

        foreach ($lemma_obj->meanings as $meaning) {
            $text_links = [];               
            foreach ($words as $word) {
                $relevance = 1;
                $existLink = $meaning->texts()->wherePivot('text_id',$word->text_id)
                              ->wherePivot('w_id',$word->w_id);
                // if exists links between this meaning and this word, get their relevance
                if ($existLink->count()>0) {                    
//dd($existLink->first());                    
                    $relevance = $existLink->first()->pivot->relevance;
                }
            
                // if some another meaning has positive evaluation with this sentence, 
                // it means that this meaning is not suitable for this example
                if (DB::table('meaning_text')->where('meaning_id','<>',$meaning->id)
                      ->where('text_id',$word->text_id)->where('w_id',$word->w_id)
                      ->where('relevance','>',1)->count()>0) {
                    $relevance = 0;
                }
                $text_links[] = ['text_id' => $word->text_id,
                                 'other_fields' =>
                                    ['sentence_id'=>$word->sentence_id, 
                                     'word_id'=>$word->id, 
                                     'w_id'=>$word->w_id, 
                                     'relevance'=>$relevance]                
                                ];
            }
        
            $meaning->texts()->detach();

            foreach ($text_links as $link) {
                $meaning->texts()->attach($link['text_id'],$link['other_fields']);
            }
        }
 * 
 */
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
