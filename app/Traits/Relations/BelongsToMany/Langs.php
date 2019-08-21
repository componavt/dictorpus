<?php namespace App\Traits\Relations\BelongsToMany;

use App\Models\Dict\Lang;

trait Langs
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function langs()
    {
        return $this->belongsToMany(Lang::class,'gramset_pos','gramset_id','lang_id');
    }
}