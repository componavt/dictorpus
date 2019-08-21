<?php namespace App\Traits\Relations\HasMany;

use App\Models\Dict\LemmaBase;

trait Bases
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bases()
    {
        return $this->hasMany(LemmaBase::class);
    }
}