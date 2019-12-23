<?php namespace App\Traits\Relations\BelongsTo;

trait ConceptCategory
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function conceptCategory()
    {
        return $this->belongsTo('App\Models\Dict\ConceptCategory');
    }    
}