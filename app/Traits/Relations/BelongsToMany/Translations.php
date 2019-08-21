<?php namespace App\Traits\Relations\BelongsToMany;

use App\Models\Dict\Lang;

trait Translations
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function translations(){
        return $this->belongsToMany(Lang::class,'meaning_translation','meaning1_id','lang_id')
                    ->withPivot('meaning2_id');
    }
}