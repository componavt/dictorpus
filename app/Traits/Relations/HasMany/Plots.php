<?php namespace App\Traits\Relations\HasMany;

use App\Models\Corpus\Plot;

trait Plots
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function plots()
    {
        return $this->hasMany(Plot::class);
    }
}