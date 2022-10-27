<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

use App\Library\Str;

class Cycle extends Model
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
    
    // Belongs To Many Relations
    use \App\Traits\Relations\BelongsToMany\Texts;
    use \App\Traits\Relations\BelongsToMany\Topics;
    
    // Methods
    use \App\Traits\Methods\getByGenreID;
    use \App\Traits\Methods\getListForField;
    use \App\Traits\Methods\getNameAttribute;
    use \App\Traits\Methods\getNameByID;
    
    /** Gets list of objects
     * 
     * @return Array [1=>'Bridal laments',..]
     */
    public static function getList($field_id=NULL)
    {    
        return self::getListForField($field_id, 'genre_id');
    }
    
    public static function search(Array $url_args) {
        $locale = LaravelLocalization::getCurrentLocale();
        
        $objs = self::orderBy('sequence_number')->orderBy('name_'.$locale);
        $objs = self::searchById($objs, $url_args['search_id']);
        $objs = self::searchByName($objs, $url_args['search_name']);
        $objs = self::searchByGenres($objs, $url_args['search_genre']);
        $objs = self::searchByCorpus($objs, $url_args['search_corpus']);

        return $objs;
    }

    use \App\Traits\Methods\search\byGenres;
    use \App\Traits\Methods\search\byID;
    use \App\Traits\Methods\search\byName;
    
    public static function searchByCorpus($objs, $corpus_id) {
        if (!sizeof($corpus_id)) {
            return $objs;
        }
        return $objs->whereIn('genre_id', function($q) use ($corpus_id){
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
