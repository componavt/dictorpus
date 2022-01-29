<?php namespace App\Traits\Relations\HasMany;

use App\Models\Corpus\SentenceFragment;

trait SentenceFragments
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fragments(){
        return $this->hasMany(SentenceFragment::class);
    }
}