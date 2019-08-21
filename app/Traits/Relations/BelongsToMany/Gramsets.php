<?php namespace App\Traits\Relations\BelongsToMany;

use App\Models\Dict\Gramset;
// Use for Lang

trait Gramsets
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function gramsets()
    {
        return $this->belongsToMany(Gramset::class,'gramset_pos','lang_id','gramset_id');
    }
}