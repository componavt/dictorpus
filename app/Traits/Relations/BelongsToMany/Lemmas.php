<?php namespace App\Traits\Relations\BelongsToMany;

use App\Models\Dict\Lemma;

trait Lemmas
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function lemmas($pos_id='', $lang_id=''){
        $builder = $this->belongsToMany(Lemma::class,'lemma_wordform');
        if ($pos_id) {
            $builder = $builder->whereIn('lemma_id',function($query) use ($pos_id){
                                $query->select('id')
                                ->from(with(new Lemma)->getTable())
                                ->where('pos_id', $pos_id);
                            });
        }
        if ($lang_id) {
            $builder = $builder->whereIn('lemma_id',function($query) use ($lang_id){
                                $query->select('id')
                                ->from(with(new Lemma)->getTable())
                                ->where('lang_id', $lang_id);
                            });
        }
//dd($builder->toSql());        
        return $builder;
    }
}