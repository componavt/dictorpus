<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

//use App\Library\Grammatic;

use DB;

class LemmaWordform extends Model
{
    protected $table = 'lemma_wordform';
    
    public $timestamps = false;
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

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
    public static function selectWhereLang($lang_id) {
        return DB::table('lemma_wordform')
                     ->whereIn('lemma_id',function($q) use ($lang_id){
                         $q->select('id')->from('lemmas')
                           ->where('lang_id', $lang_id);
                     });        
    }
}
