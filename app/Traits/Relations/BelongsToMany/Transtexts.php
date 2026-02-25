<?php namespace App\Traits\Relations\BelongsToMany;

trait Transtexts
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function transtexts(){
        $builder = $this->belongsToMany('App\Models\Corpus\Transtext');
        return $builder;
    }
}