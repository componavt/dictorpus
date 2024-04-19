<?php namespace App\Traits\Relations\HasMany;

use App\Models\Dict\Meaning;

trait Meanings
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function meanings()
    {
        return $this->hasMany(Meaning::class)->orderBy('meaning_n');
//        return $this->hasMany('App\Models\Dict\Meaning'); // is working too
    }
}