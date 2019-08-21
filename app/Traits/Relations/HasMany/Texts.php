<?php namespace App\Traits\Relations\HasMany;

use App\Models\Corpus\Text;

trait Texts
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function texts()
    {
        return $this->hasMany(Text::class);
    }
}