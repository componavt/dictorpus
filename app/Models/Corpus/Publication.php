<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

use App\Library\Str;

class Publication extends Model
{
    public $timestamps = false;
    protected $fillable = ['authors', 'title', 'addition_info', 'year'];

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
        return $this->title;
    }

    // Belongs To Many Relations    
    use \App\Traits\Relations\BelongsToMany\Sources;

    // Methods
    use \App\Traits\Methods\search\byID;
    
    public function texts() {
        $id = $this->id;
        return Text::whereNotNull('source_id')
                ->whereIn('source_id', function ($q) use ($id) {
                    $q->select('id')->from('sources')
                      ->wherePublicationId($id);
                });
    }
    
    /** Gets list of publications
     * 
     * @return Array [1=>'Dialectal texts',..]
     */
    public static function getList()
    {
        $corpuses = self::orderBy('title')->get();

        $list = array();
        foreach ($corpuses as $row) {
            $list[$row->id] = ($row->authors ? $row->authors.'. ' : ''). $row->title. ($row->year ? '. '. $row->year : '');
        }

        return $list;
    }

    public static function urlArgs($request)
    {
        $url_args = Str::urlArgs($request) + [
            'limit_num'       => (int)$request->input('limit_num'),
            'page'            => (int)$request->input('page'),
            'search_authors'     => $request->input('search_authors'),
            'search_id'  => (int)$request->input('search_id'),
            'search_title'     => $request->input('search_title'),
            'search_year_from'     => $request->input('search_year_from'),
            'search_year_to'     => $request->input('search_year_to'),
       ];

        return $url_args;
    }
    
    public static function search(array $url_args)
    {
        $objs = self::orderBy('title');
        $objs = self::searchById($objs, $url_args['search_id']);
        
        if (!empty($url_args['search_authors'])) {
            $objs->where('authors', 'like', '%'.$url_args['search_authors'].'%');
        }

        if (!empty($url_args['search_title'])) {
            $objs->where('title', 'like', '%'.$url_args['search_title'].'%');
        }
        
        if (!empty($url_args['search_year_from'])) {
            $objs->where(function ($q) use ($url_args) {
                $q->whereNull('year')
                  ->orWhere('year', '>=', $url_args['search_year_from']);
            });
        }
        
        if (!empty($url_args['search_year_to'])) {
            $objs->where(function ($q) use ($url_args) {
                $q->whereNull('year')
                  ->orWhere('year', '<=', $url_args['search_year_to']);
            });
        }
        
        return $objs;
    }
}
