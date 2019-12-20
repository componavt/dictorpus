<?php namespace App\Traits\Relations\BelongsTo;

trait Source
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function source()
    {
        return $this->belongsTo('App\Models\Corpus\Source');
    }
}