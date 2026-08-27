<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;

use App\Library\Str;

use App\Models\Corpus\Pubpart;

class Publication extends Model implements HasMediaConversions
{
    public $timestamps = false;
    protected $fillable = ['is_periodic', 'authors', 'title', 'addition_info', 'year'];

    use HasMediaTrait;
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }

    public function registerMediaConversions()
    {
        $this->addMediaConversion('thumb')
            ->setWidth(200)
            ->performOnCollections('covers');
    }

    public function registerMediaCollections()
    {
        $this->addMediaCollection('covers')
            ->singleFile();
    }

    public function identifiableName()
    {
        return $this->title;
    }

    // Belongs To Many Relations    
    use \App\Traits\Relations\BelongsToMany\Sources;
    use \App\Traits\Relations\HasMany\Pubparts;

    // Methods
    use \App\Traits\Methods\search\byID;

    public function getFullInfoAttribute(): String
    {
        return ($this->authors ? $this->authors . '. ' : '') .
            $this->title .
            ($this->add_information ? '. ' . $this->add_information : '') .
            ($this->year ? '. ' . $this->year : '');
    }

    public function getTitleForListAttribute(): String
    {
        return ($this->authors ? $this->authors . '. ' : '') .
            $this->title .
            ($this->year ? '. ' . $this->year : '');
    }

    public function texts()
    {
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
        $objs = self::orderBy('title')->get();

        $list = array();
        foreach ($objs as $row) {
            $list[$row->id] = $row->title_for_list;
        }

        return $list;
    }

    public static function store(array $data, $photo = null)
    {
        if (!empty($data['is_periodic'])) {
            $data['year'] = null;
        }
        $publication = self::create($data);

        $publication->storeAddition($data);
        $publication->storeCover($photo);

        return $publication;
    }

    public function modify(array $data, $photo = null)
    {
        if (!empty($data['is_periodic'])) {
            $data['year'] = null;
        }
        $this->fill($data)->save();

        $this->storeAddition($data);
        $this->storeCover($photo);
    }

    public function storeAddition(array $data)
    {
        if (!empty($data['pubparts']) && is_array($data['pubparts'])) {
            foreach ($data['pubparts'] as $pubpart_id => $pubpart_data) {
                if (empty($pubpart_data['title']) && empty($pubpart_data['number']) && empty($pubpart_data['year']) && empty($pubpart_data['issue_date'])) {
                    continue;
                }
                $pubpart = Pubpart::find($pubpart_id);
                $pubpart_data['publication_id'] = $this->id;
                $pubpart->fill($pubpart_data)->save();
            }
        }

        if (!empty($data['new_pubparts']) && is_array($data['new_pubparts'])) {
            foreach ($data['new_pubparts'] as $pubpart_data) {
                if (empty($pubpart_data['title']) && empty($pubpart_data['number']) && empty($pubpart_data['year']) && empty($pubpart_data['issue_date'])) {
                    continue;
                }
                $pubpart_data['publication_id'] = $this->id;
                Pubpart::create($pubpart_data);
            }
        }
    }

    protected function storeCover($photo)
    {
        if (!$photo) {
            return;
        }

        $this->clearMediaCollection('covers');

        $this->addMedia($photo)
            ->toCollection('covers');
    }

    public function defaultPubpartYear()
    {
        $pubpart = $this->pubparts()
            ->whereNotNull('year')
            ->orderBy('sequence_number', 'desc')
            ->first();

        if ($pubpart) {
            return $pubpart->year;
        }

        return $this->year;
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
            $objs->where('authors', 'like', '%' . $url_args['search_authors'] . '%');
        }

        if (!empty($url_args['search_title'])) {
            $objs->where('title', 'like', '%' . $url_args['search_title'] . '%');
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
