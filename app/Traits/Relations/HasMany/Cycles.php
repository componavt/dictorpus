<?php namespace App\Traits\Relations\HasMany;

use App\Models\Corpus\Cycle;

trait Cycles
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cycles()
    {
        return $this->hasMany(Cycle::class);
    }
}