<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

use App\Library\Str;

use App\Models\Dict\Lang;

class Genre extends Model
{
    public $timestamps = false;
    
    protected $fillable = ['name_en','name_ru', 'corpus_id', 'parent_id', 'sequence_number'];
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }

    // Belongs To Relations
    use \App\Traits\Relations\BelongsTo\Corpus;
    
    public function parent()
    {
        return $this->belongsTo(Genre::class, 'parent_id');
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
        $locale = LaravelLocalization::getCurrentLocale();

        $genres = self::orderBy('name_'.$locale)->get();
                
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
    
    public static function search(Array $url_args) {
        $locale = LaravelLocalization::getCurrentLocale();
        $genres = self::orderBy('sequence_number')->orderBy('name_'.$locale);
        $genres = self::searchByName($genres, $url_args['search_name']);
        
        if ($url_args['search_id']) {
            $genres = $genres->where('id',$url_args['search_id']);
        }

        if ($url_args['search_corpus']) {
            $genres = $genres->where('corpus_id',$url_args['search_corpus']);
        }

        return $genres;
    }
    
    public static function searchByName($genres, $name) {
        if (!$name) {
            return $genres;
        }
        return $genres->where(function($q) use ($name){
                            $q->where('name_en','like', $name)
                              ->orWhere('name_ru','like', $name);
                });
    }
    
    public static function urlArgs($request) {
        $url_args = Str::urlArgs($request) + [
                    'search_corpus'   => (int)$request->input('search_corpus'),
                    'search_id'  => (int)$request->input('search_id'),
                    'search_name' => $request->input('search_name'),
                ];
        
        return $url_args;
    }
}
