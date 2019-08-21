<?php namespace App\Traits\Relations\BelongsTo;

trait Lemma
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lemma()
    {
        return $this->belongsTo('App\Models\Dict\Lemma');
    }
}