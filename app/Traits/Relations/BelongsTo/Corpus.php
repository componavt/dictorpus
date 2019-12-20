<?php namespace App\Traits\Relations\BelongsTo;

trait Corpus
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function corpus()
    {
        return $this->belongsTo("App\Models\Corpus\Corpus");
    }
}