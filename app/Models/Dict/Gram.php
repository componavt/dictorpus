<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

use LaravelLocalization;

class Gram extends Model
{
    public $timestamps = false;
    protected $fillable = ['gram_category_id', 'name_short_en', 'name_en', 'name_short_ru', 'name_ru', 'sequence_number', 'conll'];
        
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }

    /** Gets name of this grammatical attribute, takes into account locale.
     * 
     * @return String
     */
    public function getNameAttribute() : String
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $column = "name_" . $locale;
        return $this->{$column};
    }

    /** Gets short name of this grammatical attribute, takes into account locale.
     * 
     * @return String
     */
    public function getNameShortAttribute() : String
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $column = "name_short_" . $locale;
        if (!$this->{$column}) {
            $column = "name_" . $locale;
        }
        return $this->{$column};
    }

    /** Gets name of this grammatical attribute with short name, takes into account locale.
     * 
     * @return String
     */
    public function getNameWithShort() : String
    {
        $name = $this->name;
        $locale = LaravelLocalization::getCurrentLocale();
        $short_name_column = 'name_short_'. $locale;
        if ($this->{$short_name_column}) {
            $name .= ' ('. $this->{$short_name_column} . ')';
        }    
        return $name;
    }
    
    
    /** Gets all grams for given category sorted, 
     * for example objects "sg", "pl" ($category_id is 2), 
     * or case objects: "nominative", "genititive", ... (when ($category_id is 1).
     * 
     * @param int $category_id ID of category of grams
     * 
     * @return \Illuminate\Http\Response
     */
    public static function getByCategory($category_id)
    {
        return self::where('gram_category_id',$category_id)->orderBy('sequence_number')->get();
         
    }
    
    /** Gets ordered list of grams for the grammatical category
     * 
     * @param int $category_id
     * @return Array [1=>'номинатив',..]
     */
    public static function getList(int $category_id)
    {
        $grams = self::getByCategory($category_id);
                
        $list = [];
        foreach ($grams as $gram) {
            $list[$gram->id] = $gram->getNameWithShort();
        }
        
        return $list;         
    }
}
