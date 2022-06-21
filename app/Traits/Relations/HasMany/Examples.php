<?php namespace App\Traits\Relations\HasMany;

use App\Models\Dict\Example;

trait Examples
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function examples()
    {
        return $this->hasMany(Example::class);
    }
}