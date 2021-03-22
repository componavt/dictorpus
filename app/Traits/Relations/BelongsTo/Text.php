<?php namespace App\Traits\Relations\BelongsTo;

trait Text
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function text()
    {
        return $this->belongsTo('App\Models\Corpus\Text');
    }
}