<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

class Relation extends Model
{
    public $timestamps = false;
    protected $fillable = ['name_en', 'name_ru', 'reverse_relation_id', 'sequence_number'];
    const SynonymID = 7;


    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }

    /** Gets name of this relation, takes into account locale.
     * 
     * @return String
     */
    public function getNameAttribute() : String
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $column = "name_" . $locale;
        $name = $this->{$column};
        
        if (!$name && $locale!='ru') {
            $name = $this->name_ru;
        }
        
        return $name;
    }
    
    /** Gets reverse relation
     * 
     * Relation belongs_to Relation
     * 
     * @return Relationship, Query Builder
     */
    public function reverseRelation()
    {
        return $this->belongsTo(Relation::class,'reverse_relation_id');
/*        $reverse_relation_id = $this->reverse_relation_id;
        if (!$reverse_relation_id) {
            return false;
        }
        return self::find($reverse_relation_id);*/
        
    } 
    
    // Relation __has_many__ Meanings
    public function meanings(){
        return $this->belongsToMany(Meaning::class, 'meaning_relation', 'meaning1_id')
                    ->withPivot('meaning2_id');
    }
    
    /** Gets list of relations
     * 
     * @return Array [1=>'antonyms',..]
     */
    public static function getList()
    {     
        $relations = self::orderBy('sequence_number')->get();
        
        $list = array();
        foreach ($relations as $row) {
            $list[$row->id] = $row->name;
        }
        
        return $list;         
    }
    
}
