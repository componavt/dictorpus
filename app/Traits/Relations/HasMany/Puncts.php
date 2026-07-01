<?php

namespace App\Traits\Relations\HasMany;

use App\Models\Corpus\Punct;

trait Puncts
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function puncts()
    {
        return $this->hasMany(Punct::class);
    }
}
