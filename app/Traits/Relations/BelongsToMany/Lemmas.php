<?php namespace App\Traits\Relations\BelongsToMany;

use App\Models\Dict\Lemma;

trait Lemmas
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function lemmas(){
        return $this->belongsToMany(Lemma::class);
    }
}