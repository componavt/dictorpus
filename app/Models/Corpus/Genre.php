<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

use App\Library\Str;

use App\Models\Corpus\Collection;
use App\Models\Dict\Lang;

class Genre extends Model
{
    const LEGEND_ID = 60;
    public $timestamps = false;
    
    protected $fillable = ['name_en','name_ru', 'name_pl_en', 'name_pl_ru', 'corpus_id', 'parent_id', 'sequence_number'];
    
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
    use \App\Traits\Relations\BelongsTo\Corpus;
    
    // Belongs To Many Relations
    use \App\Traits\Relations\BelongsToMany\Texts;
    
    // Has Many Relations
    use \App\Traits\Relations\HasMany\Motypes;
    use \App\Traits\Relations\HasMany\Plots;
    use \App\Traits\Relations\HasMany\Cycles;
    
    public function parent()
    {
        return $this->belongsTo(Genre::class, 'parent_id');
    }
    
    public function children()
    {
        return $this->hasMany(Genre::class, 'parent_id', 'id')
                ->orderBy('sequence_number');
    }
    
    public function getSubGenres() {
        $out = [];
        foreach ($this->children as $genre) {
            $out[] = $genre;
            $out = array_merge($out, $genre->getSubGenres());
        }
        return $out;
    }
    
    public function getSubGenreIds() {
        $out = [];
        foreach ($this->children as $genre) {
            $out[] = $genre->id;
            $out = array_merge($out, $genre->getSubGenreIds());
        }
        return $out;
    }
    
    public function getNamePlAttribute() : String
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $column = "name_pl_" . $locale;
        if ($this->{$column}) {
            return $this->{$column};
        }
        
        $column = "name_" . $locale;
        return $this->{$column};
    }
/*    
    // Genre __has_many__ Texts
    public function texts(){
        return $this->belongsToMany(Text::class,'genre_text');
    }
*/    
    public function numberInList() {
        $count = self::whereParentId($this->parent_id)
//            ->whereCorpusId($this->corpus_id)
            ->where('sequence_number', '<', $this->sequence_number)
            ->count();
        return ($this->parent_id ? $this->parent->numberInList().'.' : '').($count+1);
    }
    
    /** Gets name of this genre by code, takes into account locale.
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
        
    /** Gets list of genres
     * 
     * @return Array [1=>'Bridal laments',..]
     */
    public static function getList($corpus_id=NULL) {     
        $locale = LaravelLocalization::getCurrentLocale();
        
        $genres = self::orderBy('name_'.$locale);
        
        if ($corpus_id) {        
            $genres = $genres->whereCorpusId($corpus_id);
        }
        
        $genres = $genres->get();
        
        $list = array();
        foreach ($genres as $row) {
            $list[$row->id] = $row->name;
        }
        
        return $list;         
    }
    
    public static function getNumeredList($corpus_id=NULL) {     
        $locale = LaravelLocalization::getCurrentLocale();
        
        $genres = self::orderBy('sequence_number');
        
        if ($corpus_id) {        
            $genres = $genres->whereCorpusId($corpus_id);
        }
        
        $genres = $genres->get();
        
        $list = array();
        foreach ($genres as $row) {
            $list[$row->id] = $row->numberInList(). '. '. $row->name;
        }
        
        return $list;         
    }
    
    /**
     * 
     * @param int $collection_id
     * @return Builder
     */
    public function collectionTexts($collection_id) {
        $lang_id = Collection::getCollectionLangs($collection_id);
        $genre_ids = array_merge([$this->id],$this->getSubGenreIds());
        return Text::whereIn('lang_id', $lang_id)
                          ->whereIn('id', function ($q) use ($genre_ids) {
                            $q->select('text_id')->from('genre_text')
                              ->whereIn('genre_id',$genre_ids);
                        });
        
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

        $genres = self::whereParentId(0)
                      ->orderBy('name_'.$locale)->get();
        $genre_groups = [];        
        foreach ($genres as $genre) {   
            $genre_groups[$genre->name] = array_merge([$genre->id],
                        self::whereParentId($genre->id)
                          ->get()->pluck('id')->toArray());
            
        }

        foreach ($genre_groups as $genre_name=>$genres) {   
            $for_all=Text::whereIn('id', function ($query) use ($genres) {
                            $query->select('text_id')->from('genre_text')
                                  ->whereIn('genre_id', $genres);
                        })->count();
            if (!$for_all) {
                continue;
            }            
            foreach (Lang::projectLangs() as $lang) {
                $num_texts = Text::whereLangId($lang->id)
                        ->whereIn('id', function ($query) use ($genres) {
                            $query->select('text_id')->from('genre_text')
                                  ->whereIn('genre_id', $genres);
                        })->count();
                $out[$lang->name][$genre_name] = number_format($num_texts, 0, ',', ' ');
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
