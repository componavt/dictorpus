<?php namespace App\Traits\Relations\BelongsToMany;

use LaravelLocalization;

trait DialectsFromWordforms
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function dialects(){
        $locale = LaravelLocalization::getCurrentLocale();
        return $this->belongsToMany('App\Models\Dict\Dialect', 'lemma_wordform')
//                    ->withPivot('gramset_id','wordform_id')
                    ->orderBy('name_'.$locale);
    }
    
    public function getDialectIds() {
        $dialects = $this->dialects()->groupBy('id')->orderBy('sequence_number')->get();
        $ids = [];
        foreach ($dialects as $dialect) {
            $ids[] = $dialect->id;
        }
        return $ids;
    }
}