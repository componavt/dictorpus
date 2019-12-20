<?php namespace App\Traits\Relations\BelongsTo;

trait Transtext
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transtext()
    {
        return $this->belongsTo('App\Models\Corpus\Transtext');
    }
}