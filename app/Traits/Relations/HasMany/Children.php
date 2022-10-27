<?php namespace App\Traits\Relations\HasMany;

trait Children
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(){
        return $this->hasMany(self::class);
    }
}