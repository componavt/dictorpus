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
    public $incrementing=false;
    protected $fillable = ['id','animacy','abbr','number','reflexive', 'phonetics',
        'transitive','prontype_id','numtype_id','degree_id','advtype_id', 'comptype_id'];
    public $featuresByPOS = [1  => ['degree_id'],                   // adjective
                             2  => ['advtype_id', 'degree_id'],     // adverb
                             5  => ['animacy', 'abbr', 'number'], // noun
                             6  => ['numtype_id'],                  // numeral
                             10 => ['prontype_id', 'number'],                 // pronoun
                             11 => ['reflexive', 'transitive'],     // verb                             
                             14 => ['animacy', 'abbr', 'number'], // proper noun
                             19 => ['comptype_id'], // phrases
                            ];
    public $feas_conll_codes = [
        'animacy'    => [1 => 'Animacy=Anim',
                         0 => 'Animacy=Inan'],
        'abbr'       => [1 => 'Abbr=Yes'],
        'number'     => [1 => 'Number=Plur', 
                         2 => 'Number=Sing'],
        'reflexive'  => [1 => 'Reflex=Yes'],
        'transitive' => [1 => 'Subcat=Trans',
                         0 => 'Subcat=Intr'],
        "prontype_id" =>[1 => 'PronType=Prs',
                         2 => 'PronType=Prs|Poss=Yes',
                         3 => 'PronType=Prs|Reflex=Yes',
                         4 => 'PronType=Rcp',
                         5 => 'PronType=Ind',
                         6 => 'PronType=Dem',
                         7 => 'PronType=Int',
                         8 => 'PronType=Rel',
                         9 => 'PronType=Neg'],
        "numtype_id" => [1 => 'NumType=Num',
                         2 => 'NumType=Sets',
                         3 => 'NumType=Ord',
                         4 => 'NumType=Frac'],
        "advtype_id" => [1 => 'AdvType=Man', 
                         2 => 'AdvType=Sta',
                         3 => 'AdvType=Loc',
                         4 => '',
                         5 => 'AdvType=Deg',
                         6 => 'AdvType=Mod'],
        "degree_id"  => [1 => 'Degree=Pos',
                         2 => 'Degree=Cmp',
                         3 => 'Degree=Sup'],
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
    
    // Belongs To Relations
    public function lemma()
    {
        return $this->belongsTo(Lemma::class,'id','id');
    }    
    
    public function remove() {
        $this->delete();
    }
    
    public static function getNumberID($name) {
        if ($name == 'pl') {
            return 1;
        } elseif ($name == 'sg') {
            return 2;
        }
    }

        public function allowFeatures() {
//dd($this->lemma);        
        $pos_id = $this->lemma->pos_id;
//dd($pos_id);        
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
            } elseif ($field == 'number') {
                $features[$field] = ['title'=>'number', 'value'=>$value];
            } elseif($value===0) {
                $features[$field] = 'in'.$field;
            } elseif($value) {
                $features[$field] = $field;
            }
        }
        return $features;
    }
    
    /**
     * 
     * @param INT $id
     * @param Array $features
     */
    public static function store($id, $features) {
//dd($features);        
        $lemma_feature = LemmaFeature::find($id);
        if (!$lemma_feature) {
            $lemma_feature = LemmaFeature::create(['id'=>$id]);
        }
//dd($lemma_feature->fillable);        
        foreach ($lemma_feature->fillable as $field) {
            if ($field=='id') {
                continue;
            }
//print "<p>". $field;           
//dd($lemma_feature->isAllowFeature('comptype_id'));            
            if (isset($features[$field]) && $lemma_feature->isAllowFeature($field)) {
                $lemma_feature->$field = $features[$field];
            } else {
                $lemma_feature->$field = NULL;
            }
        }
//dd($lemma_feature);        
        $lemma_feature->save();
    }
    
    public function toCONLL() {
        $features = [];
        foreach ($this->allowFeatures() as $field) {
            $value = $this->$field;
//print "$value\n";   
            if (!$value) {
                continue;
            }
            if (isset($this->feas_conll_codes[$field])):              
                $feas = $this->feas_conll_codes[$field];
//print "code:".$feas[$value]."\n";
                if ($feas[$value]!==''):
                    $features[] = $feas[$value];
                else:
//dd($field);                    
                endif;
            endif;
        }
//dd($features);        
        return $features;
    }
}
