<?php namespace App\Traits\Relations\HasMany;

use App\Models\Corpus\Genre;

trait Genres
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function genres()
    {
        return $this->hasMany(Genre::class);
    }
}