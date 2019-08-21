<?php namespace App\Traits\Relations\BelongsTo;

trait Dialect
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dialect()
    {
        return $this->belongsTo('App\Models\Dict\Dialect');
    }    
}