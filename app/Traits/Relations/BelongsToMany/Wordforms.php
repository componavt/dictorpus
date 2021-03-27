<?php namespace App\Traits\Relations\BelongsToMany;

trait Wordforms
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function wordforms(){
        return $this->belongsToMany('App\Models\Dict\Wordform','lemma_wordform')
                    ->withPivot('gramset_id','dialect_id', 'affix');
    }
}