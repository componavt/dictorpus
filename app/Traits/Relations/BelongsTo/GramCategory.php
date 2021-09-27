<?php namespace App\Traits\Relations\BelongsTo;

trait GramCategory
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gramCategory()
    {
        return $this->belongsTo('App\Models\Dict\GramCategory');
    }    
}