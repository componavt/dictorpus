<?php namespace App\Traits\Relations\BelongsToMany;

use App\Models\Dict\Meaning;

trait Meanings
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function meanings(){
        $builder = $this->belongsToMany(Meaning::class)
//                 -> withPivot('w_id')
                 -> withPivot('relevance');
        return $builder;
    }
}