<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use App\Library\Str;

class Bible extends Model
{
    public $timestamps = false;

    protected $fillable = ['name_en', 'name_ru', 'sequence_number'];

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
    use \App\Traits\Relations\BelongsToMany\Texts;

    // Methods
    use \App\Traits\Methods\getListForField;
    use \App\Traits\Methods\getNameAttribute;
    use \App\Traits\Methods\getNameByID;
    use \App\Traits\Methods\search\byID;
    use \App\Traits\Methods\search\byName;

    /** Gets list of objects
     * 
     * @return Array [1=>'Bridal laments',..]
     */
    public static function getList()
    {
        $objs = self::orderBy('sequence_number');

        $list = [];
        foreach ($objs->get() as $row) {
            $list[$row->id] = $row->name;
        }

        return $list;
    }

    public static function search(array $url_args)
    {
        $locale = LaravelLocalization::getCurrentLocale();

        $objs = self::orderBy('name_' . $locale);
        $objs = self::searchById($objs, $url_args['search_id']);
        $objs = self::searchByName($objs, $url_args['search_name']);

        return $objs;
    }

    public static function urlArgs($request)
    {
        $url_args = Str::urlArgs($request) + [
            'search_id'  => (int)$request->input('search_id'),
            'search_name' => $request->input('search_name'),
        ];

        return $url_args;
    }
}
