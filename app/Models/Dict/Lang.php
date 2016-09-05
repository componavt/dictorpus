<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

class Lang extends Model
{
    public $timestamps = false;
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    public static function boot()
    {
        parent::boot();
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

    /** Gets name of this lang by code, takes into account locale.
     * 
     * @return String
     */
    public static function getNameByCode($code) : String
    {
        $lang = self::where('code',$code)->first();
        if ($lang) {
            return $lang->getNameAttribute();
        }
    }
           
    // Lang __has_many__ Lemma
    public function lemmas()
    {
        return $this->hasMany(Lemma::class);
    }

    /** Gets list of languages
     * 
     * @return Array [1=>'Vepsian',..]
     */
    public static function getList()
    {     
        $locale = LaravelLocalization::getCurrentLocale();
        
        $languages = self::orderBy('name_'.$locale)->get();
        
        $list = array();
        foreach ($languages as $row) {
            $list[$row->id] = $row->name;
        }
        
        return $list;         
    }
        
    /** Gets list of languages in the certain order: $first_lang, Russian, English, the others in alfabetic order
     * 
     * @return Array [1=>'Vepsian',..]
     */
    public static function getListWithPriority($first_lang_id)
    {     
        $locale = LaravelLocalization::getCurrentLocale();
        
        $languages = self::orderBy('name_'.$locale)->get();
        
        $list[$first_lang_id] = self::find($first_lang_id)->name;
        
        $ru_lang = self::where('code','ru')->first();
        if (!isset($list[$ru_lang->id])) {
            $list[$ru_lang->id] = $ru_lang->name;
        }
        
        $en_lang = self::where('code','en')->first();
        if (!isset($list[$en_lang->id])) {
            $list[$en_lang->id] = $en_lang->name;
        }
        
        foreach ($languages as $row) {
            if (!isset($list[$row->id])) {
                $list[$row->id] = $row->name;
            }
        }
        
        return $list;         
    }
        
    /** Gets list of languages
     * 
     * @return Array [1=>'Vepsian',..]
     */
    public static function getListWithQuantity($method_name)
    {     
        $locale = LaravelLocalization::getCurrentLocale();
        
        $languages = self::orderBy('name_'.$locale)->get();
        
        $list = array();
        foreach ($languages as $row) {
            $count=$row->$method_name()->count();
            $name = $row->name;
            if ($count) {
                $name .= " ($count)";
            }
            $list[$row->id] = $name;
        }
        
        return $list;         
    }
}
