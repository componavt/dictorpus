<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use DB;
use URL;
use LaravelLocalization;
use \Venturecraft\Revisionable\Revision;

class LemmaFeature extends Model
{
    public $timestamps = false;
    protected $fillable = ['id','animacy','abbr','plur_tan','reflexive',
        'transitive','prontype_id','numtype_id','degree_id','advtype_id'];

    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.
    protected $revisionFormattedFields = array(
//        'title'  => 'string:<strong>%s</strong>',
//        'public' => 'boolean:No|Yes',
//        'modified_at' => 'datetime:d/m/Y g:i A',
//        'deleted_at' => 'isEmpty:Active|Deleted'
        'updated_at' => 'datetime:m/d/Y g:i A'
    );
    protected $revisionFormattedFieldNames = array(
//        'title' => 'Title',
//        'small_name' => 'Nickname',
//        'deleted_at' => 'Deleted At'
    );
    
    public static function boot()
    {
        parent::boot();
    }
    
    // Lemma __belongs_to__ Lang
    public function lemma()
    {
        return $this->belongsTo(Lemma::class,'id','id');
    }    
    
    public function remove() {
        $this->delete();
    }
    
    public static function store($id, $request) {
        $lemma_feature = LemmaFeature::find($id);
        if (!$lemma_feature) {
            $lemma_feature = LemmaFeature::create(['id'=>$id]);
        }
        
        foreach ($lemma_feature->fillable as $field) {
            if ($field!='id') {
                if (isset($request->$field)) {
                    $lemma_feature->$field = $request->$field;
                } else {
                    $lemma_feature->$field = NULL;
                }
            }
        }
        $lemma_feature->save();
    }
}
