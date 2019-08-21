<?php namespace App\Traits\Relations\BelongsToMany;

use App\Models\Dict\PartOfSpeech;

trait PartsOfSpeech
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function parts_of_speech()
    {
        return $this->belongsToMany(PartOfSpeech::class,'gramset_pos','gramset_id','pos_id');
    }
}