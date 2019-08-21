<?php namespace App\Traits\Relations\HasMany;

use App\Models\Corpus\Corpus;

trait Corpuses
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function corpuses()
    {
        return $this->hasMany(Corpus::class);
    }
}