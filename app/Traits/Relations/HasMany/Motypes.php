<?php namespace App\Traits\Relations\HasMany;

use App\Models\Corpus\Motype;

trait Motypes
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function motypes()
    {
        return $this->hasMany(Motype::class);
    }
}