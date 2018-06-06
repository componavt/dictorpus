<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

use App\Models\Corpus\Corpus;
use App\Models\Dict\Lemma;
use App\Models\Corpus\Text;

class Lang extends Model
{
    public $timestamps = false;
    protected $fillable = ['name_en', 'name_ru', 'code'];
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }
    
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

    /** Gets ID of this lang by code, takes into account locale.
     * 
     * @return int
     */
    public static function getIDByCode($code) : Int
    {
        $lang = self::where('code',$code)->first();
        if ($lang) {
            return $lang->id;
        }
    }
           
    /** Gets name of this lang by code, takes into account locale.
     * 
     * @return String
     */
    public static function getNameByCode($code) : String
    {
        $lang = self::where('code',$code)->first();
        if ($lang) {
            return $lang->name;
        }
    }
           
    // Lang __has_many__ Lemma
    public function lemmas()
    {
        return $this->hasMany(Lemma::class);
    }

    // Lang __has_many__ Corpus
    public function corpuses()
    {
        return $this->hasMany(Corpus::class);
    }

    // Lang __has_many__ Texts
    public function texts()
    {
        return $this->hasMany(Text::class);
    }

    // Lang __has_many__ Dialects
    public function dialects()
    {
        return $this->hasMany(Dialect::class);
    }

    // Lang __has_many__ Gramset
    public function gramsets()
    {
        return $this->belongsToMany(Gramset::class,'gramset_pos','lang_id','gramset_id');
    }
     
    /** Gets list of languages
     * 
     * @return Array [1=>'Vepsian',..]
     */
    public static function getList($without=[])
    {     
        $locale = LaravelLocalization::getCurrentLocale();
        
        $languages = self::orderBy('name_'.$locale)->get();
        
        $list = array();
        foreach ($languages as $row) {
            if (!in_array($row->id, $without)) {
                $list[$row->id] = $row->name;
            }
        }
        
        return $list;         
    }
        
    /** Gets list of languages in the certain order: $first_lang, Russian, English, the others in alfabetic order
     * 
     * @return Array [1=>'Vepsian',..]
     */
    public static function getListWithPriority($first_lang_id='')
    {     
        $locale = LaravelLocalization::getCurrentLocale();
        
        if (!$first_lang_id) {
            $first_lang_id = self::getIDByCode($locale);
        }
        
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

    /** Takes data from search form (part of speech, language) and 
     * returns string for url such_as 
     * pos_id=$pos_id&lang_id=$lang_id
     * IF value is empty, the pair 'argument-value' is ignored
     * 
     * @param Array $url_args - array of pairs 'argument-value', f.e. ['pos_id'=>11, lang_id=>1]
     * @return String f.e. 'pos_id=11&lang_id=1'
     */
    public static function searchValuesByURL(Array $url_args=NULL) : String
    {
        $url = '';
        if (isset($url_args) && sizeof($url_args)) {
            $tmp=[];
            foreach ($url_args as $a=>$v) {
                if (is_array($v)) {
                    foreach ($v as $k=>$value) {
//                        $tmp[] = $a."[".$k."]=".$value;
                        $tmp[] = $a."%5B%5D=".$value;
                    }
                }
                elseif ($v!='' && !($a=='page' && $v==1) && !($a=='limit_num' && $v==10)) {
                    $tmp[] = "$a=$v";
                }
            }
            if (sizeof ($tmp)) {
                $url .= "?".implode('&',$tmp);
            }
        }
        
        return $url;
    }
    
    public static function isLangKarelian($lang_id) {
        if (in_array($lang_id,[4,5,6])) {
            return true;
        }
        return false;
    }
}
