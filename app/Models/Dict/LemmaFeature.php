<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use DB;
use URL;
use Lang;
use LaravelLocalization;
use \Venturecraft\Revisionable\Revision;

class LemmaFeature extends Model
{
    public $timestamps = false;
    protected $fillable = ['id','animacy','abbr','plur_tan','reflexive',
        'transitive','prontype_id','numtype_id','degree_id','advtype_id'];
    public $featuresByPOS = [1  => ['degree_id'],                   // adjective
                             2  => ['advtype_id', 'degree_id'],     // adverb
                             5  => ['animacy', 'abbr', 'plur_tan'], // noun
                             6  => ['numtype_id'],                  // numeral
                             10 => ['prontype_id'],                 // pronoun
                             11 => ['reflexive', 'transitive'],     // verb                             
                             14 => ['animacy', 'abbr', 'plur_tan'], // proper noun
                            ];

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
    
    public function allowFeatures() {
//dd($this->lemma);        
        $pos_id = $this->lemma->pos_id;
        if (isset($this->featuresByPOS[$pos_id])) {
            return $this->featuresByPOS[$pos_id];
        } 
        return [];
    }

    public function isAllowFeature($feature) {
        if (in_array($feature, $this->allowFeatures())) {
            return true;
        }
        return false;
    }
    
    /**
     * is there at least one filled allowed feature
     * 
     * @return Boolean
     */
    public function isExistsFilledFeatures() : Boolean {
        foreach ($this->allowFeatures()as $field) {
            if ($this->$field) {
                return true;
            }
        }
        return false;
    }
    
    public function filledFeatures() : Array {
        $features = [];
        foreach ($this->allowFeatures() as $field) {
            if (!$this->$field) {
                continue;
            }
            $value = $this->$field;
            if (preg_match('/^(.+)_id$/',$field,$regs)) {
                $features[$field] = ['title'=>$regs[1], 'value'=>$value];
            } elseif($value===0) {
                $features[$field] = 'in'.$field;
            } elseif($value) {
                $features[$field] = $field;
            }
        }
        return $features;
    }
    
    public static function store($id, $request) {
//dd($request);        
        $lemma_feature = LemmaFeature::find($id);
        if (!$lemma_feature) {
            $lemma_feature = LemmaFeature::create(['id'=>$id]);
        }
//dd($lemma_feature->fillable);        
        foreach ($lemma_feature->fillable as $field) {
            if ($field=='id') {
                continue;
            }
//print "<p>". $request[$field];           
            if (isset($request->$field) && $lemma_feature->isAllowFeature($field)) {
                $lemma_feature->$field = $request->$field;
            } else {
                $lemma_feature->$field = NULL;
            }
        }
//dd($lemma_feature);        
        $lemma_feature->save();
    }
}
