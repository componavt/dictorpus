<?php namespace App\Traits\Relations\BelongsToMany;

use App\Models\Corpus\Cycle;

trait Cycles
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cycles(){
        return $this->belongsToMany(Cycle::class)
                ->orderBy('genre_id')->orderBy('sequence_number');
    }
    
    /**
     * Gets IDs of cycles for cycle's form field
     *
     * @return Array
     */
    public function cycleValue():Array{
        $value = [];
        if ($this->cycles) {
            foreach ($this->cycles as $cycle) {
                $value[] = $cycle->id;
            }
        }
        return $value;
    }
    
}