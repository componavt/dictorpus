<?php namespace App\Traits\Relations\HasMany;

use App\Models\Dict\Dialect;

trait Dialects
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dialects()
    {
        return $this->hasMany(Dialect::class);
    }
    
    public function countDialects() {
        return $this->dialects()->count();
    }
}