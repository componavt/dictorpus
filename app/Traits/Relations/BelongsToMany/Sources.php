<?php namespace App\Traits\Relations\BelongsToMany;

trait Sources
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sources(){
        $builder = $this->belongsToMany('App\Models\Corpus\Source');
        return $builder;
    }
}