<?php namespace App\Traits\Relations\BelongsTo;

trait ReverseLemma
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reverseLemma()
    {
        return $this->belongsTo('App\Models\Dict\ReverseLemma','id');
    }    
}