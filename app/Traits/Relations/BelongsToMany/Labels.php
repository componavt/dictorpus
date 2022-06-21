<?php namespace App\Traits\Relations\BelongsToMany;

use App\Models\Dict\Label;

trait Labels
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function labels()
    {
        return $this->belongsToMany(Label::class)
                ->withPivot('status');
    }
}