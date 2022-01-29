<?php namespace App\Traits\Relations\HasMany;

use App\Models\Corpus\SentenceTranslation;

trait SentenceTranslations
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations(){
        return $this->hasMany(SentenceTranslation::class);
    }
}