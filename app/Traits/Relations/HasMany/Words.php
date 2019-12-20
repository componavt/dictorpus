<?php namespace App\Traits\Relations\HasMany;

use App\Models\Corpus\Word;

trait Words
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function words(){
        return $this->hasMany(Word::class);
    }
}