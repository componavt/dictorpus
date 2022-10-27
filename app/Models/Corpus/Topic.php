<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

use LaravelLocalization;

use App\Library\Str;

class Topic extends Model
{
    public $timestamps = false;
    
    protected $fillable = ['name_en','name_ru', 'sequence_number'];
    
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
    use \App\Traits\Relations\BelongsToMany\Plots;
    use \App\Traits\Relations\BelongsToMany\Texts;
    
    // Methods
    use \App\Traits\Methods\getNameAttribute;
    
    /** Gets name of this plot, takes into account locale.
     * 
     * @return String
     */
    public function getGenreIdAttribute() : String
    {
        $plot = $this->plots()->first();
        if ($plot) {
            return $plot->genre_id;
        }
        return '';
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
    public static function getList()
    {     
//        $locale = LaravelLocalization::getCurrentLocale();
//        $recs = self::orderBy('name_'.$locale);
        
        $recs = self::orderBy('sequence_number');
        
        $list = [];
        foreach ($recs->get() as $row) {
            $list[$row->id] = $row->name;
        }
        
        return $list;         
    }
    
    public static function search(Array $url_args) {
        $locale = LaravelLocalization::getCurrentLocale();
        $builder = self::orderBy('name_'.$locale);//orderBy('sequence_number')->
        $builder = self::searchByName($builder, $url_args['search_name']);
        $builder = self::searchByPlotGenreCorpus($builder, $url_args['search_plot'], 
                $url_args['search_genre'], $url_args['search_corpus']);
        
        if ($url_args['search_id']) {
            $builder = $builder->where('id',$url_args['search_id']);
        }

        return $builder;
    }
    
    public static function searchByName($builder, $name) {
        if (!$name) {
            return $builder;
        }
        return $builder->where(function($q) use ($name){
                            $q->where('name_en','like', $name)
                              ->orWhere('name_ru','like', $name);
                });
    }
    
    public static function searchByPlotGenreCorpus($builder, $plot_id, $genre_id, $corpus_id) {
        if (!sizeof($plot_id) && !sizeof($genre_id) && !sizeof($corpus_id)) {
            return $builder;
        }
        return $builder->whereIn('id', function($q1) use ($plot_id, $genre_id, $corpus_id){
                    $q1->select('topic_id')->from('plot_topic');
                    if (sizeof($plot_id)) {
                        $q1->whereIn('plot_id',$plot_id);
                    }
                    if (sizeof($genre_id) || sizeof($corpus_id)) {
                        $q1->whereIn('plot_id', function ($q2) use ($genre_id, $corpus_id) {
                            $q2->select('id')->from('plots');
                            if (sizeof($genre_id)) {
                                $q2->whereIn('genre_id',$genre_id);
                            }
                            if (sizeof($corpus_id)) {
                                $q2->whereIn('genre_id', function ($q3) use ($corpus_id) {
                                    $q3->select('id')->from('genres')
                                       ->whereIn('corpus_id',$corpus_id);
                                });
                            }
                        });
                    }
                });
    }
    
    public static function urlArgs($request) {
        $url_args = Str::urlArgs($request) + [
                    'search_corpus'   => (array)$request->input('search_corpus'),
                    'search_genre'   => (array)$request->input('search_genre'),
                    'search_id'  => (int)$request->input('search_id'),
                    'search_name' => $request->input('search_name'),
                    'search_plot'   => (array)$request->input('search_plot'),
                ];
        
        return $url_args;
    }
    
    public function plotsToString($div=', ') {
        $out = [];
        foreach ($this->plots as $plot) {
            $out[] = $plot->name;
        }
        return join($div, $out);
    }
    
    public static function nextSequenceNumber($plot_ids=[]) {
        $last_topic = self::latest('sequence_number');
        if (sizeof($plot_ids)) {
            $last_topic->whereIn('id', function ($q) use ($plot_ids) {
                $q->select('topic_id')->from('plot_topic')
                  ->whereIn('plot_id', $plot_ids);
            });
        }
        $last_topic = $last_topic->first();
        if (!$last_topic) {
            return 1;
        }
        return 1+ $last_topic->sequence_number ?? 0;        
    }
    
    public function saveAddition($plot_ids) {
        if (!$this->sequence_number) {
            $this->sequence_number = self::nextSequenceNumber($plot_ids);
            $this->save();
        }
        $this->plots()->detach();
        $this->plots()->attach($plot_ids);        
    }
}
