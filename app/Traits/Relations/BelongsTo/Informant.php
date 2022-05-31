<?php namespace App\Traits\Relations\BelongsTo;

trait Informant
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function informant()
    {
        return $this->belongsTo("App\Models\Corpus\Informant");
    }
}