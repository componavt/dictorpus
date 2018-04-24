<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

use App\Models\Corpus\Text;

class Dialect extends Model
{
    public $timestamps = false;
    protected $fillable = ['lang_id', 'name_en', 'name_ru', 'code', 'sequence_number'];
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }
    
    // Dialect __belongs_to__ Lang
    public function lang()
    {
        return $this->belongsTo(Lang::class);
    }    

    /** Gets name of this dialect, takes into account locale.
     * 
     * @return String
     */
    public function getNameAttribute() : String
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $column = "name_" . $locale;
        return $this->{$column};
    }
    
    // Dialect __has_many__ Wordforms
    public function wordforms(){
        $builder = $this->belongsToMany(Wordform::class,'lemma_wordform')
                ->distinct('wordform_id');
        return $builder;
    }

    // Dialect __has_many__ Texts
    public function texts(){
        $builder = $this->belongsToMany(Text::class,'dialect_text');
        return $builder;
    }

    /** Gets name of dialects  by ID,
     * 
     * @param $id - dialect ID
     * @return string - localizated name of dialect
     */
    public static function getNameByID($id)
    {     
        $dialect = self::find($id);
        if ($dialect) {
            return $dialect->name;
        } else {
            return NULL;
        }
    }

    /** Gets list of dialects for language $lang_id,
     * if $lang_id is empty, gets all dialects
     * 
     * @param $lang_id - language ID
     * @return Array [1=>'Northern Veps',..]
     */
    public static function getList($lang_id=NULL)
    {     
        $locale = LaravelLocalization::getCurrentLocale();
        
//        $dialects = self::orderBy('name_'.$locale);
        $dialects = self::orderBy('sequence_number');
        
        if ($lang_id) {
            $dialects = $dialects->where('lang_id',$lang_id);
        }
        
        $dialects = $dialects->get();
        
        $list = array();
        foreach ($dialects as $row) {
            $list[$row->id] = $row->name;
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
                if ($v!='') {
                    $tmp[] = "$a=$v";
                }
            }
            if (sizeof ($tmp)) {
                $url .= "?".implode('&',$tmp);
            }
        }
        
        return $url;
    }
    public static function totalCount(){
        return self::count();
    }     
}
