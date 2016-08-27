<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

class LemmaWordform extends Model
{
    protected $table = 'lemma_wordform';
    
    public $timestamps = false;
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    public static function boot()
    {
        parent::boot();
    }
/*    
    public function wordforms(){
        $builder = $this->belongsToMany('App\Models\Dict\Wordform','lemma_wordform',);
//        $builder->getQuery()->getQuery()->distinct = TRUE;
        return $builder;//->get();
    }
    
 * 
 */
}
