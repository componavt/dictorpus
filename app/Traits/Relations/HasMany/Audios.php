<?php namespace App\Traits\Relations\HasMany;

use App\Models\Dict\Audio;

trait Audios
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function audios(){
        return $this->hasMany(Audio::class);
    }
}