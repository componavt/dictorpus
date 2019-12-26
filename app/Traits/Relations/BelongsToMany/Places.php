<?php namespace App\Traits\Relations\BelongsToMany;

use LaravelLocalization;
use App\Models\Corpus\Place;

trait Places
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function places(){
        $locale = LaravelLocalization::getCurrentLocale();
        return $this->belongsToMany(Place::class)
                    ->orderBy('name_'.$locale);
    }    
}