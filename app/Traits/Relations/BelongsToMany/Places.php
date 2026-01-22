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
    
    /**
     * Gets IDs of places for place's form field
     *
     * @return Array
     */
    public function placeValue():Array{
        $value = [];
        if ($this->places) {
            foreach ($this->places as $place) {
                $value[] = $place->id;
            }
        }
        return $value;
    }
    
    public function placesToString($link='') {
        $out = [];
        foreach ($this->places as $place) {
            $name=$place->name;
            if ($link) {
                $name = to_link($name, $link.$place->id);
            }
            $out[] = $name;
        }
        return join(", ", $out);
    }

}