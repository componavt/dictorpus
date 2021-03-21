<?php namespace App\Traits\Relations\HasMany;

use App\Models\Corpus\Sentence;

trait Sentences
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sentences(){
        return $this->hasMany(Sentence::class);
    }
}