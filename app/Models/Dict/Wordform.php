<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

class Wordform extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['wordform'];

    use \Venturecraft\Revisionable\RevisionableTrait;

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
}
