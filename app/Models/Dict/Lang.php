<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

use App\Models\Corpus\Corpus;
use App\Models\Corpus\Text;
use App\Models\Corpus\Word;
use App\Models\Dict\Lemma;

class Lang extends Model
{
    public $timestamps = false;
    protected $fillable = ['name_en', 'name_ru', 'code', 'sequence_number'];
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }
    
    // Belongs To Many Relations
    use \App\Traits\Relations\BelongsToMany\Gramsets;

    // Has Many Relations
    use \App\Traits\Relations\HasMany\Corpuses;
    use \App\Traits\Relations\HasMany\Dialects;
    use \App\Traits\Relations\HasMany\Lemmas;
    use \App\Traits\Relations\HasMany\ReverseLemmas;
    use \App\Traits\Relations\HasMany\Texts;
    
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
           
    /** Gets name of this lang by code, takes into account locale.
     * 
     * @return String
     */
    public static function getNameByID($id) : String
    {
        $lang = self::where('id',$id)->first();
        if ($lang) {
            return $lang->name;
        }
    }
           
     
    /** Gets list of languages
     * 
     * @return Array [1=>'Vepsian',..]
     */
    public static function getList($without=[])
    {     
        $locale = LaravelLocalization::getCurrentLocale();
        
        $languages = self::orderBy('sequence_number')
                //orderBy('name_'.$locale)
                ->get();
        
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
        
        $languages = self::orderBy('sequence_number')
                //orderBy('name_'.$locale)
                ->get();
        
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
        
    /** Gets list of interface languages: Russian, English 
     * 
     * @return Array [2=>'Russian', 3=>'English']
     */
    public static function getListInterface()
    {     
        $ru_lang = self::where('code','ru')->first();
        if (!isset($list[$ru_lang->id])) {
            $list[$ru_lang->id] = $ru_lang->name;
        }
        
        $en_lang = self::where('code','en')->first();
        if (!isset($list[$en_lang->id])) {
            $list[$en_lang->id] = $en_lang->name;
        }
        
        return $list;         
    }
        
    /** Gets list of main meaning languages: Russian, English, Finnish 
     * 
     * @return Array [2=>'Russian', 3=>'English', 7=>'Finnish']
     */
    public static function getListForMeaning()
    {     
        $langs_for_meaning = self::getListInterface();
        $fi_lang = self::where('code','fi')->first();
        $langs_for_meaning[$fi_lang->id] = $fi_lang->name;      
        return $langs_for_meaning;         
    }
        
    /** Gets list of languages
     * 
     * @return Array [1=>'Vepsian',..]
     */
    public static function getListWithQuantity($method_name, $only_project_langs=false)
    {     
        $locale = LaravelLocalization::getCurrentLocale();
        
        $languages = $only_project_langs
                ? self::projectLangs()
                : self::orderBy('sequence_number')->get();
        
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

    public static function projectLangs() {
        $lang_coll = self::whereNotIn('code', ['en','ru','fi'])
                ->orderBy('sequence_number')->get();
        return $lang_coll;       
    }
    
    public static function projectLangIDs() {
        $ids = [];
        foreach (self::projectLangs() as $lang) {
           $ids[] = $lang->id; 
        }
        return $ids;       
    }
    
    public static function nonProjectLangs() {
        $lang_coll = self::whereIn('code', ['en','ru','fi'])
                ->orderBy('id')->get();
        return $lang_coll;       
    }
    
    public static function nonProjectLangIDs() {
        $ids = [];
        foreach (self::nonProjectLangs() as $lang) {
           $ids[] = $lang->id; 
        }
        return $ids;       
    }
    
    public static function countMarked() {
        $out = [];
        foreach (self::projectLangs() as $lang) {
            $total = Word::countByLang($lang->id);
            $marked = Word::countMarked($lang->id);
            $checked = Text::countCheckedWords($lang->id);
            $proc = $total ? 100*$marked/$total : 0;
            $out['total'][$lang->name] = number_format($total, 0,',', ' ');
            $out['marked'][$lang->name] = number_format($marked, 0,',', ' ');
            $out['checked'][$lang->name] = number_format($checked, 0,',', ' ');
            $out['ratio'][$lang->name] = number_format($proc, 1, ',', ' ');
//            $out[$lang->id] = [0=>$lang->name, 1=>number_format($proc, 0, ',', ' ')];
        }
        return $out;
    }
    
    public static function countLemmas() {
        $out = [];
        foreach (self::projectLangs() as $lang) {
            $total = Lemma::countByLang($lang->id);
            $out[$lang->name] = number_format($total, 0, ',', ' ');
        }
        return $out;
    }
    
    public static function countWordforms() {
        $out = [];
        foreach (self::projectLangs() as $lang) {
            $total = Wordform::countByLang($lang->id);
            $out[$lang->name] = number_format($total, 0, ',', ' ');
        }
        return $out;
    }
    
    public function mainDialect() {
        return self::mainDialectByID($this->id);
    }
    
    public static function mainDialectByID($lang_id) {
        switch ($lang_id) {
            case 1: return 43;
            case 4: return 46;
            case 5: return 44;
            case 6: return 42;
        }
        return NULL;
    }
    
}
