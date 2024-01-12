<?php namespace App\Traits\Relations\BelongsToMany;

use App\Models\Corpus\Genre;

trait Genres
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function genres(){
        return $this->belongsToMany(Genre::class);
    }
    
    /**
     * Gets IDs of genres for genre's form field
     *
     * @return Array
     */
    public function genreValue():Array{
        $value = [];
        if ($this->genres) {
            foreach ($this->genres as $genre) {
                $value[] = $genre->id;
            }
        }
        return $value;
    }
    
    public function genresToString($link=null) {
        return $this->relationsToString('genres', $link);
    }
    
}