<?php namespace App\Traits\Relations\HasMany;

use App\Models\Dict\ReverseLemma;

trait ReverseLemmas
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reverseLemmas()
    {
        return $this->hasMany(ReverseLemma::class);
    }
}