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
        return $this->belongsToMany(Gramset::class, 'lemma_wordform')
             ->wherePivot('lemma_id', $lemma_id)
             ->wherePivot('dialect_id', $dialect_id)->first();
    } 
    
    /**
     * Stores relations with array of wordform (with gramsets) and create Wordform if is not exists
     * 
     * @param array $wordforms array of wordforms with pairs "id of gramset - wordform"
     * @param Lemma $lemma object of lemma
     * 
     * @return NULL
     */
    public static function storeLemmaWordformGramsets($wordforms, $lemma)
    {
        if(!$wordforms || !is_array($wordforms)) {
            return;
        }
        foreach($wordforms as $gramset_id=>$wordform_text) {
            if ($wordform_text) {
                $wordform_obj = self::firstOrCreate(['wordform'=>$wordform_text]);
                
                if (DB::table('lemma_wordform')->where('lemma_id',$lemma->id)
                                               ->where('wordform_id',$wordform_obj->id)
                                               ->where('gramset_id',$gramset_id)
                                               ->count() == 0) {
                    $lemma-> wordforms()->attach($wordform_obj->id, ['gramset_id'=>$gramset_id, 'dialect_id'=>NULL]);
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
    public static function storeLemmaWordformsEmpty($wordforms, $lemma)
    {
        if(!$wordforms || !is_array($wordforms)) {
            return;
        }
        foreach($wordforms as $wordform_info) {
            if ($wordform_info['wordform']) {
                $wordform_obj = Wordform::firstOrCreate(['wordform'=>$wordform_info['wordform']]);
//                    if (!$lemma-> wordforms->has('id', '=', $wordform_obj->id)) {
                if (DB::table('lemma_wordform')->where('lemma_id',$lemma->id)
                                               ->where('wordform_id',$wordform_obj->id)
                                               ->where('gramset_id',$wordform_info['gramset'])
                                               ->count() == 0) {
                    $lemma-> wordforms()->attach($wordform_obj->id, ['gramset_id'=>$wordform_info['gramset'], 'dialect_id'=>'NULL']);
                }
            }
        }
    }
}
