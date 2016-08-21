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

    /** Gets gramset by lemma, dialect (if presented) and wordform.
     * 
     * @param int $lemma_id
     * @param int $dialect_id
     * @return Gramset
     */
    public function lemmaDialectGramset($lemma_id, $dialect_id=NULL)
    {
        return $this->belongsToMany(Gramset::class, 'lemma_wordform')
             ->wherePivot('lemma_id', $lemma_id)
             ->wherePivot('dialect_id', $dialect_id)->first();
    }        
}
