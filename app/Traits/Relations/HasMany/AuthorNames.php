<?php namespace App\Traits\Relations\HasMany;

use App\Models\Corpus\AuthorName;

trait AuthorNames
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function authorNames()
    {
        return $this->hasMany(AuthorName::class);
    }
}