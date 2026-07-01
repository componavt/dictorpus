<?php

namespace App\Traits\Relations\BelongsTo;

trait Putype
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function putype()
    {
        return $this->belongsTo('App\Models\Corpus\Putype');
    }
}
