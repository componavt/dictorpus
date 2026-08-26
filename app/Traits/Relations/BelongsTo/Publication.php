<?php

namespace App\Traits\Relations\BelongsTo;

trait Publication
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function publication()
    {
        return $this->belongsTo('App\Models\Corpus\Publication');
    }
}
