<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

//use LaravelLocalization;

class GramCategory extends Model
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
    
    // Methods
    use \App\Traits\Methods\getNameAttribute;
    
    /** Gets list of categories
     * 
     * @return Array [1=>'case',..]
     */
    public static function getList()
    {     
        $categories = self::orderBy('id')->get();
        
        $list = array();
        foreach ($categories as $row) {
            $list[$row->id] = $row->name;
        }
        
        return $list;         
    }
    
    /** Gets list of name_en categories for field names of grams
     * 
     * @return Array ['case',..]
     */
    public static function getNames()
    {     
        $categories = self::orderBy('sequence_number')->get();
        
        $list = array();
        foreach ($categories as $row) {
            $list[] = $row->name_en;
        }
        
        return $list;         
    }
    
    public static function getIDByName($name) {
        $category = self::where('name_en', $name)->first();
        if (!$category) {
            return null;
        }
        return $category->id;
    }
}
