<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;
use DB;

use App\Models\Dict\Lang;
use App\Models\Corpus\Text;

class Corpus extends Model
{
    public $timestamps = false;
    protected $fillable = ['name_en', 'name_ru'];
    
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
    
    // Has Many Relations
    use \App\Traits\Relations\HasMany\Genres;
    use \App\Traits\Relations\HasMany\Texts;

    /** Gets name of this corpus by code, takes into account locale.
     * 
     * @return String
     */
    public static function getNameByID($id) : String
    {
        $item = self::where('id',$id)->first();
        if ($item) {
            return $item->name;
        }
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
    
    /**
     * count the number of texts of subcorpuses and group by language
     * 
     * select corpus_id, lang_id, count(*) from texts group by corpus_id, lang_id;
     * 
     * @return array [<corpus_name> => [<lang_name> => <number_of_texts>, ... ], ... ]
     *              i.e. ['библейские тексты (переводные)'=>['вепсский'=>467, 'карельский: собственно карельское наречие'=>2, ...], ...]
     */
    public static function countTextsByIDGroupByLang() {
        $out = [];

        $corpuses = self::all();
                
        foreach ($corpuses as $corpus) {        
            foreach (Lang::projectLangs() as $lang) {
                $num_texts = Text::whereLangId($lang->id)
                        ->whereCorpusId($corpus->id)
                        ->count();
                $out[$lang->name][$corpus->name] = number_format($num_texts, 0, ',', ' ');
            }
        }
        return $out;
    }
}
