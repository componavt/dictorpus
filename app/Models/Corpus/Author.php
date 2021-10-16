<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

use App\Library\Str;
use App\Models\Corpus\AuthorName;

class Author extends Model
{
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
    
    // Belongs To Many Relations
    use \App\Traits\Relations\BelongsToMany\Texts;
    
    // Has Many Relations
    use \App\Traits\Relations\HasMany\AuthorNames;

    public function namesToString() {
        $names = [];
        foreach ($this->authorNames as $name) {
            $names[]= $name->lang->code.': '.$name->name;
        }
        return join(', ', $names);
    }
    
    public function getNameByLang($lang_id) {
        $name = $this->authorNames()->where('lang_id',$lang_id)->first();
        return $name->name ?? null;
    }
    
    /** Gets list of authors
     * 
     * @return Array [1=>'Komissarova, Irina',..]
     */
    public static function getList()
    {     
        $locale = LaravelLocalization::getCurrentLocale();
        
        $authors = self::orderBy('name_'.$locale)->get();
        
        $list = array();
        foreach ($authors as $row) {
            $list[$row->id] = $row->name;
        }
        
        return $list;         
    }
    

    /**
     * 
     * @param array $names [<lang1_id> => <author_name1>,  <lang2_id> => <author_name2>, ...]]
     * @return Meaning - object
     */
    public function updateNames($names){
//dd($names);        
        foreach ($names as $lang=>$name) {
            if ($name) { 
                $name_obj = AuthorName::firstOrCreate(['author_id' => $this->id, 'lang_id' => $lang]);
                $name_obj -> name = $name;
                $name_obj -> save();
            } else {
                $name_obj = AuthorName::where('author_id',$this->id)->where('lang_id',$lang)->first();
                if ($name_obj) {
                    $name_obj -> delete();
                }
            }
        }
        return null;
    }
    
    public static function searchByName($builder, $search_name) {
        if (!$search_name) {
            return $builder;
        }
        return $builder ->where('name_en','like', $search_name)
                        ->orWhere('name_ru','like', $search_name)
                        ->orwhereIn('id', function ($q2) use ($search_name) {
                            $q2->select('author_id')->from('author_names')
                               ->where('name', 'like', $search_name);
                    });
    }
    
    public static function urlArgs($request) {
        $url_args = Str::urlArgs($request) + [
                    'limit_num'       => (int)$request->input('limit_num'),
                    'page'            => (int)$request->input('page'),
                    'search_name'     => $request->input('search_name'),
                ];
              
        return $url_args;
    }
    
}
