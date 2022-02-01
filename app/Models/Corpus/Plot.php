<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

use App\Library\Str;

use App\Models\Dict\Lang;

class Plot extends Model
{
    public $timestamps = false;
    
    protected $fillable = ['name_en','name_ru', 'genre_id', 'sequence_number'];
    
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
    use \App\Traits\Relations\BelongsTo\Genre;
    
    /** Gets name of this plot, takes into account locale.
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
        return $this->belongsToMany(Text::class,'plot_text');
    }
    
    /** Gets name by code, takes into account locale.
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
        
    /** Gets list of plots
     * 
     * @return Array [1=>'Bridal laments',..]
     */
    public static function getList($genre_id=NULL)
    {     
        $locale = LaravelLocalization::getCurrentLocale();
        
        $plots = self::orderBy('name_'.$locale);
        
        if ($genre_id) {        
            $plots = $plots->whereCorpusId($genre_id);
        }
        
        $list = [];
        foreach ($plots->get() as $row) {
            $list[$row->id] = $row->name;
        }
        
        return $list;         
    }
    
    /**
     * count the number of texts of plots and group by language
     * 
     * @return array [<plot_name> => [<lang_name> => <number_of_texts>, ... ], ... ]
     *              i.e. ['сказки'=>['вепсский'=>467, 'карельский: собственно карельское наречие'=>2, ...], ...]
     */
/*    public static function countTextsByIDGroupByLang() {
        $out = [];
        $locale = LaravelLocalization::getCurrentLocale();

        $plots = self::whereParentId(0)
                      ->orderBy('name_'.$locale)->get();
        $plot_groups = [];        
        foreach ($plots as $plot) {   
            $plot_groups[$plot->name] = array_merge([$plot->id],
                        self::whereParentId($plot->id)
                          ->get()->pluck('id')->toArray());
            
        }

        foreach ($plot_groups as $plot_name=>$plots) {   
            $for_all=Text::whereIn('id', function ($query) use ($plots) {
                            $query->select('text_id')->from('plot_text')
                                  ->whereIn('plot_id', $plots);
                        })->count();
            if (!$for_all) {
                continue;
            }            
            foreach (Lang::projectLangs() as $lang) {
                $num_texts = Text::whereLangId($lang->id)
                        ->whereIn('id', function ($query) use ($plots) {
                            $query->select('text_id')->from('plot_text')
                                  ->whereIn('plot_id', $plots);
                        })->count();
                $out[$lang->name][$plot_name] = number_format($num_texts, 0, ',', ' ');
            }
        }
        return $out;
    }*/
    
    public static function search(Array $url_args) {
        $locale = LaravelLocalization::getCurrentLocale();
        $plots = self::orderBy('sequence_number')->orderBy('name_'.$locale);
        $plots = self::searchByName($plots, $url_args['search_name']);
        $plots = self::searchByCorpus($plots, $url_args['search_corpus']);
        
        if ($url_args['search_id']) {
            $plots = $plots->where('id',$url_args['search_id']);
        }

        if ($url_args['search_genre']) {
            $plots = $plots->whereIn('genre_id',$url_args['search_genre']);
        }

        return $plots;
    }
    
    public static function searchByName($plots, $name) {
        if (!$name) {
            return $plots;
        }
        return $plots->where(function($q) use ($name){
                            $q->where('name_en','like', $name)
                              ->orWhere('name_ru','like', $name);
                });
    }
    
    public static function searchByCorpus($plots, $corpus_id) {
        if (!sizeof($corpus_id)) {
            return $plots;
        }
        return $plots->whereIn('genre_id', function($q) use ($corpus_id){
                            $q->select('id')->from('genres')
                              ->whereIn('corpus_id',$corpus_id);
                });
    }
    public static function urlArgs($request) {
        $url_args = Str::urlArgs($request) + [
                    'search_corpus'   => (array)$request->input('search_corpus'),
                    'search_genre'   => (array)$request->input('search_genre'),
                    'search_id'  => (int)$request->input('search_id'),
                    'search_name' => $request->input('search_name'),
                ];
        
        return $url_args;
    }
}
