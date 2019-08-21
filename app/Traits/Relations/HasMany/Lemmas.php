<?php namespace App\Traits\Relations\HasMany;

use App\Models\Dict\Lemma;

trait Lemmas
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function lemmas()
    {
        return $this->hasMany(Lemma::class);
    }
}