<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

use App\Library\Str;

use App\Models\Corpus\Word;

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
    
    // Methods
    use \App\Traits\Methods\getNameAttribute;
    
    // Belongs To Relations
    use \App\Traits\Relations\BelongsTo\Lang;

    // Belongs To Many Relations
    use \App\Traits\Relations\BelongsToMany\Texts;
    
    public function wordforms(){
        $builder = $this->belongsToMany(Wordform::class,'lemma_wordform')
                ->distinct('wordform_id');
        return $builder;
    }

    /** Gets ID of this dialect by code.
     * 
     * @return int
     */
    public static function getIDByCode($code) : Int
    {
        $dialect = self::where('code',$code)->first();
        if ($dialect) {
            return $dialect->id;
        }
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

    public static function getByLang($lang_id) {
        return self::where('lang_id', $lang_id)->get();
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
    
    /** Gets list of dialects group by languages
     * 
     * @return Array ['Vepsian' => [1=>'New written Veps',..], ...]
     */
    public static function getGroupedList()
    {
        $langs = self::groupBy('lang_id')->orderBy('lang_id')->get();
        
        $list = [];
        foreach ($langs as $row) {
            foreach (self::getList($row->lang_id) as $dialect_title => $dialect_id) {
                $list[Lang::getNameByID($row->lang_id)][$dialect_title] = $dialect_id;
            }
        }
        
        return $list;         
    }
    
    public static function getLangIDByID($dialect_id) {
        $dialect = self::find($dialect_id);
        if (!$dialect) {
            return NULL;
        }
        return $dialect->lang_id;
    }
        
    public function textsByGenre($genre_id){
        return $this->texts()->whereIn('text_id', function ($q) use ($genre_id) {
            $q->select('text_id')->from('genre_text')
              ->whereGenreId($genre_id);
        })->get();
    } 
    
    public function totalTexts() {
        return sizeof($this->texts);
    }
    
    public function totalWords() {
        $dialect_id = $this->id;
        return Word::whereIn('text_id', function ($q) use ($dialect_id) {
            $q -> select('text_id')->from('dialect_text')
               -> whereDialectId($dialect_id);
        })->count();
    }
    
    public static function urlArgs($request) {
        $url_args = Str::urlArgs($request) + [
                    'search_lang'     => (int)$request->input('search_lang'),
                ];
        
        return $url_args;
    }
    
    public static function search(Array $url_args) {
        $builder = self::orderBy('lang_id')->orderBy('sequence_number')->orderBy('id');
        $builder = self::searchByLang($builder, $url_args['search_lang']);
        return $builder;
    }
    
    public static function searchByLang($builder, $lang) {
        if (!$lang) {
            return $builder;
        }
        return $builder->where('lang_id',$lang);
    }
    
}
