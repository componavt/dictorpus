<?php namespace App\Traits\Relations\BelongsToMany;

use App\Models\Corpus\Plot;

trait Plots
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function plots(){
        return $this->belongsToMany(Plot::class)
                    ->orderBy('genre_id')->orderBy('sequence_number');

    }
    
    /**
     * Gets IDs of plots for plot's form field
     *
     * @return Array
     */
    public function plotValue():Array{
        $value = [];
        if ($this->plots) {
            foreach ($this->plots as $plot) {
                $value[] = $plot->id;
            }
        }
        return $value;
    }
    
}