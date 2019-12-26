<?php namespace App\Traits\Relations\BelongsToMany;

use LaravelLocalization;

trait WordformDialects
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function wordformDialects(){
        $locale = LaravelLocalization::getCurrentLocale();
        return $this->belongsToMany('App\Models\Dict\Dialect', 'lemma_wordform')
//                    ->withPivot('gramset_id','wordform_id')
                    ->orderBy('name_'.$locale);
    }
    
    public function getWordformDialectIds() {
        $dialects = $this->wordformDialects()->groupBy('id')->orderBy('sequence_number')->get();
        $ids = [];
        foreach ($dialects as $dialect) {
            $ids[] = $dialect->id;
        }
        return $ids;
    }
}