<?php namespace App\Traits\Relations\BelongsTo;

trait Meaning
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function meaning()
    {
        return $this->belongsTo('App\Models\Dict\Meaning');
    }
}