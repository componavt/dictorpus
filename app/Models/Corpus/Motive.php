<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

use App\Models\Corpus\Motype;

class Motive extends Model
{
    public $timestamps = false;
    
    protected $fillable = ['name_en', 'name_ru', 'motype_id', 'parent_id', 'code'];
    
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
    use \App\Traits\Relations\BelongsTo\ParentTrait;
    
    // Belongs To Many Relations
    use \App\Traits\Relations\BelongsToMany\Texts;
    
    // Has Many Relations
    use \App\Traits\Relations\HasMany\Children;
    
    // Methods
    use \App\Traits\Methods\getNameAttribute;
    use \App\Traits\Methods\getNameByID;
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function motype()
    {
        return $this->belongsTo(Motype::class);
    }
    
    public function genreName($with_number=false)
    {
        if (!$this->motype) {
            return;
        }
        if (!$this->motype->genre) {
            return;
        }
        return ($with_number ? $this->motype->genre->numberInList() : '')
             . '. '. $this->motype->genre->name;
    }
    
    public function getFullCodeAttribute() {
        $code = $this->code;
        if ($this->parent) {
           $code = $this->parent->code. '.'. $code;
        }
        if ($this->motype) {
           $code = $this->motype->code. '.'. $code;
        }
        return $code;
    }
    
    public function getFullNameAttribute() {
        $name = $this->name;
        if ($this->parent) {
           $name = $this->parent->name. ' ('. $name. ')';
        }
        return $name;
    }
        
    public function getFullNameWithCodeAttribute() {
        $name = $this->name;
        if ($this->parent) {
           $name = $this->parent->name. ' ('. $name. ')';
        }
        return $this->full_code.'. '.$name;
    }
        
    public static function getList($motype_id=NULL, $parent_id='undef') {     
        $recs = self::orderBy('motype_id')->orderBy('code');
        
        if ($motype_id) {        
            $recs = $recs->whereMotypeId($motype_id);
        }
        
        if ($parent_id === NULL) {            
            $recs = $recs->whereNull('parent_id');
        } elseif ($parent_id != 'undef') {            
            $recs = $recs->whereParentId($parent_id);
        }
        
        $recs = $recs->get();
        
        $list = array();
        foreach ($recs as $row) {
            $list[$row->id] = $row->full_code. '. '. $row->full_name;
        }
        
        return $list;         
    }
    
    public static function getGroupedList()
    {
        $types = Motype::orderBy('code')->get();
        
        $list = [];
        
 //       $locale = LaravelLocalization::getCurrentLocale();
        
        foreach ($types as $type) {
            foreach (self::whereMotypeId($type->id)->get() as $parent) {
                $children = self::whereParentId($parent->id);
                if (!$children->count()) {
                    $list[$type->code.'. '.$type->name][$parent->id] = $parent->code.'. '.$parent->name;
                } else {
                    foreach ($children->get() as $child) {
                        $list[$type->code.'. '.$type->name][$child->id] = $parent->code.$child->code.'. '.$parent->name.' '.$child->name;                        
                    }
                }
            }
        }
        
        return $list;         
    }
    
    public static function urlArgs($request) {
        $url_args = url_args($request) + [
                    'search_genre'   => (array)$request->input('search_genre'),
                    'search_motype'   => (array)$request->input('search_motype'),
                    'search_id'  => (int)$request->input('search_id'),
                    'search_name' => $request->input('search_name'),
                ];
        
        if ($url_args['search_id']==0) {
            $url_args['search_id'] = '';
        }
        return $url_args;
    }
    
    public static function search(Array $url_args) {
        $recs = self::orderBy('motype_id')->orderBy('code');
        $recs = self::searchByGenres($recs, $url_args['search_genre']);
        $recs = self::searchById($recs, $url_args['search_id']);
        $recs = self::searchByName($recs, $url_args['search_name']);
        
        if ($url_args['search_motype']) {
            $recs->whereIn('motype_id', $url_args['search_motype']);
        }
        return $recs;
    }
    
    use \App\Traits\Methods\search\byID;
    use \App\Traits\Methods\search\byName;   
    
    public static function searchByGenres($objs, $genres) {
        if (!sizeof($genres)) {
            return $objs;
        }

        foreach (Genre::whereIn('parent_id', $genres)->get() as $g) {
            $genres[] = $g->id;
        }
        
        return $objs->whereIn('motype_id', function ($q) use ($genres) {
                        $q->select('id')->from('motypes')
                          ->whereIn('genre_id',$genres);
                    });
    }
    
}
