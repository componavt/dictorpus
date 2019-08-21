<?php namespace App\Traits\Relations\BelongsToMany;

use App\Models\Dict\Relation;

trait MeaningRelations
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function meaningRelations(){
        return $this->belongsToMany(Relation::class,'meaning_relation','meaning1_id','relation_id')
                    ->withPivot('meaning2_id');
    }
}