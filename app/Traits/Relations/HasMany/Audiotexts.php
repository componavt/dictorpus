<?php namespace App\Traits\Relations\HasMany;

use App\Models\Corpus\Audiotext;

trait Audiotexts
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function audiotexts(){
        return $this->hasMany(Audiotext::class);
    }
}