<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

class PartOfSpeech extends Model
{
    protected $table = 'parts_of_speech';
    protected $fillable = ['name_en', 'name_ru', 'code', 'category'];
    
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
    
    // PartOfSpeech __has_many__ Gramset
    public function gramsets()
    {
        return $this->belongsToMany(Gramset::class,'gramset_pos','pos_id', 'gramset_id')
                ->orderBy('sequence_number');
    }
     
    // PartOfSpeech __has_many__ Lemma
    public function lemmas()
    {
        return $this->hasMany(Lemma::class,'pos_id');
    }
    
    public function identifiableName()
    {
        return $this->name;
    }    

    /** Gets localised name of this part of speech (current $locale used).
     * 
     * @return String
     */
    public function getNameAttribute()
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $column = "name_" . $locale;
        return $this->{$column};
    }
        
    // muttator
    public function getCodeAttribute($code)
    {
        if (in_array($code, ['POSTP', 'PREP'])) {
            $code = "ADP";
        }
        return $code;
    }
        
    // muttator
    public function getUnimorphAttribute()
    {
        $code = $this->code;
        if (in_array($code, ['CCONJ', 'SCONJ'])) {
            $code = "CONJ";
        } elseif ($code == 'NOUN') {
            $code = "N";
        } elseif ($code == 'PRON') {
            $code = "PRO";
        } elseif ($code == 'VERB') {
            $code = "V";
        }
        return $code;
    }
        
    // PartOfSpeech has many Wordforms through Lemma
    public function wordforms()
    {
        $lemmas = $this->lemmas;
        $wordforms = collect([]);
        foreach ($lemmas as $lemma) {
            $wordforms = $wordforms -> merge($lemma->wordforms);
        }
//        return $this->hasManyThrough('App\Models\Dict\Wordform', 'App\Models\Dict\Lemma', 'pos_id');
    }
    
    public static function getIDByCode($code)
    {
        $pos = self::getByCode($code);
        if ($pos && isset($pos->id)) {
//dd($pos->id);
            return $pos->id;
        }
    }
        
    public static function getNameById($id)
    {
        $pos = self::find($id);
        if ($pos && isset($pos->name)) {
//dd($pos->id);
            return $pos->name;
        }
    }
        
    public static function getByCode($code)
    {
        $pos = self::where('code', $code)->first();
        return $pos;
    }
        
    /** Gets all parts of speech for this category
     * 
     * @param int $category category of parts of speech
     * 
     * @return \Illuminate\Http\Response
     */
    public static function getByCategory($category, $order_by='id')
    {
        return self::where('category',$category)->orderBy($order_by)->get();
         
    }
    
    public static function getPhraseID() {
        return self::getIDByCode('PHRASE');
    }
        
    public static function getNameIDs() {
        return [self::getIDByCode('ADJ'), 
                self::getIDByCode('NOUN'), 
                self::getIDByCode('NUM'), 
                self::getIDByCode('PRON'), 
                self::getIDByCode('PROPN'), 
                self::getIDByCode('PRE')];
    }
    
    public static function isNameId($id) {
        return in_array($id, self::getNameIDs());
    }
    
    public function isName() {
        return self::isNameId($this->id);
    }
    
    public static function getVerbID() {
        return self::getIDByCode('VERB');
    }
    
    public static function isVerbId($id) {
        return $id==self::getVerbID();
    }
    
    public function isVerb() {
        return self::isVerbId($this->id);
    }
    
    /** Gets list of parts of speech, sorts by category and alphabetically 
     * 
     * @return Array [1=>'Adjective',..]
     */
    public static function getList()
    {
        $parts_of_speech = [];
        
        $locale = LaravelLocalization::getCurrentLocale();
        
        $pos_collec = self::where('name_'.$locale, '<>', '')->orderBy('category')
                          ->orderBy('name_'.$locale)->get();
        
        foreach ($pos_collec as $pos) {
            $parts_of_speech[$pos->id] = $pos->name;
        }
        
        return $parts_of_speech;         
    }
        
    /** Gets list of parts of speech group by category
     * 
     * @return Array ['Open class words' => [1=>'Adjective',..], ...]
     */
    public static function getGroupedList()
    {
        $categories = self::select('category')->groupBy('category')->orderBy('category')->get();
        
        $pos_grouped = array();
        
        $locale = LaravelLocalization::getCurrentLocale();
        
        foreach ($categories as $row) {
            foreach (self::getByCategory($row->category, 'name_'.$locale) as $pos) {
                $pos_grouped[\Lang::get('dict.pos_category_'.$row->category)][$pos->id] = $pos->name;
            }
        }
        
        return $pos_grouped;         
    }
        
    /** Gets list of parts of speech group by category with quantity of records of $model_name
     * 
     * @return Array ['Open class words' => [1=>'Adjective (5)',..], ...]
     */
    public static function getGroupedListWithQuantity($method_name)
    {
        $categories = self::select('category')->groupBy('category')->orderBy('category')->get();
        
        $pos_grouped = array();
        
        $locale = LaravelLocalization::getCurrentLocale();
        
        foreach ($categories as $row) {
            foreach (self::getByCategory($row->category, 'name_'.$locale) as $pos) {
                $count=0;
                $pos_name = $pos->name;
                if ($pos->$method_name()) {
                    $count=$pos->$method_name()->count();
                }
                if ($count) {
                    $pos_name .= " ($count)";
                }
                $pos_grouped[\Lang::get('dict.pos_category_'.$row->category)][$pos->id] = $pos_name;
            }
        }
        
        return $pos_grouped;         
    }
    
    public function isChangeable() {
        if ($this->gramsets && sizeof($this->gramsets)) {
            return true;
        } 
        return false;            
    }
    
    public static function changeablePOSList() {
        $out = [];
        foreach (self::all() as $pos) {
            if ($pos->isChangeable()) {
                $out[] = $pos;
            }
        } 
//dd($out);
        return $out;
    }
    
        
}

// 
// + sequence_number TINYINT, see http://universaldependencies.org/u/pos/index.html
// (1) Open class words
// (2) Closed class words
// (3) Other
// 
//      + Auxiliary verb, вспомогательный глагол, AUX 
//      + Determiner, детерминатив, DET 
// rename: INTER -> INTJ (interjection, междометие)
// rename: N     -> NOUN (noun, существительное)
// 
//      + Proper noun, имя собственное, PROPN
//      + Subordinating conjunction, подчинительный союз, SCONJ
//      
// (3) Other
//      + Punctuation, пунктуация, PUNCT
//      + Symbol, символ, SYM
//      + Other, другое, X