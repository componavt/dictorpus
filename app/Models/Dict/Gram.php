<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

use LaravelLocalization;

class Gram extends Model
{
    public $timestamps = false;
    protected $fillable = ['gram_category_id', 'name_short_en', 'name_en', 'name_short_ru', 'name_ru', 'sequence_number', 'conll', 'unimorph'];
        
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }

    // Belongs To Relations
    use \App\Traits\Relations\BelongsTo\GramCategory;
    
    // Methods
    use \App\Traits\Methods\getNameAttribute;

    public function getShortNameAttribute() : String
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $column = "name_short_" . $locale;
        return $this->{$column};
    }

    public function getCodeAttribute() : String
    {
        $v = $this->unimorph;
        if (!$v && $this->name_en=='infinitive II') {
          return "2NFIN";  
        } elseif (!$v && $this->name_en=='infinitive III') {
          return "3NFIN";  
        }        
        return str_replace(';','_',$v);
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
    
    public static function getByCode($code)
    {
        if ($code == "2NFIN") {
            return self::whereNameEn($code, 'infinitive II')->first();
        } elseif ($code == "3NFIN") {
            return self::whereNameEn($code, 'infinitive III')->first();
        }        
        $code = str_replace('_', ';', $code);
        return self::whereUnimorph($code)->first();
         
    }
    
    public static function getNameByCode($code)
    {
        $item = self::getByCode($code);
        if ($item && isset($item->name)) {
//dd($pos->id);
            return $item->name;
        }
    }
    /** Gets ordered list of grams for the grammatical category
     * 
     * @param int $category_id
     * @return Array [1=>'номинатив',..]
     */
    public static function getList(int $category_id, $with_short_name=true)
    {
        $grams = self::getByCategory($category_id);
                
        $list = [];
        foreach ($grams as $gram) {
            if ($with_short_name) {
                $list[$gram->id] = $gram->getNameWithShort();
            } else {
                $list[$gram->id] = $gram->name;
            }
        }
        
        return $list;         
    }
    
    /**
     * Get list of grams for words in texts
     * 
     * @return Array ['падеж'=>['NOM' => 'номинатив', ...], ...]
     */
    public static function getListForCorpus() {
        $grams = [];        
        $locale = LaravelLocalization::getCurrentLocale();

        $gram_categories = GramCategory::all()->sortBy('sequence_number');
        $grams = array();
        
        foreach ($gram_categories as $gc) {         //   id is gram_category_id
            $grams[$gc->id][0] = $gc->name;
            foreach (self::getByCategory($gc->id) as $g) {
                $grams[$gc->id][1][$g->code] = $g->name;
            }
        }
        //case to beginning
        $cases = $grams[1];
        unset($grams[1]);
        return [1=>$cases]+$grams;         
    }
}
