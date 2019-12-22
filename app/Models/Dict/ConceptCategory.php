<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

class ConceptCategory extends Model
{
    public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'name_en', 'name_ru'];
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }

    use \App\Traits\Methods\getNameAttribute;
    
    /** Gets list of relations
     * 
     * @return Array ['A11'=>'Небо; небесные тела',..]
     */
    public static function getList()
    {     
        $relations = self::orderBy('id')->get();
        
        $list = array();
        foreach ($relations as $row) {
            $list[$row->id] = $row->name;
        }
        
        return $list;         
    }
    
}
