<?php namespace App\Traits\Relations\BelongsTo;

trait GramsetCategory
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gramsetCategory()
    {
        return $this->belongsTo('App\Models\Dict\GramsetCategory');
    }    
}