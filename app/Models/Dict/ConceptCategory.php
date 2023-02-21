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
    
    // Has Many Relations
    use \App\Traits\Relations\HasMany\Concepts;
    
    public function getSectionAttribute() : String
    {
        return trans("dict.concept_section_".substr($this->id, 0,1));
    }    
    
    /** Gets list dropdown form
     * 
     * @return Array [<key> => <value>,..]
     */
    public static function getList()
    {     
        $objs = self::orderBy('id')->get();
        
        $list = array();
        foreach ($objs as $row) {
            $list[$row->id] = $row->id .'. '. $row->name;
        }
        
        return $list;         
    }
}
