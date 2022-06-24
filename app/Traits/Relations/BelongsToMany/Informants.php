<?php namespace App\Traits\Relations\BelongsToMany;

use App\Models\Corpus\Informant;

trait Informants
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function informants(){
        return $this->belongsToMany(Informant::class);
    }
}