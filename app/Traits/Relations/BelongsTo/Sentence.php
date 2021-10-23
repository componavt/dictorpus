<?php namespace App\Traits\Relations\BelongsTo;

trait Sentence
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sentence()
    {
        return $this->belongsTo('App\Models\Corpus\Sentence');
    }
}