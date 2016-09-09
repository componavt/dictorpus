<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

use App\Models\Dict\Lang;
use App\Models\Corpus\Text;

class Corpus extends Model
{
    public $timestamps = false;
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    public static function boot()
    {
        parent::boot();
    }

    /** Gets name of this corpus, takes into account locale.
     * 
     * @return String
     */
    public function getNameAttribute() : String
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $column = "name_" . $locale;
        return $this->{$column};
    }
    
    /** Gets lang, takes into account locale.
     * 
     * Corpus belongs_to Lang
     * 
     * @return Relationship, Query Builder
     */
    public function lang()
    {
        return $this->belongsTo(Lang::class);
    }    
    
    // Corpus __has_many__ Texts
    public function texts()
    {
        return $this->hasMany(Text::class);
    }

        
    /** Gets list of languages
     * 
     * @return Array [1=>'Dialectal texts',..]
     */
    public static function getList()
    {     
        $locale = LaravelLocalization::getCurrentLocale();
        
        $corpuses = self::orderBy('name_'.$locale)->get();
        
        $list = array();
        foreach ($corpuses as $row) {
            $list[$row->id] = $row->name;
        }
        
        return $list;         
    }
    /** Gets list of corpuses
     * 
     * @return Array [1=>'Dialectal texts (199)',..]
     */
    public static function getListWithQuantity($method_name)
    {     
        $locale = LaravelLocalization::getCurrentLocale();
        
        $corpuses = self::orderBy('name_'.$locale)->get();
        
        $list = array();
        foreach ($corpuses as $row) {
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
