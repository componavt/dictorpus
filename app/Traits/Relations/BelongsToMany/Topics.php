<?php namespace App\Traits\Relations\BelongsToMany;

use App\Models\Corpus\Topic;

trait Topics
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function topics(){
        return $this->belongsToMany(Topic::class)//->withPivot('sequence_number')
                    ->orderBy('topics.sequence_number');
    }
    
    /**
     * Gets IDs of plots for plot's form field
     *
     * @return Array
     */
    public function topicValue():Array{
        return $this->topics()->pluck('id')->toArray();
/*        $value = [];
        if ($this->topics) {
            foreach ($this->topics as $topic) {
                $value[] = $topic->id;
            }
        }
        return $value;*/
    }
    
}