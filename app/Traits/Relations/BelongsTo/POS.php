<?php namespace App\Traits\Relations\BelongsTo;

use App\Models\Dict\PartOfSpeech;

trait POS
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pos()
    {
        return $this->belongsTo(PartOfSpeech::class);
    }
}