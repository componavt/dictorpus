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
    public static function selectWhereLang(int $lang_id) {
        return DB::table('lemma_wordform')
                     ->whereIn('lemma_id',function($q) use ($lang_id){
                         $q->select('id')->from('lemmas')
                           ->where('lang_id', $lang_id);
                     });        
    }
    
    public static function storeByPrediction(string $prediction, string $interpretation, int $lang_id) {
        list($lemma, $meaning_or_pos, $gramset_id) = preg_split("/\_/", $prediction);
        if (is_int($lemma)) {
            $lemma_obj = Lemma::find($lemma);
            if (!$lemma_obj) {
                return [null, null, null];
            }
            return [$lemma_obj, $meaning_or_pos, $gramset_id];
        }
        $lemma_obj = Lemma::store($lemma, $meaning_or_pos, $lang_id);
        if (!$lemma_obj) {
            return [null, null, null];
        }
        $meaning_obj=Meaning::storeLemmaMeaning($lemma_obj->id, 1, [2=>$interpretation]);
        return [$lemma_obj, $meaning_obj->id, $gramset_id];
    }
}
