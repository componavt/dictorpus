<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

use LaravelLocalization;

class GramsetCategory extends Model
{
    public $timestamps = false;
    protected $fillable = ['name_en', 'name_ru', 'pos_category_id', 'sequence_number'];
    
    public function identifiableName()
    {
        return $this->name;
    }    

    /** Gets name of this lang, takes into account locale.
     * 
     * @return String
     */
    public function getNameAttribute() : String
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $column = "name_" . $locale;
        return $this->{$column};
    }

    /** Gets list of languages
     * 
     * @return Array [1=>'Vepsian',..]
     */
    public static function getList($pos_category_id=NULL)
    {     
        $locale = LaravelLocalization::getCurrentLocale();
        
        $languages = self::orderBy('sequence_number');
        if ($pos_category_id) {
            $languages->where('pos_category_id',$pos_category_id);
        }
                
        $list = array();
        foreach ($languages->get() as $row) {
            $list[$row->id] = $row->name;
        }
        
        return $list;         
    }
        
}
