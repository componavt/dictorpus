<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use DB;

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

    /**
     * Stores relations with array of wordform (with gramsets) and create Wordform if is not exists
     * 
     * @param array $wordforms array of wordforms with pairs "id of gramset - wordform",
     *                         f.e. [<gramset_id1> => [<dialect_id1> => <wordform1>, ...], ..] ]
     * @param Lemma $lemma object of lemma
     * @param array $dialects array of dialects with pairs gramset - dialect
     *                         f.e. [<gramset_id1> => [<dialect_id1>, ...], ..] ]
     *                        is neccessary for changing dialect of wordform
     * 
     * @return NULL
     */
    public static function storeLemmaWordformGramsets($wordforms, $lemma, $dialects)
    {
        if(!$wordforms || !is_array($wordforms)) {
            return;
        }
//print "<pre>";
  //      var_dump($dialects);       

        foreach($wordforms as $gramset_id=>$wordform_dialect) {
            if (!(int)$gramset_id) {
                $gramset_id = NULL;
            }
            foreach ($wordform_dialect as $old_dialect_id => $wordform_texts) {
                if (!(int)$old_dialect_id) {
                    $old_dialect_id = NULL;
                }
                $lemma-> wordforms()
                      ->wherePivot('gramset_id',$gramset_id)
                      ->wherePivot('dialect_id',$old_dialect_id)
                      ->detach();
                if (isset($dialects[$gramset_id]) && (int)$dialects[$gramset_id]) {
                    $dialect_id = (int)$dialects[$gramset_id];
                } else {
                    $dialect_id = NULL;
                }
    //print "<br>".$lemma->id." = $dialect_id";  
                foreach ($wordform_texts as $wordform_text) {
                    if ($wordform_text) {
                        foreach (preg_split("/\//",$wordform_text) as $word) {
                            $wordform_obj = self::firstOrCreate(['wordform'=>trim($word)]);
        //print "<br>". $wordform_obj->id ." = $wordform_text = $gramset_id = $dialect_id";  
                            $exist_wordforms = $lemma-> wordforms()
                                                     ->wherePivot('gramset_id',$gramset_id)
                                                     ->wherePivot('dialect_id',$dialect_id)
                                                     ->wherePivot('wordform_id',$wordform_obj->id);
                            if (!$exist_wordforms->count()) {
                                $lemma-> wordforms()->attach($wordform_obj->id, ['gramset_id'=>$gramset_id, 'dialect_id'=>$dialect_id]);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Stores relations with array of wordform (without gramsets изначально) and create Wordform if is not exists
     * 
     * @param array $wordforms array of wordforms with pairs "id of gramset - wordform"
     * @param Lemma $lemma_id object of lemma
     * 
     * @return NULL
     */
    public static function storeLemmaWordformsEmpty($wordforms, $lemma, $dialect_id='')
    {
        if(!$wordforms || !is_array($wordforms)) {
            return;
        }
//dd($wordforms);        
        foreach($wordforms as $wordform_info) {
            $wordform_info['wordform'] = trim($wordform_info['wordform']);
            if ($wordform_info['wordform']) {
                $wordform_obj = Wordform::firstOrCreate(['wordform'=>$wordform_info['wordform']]);
//                    if (!$lemma-> wordforms->has('id', '=', $wordform_obj->id)) {
                if (DB::table('lemma_wordform')->where('lemma_id',$lemma->id)
                                               ->where('wordform_id',$wordform_obj->id)
                                               ->where('gramset_id',$wordform_info['gramset'])
                                               ->count() == 0) {
                    if (!(int)$wordform_info['gramset']) {
                        $wordform_info['gramset'] = NULL;
                    }
                    if (!(int)$wordform_info['dialect']) {
                        if ((int)$dialect_id) {
                            $wordform_info['dialect'] = (int)$dialect_id;
                        } else {
                            $wordform_info['dialect'] = NULL;
                        }
                    } 
//dd($wordform_info['gramset']);
                    $lemma-> wordforms()->attach($wordform_obj->id, 
                            ['gramset_id'=>$wordform_info['gramset'], 'dialect_id'=>$wordform_info['dialect']]);
//print "<P>". $wordform_obj->id.",". $wordform_info['gramset'];
                }
            }
        }
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
            $query = "select * from words, texts "
                              . "where words.text_id = texts.id and texts.lang_id = ".$lang_id
                                . " and word like '".$word."'";
            $meaning->updateTextLinksForQuery($query);
        }
    }
    
}
