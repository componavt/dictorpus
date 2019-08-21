<?php namespace App\Traits\Relations\BelongsToMany;

trait Texts
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function texts(){
        $builder = $this->belongsToMany('App\Models\Corpus\Text');
        return $builder;
    }
}