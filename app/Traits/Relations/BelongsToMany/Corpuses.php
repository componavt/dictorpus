<?php namespace App\Traits\Relations\BelongsToMany;

use App\Models\Corpus\Corpus;

trait Corpuses
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function corpuses(){
        return $this->belongsToMany(Corpus::Class);
    }
    
    /**
     * Gets IDs of authors for author's form field
     *
     * @return Array
     */
    public function corpusValue():Array{
        $corpus_value = [];
        foreach ($this->corpuses as $corpus) {
            $corpus_value[] = $corpus->id;
        }
        return $corpus_value;
    }

    public function corpusesToString($link=null) {
        return $this->relationsToString('corpuses', $link);
    }
}