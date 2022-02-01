<?php namespace App\Traits\Relations\BelongsTo;

trait Genre
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function genre()
    {
        return $this->belongsTo("App\Models\Corpus\Genre");
    }
}