<?php namespace App\Traits\Relations\BelongsToMany;

use LaravelLocalization;

trait Dialects
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
}