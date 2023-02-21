<?php namespace App\Traits\Relations\HasMany;

use App\Models\Dict\Concept;

trait Concepts
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function concepts(){
        return $this->hasMany(Concept::class);
    }
}