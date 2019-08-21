<?php namespace App\Traits\Relations\HasMany;

use App\Models\Dict\MeaningText;

trait MeaningTexts
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function meaningTexts()
    {
        return $this->hasMany(MeaningText::class);
    }
}