<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

class Motype extends Model
{
    public $timestamps = false;
    
    protected $fillable = ['name_en','name_ru', 'genre_id', 'code'];
    
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
    
    // Has Many Relations
    use \App\Traits\Relations\HasMany\Motives;
    
    // Methods
    use \App\Traits\Methods\getByGenreID;
    use \App\Traits\Methods\getNameAttribute;
    use \App\Traits\Methods\getNameByID;
    
    public function texts()
    {
        $motype_id = $this->id;
        $texts = Text::whereIn('id', function($query) use ($motype_id) {
                                $query->select('text_id')->from('motive_text')
                                      ->whereIn('motive_id', function ($q2) use ($motype_id) {
                                          $q2->select('id')->from('motives')
                                             ->whereMotypeId($motype_id)
                                             ->orWhereIn('parent_id', function ($q3) use ($motype_id) {
                                                 $q3->select('id')->from('motives')
                                                    ->whereMotypeId($motype_id);
                                             });
                                      });
                              });
        return $texts;
    }
    
    public static function getList($genre_id=NULL) {     
        $recs = self::orderBy('genre_id')->orderBy('code');
        
        if ($genre_id) {        
            $recs = $recs->whereGenreId($genre_id);
        }
        
        $recs = $recs->get();
        
        $list = array();
        foreach ($recs as $row) {
            $list[$row->id] = $row->code. '. '. $row->name;
        }
        
        return $list;         
    }
    
    public static function urlArgs($request) {
        $url_args = url_args($request) + [
                    'search_genre'   => (array)$request->input('search_genre'),
                    'search_id'  => (int)$request->input('search_id'),
                    'search_name' => $request->input('search_name'),
                ];
        
        return $url_args;
    }
    
    public static function search(Array $url_args) {
        $recs = self::orderBy('genre_id')->orderBy('code');
        $recs = self::searchByGenres($recs, $url_args['search_genre']);
        $recs = self::searchById($recs, $url_args['search_id']);
        $recs = self::searchByName($recs, $url_args['search_name']);
//dd(to_sql($recs));        
        return $recs;
    }
    
    use \App\Traits\Methods\search\byGenres;
    use \App\Traits\Methods\search\byID;
    use \App\Traits\Methods\search\byName;    
            
}
