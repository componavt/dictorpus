<?php

namespace App\Traits\Relations\HasMany;

use App\Models\Corpus\Pubpart;

trait Pubparts
{
    public function pubparts()
    {
        return $this->hasMany(Pubpart::class)
            ->orderBy('sequence_number');
    }
}
