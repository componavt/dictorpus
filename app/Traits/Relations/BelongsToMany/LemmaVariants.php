<?php namespace App\Traits\Relations\BelongsToMany;

use App\Models\Dict\Lemma;

trait LemmaVariants
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function variants(){
        return $this->belongsToMany(Lemma::class,'lemma_variants','lemma1_id','lemma2_id');
    }
}