<?php namespace App\Traits\Relations\BelongsToMany;

use App\Models\Corpus\Author;

trait Authors
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function authors(){
        return $this->belongsToMany(Author::Class);
    }
    
    /**
     * Gets IDs of authors for author's form field
     *
     * @return Array
     */
    public function authorValue():Array{
        $author_value = [];
        foreach ($this->authors as $author) {
            $author_value[] = $author->id;
        }
        return $author_value;
    }

}