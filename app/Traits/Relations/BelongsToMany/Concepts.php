<?php namespace App\Traits\Relations\BelongsToMany;

use App\Models\Dict\Concept;

trait Concepts
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function concepts(){
        return $this->belongsToMany(Concept::Class);
    }
}