<?php namespace App\Traits\Relations\HasMany;

use App\Models\Corpus\Monument;

trait Monuments
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function monuments()
    {
        return $this->hasMany(Monument::class);
    }
}