<?php namespace App\Traits\Relations\BelongsToMany;

use App\Models\Corpus\Motive;

trait Motives
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function motives(){
        return $this->belongsToMany(Motive::class)
                    ->orderBy('code');

    }
    
    /**
     * Gets IDs of motives for motive's form field
     *
     * @return Array
     */
    public function motiveValue():Array{
        $value = [];
        if ($this->motives) {
            foreach ($this->motives as $motive) {
                $value[] = $motive->id;
            }
        }
        return $value;
    }
    
}