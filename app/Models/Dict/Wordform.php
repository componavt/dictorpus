<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

class Wordform extends Model
{
    public function lemmaDialectGramsets($lemma_id, $dialect_id=NULL){
        return $this->belongsToMany(Gramset::class, 'lemma_wordform')
             ->wherePivot('lemma_id', $lemma_id)
             ->wherePivot('dialect_id', $dialect_id)->get();
    }
}
