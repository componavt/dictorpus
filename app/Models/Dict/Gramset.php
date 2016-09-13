<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use DB;

class Gramset extends Model
{
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

    // Gramset __belongs_to__ Dialect
    public function dialect()
    {
        return $this->belongsTo(Dialect::class);
    }
    
    // Gramset __belongs_to__ Gram
    public function gramNumber()
    {
        return $this->belongsTo(Gram::class, 'gram_id_number');
    }
    
    public function gramCase()
    {
        return $this->belongsTo(Gram::class, 'gram_id_case');
    }
    
    public function gramTense()
    {
        return $this->belongsTo(Gram::class, 'gram_id_tense');
    }
    
    // Gramset __has_many__ PartOfSpeech
    public function parts_of_speech()
    {
        return $this->belongsToMany(PartOfSpeech::class,'gramset_pos','gramset_id','pos_id');
    }
     
    /** Gets concatenated list of grammatical attributes for this gramset
     * 
     * @param String $glue
     * @return String concatenated list of grammatical attributes (e.g. 'ед. ч., номинатив')
     */
    public function gramsetString(String $glue=', ') : String
    {
        $list = array();
        if ($this->gram_id_number){
            $list[] = $this->gramNumber->name_short;
        }
            
        if ($this->gram_id_case){
            $list[] = $this->gramCase->name_short;
        }
            
        if ($this->gram_id_tense){
            $list[] = $this->gramTense->name_short;
        }
            
        return join($glue, $list);
    }
    
    /** Gets ordered list of gramsets for the part of speech
     * 
     * @param int $pos_id
     * @return Array [1=>'ед. ч., номинатив',..]
     */
    public static function getList(int $pos_id)
    {
        // select id from gramsets,gramset_pos where gramset_pos.gramset_id=gramsets.id and gramset_pos.pos_id=5 group by id order by sequence_number;

        $gramsets = DB::table('gramsets')
                      ->join('gramset_pos', 'gramsets.id', '=', 'gramset_pos.gramset_id')
                      ->select('gramsets.id')
                      ->where('gramset_pos.pos_id',$pos_id)
                      ->groupBy('gramsets.id')
                      ->orderBy('sequence_number')
                      ->get();
        
        $list = array();
        foreach ($gramsets as $row) {
            $gramset = self::find($row->id);
            $list[$row->id] = $gramset->gramsetString();
        }
        
        return $list;         
    }

}
