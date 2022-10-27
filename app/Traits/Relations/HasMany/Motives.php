<?php namespace App\Traits\Relations\HasMany;

use App\Models\Corpus\Motive;

trait Motives
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function motives()
    {
        return $this->hasMany(Motive::class);
    }
}