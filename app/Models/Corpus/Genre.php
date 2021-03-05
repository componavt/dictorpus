<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

use App\Models\Dict\Lang;

class Genre extends Model
{
    public $timestamps = false;
    
    protected $fillable = ['name_en','name_ru'];
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }

    /** Gets name of this genre, takes into account locale.
     * 
     * @return String
     */
    public function getNameAttribute() : String
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $column = "name_" . $locale;
        return $this->{$column};
    }
    
    // Genre __has_many__ Texts
    public function texts(){
        return $this->belongsToMany(Text::class,'genre_text');
    }
    
    /** Gets list of genres
     * 
     * @return Array [1=>'Bridal laments',..]
     */
    public static function getList()
    {     
        $locale = LaravelLocalization::getCurrentLocale();
        
        $genres = self::orderBy('name_'.$locale)->get();
        
        $list = array();
        foreach ($genres as $row) {
            $list[$row->id] = $row->name;
        }
        
        return $list;         
    }
    
    /**
     * count the number of texts of genres and group by language
     * 
     * @return array [<genre_name> => [<lang_name> => <number_of_texts>, ... ], ... ]
     *              i.e. ['сказки'=>['вепсский'=>467, 'карельский: собственно карельское наречие'=>2, ...], ...]
     */
    public static function countTextsByIDGroupByLang() {
        $out = [];

        $genres = self::all();
                
        foreach ($genres as $genre) {   
            $genre_id=$genre->id;
            foreach (Lang::projectLangs() as $lang) {
                $num_texts = Text::whereLangId($lang->id)
                        ->whereIn('id', function ($query) use ($genre_id) {
                            $query->select('text_id')->from('genre_text')
                                  ->whereGenreId($genre_id);
                        })
                        ->count();
                $out[$lang->name][$genre->name] = number_format($num_texts, 0, ',', ' ');
            }
        }
        return $out;
    }
}
